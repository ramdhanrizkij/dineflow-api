<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Menu;
use App\Models\MenuAddons;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddons;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $status    = $request->query('status');
        $tableId   = $request->query('table_id');
        $sort      = $request->query('sort', 'created_at');
        $direction = $request->query('sort_direction', 'desc');

        $allowedSorts = ['id', 'order_number', 'status', 'total', 'opened_at', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'desc';

        $query = Order::with(['table:id,code', 'openedBy:id,name', 'items']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($tableId) {
            $query->where('table_id', $tableId);
        }

        $orders = $query->orderBy($sort, $direction)->paginate($perPage);

        $orders->through(fn ($order) => $this->formatOrderSummary($order));

        return ApiResponse::paginated($orders, 'Orders retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $openedBy    = Auth::id();

            $order = Order::create([
                'order_number'  => $orderNumber,
                'table_id'      => $request->table_id,
                'customer_name' => $request->customer_name,
                'status'        => 'draft',
                'opened_by'     => $openedBy,
                'opened_at'     => now(),
            ]);

            $this->syncItems($order, $request->items);
            $this->recalculateTotals($order);
            $this->logStatusChange($order, null, 'draft', $openedBy);

            return $order->load(['table:id,code', 'openedBy:id,name', 'items.menu:id,name', 'items.variant:id,name', 'items.addons.addon:id,name']);
        });

        return ApiResponse::created($this->formatOrderDetail($order), 'Order created successfully');
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['table:id,code', 'openedBy:id,name', 'closedBy:id,name', 'items.menu:id,name', 'items.variant:id,name', 'items.addons.addon:id,name', 'payment']);

        return ApiResponse::success($this->formatOrderDetail($order), 'Order retrieved successfully');
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order = DB::transaction(function () use ($request, $order) {
            $userId = Auth::id();

            if ($request->filled('status') && $request->status !== $order->status) {
                $fromStatus = $order->status;

                $updateData = ['status' => $request->status];

                if ($request->status === 'closed') {
                    $updateData['closed_by'] = $userId;
                    $updateData['closed_at'] = now();
                }

                $order->update($updateData);
                $this->logStatusChange($order, $fromStatus, $request->status, $userId);
            }

            if ($request->has('customer_name')) {
                $order->update(['customer_name' => $request->customer_name]);
            }

            if ($request->has('items')) {
                $this->syncItems($order, $request->items);
                $this->recalculateTotals($order);
            }

            return $order->load(['table:id,code', 'openedBy:id,name', 'closedBy:id,name', 'items.menu:id,name', 'items.variant:id,name', 'items.addons.addon:id,name', 'payment']);
        });

        return ApiResponse::success($this->formatOrderDetail($order), 'Order updated successfully');
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return ApiResponse::deleted('Order deleted successfully');
    }

    public function statusLogs(Order $order): JsonResponse
    {
        $logs = $order->statusLogs()
            ->with('changedBy:id,name')
            ->orderBy('changed_at', 'asc')
            ->get()
            ->map(fn ($log) => [
                'id'          => $log->id,
                'from_status' => $log->from_status,
                'to_status'   => $log->to_status,
                'changed_by'  => $log->changedBy?->name,
                'changed_at'  => $log->changed_at,
            ]);

        return ApiResponse::success($logs, 'Status logs retrieved successfully');
    }

    public function payments(Order $order): JsonResponse
    {
        $payment = $order->payment()->with('paidBy:id,name')->first();

        return ApiResponse::success(
            $payment ? $this->formatPayment($payment) : null,
            'Payment retrieved successfully'
        );
    }

    public function storePayment(StorePaymentRequest $request, Order $order): JsonResponse
    {
        if ($order->payment()->exists()) {
            return ApiResponse::error('This order has already been paid.', 422);
        }

        $changeAmount = max(0, $request->amount_paid - $order->total);

        $payment = Payment::create([
            'order_id'       => $order->id,
            'payment_method' => $request->payment_method,
            'amount_paid'    => $request->amount_paid,
            'change_amount'  => $changeAmount,
            'paid_by'        => Auth::id(),
            'paid_at'        => now(),
        ]);

        $fromStatus = $order->status;
        $order->update([
            'status'    => 'closed',
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);
        $this->logStatusChange($order, $fromStatus, 'closed', Auth::id());

        $payment->load('paidBy:id,name');

        return ApiResponse::created($this->formatPayment($payment), 'Payment recorded successfully');
    }

    private function syncItems(Order $order, array $items): void
    {
        $order->items()->delete();

        foreach ($items as $itemData) {
            $menu    = Menu::findOrFail($itemData['menu_id']);
            $variant = isset($itemData['variant_id'])
                ? MenuVariant::find($itemData['variant_id'])
                : null;

            $price = $variant ? $variant->price : $menu->base_price;

            $orderItem = OrderItem::create([
                'order_id'       => $order->id,
                'menu_id'        => $menu->id,
                'variant_id'     => $variant?->id,
                'qty'            => $itemData['qty'],
                'price_snapshot' => $price,
                'status'         => 'pending',
                'notes'          => $itemData['notes'] ?? null,
            ]);

            if (!empty($itemData['addon_ids'])) {
                foreach ($itemData['addon_ids'] as $addonId) {
                    $addon = MenuAddons::findOrFail($addonId);
                    OrderItemAddons::create([
                        'order_item_id'  => $orderItem->id,
                        'addon_id'       => $addon->id,
                        'price_snapshot' => $addon->price,
                    ]);
                }
            }
        }
    }

    private function recalculateTotals(Order $order): void
    {
        $order->load('items.addons');

        $subtotal = $order->items->sum(function ($item) {
            $addonTotal = $item->addons->sum('price_snapshot');
            return ($item->price_snapshot + $addonTotal) * $item->qty;
        });

        $tax           = round($subtotal * 0.10, 2);
        $serviceCharge = round($subtotal * 0.05, 2);
        $total         = round($subtotal + $tax + $serviceCharge, 2);

        $order->update([
            'subtotal'       => $subtotal,
            'tax'            => $tax,
            'service_charge' => $serviceCharge,
            'total'          => $total,
        ]);
    }

    private function logStatusChange(Order $order, ?string $from, string $to, int $userId): void
    {
        $order->statusLogs()->create([
            'from_status' => $from,
            'to_status'   => $to,
            'changed_by'  => $userId,
            'changed_at'  => now(),
        ]);
    }

    private function formatOrderSummary(Order $order): array
    {
        return [
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'table'          => $order->table?->code,
            'customer_name'  => $order->customer_name,
            'status'         => $order->status,
            'items_count'    => $order->items->count(),
            'subtotal'       => $order->subtotal,
            'tax'            => $order->tax,
            'service_charge' => $order->service_charge,
            'total'          => $order->total,
            'opened_by'      => $order->openedBy?->name,
            'opened_at'      => $order->opened_at,
        ];
    }

    private function formatOrderDetail(Order $order): array
    {
        return [
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'table'          => $order->table?->code,
            'customer_name'  => $order->customer_name,
            'status'         => $order->status,
            'subtotal'       => $order->subtotal,
            'tax'            => $order->tax,
            'service_charge' => $order->service_charge,
            'total'          => $order->total,
            'opened_by'      => $order->openedBy?->name,
            'opened_at'      => $order->opened_at,
            'closed_by'      => $order->closedBy?->name,
            'closed_at'      => $order->closed_at,
            'items'          => $order->items->map(fn ($item) => [
                'id'             => $item->id,
                'menu'           => $item->menu?->name,
                'variant'        => $item->variant?->name,
                'qty'            => $item->qty,
                'price_snapshot' => $item->price_snapshot,
                'status'         => $item->status,
                'notes'          => $item->notes,
                'addons'         => $item->addons->map(fn ($a) => [
                    'id'             => $a->id,
                    'name'           => $a->addon?->name,
                    'price_snapshot' => $a->price_snapshot,
                ]),
            ]),
            'payment'        => $order->payment ? $this->formatPayment($order->payment) : null,
            'created_at'     => $order->created_at,
            'updated_at'     => $order->updated_at,
        ];
    }

    private function formatPayment(Payment $payment): array
    {
        return [
            'id'             => $payment->id,
            'order_id'       => $payment->order_id,
            'payment_method' => $payment->payment_method,
            'amount_paid'    => $payment->amount_paid,
            'change_amount'  => $payment->change_amount,
            'paid_by'        => $payment->paidBy?->name,
            'paid_at'        => $payment->paid_at,
        ];
    }
}
