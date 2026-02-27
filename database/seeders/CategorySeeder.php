<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Makanan Utama',
                'description' => 'Makanan berat sebagai hidangan utama seperti nasi goreng, mie, soto, dan bakso.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Lauk Pauk',
                'description' => 'Berbagai pilihan lauk pendamping nasi seperti ayam, ikan, tempe, dan tahu.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sayuran',
                'description' => 'Pilihan masakan sayuran segar seperti capcay, tumis kangkung, dan sayur sop.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Minuman',
                'description' => 'Aneka minuman segar dan hangat, mulai dari teh, jus buah, hingga kopi.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Camilan & Dessert',
                'description' => 'Pilihan camilan dan penutup seperti pisang goreng, martabak, dan es krim.',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
