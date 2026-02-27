<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuAddons;
use App\Models\MenuVariant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $makananUtama  = Category::where('name', 'Makanan Utama')->first();
        $laukPauk      = Category::where('name', 'Lauk Pauk')->first();
        $sayuran       = Category::where('name', 'Sayuran')->first();
        $minuman       = Category::where('name', 'Minuman')->first();
        $camilanDessert = Category::where('name', 'Camilan & Dessert')->first();

        // ─────────────────────────────────────────
        // MAKANAN UTAMA
        // ─────────────────────────────────────────

        $nasiGoreng = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur, ayam suwir, bakso, udang, sayuran, dan kerupuk. Disajikan dengan acar timun wortel.',
            'base_price'  => 28000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $nasiGoreng->id, 'name' => 'Regular',     'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $nasiGoreng->id, 'name' => 'Jumbo',       'additional_price' => 8000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $nasiGoreng->id, 'name' => 'Ekstra Telur', 'price' => 3000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $nasiGoreng->id, 'name' => 'Level Pedas 1-5', 'price' => 0, 'is_required' => false]);

        $nasiGorengSeafood = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Nasi Goreng Seafood',
            'description' => 'Nasi goreng dengan cumi, udang jumbo, dan ikan fillet. Dilengkapi telur, sayuran, dan sambal terasi.',
            'base_price'  => 35000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $nasiGorengSeafood->id, 'name' => 'Regular',     'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $nasiGorengSeafood->id, 'name' => 'Jumbo',       'additional_price' => 10000,'is_default' => false]);
        MenuAddons::create(['menu_id'  => $nasiGorengSeafood->id, 'name' => 'Ekstra Nasi',  'price' => 5000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $nasiGorengSeafood->id, 'name' => 'Level Pedas 1-5', 'price' => 0, 'is_required' => false]);

        $mieGoreng = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Mie Goreng',
            'description' => 'Mie goreng dengan ayam, bakso, telur, kol, bihun, dan bumbu kecap manis khas. Disajikan dengan acar.',
            'base_price'  => 25000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $mieGoreng->id, 'name' => 'Regular',     'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $mieGoreng->id, 'name' => 'Jumbo',       'additional_price' => 7000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $mieGoreng->id,  'name' => 'Ekstra Telur', 'price' => 3000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $mieGoreng->id,  'name' => 'Ekstra Bakso (isi 3)', 'price' => 5000, 'is_required' => false]);

        $mieAyam = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Mie Ayam',
            'description' => 'Mie ayam asli dengan kuah kaldu ayam bening, topping daging ayam cincang berbumbu, caisim, dan bawang goreng.',
            'base_price'  => 23000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $mieAyam->id, 'name' => 'Kuah',      'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $mieAyam->id, 'name' => 'Bakso',     'additional_price' => 5000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $mieAyam->id,  'name' => 'Ekstra Ayam', 'price' => 8000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $mieAyam->id,  'name' => 'Pangsit Goreng', 'price' => 5000, 'is_required' => false]);

        $sotoAyam = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Soto Ayam Lamongan',
            'description' => 'Soto ayam khas Lamongan dengan kuah kuning bening, ayam suwir, bihun, telur, tauge, dan ditabur koya. Disajikan dengan nasi.',
            'base_price'  => 22000,
            'is_active'   => true,
        ]);
        MenuAddons::create(['menu_id' => $sotoAyam->id, 'name' => 'Ekstra Ayam',   'price' => 8000, 'is_required' => false]);
        MenuAddons::create(['menu_id' => $sotoAyam->id, 'name' => 'Ekstra Telur',  'price' => 3000, 'is_required' => false]);
        MenuAddons::create(['menu_id' => $sotoAyam->id, 'name' => 'Kerupuk Udang', 'price' => 3000, 'is_required' => false]);

        $bakso = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Bakso Sapi Spesial',
            'description' => 'Bakso daging sapi asli dengan campuran bakso urat, tahu bakso, dan mie kuning. Kuah kaldu sapi gurih dengan taburan bawang goreng.',
            'base_price'  => 25000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $bakso->id, 'name' => 'Bakso Biasa',  'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $bakso->id, 'name' => 'Bakso Mercon', 'additional_price' => 3000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $bakso->id,  'name' => 'Ekstra Bakso Urat', 'price' => 5000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $bakso->id,  'name' => 'Ekstra Mie',        'price' => 3000, 'is_required' => false]);

        $gadoGado = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Gado-Gado',
            'description' => 'Sayuran rebus segar (bayam, kol, tauge, kentang, wortel) dilengkapi tahu goreng, tempe, telur rebus, dan lontong. Dengan saus kacang spesial dan kerupuk.',
            'base_price'  => 22000,
            'is_active'   => true,
        ]);
        MenuAddons::create(['menu_id' => $gadoGado->id, 'name' => 'Ekstra Lontong',   'price' => 3000, 'is_required' => false]);
        MenuAddons::create(['menu_id' => $gadoGado->id, 'name' => 'Ekstra Saus Kacang', 'price' => 2000, 'is_required' => false]);

        $nasiUduk = Menu::create([
            'category_id' => $makananUtama->id,
            'name'        => 'Nasi Uduk Komplit',
            'description' => 'Nasi uduk gurih dimasak dengan santan dan rempah, disertai ayam goreng, tempe orek, bihun goreng, emping, dan sambal kacang.',
            'base_price'  => 28000,
            'is_active'   => true,
        ]);
        MenuAddons::create(['menu_id' => $nasiUduk->id, 'name' => 'Ekstra Ayam',  'price' => 12000, 'is_required' => false]);
        MenuAddons::create(['menu_id' => $nasiUduk->id, 'name' => 'Ekstra Tempe', 'price' => 5000,  'is_required' => false]);

        // ─────────────────────────────────────────
        // LAUK PAUK
        // ─────────────────────────────────────────

        $ayamGoreng = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Ayam Goreng',
            'description' => 'Ayam kampung goreng crispy berbumbu bacem khas Jawa. Bumbu meresap hingga ke dalam, renyah di luar dan juicy di dalam.',
            'base_price'  => 28000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $ayamGoreng->id, 'name' => '1/4 Ayam', 'additional_price' => 0,     'is_default' => true]);
        MenuVariant::create(['menu_id' => $ayamGoreng->id, 'name' => '1/2 Ayam', 'additional_price' => 20000, 'is_default' => false]);

        $ayamBakar = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Ayam Bakar',
            'description' => 'Ayam kampung dibakar dengan bumbu kecap dan rempah pilihan. Disajikan dengan lalapan, sambal terasi, dan nasi putih.',
            'base_price'  => 32000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $ayamBakar->id, 'name' => '1/4 Ayam', 'additional_price' => 0,     'is_default' => true]);
        MenuVariant::create(['menu_id' => $ayamBakar->id, 'name' => '1/2 Ayam', 'additional_price' => 22000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $ayamBakar->id,  'name' => 'Ekstra Sambal Terasi', 'price' => 2000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $ayamBakar->id,  'name' => 'Ekstra Lalapan',       'price' => 3000, 'is_required' => false]);

        $ikanGoreng = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Ikan Mas Goreng',
            'description' => 'Ikan mas segar digoreng crispy dengan bumbu kunyit, jahe, dan bawang putih. Disajikan dengan lalapan dan sambal bawang.',
            'base_price'  => 32000,
            'is_active'   => true,
        ]);
        MenuAddons::create(['menu_id' => $ikanGoreng->id, 'name' => 'Ekstra Sambal',   'price' => 2000, 'is_required' => false]);
        MenuAddons::create(['menu_id' => $ikanGoreng->id, 'name' => 'Ekstra Lalapan',  'price' => 3000, 'is_required' => false]);

        $telurDadar = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Telur Dadar',
            'description' => 'Telur ayam dadar tipis dengan irisan daun bawang dan bawang merah. Gurih dan renyah pinggirnya.',
            'base_price'  => 8000,
            'is_active'   => true,
        ]);

        $tempeSambal = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Tempe Goreng',
            'description' => 'Tempe iris tipis digoreng crispy dengan balutan tepung bumbu ringan. Renyah luar, lembut dalam.',
            'base_price'  => 8000,
            'is_active'   => true,
        ]);

        $tahuGoreng = Menu::create([
            'category_id' => $laukPauk->id,
            'name'        => 'Tahu Goreng',
            'description' => 'Tahu sutera goreng dengan kulit luar renyah dan isian lembut. Disajikan dengan saus kecap sambal.',
            'base_price'  => 7000,
            'is_active'   => true,
        ]);

        // ─────────────────────────────────────────
        // SAYURAN
        // ─────────────────────────────────────────

        $capcay = Menu::create([
            'category_id' => $sayuran->id,
            'name'        => 'Capcay Goreng',
            'description' => 'Tumis campuran sayuran segar (wortel, kol, jagung muda, bakso, daging ayam) dengan saus tiram dan kecap asin.',
            'base_price'  => 22000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $capcay->id, 'name' => 'Goreng', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $capcay->id, 'name' => 'Kuah',   'additional_price' => 0,    'is_default' => false]);
        MenuAddons::create(['menu_id'  => $capcay->id,  'name' => 'Ekstra Ayam',  'price' => 8000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $capcay->id,  'name' => 'Ekstra Udang', 'price' => 10000,'is_required' => false]);

        $tumisKangkung = Menu::create([
            'category_id' => $sayuran->id,
            'name'        => 'Tumis Kangkung Belacan',
            'description' => 'Kangkung segar ditumis dengan bumbu belacan, bawang merah, bawang putih, dan cabai rawit. Harum dan menggugah selera.',
            'base_price'  => 18000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $tumisKangkung->id, 'name' => 'Level Pedas Sedang', 'additional_price' => 0, 'is_default' => true]);
        MenuVariant::create(['menu_id' => $tumisKangkung->id, 'name' => 'Level Pedas Extra',  'additional_price' => 0, 'is_default' => false]);
        MenuVariant::create(['menu_id' => $tumisKangkung->id, 'name' => 'Tidak Pedas',        'additional_price' => 0, 'is_default' => false]);

        $sayurSop = Menu::create([
            'category_id' => $sayuran->id,
            'name'        => 'Sayur Sop',
            'description' => 'Sayur sop berkuah bening dengan wortel, kentang, buncis, kol, daging ayam, dan taburan seledri serta bawang goreng.',
            'base_price'  => 16000,
            'is_active'   => true,
        ]);
        MenuAddons::create(['menu_id' => $sayurSop->id, 'name' => 'Ekstra Ayam', 'price' => 8000, 'is_required' => false]);

        // ─────────────────────────────────────────
        // MINUMAN
        // ─────────────────────────────────────────

        $esTeh = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Teh',
            'description' => 'Teh tubruk pilihan diseduh dengan air mendidih. Tersedia panas atau dingin.',
            'base_price'  => 5000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $esTeh->id, 'name' => 'Es Teh Manis', 'additional_price' => 0, 'is_default' => true]);
        MenuVariant::create(['menu_id' => $esTeh->id, 'name' => 'Teh Panas',    'additional_price' => 0, 'is_default' => false]);
        MenuVariant::create(['menu_id' => $esTeh->id, 'name' => 'Teh Tawar',    'additional_price' => 0, 'is_default' => false]);

        $esJeruk = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Es Jeruk',
            'description' => 'Jeruk peras segar dipadu dengan gula dan es batu. Segar dan menyegarkan.',
            'base_price'  => 8000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $esJeruk->id, 'name' => 'Es',   'additional_price' => 0, 'is_default' => true]);
        MenuVariant::create(['menu_id' => $esJeruk->id, 'name' => 'Panas', 'additional_price' => 0, 'is_default' => false]);

        $esCampur = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Es Campur',
            'description' => 'Minuman segar berisi kolang-kaling, nata de coco, cincau hitam, buah nangka, dan agar-agar. Disiram santan dan sirup merah.',
            'base_price'  => 15000,
            'is_active'   => true,
        ]);

        $jusMangga = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Jus Mangga',
            'description' => 'Jus mangga harum manis dari buah mangga pilihan, segar tanpa pewarna dan pengawet.',
            'base_price'  => 13000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $jusMangga->id, 'name' => 'Regular (300ml)', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $jusMangga->id, 'name' => 'Large (500ml)',   'additional_price' => 5000, 'is_default' => false]);

        $jusAlpukat = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Jus Alpukat',
            'description' => 'Jus alpukat krim pilihan, kental dan creamy. Disajikan dengan coklat atau susu kental manis di atasnya.',
            'base_price'  => 16000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $jusAlpukat->id, 'name' => 'Regular (300ml)', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $jusAlpukat->id, 'name' => 'Large (500ml)',   'additional_price' => 5000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $jusAlpukat->id,  'name' => 'Topping Coklat',  'price' => 2000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $jusAlpukat->id,  'name' => 'Extra SKM',       'price' => 1000, 'is_required' => false]);

        $airMineral = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Air Mineral',
            'description' => 'Air mineral dalam kemasan botol.',
            'base_price'  => 5000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $airMineral->id, 'name' => 'Botol 600ml', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $airMineral->id, 'name' => 'Galon Kecil', 'additional_price' => 5000, 'is_default' => false]);

        $kopiHitam = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Kopi Hitam Tubruk',
            'description' => 'Kopi tubruk arabika pilihan diseduh langsung dengan air panas. Khas dan aromanya kuat.',
            'base_price'  => 8000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $kopiHitam->id, 'name' => 'Panas', 'additional_price' => 0, 'is_default' => true]);
        MenuVariant::create(['menu_id' => $kopiHitam->id, 'name' => 'Iced',  'additional_price' => 2000, 'is_default' => false]);

        $kopiSusu = Menu::create([
            'category_id' => $minuman->id,
            'name'        => 'Kopi Susu',
            'description' => 'Espresso kuat dicampur susu segar, creamy dan manis. Tersedia panas atau iced.',
            'base_price'  => 14000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $kopiSusu->id, 'name' => 'Panas', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $kopiSusu->id, 'name' => 'Iced',  'additional_price' => 3000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $kopiSusu->id,  'name' => 'Extra Shot Espresso', 'price' => 6000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $kopiSusu->id,  'name' => 'Gula Aren (non-gula pasir)', 'price' => 3000, 'is_required' => false]);

        // ─────────────────────────────────────────
        // CAMILAN & DESSERT
        // ─────────────────────────────────────────

        $pisangGoreng = Menu::create([
            'category_id' => $camilanDessert->id,
            'name'        => 'Pisang Goreng',
            'description' => 'Pisang kepok manis digoreng dengan balutan tepung renyah. Disajikan hangat dengan topping pilihan.',
            'base_price'  => 12000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $pisangGoreng->id, 'name' => '3 Pcs', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $pisangGoreng->id, 'name' => '5 Pcs', 'additional_price' => 6000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $pisangGoreng->id,  'name' => 'Saus Coklat',   'price' => 3000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $pisangGoreng->id,  'name' => 'Saus Keju',     'price' => 4000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $pisangGoreng->id,  'name' => 'Saus Strawberry', 'price' => 3000, 'is_required' => false]);

        $martabakManis = Menu::create([
            'category_id' => $camilanDessert->id,
            'name'        => 'Martabak Manis',
            'description' => 'Martabak terang bulan dengan kulit tebal berbusa, isian kacang, wijen, dan mentega. Tersedia berbagai varian topping.',
            'base_price'  => 22000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $martabakManis->id, 'name' => 'Original (Kacang-Wijen)', 'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $martabakManis->id, 'name' => 'Coklat Keju',             'additional_price' => 8000, 'is_default' => false]);
        MenuVariant::create(['menu_id' => $martabakManis->id, 'name' => 'Green Tea Keju',          'additional_price' => 10000,'is_default' => false]);

        $kerupukUdang = Menu::create([
            'category_id' => $camilanDessert->id,
            'name'        => 'Kerupuk Udang',
            'description' => 'Kerupuk udang asli goreng crispy, cocok sebagai pelengkap makan.',
            'base_price'  => 5000,
            'is_active'   => true,
        ]);

        $esKrim = Menu::create([
            'category_id' => $camilanDessert->id,
            'name'        => 'Es Krim 2 Scoop',
            'description' => 'Es krim homemade pilihan rasa, disajikan dalam cup dengan wafer roll.',
            'base_price'  => 14000,
            'is_active'   => true,
        ]);
        MenuVariant::create(['menu_id' => $esKrim->id, 'name' => 'Coklat - Vanila',   'additional_price' => 0,    'is_default' => true]);
        MenuVariant::create(['menu_id' => $esKrim->id, 'name' => 'Stroberi - Vanila', 'additional_price' => 0,    'is_default' => false]);
        MenuVariant::create(['menu_id' => $esKrim->id, 'name' => 'Taro - Coklat',     'additional_price' => 2000, 'is_default' => false]);
        MenuAddons::create(['menu_id'  => $esKrim->id,  'name' => 'Extra Scoop',   'price' => 7000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $esKrim->id,  'name' => 'Topping Oreo',  'price' => 5000, 'is_required' => false]);
        MenuAddons::create(['menu_id'  => $esKrim->id,  'name' => 'Topping Mesis', 'price' => 3000, 'is_required' => false]);
    }
}
