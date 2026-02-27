<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuAddons;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('email', 'admin@dineflow.com')->first();
        $waiter  = User::where('email', 'waiter@dineflow.com')->first();
        $cashier = User::where('email', 'cashier@dineflow.com')->first();
        $siti    = User::where('email', 'siti.waiter@dineflow.com')->first();
        $budi    = User::where('email', 'budi.waiter@dineflow.com')->first();
        $rina    = User::where('email', 'rina.cashier@dineflow.com')->first();

        $tables = Table::pluck('id', 'code');

        $menus    = Menu::pluck('id', 'name');
        $variants = MenuVariant::all()->groupBy('menu_id');
        $addons   = MenuAddons::all()->groupBy('menu_id');

        $getVariant = function (int $menuId, string $variantName) use ($variants): ?MenuVariant {
            return $variants->get($menuId)?->firstWhere('name', $variantName);
        };

        $getAddon = function (int $menuId, string $addonName) use ($addons): ?MenuAddons {
            return $addons->get($menuId)?->firstWhere('name', $addonName);
        };

        // Helper: count total price from (base_price + additional_price variant)
        $itemPrice = function (int $menuId, ?int $variantId) use ($variants): float {
            $menu          = Menu::find($menuId);
            $additionalPrice = 0;
            if ($variantId) {
                $variant         = MenuVariant::find($variantId);
                $additionalPrice = (float) ($variant?->additional_price ?? 0);
            }

            return (float) $menu->base_price + $additionalPrice;
        };

        // Sample Order 1
        $openedAt1 = Carbon::yesterday()->setTime(17, 12, 0);
        $closedAt1 = Carbon::yesterday()->setTime(17, 58, 0);

        $menuNasiGoreng = $menus['Nasi Goreng Spesial'];
        $menuAyamGoreng = $menus['Ayam Goreng'];
        $menuEsTeh      = $menus['Teh'];
        $menuKerupuk    = $menus['Kerupuk Udang'];

        $varNasiGorengReg  = $getVariant($menuNasiGoreng, 'Regular');
        $varAyamGoreng14   = $getVariant($menuAyamGoreng, '1/4 Ayam');
        $varEsTehManis     = $getVariant($menuEsTeh, 'Es Teh Manis');
        $addonEkstraTelur  = $getAddon($menuNasiGoreng, 'Ekstra Telur');

        $priceNasiGoreng1  = $itemPrice($menuNasiGoreng, $varNasiGorengReg?->id);  // 28000
        $priceAyamGoreng1  = $itemPrice($menuAyamGoreng, $varAyamGoreng14?->id);   // 28000
        $priceEsTeh1       = $itemPrice($menuEsTeh, $varEsTehManis?->id);          // 5000
        $priceKerupuk1     = $itemPrice($menuKerupuk, null);                        // 5000

        $subtotal1     = ($priceNasiGoreng1 * 1) + ($priceAyamGoreng1 * 1)
                       + ($priceEsTeh1 * 2) + ($priceKerupuk1 * 1) + 3000; // +addon telur
        $tax1          = round($subtotal1 * 0.10, 2);
        $service1      = round($subtotal1 * 0.05, 2);
        $total1        = $subtotal1 + $tax1 + $service1;

        $order1 = Order::create([
            'order_number'   => 'ORD-20260226-001',
            'table_id'       => $tables['T-03'],
            'customer_name'  => 'Pak Hendra',
            'status'         => 'closed',
            'opened_by'      => $waiter->id,
            'closed_by'      => $cashier->id,
            'opened_at'      => $openedAt1,
            'closed_at'      => $closedAt1,
            'subtotal'       => $subtotal1,
            'tax'            => $tax1,
            'service_charge' => $service1,
            'total'          => $total1,
        ]);

        $itemNG1 = OrderItem::create([
            'order_id'       => $order1->id,
            'menu_id'        => $menuNasiGoreng,
            'variant_id'     => $varNasiGorengReg?->id,
            'qty'            => 1,
            'price_snapshot' => $priceNasiGoreng1,
            'status'         => 'served',
            'notes'          => 'Pedas sedang ya',
        ]);
        // Addon: Ekstra Telur
        DB::table('order_item_addons')->insert([
            'order_item_id'  => $itemNG1->id,
            'addon_id'       => $addonEkstraTelur->id,
            'price_snapshot' => $addonEkstraTelur->price,
            'created_at'     => $openedAt1,
            'updated_at'     => $openedAt1,
        ]);

        OrderItem::create([
            'order_id'       => $order1->id,
            'menu_id'        => $menuAyamGoreng,
            'variant_id'     => $varAyamGoreng14?->id,
            'qty'            => 1,
            'price_snapshot' => $priceAyamGoreng1,
            'status'         => 'served',
        ]);
        OrderItem::create([
            'order_id'       => $order1->id,
            'menu_id'        => $menuEsTeh,
            'variant_id'     => $varEsTehManis?->id,
            'qty'            => 2,
            'price_snapshot' => $priceEsTeh1,
            'status'         => 'served',
        ]);
        OrderItem::create([
            'order_id'       => $order1->id,
            'menu_id'        => $menuKerupuk,
            'variant_id'     => null,
            'qty'            => 1,
            'price_snapshot' => $priceKerupuk1,
            'status'         => 'served',
        ]);

        // Status logs order 1
        $this->createStatusLogs($order1->id, $waiter->id, $cashier->id, $openedAt1);

        // Payment order 1 — bayar tunai
        DB::table('payments')->insert([
            'order_id'       => $order1->id,
            'payment_method' => 'cash',
            'amount_paid'    => 90000,
            'change_amount'  => round(90000 - $total1, 2),
            'paid_by'        => $cashier->id,
            'paid_at'        => $closedAt1,
            'created_at'     => $closedAt1,
            'updated_at'     => $closedAt1,
        ]);

        // Sample Order 2
        $openedAt2 = Carbon::yesterday()->setTime(19, 5, 0);
        $closedAt2 = Carbon::yesterday()->setTime(20, 22, 0);

        $menuMieGoreng  = $menus['Mie Goreng'];
        $menuSoto       = $menus['Soto Ayam Lamongan'];
        $menuTempe      = $menus['Tempe Goreng'];

        $varMieGorengReg = $getVariant($menuMieGoreng, 'Regular');

        $priceNasiGoreng2 = $itemPrice($menuNasiGoreng, $varNasiGorengReg?->id);  // 28000
        $priceMieGoreng2  = $itemPrice($menuMieGoreng, $varMieGorengReg?->id);    // 25000
        $priceSoto2       = $itemPrice($menuSoto, null);                           // 22000
        $priceEsTeh2      = $itemPrice($menuEsTeh, $varEsTehManis?->id);           // 5000
        $menuEsJeruk      = $menus['Es Jeruk'];
        $varEsJerukEs     = $getVariant($menuEsJeruk, 'Es');
        $priceEsJeruk2    = $itemPrice($menuEsJeruk, $varEsJerukEs?->id);          // 8000
        $priceTempe2      = $itemPrice($menuTempe, null);                           // 8000

        $subtotal2 = ($priceNasiGoreng2 * 2) + $priceMieGoreng2 + ($priceSoto2 * 2)
                   + ($priceEsTeh2 * 3) + $priceEsJeruk2 + ($priceTempe2 * 2);
        $tax2      = round($subtotal2 * 0.10, 2);
        $service2  = round($subtotal2 * 0.05, 2);
        $total2    = $subtotal2 + $tax2 + $service2;

        $order2 = Order::create([
            'order_number'   => 'ORD-20260226-002',
            'table_id'       => $tables['T-05'],
            'customer_name'  => 'Keluarga Santoso',
            'status'         => 'closed',
            'opened_by'      => $siti->id,
            'closed_by'      => $rina->id,
            'opened_at'      => $openedAt2,
            'closed_at'      => $closedAt2,
            'subtotal'       => $subtotal2,
            'tax'            => $tax2,
            'service_charge' => $service2,
            'total'          => $total2,
        ]);

        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuNasiGoreng, 'variant_id' => $varNasiGorengReg?->id, 'qty' => 2, 'price_snapshot' => $priceNasiGoreng2, 'status' => 'served']);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuMieGoreng,  'variant_id' => $varMieGorengReg?->id,  'qty' => 1, 'price_snapshot' => $priceMieGoreng2,  'status' => 'served']);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuSoto,       'variant_id' => null,                   'qty' => 2, 'price_snapshot' => $priceSoto2,        'status' => 'served', 'notes' => 'Satu porsi tanpa koya']);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuEsTeh,      'variant_id' => $varEsTehManis?->id,    'qty' => 3, 'price_snapshot' => $priceEsTeh2,       'status' => 'served']);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuEsJeruk,    'variant_id' => $varEsJerukEs?->id,    'qty' => 1, 'price_snapshot' => $priceEsJeruk2,     'status' => 'served']);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menuTempe,      'variant_id' => null,                   'qty' => 2, 'price_snapshot' => $priceTempe2,       'status' => 'served']);

        $this->createStatusLogs($order2->id, $siti->id, $rina->id, $openedAt2);

        DB::table('payments')->insert([
            'order_id'       => $order2->id,
            'payment_method' => 'qris',
            'amount_paid'    => $total2,
            'change_amount'  => 0,
            'paid_by'        => $rina->id,
            'paid_at'        => $closedAt2,
            'created_at'     => $closedAt2,
            'updated_at'     => $closedAt2,
        ]);

        // Sample order 3
        $openedAt3 = Carbon::today()->setTime(10, 30, 0);
        $closedAt3 = Carbon::today()->setTime(11, 5, 0);

        $menuGadoGado    = $menus['Gado-Gado'];
        $menuKopiSusu    = $menus['Kopi Susu'];
        $menuPisangGoreng = $menus['Pisang Goreng'];

        $varKopiSusuIced     = $getVariant($menuKopiSusu, 'Iced');
        $varPisangGoreng3pcs = $getVariant($menuPisangGoreng, '3 Pcs');
        $addonSausCoklat     = $getAddon($menuPisangGoreng, 'Saus Coklat');

        $priceGadoGado3    = $itemPrice($menuGadoGado, null);                             // 22000
        $priceKopiSusu3    = $itemPrice($menuKopiSusu, $varKopiSusuIced?->id);            // 14000+3000=17000
        $pricePisang3      = $itemPrice($menuPisangGoreng, $varPisangGoreng3pcs?->id);    // 12000

        $subtotal3   = $priceGadoGado3 + $priceKopiSusu3 + $pricePisang3 + (float) ($addonSausCoklat?->price ?? 0);
        $tax3        = round($subtotal3 * 0.10, 2);
        $service3    = round($subtotal3 * 0.05, 2);
        $total3      = $subtotal3 + $tax3 + $service3;

        $order3 = Order::create([
            'order_number'   => 'ORD-20260227-001',
            'table_id'       => $tables['T-02'],
            'customer_name'  => 'Bu Dewi',
            'status'         => 'closed',
            'opened_by'      => $budi->id,
            'closed_by'      => $cashier->id,
            'opened_at'      => $openedAt3,
            'closed_at'      => $closedAt3,
            'subtotal'       => $subtotal3,
            'tax'            => $tax3,
            'service_charge' => $service3,
            'total'          => $total3,
        ]);

        OrderItem::create(['order_id' => $order3->id, 'menu_id' => $menuGadoGado, 'variant_id' => null, 'qty' => 1, 'price_snapshot' => $priceGadoGado3, 'status' => 'served']);
        OrderItem::create(['order_id' => $order3->id, 'menu_id' => $menuKopiSusu, 'variant_id' => $varKopiSusuIced?->id, 'qty' => 1, 'price_snapshot' => $priceKopiSusu3, 'status' => 'served']);

        $itemPisang3 = OrderItem::create([
            'order_id'       => $order3->id,
            'menu_id'        => $menuPisangGoreng,
            'variant_id'     => $varPisangGoreng3pcs?->id,
            'qty'            => 1,
            'price_snapshot' => $pricePisang3,
            'status'         => 'served',
        ]);
        DB::table('order_item_addons')->insert([
            'order_item_id'  => $itemPisang3->id,
            'addon_id'       => $addonSausCoklat->id,
            'price_snapshot' => $addonSausCoklat->price,
            'created_at'     => $openedAt3,
            'updated_at'     => $openedAt3,
        ]);

        $this->createStatusLogs($order3->id, $budi->id, $cashier->id, $openedAt3);

        DB::table('payments')->insert([
            'order_id'       => $order3->id,
            'payment_method' => 'cash',
            'amount_paid'    => 70000,
            'change_amount'  => round(70000 - $total3, 2),
            'paid_by'        => $cashier->id,
            'paid_at'        => $closedAt3,
            'created_at'     => $closedAt3,
            'updated_at'     => $closedAt3,
        ]);

        // Sample order 4
        $openedAt4 = Carbon::today()->setTime(12, 15, 0);
        $closedAt4 = Carbon::today()->setTime(12, 55, 0);

        $menuBakso      = $menus['Bakso Sapi Spesial'];
        $menuKopiHitam  = $menus['Kopi Hitam Tubruk'];

        $varBaksoMercon  = $getVariant($menuBakso, 'Bakso Mercon');
        $varKopiPanas    = $getVariant($menuKopiHitam, 'Panas');

        $priceBakso4    = $itemPrice($menuBakso, $varBaksoMercon?->id);     // 25000+3000=28000
        $priceKopi4     = $itemPrice($menuKopiHitam, $varKopiPanas?->id);   // 8000
        $priceKerupuk4  = $itemPrice($menuKerupuk, null);                    // 5000

        $subtotal4   = $priceBakso4 + $priceKopi4 + $priceKerupuk4;
        $tax4        = round($subtotal4 * 0.10, 2);
        $service4    = round($subtotal4 * 0.05, 2);
        $total4      = $subtotal4 + $tax4 + $service4;

        $order4 = Order::create([
            'order_number'   => 'ORD-20260227-002',
            'table_id'       => $tables['T-07'],
            'customer_name'  => 'Pak Doni',
            'status'         => 'closed',
            'opened_by'      => $waiter->id,
            'closed_by'      => $rina->id,
            'opened_at'      => $openedAt4,
            'closed_at'      => $closedAt4,
            'subtotal'       => $subtotal4,
            'tax'            => $tax4,
            'service_charge' => $service4,
            'total'          => $total4,
        ]);

        OrderItem::create(['order_id' => $order4->id, 'menu_id' => $menuBakso,     'variant_id' => $varBaksoMercon?->id, 'qty' => 1, 'price_snapshot' => $priceBakso4,   'status' => 'served', 'notes' => 'Pakai banyak sawi']);
        OrderItem::create(['order_id' => $order4->id, 'menu_id' => $menuKopiHitam, 'variant_id' => $varKopiPanas?->id,  'qty' => 1, 'price_snapshot' => $priceKopi4,     'status' => 'served']);
        OrderItem::create(['order_id' => $order4->id, 'menu_id' => $menuKerupuk,   'variant_id' => null,                'qty' => 1, 'price_snapshot' => $priceKerupuk4,  'status' => 'served']);

        $this->createStatusLogs($order4->id, $waiter->id, $rina->id, $openedAt4);

        DB::table('payments')->insert([
            'order_id'       => $order4->id,
            'payment_method' => 'transfer',
            'amount_paid'    => $total4,
            'change_amount'  => 0,
            'paid_by'        => $rina->id,
            'paid_at'        => $closedAt4,
            'created_at'     => $closedAt4,
            'updated_at'     => $closedAt4,
        ]);

        // Sample order 5 - Active
        $openedAt5 = Carbon::now()->subMinutes(25);

        $menuNasiGorengSfood = $menus['Nasi Goreng Seafood'];
        $menuAyamBakar       = $menus['Ayam Bakar'];
        $menuCapcay          = $menus['Capcay Goreng'];
        $menuJusAlpukat      = $menus['Jus Alpukat'];

        $varNasiGorengSfoodReg = $getVariant($menuNasiGorengSfood, 'Regular');
        $varAyamBakar14        = $getVariant($menuAyamBakar, '1/4 Ayam');
        $varCapcayGoreng       = $getVariant($menuCapcay, 'Goreng');
        $varEsJeruk5           = $getVariant($menuEsJeruk, 'Es');
        $varJusAlpukatReg      = $getVariant($menuJusAlpukat, 'Regular (300ml)');
        $addonToppingCoklat    = $getAddon($menuJusAlpukat, 'Topping Coklat');

        $priceNasiSfood5  = $itemPrice($menuNasiGorengSfood, $varNasiGorengSfoodReg?->id); // 35000
        $priceAyamBakar5  = $itemPrice($menuAyamBakar, $varAyamBakar14?->id);              // 32000
        $priceCapcay5     = $itemPrice($menuCapcay, $varCapcayGoreng?->id);                // 22000
        $priceEsJeruk5    = $itemPrice($menuEsJeruk, $varEsJeruk5?->id);                   // 8000
        $priceJusAlpukat5 = $itemPrice($menuJusAlpukat, $varJusAlpukatReg?->id);          // 16000

        $addonCoklat = (float) ($addonToppingCoklat?->price ?? 0); // 2000 per item
        $subtotal5   = ($priceNasiSfood5 * 3) + ($priceAyamBakar5 * 2) + $priceCapcay5
                     + ($priceEsJeruk5 * 3) + ($priceJusAlpukat5 * 3) + ($addonCoklat * 3);
        $tax5        = round($subtotal5 * 0.10, 2);
        $service5    = round($subtotal5 * 0.05, 2);
        $total5      = $subtotal5 + $tax5 + $service5;

        $order5 = Order::create([
            'order_number'   => 'ORD-20260227-003',
            'table_id'       => $tables['T-01'],
            'customer_name'  => 'Rapat Tim Marketing',
            'status'         => 'in_kitchen',
            'opened_by'      => $siti->id,
            'closed_by'      => null,
            'opened_at'      => $openedAt5,
            'closed_at'      => null,
            'subtotal'       => $subtotal5,
            'tax'            => $tax5,
            'service_charge' => $service5,
            'total'          => $total5,
        ]);

        OrderItem::create(['order_id' => $order5->id, 'menu_id' => $menuNasiGorengSfood, 'variant_id' => $varNasiGorengSfoodReg?->id, 'qty' => 3, 'price_snapshot' => $priceNasiSfood5,  'status' => 'in_kitchen', 'notes' => 'Dua porsi tidak pedas, satu porsi extra pedas']);
        OrderItem::create(['order_id' => $order5->id, 'menu_id' => $menuAyamBakar,       'variant_id' => $varAyamBakar14?->id,        'qty' => 2, 'price_snapshot' => $priceAyamBakar5,  'status' => 'in_kitchen']);
        OrderItem::create(['order_id' => $order5->id, 'menu_id' => $menuCapcay,          'variant_id' => $varCapcayGoreng?->id,       'qty' => 1, 'price_snapshot' => $priceCapcay5,     'status' => 'in_kitchen']);
        OrderItem::create(['order_id' => $order5->id, 'menu_id' => $menuEsJeruk,         'variant_id' => $varEsJeruk5?->id,           'qty' => 3, 'price_snapshot' => $priceEsJeruk5,    'status' => 'served', 'notes' => 'Kurang manis']);

        for ($i = 0; $i < 3; $i++) {
            $itemJus = OrderItem::create([
                'order_id'       => $order5->id,
                'menu_id'        => $menuJusAlpukat,
                'variant_id'     => $varJusAlpukatReg?->id,
                'qty'            => 1,
                'price_snapshot' => $priceJusAlpukat5,
                'status'         => 'served',
            ]);
            if ($addonToppingCoklat) {
                DB::table('order_item_addons')->insert([
                    'order_item_id'  => $itemJus->id,
                    'addon_id'       => $addonToppingCoklat->id,
                    'price_snapshot' => $addonToppingCoklat->price,
                    'created_at'     => $openedAt5,
                    'updated_at'     => $openedAt5,
                ]);
            }
        }

        $logAt5 = $openedAt5;
        DB::table('order_status_logs')->insert([
            ['order_id' => $order5->id, 'from_status' => null,        'to_status' => 'draft',      'changed_by' => $siti->id, 'changed_at' => $logAt5,            'created_at' => $logAt5,            'updated_at' => $logAt5],
            ['order_id' => $order5->id, 'from_status' => 'draft',     'to_status' => 'submitted',  'changed_by' => $siti->id, 'changed_at' => $logAt5->copy()->addMinutes(3), 'created_at' => $logAt5->copy()->addMinutes(3), 'updated_at' => $logAt5->copy()->addMinutes(3)],
            ['order_id' => $order5->id, 'from_status' => 'submitted', 'to_status' => 'in_kitchen', 'changed_by' => $admin->id, 'changed_at' => $logAt5->copy()->addMinutes(5), 'created_at' => $logAt5->copy()->addMinutes(5), 'updated_at' => $logAt5->copy()->addMinutes(5)],
        ]);

        Table::where('code', 'T-01')->update(['current_order_id' => $order5->id]);
    }

    /**
     * Create complete status logs for an order that has been closed (full flow).
     */
    private function createStatusLogs(int $orderId, int $waiterId, int $cashierId, Carbon $openedAt): void
    {
        $logs = [
            ['from_status' => null,          'to_status' => 'draft',      'changed_by' => $waiterId,  'offset' => 0],
            ['from_status' => 'draft',       'to_status' => 'submitted',  'changed_by' => $waiterId,  'offset' => 2],
            ['from_status' => 'submitted',   'to_status' => 'in_kitchen', 'changed_by' => $waiterId,  'offset' => 4],
            ['from_status' => 'in_kitchen',  'to_status' => 'ready',      'changed_by' => $waiterId,  'offset' => 20],
            ['from_status' => 'ready',       'to_status' => 'served',     'changed_by' => $waiterId,  'offset' => 25],
            ['from_status' => 'served',      'to_status' => 'closed',     'changed_by' => $cashierId, 'offset' => 40],
        ];

        $inserts = [];
        foreach ($logs as $log) {
            $changedAt    = $openedAt->copy()->addMinutes($log['offset']);
            $inserts[] = [
                'order_id'    => $orderId,
                'from_status' => $log['from_status'],
                'to_status'   => $log['to_status'],
                'changed_by'  => $log['changed_by'],
                'changed_at'  => $changedAt,
                'created_at'  => $changedAt,
                'updated_at'  => $changedAt,
            ];
        }

        DB::table('order_status_logs')->insert($inserts);
    }
}
