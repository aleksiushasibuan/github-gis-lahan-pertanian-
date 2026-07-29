<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Overlay;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RtrwBengkalisSeeder extends Seeder
{
    public function run()
    {
        // 1 FILE SAJA yang mencakup SEMUA polygon
        $fileName = 'rtrw_bengkalis.geojson'; 
        $path = public_path('data/' . $fileName);
        
        $this->command->info("Mencari file di: " . $path);
        
        if (!File::exists($path)) {
            $this->command->error("File {$fileName} tidak ditemukan!");
            $this->command->info("Pastikan file ada di folder: " . public_path('data/'));
            return;
        }
        
        $this->command->info('📂 Membaca file GeoJSON RTRW Bengkalis...');
        $content = File::get($path);
        $geojson = json_decode($content, true);
        
        if (!$geojson || !isset($geojson['features'])) {
            $this->command->error('Format GeoJSON tidak valid!');
            return;
        }
        
        $jumlahFitur = count($geojson['features']);
        $this->command->info("✅ Ditemukan {$jumlahFitur} polygon kawasan dalam 1 file!");
        
        // Hitung statistik per jenis kawasan
        $statistik = [];
        $totalLuas = 0;
        
        foreach ($geojson['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            
            // Ambil jenis kawasan (sesuaikan dengan properti di file Anda)
            $jenis = $props['NAMOBJ'] ?? 
                     $props['jenis_kawasan'] ?? 
                     $props['KETERANGAN'] ?? 
                     'Lainnya';
            
            // Ambil luas
            $luas = (float)($props['luas_ha'] ?? 
                           $props['Luas_Ha'] ?? 
                           $props['SHAPE_Area'] ?? 0);
            
            $totalLuas += $luas;
            
            if (!isset($statistik[$jenis])) {
                $statistik[$jenis] = 0;
            }
            $statistik[$jenis]++;
        }
        
        // Tampilkan statistik
        $this->command->info("\n📊 STATISTIK RTRW BENGKALIS:");
        $this->command->info("─────────────────────────────────");
        $this->command->info("Total Polygon    : {$jumlahFitur}");
        $this->command->info("Total Luas       : " . number_format($totalLuas, 2) . " Ha");
        $this->command->info("\n📌 JENIS KAWASAN:");
        
        foreach ($statistik as $jenis => $jumlah) {
            $this->command->info("  • {$jenis}: {$jumlah} polygon");
        }
        
        // Simpan 1 file ke database
        $this->command->info("\n💾 Menyimpan ke database...");
        
        Overlay::updateOrCreate(
            ['file' => $fileName],
            [
                'nama' => 'RTRW Kabupaten Bengkalis',
                'jenis' => 'Rencana Tata Ruang Wilayah (Lengkap)',
                'file' => $fileName,
                'geojson_data' => $geojson,
                'jumlah_fitur' => $jumlahFitur,
                'total_luas' => $totalLuas,
                'uuid' => (string) Str::uuid(),
                'status' => 'active'
            ]
        );
        
        $this->command->info("\n✅ SUKSES! 1 file RTRW dengan {$jumlahFitur} polygon berhasil diimport ke database!");
        $this->command->info("   Semua kawasan (Hutan, Badan Air, Permukiman, dll) sudah tersimpan dalam 1 record.");
    }
}