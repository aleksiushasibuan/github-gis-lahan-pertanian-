<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lahan ‹ Admin GIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e9f5f0 100%);
            min-height: 100vh;
        }
        .form-card {
            backdrop-filter: blur(2px);
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            ring: 2px solid #2e7d32;
            border-color: #2e7d32;
        }
    </style>
</head>

<body class="font-['Inter',system-ui]">

<div class="max-w-5xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-400 to-blue-500 flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-lg"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Data Lahan</h1>
        </div>
        <p class="text-slate-500 text-sm ml-12">Perbaharui informasi lahan pertanian dengan akurat</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/60 overflow-hidden form-card">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-blue-500"></i>
                <h3 class="font-semibold text-slate-700">Formulir Edit Lahan</h3>
                <span class="text-xs text-slate-400 ml-auto"><i class="fas fa-asterisk text-red-400 text-xs"></i> isian wajib</span>
            </div>
        </div>

        <form action="{{ route('admin.lahan.update', $lahan->id) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- DESA (wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-location-dot text-blue-500 mr-1"></i> Nama Desa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_desa"
                               value="{{ old('nama_desa', $lahan->nama_desa) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Contoh: Desa Mentayan"
                               required>
                        @error('nama_desa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- PEMILIK (wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-user-circle text-slate-500 mr-1"></i> Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pemilik"
                               value="{{ old('pemilik', $lahan->pemilik) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Nama pemilik lahan"
                               required>
                        @error('pemilik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- LUAS (wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-chart-area text-emerald-500 mr-1"></i> Luas (Ha) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="luas"
                               value="{{ old('luas', $lahan->luas) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="0.00"
                               required>
                        @error('luas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- JENIS TANAMAN (BARU - DITAMBAHKAN) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-seedling text-green-500 mr-1"></i> Jenis Tanaman
                        </label>
                        <select name="jenis_tanaman" class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                            <option value="">-- Pilih Jenis Tanaman --</option>
                            <option value="Padi" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Padi') ? 'selected' : '' }}> Padi</option>
                            <option value="Jagung" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Jagung') ? 'selected' : '' }}>Jagung</option>
                            <option value="Kelapa Sawit" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Kelapa Sawit') ? 'selected' : '' }}> Kelapa Sawit</option>
                            <option value="Karet" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Karet') ? 'selected' : '' }}> Karet</option>
                            <option value="Palawija" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Palawija') ? 'selected' : '' }}> Palawija</option>
                            <option value="Sayuran" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Sayuran') ? 'selected' : '' }}> Sayuran</option>
                            <option value="Buah-buahan" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Buah-buahan') ? 'selected' : '' }}> Buah-buahan</option>
                            <option value="Lainnya" {{ (old('jenis_tanaman', $lahan->jenis_tanaman) == 'Lainnya') ? 'selected' : '' }}> Lainnya</option>
                        </select>
                        @error('jenis_tanaman') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- JENIS SAWAH -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-tint text-blue-400 mr-1"></i> Jenis Sawah
                        </label>
                        <select name="jenis" class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                            <option value="">-- Pilih Jenis Sawah --</option>
                            <option value="Sawah Tadah Hujan" {{ (old('jenis', $lahan->jenis) == 'Sawah Tadah Hujan') ? 'selected' : '' }}> Sawah Tadah Hujan</option>
                            <option value="Sawah Irigasi" {{ (old('jenis', $lahan->jenis) == 'Sawah Irigasi') ? 'selected' : '' }}> Sawah Irigasi</option>
                            <option value="Sawah Lebak" {{ (old('jenis', $lahan->jenis) == 'Sawah Lebak') ? 'selected' : '' }}> Sawah Lebak</option>
                            <option value="Sawah Pasang Surut" {{ (old('jenis', $lahan->jenis) == 'Sawah Pasang Surut') ? 'selected' : '' }}> Sawah Pasang Surut</option>
                        </select>
                        @error('jenis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- KECAMATAN -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-city text-purple-500 mr-1"></i> Kecamatan
                        </label>
                        <input type="text" name="kecamatan"
                               value="{{ old('kecamatan', $lahan->kecamatan) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Contoh: Kecamatan Bantan">
                        @error('kecamatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- POKTAN -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-users text-orange-500 mr-1"></i> Poktan
                        </label>
                        <input type="text" name="poktan"
                               value="{{ old('poktan', $lahan->poktan) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Kelompok Tani">
                        @error('poktan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- KODE PERSIL -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-barcode text-gray-500 mr-1"></i> Kode Persil
                        </label>
                        <input type="text" name="kode_persil"
                               value="{{ old('kode_persil', $lahan->kode_persil) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Contoh: MTY-71">
                        @error('kode_persil') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- KONDISI LAHAN -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-clinic-medical text-teal-500 mr-1"></i> Kondisi Lahan
                        </label>
                        <select name="kondisi_lahan" class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="Baik" {{ (old('kondisi_lahan', $lahan->kondisi_lahan) == 'Baik') ? 'selected' : '' }}> Baik</option>
                            <option value="Sedang" {{ (old('kondisi_lahan', $lahan->kondisi_lahan) == 'Sedang') ? 'selected' : '' }}> Sedang</option>
                            <option value="Rusak" {{ (old('kondisi_lahan', $lahan->kondisi_lahan) == 'Rusak') ? 'selected' : '' }}> Rusak</option>
                            <option value="Kritis" {{ (old('kondisi_lahan', $lahan->kondisi_lahan) == 'Kritis') ? 'selected' : '' }}> Kritis</option>
                        </select>
                        @error('kondisi_lahan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- POLA RUANG -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-vector-square text-indigo-500 mr-1"></i> Pola Ruang
                        </label>
                        <input type="text" name="pola_ruang"
                               value="{{ old('pola_ruang', $lahan->pola_ruang) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Contoh: Kawasan Tanaman Pangan">
                        @error('pola_ruang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- SUMBER AIR -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-water text-cyan-500 mr-1"></i> Sumber Air
                        </label>
                        <input type="text" name="sumber_air"
                               value="{{ old('sumber_air', $lahan->sumber_air) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Contoh: Air Hujan, Irigasi Teknis">
                        @error('sumber_air') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- SUMBER DATA -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <i class="fas fa-database text-yellow-600 mr-1"></i> Sumber Data
                        </label>
                        <input type="text" name="sumber"
                               value="{{ old('sumber', $lahan->sumber) }}"
                               class="w-full border border-slate-300 p-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                               placeholder="Sumber perolehan data (contoh: BPS, Dinas Pertanian)">
                        @error('sumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            <!-- BUTTON ACTIONS -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3 justify-between">
                <a href="{{ route('admin.dashboard') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-xl font-medium transition-all duration-200 text-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
                <div class="flex gap-3">
                    <button type="button" onclick="resetForm()"
                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-5 py-2.5 rounded-xl font-medium transition-all duration-200">
                        <i class="fas fa-undo-alt mr-2"></i> Reset
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all transform hover:scale-[1.02] shadow-md shadow-blue-500/20">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Informasi Tambahan -->
    <div class="mt-6 text-center text-xs text-slate-400 flex justify-center items-center gap-2">
        <i class="fas fa-info-circle"></i>
        <span>Pastikan data yang diisi akurat untuk mendukung sistem informasi geografis</span>
    </div>
</div>

<script>
    // Fungsi reset form ke nilai awal (data dari server)
    function resetForm() {
        Swal.fire({
            title: 'Reset Form?',
            text: "Semua perubahan yang belum disimpan akan hilang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-undo-alt"></i> Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset ke nilai awal dari server
                const form = document.getElementById('editForm');
                const originalData = {
                    nama_desa: '{{ addslashes($lahan->nama_desa) }}',
                    pemilik: '{{ addslashes($lahan->pemilik) }}',
                    luas: '{{ $lahan->luas }}',
                    jenis_tanaman: '{{ addslashes($lahan->jenis_tanaman) }}',
                    jenis: '{{ addslashes($lahan->jenis) }}',
                    kecamatan: '{{ addslashes($lahan->kecamatan) }}',
                    poktan: '{{ addslashes($lahan->poktan) }}',
                    kode_persil: '{{ addslashes($lahan->kode_persil) }}',
                    kondisi_lahan: '{{ addslashes($lahan->kondisi_lahan) }}',
                    pola_ruang: '{{ addslashes($lahan->pola_ruang) }}',
                    sumber_air: '{{ addslashes($lahan->sumber_air) }}',
                    sumber: '{{ addslashes($lahan->sumber) }}'
                };
                
                for (let key in originalData) {
                    let input = form.querySelector(`[name="${key}"]`);
                    if (input) input.value = originalData[key];
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Form direset',
                    text: 'Kembali ke data awal',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
    
    // Validasi sebelum submit
    document.getElementById('editForm').addEventListener('submit', function(e) {
        let pemilik = document.querySelector('[name="pemilik"]').value.trim();
        let desa = document.querySelector('[name="nama_desa"]').value.trim();
        let luas = document.querySelector('[name="luas"]').value;
        
        if (!pemilik) {
            e.preventDefault();
            Swal.fire('Error', 'Nama Pemilik harus diisi!', 'error');
            return false;
        }
        if (!desa) {
            e.preventDefault();
            Swal.fire('Error', 'Nama Desa harus diisi!', 'error');
            return false;
        }
        if (!luas || luas <= 0) {
            e.preventDefault();
            Swal.fire('Error', 'Luas lahan harus diisi dengan nilai positif!', 'error');
            return false;
        }
    });
    
    // Notifikasi sukses jika ada session flash
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>

</body>
</html>