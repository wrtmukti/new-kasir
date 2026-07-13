# Request / Rules

> File ini tempat gue nulis aturan, preferensi, dan instruksi buat lu. Tujuannya ngebentuk cara kerja lu di proyek ini.

---

## Cara Kerja

- Jawab singkat, padat, to the point. Gak usah basa-basi.
- Kalo bisa dicek dulu baru ngomong — cek dulu.
- Kalo ada error, bilang error apa + solusinya langsung.
- Kalo ada banyak opsi, jangan tanya dulu — pilih yang paling masuk akal, jalanin, baru tanya kalo gagal.
- **Aturan 3:** Admin template pake yang ada di folder `resources/views/docs/`. Kalo butuh tabel, form, card, button, layout — liat referensi dari file docs situ.

## Dokumentasi

- Simpan catatan penting di `basic-knowledge/README.md`
- Kalo gue kasih aturan kayak gini → masukin ke `basic-knowledge/rule_ai.md`

## Prioritas

1. Database & migration integrity — jangan rusak data
2. Arsitektur yang konsisten (multi-tenant, SCD Type 2)
3. Code yang bersih dan rapi

## Aturan Penting

1. **Aturan baru:** Setiap gue ngasih aturan, langsung catat di file ini. Kalo aturan baru bertolak belakang dengan aturan lama, tanya ke gue dulu.
2. **Baca basic-knowledge:** Setiap mulai sesi atau sebelum ngerjain sesuatu, baca isi `basic-knowledge/` biar konteks nyambung.
3. **Admin template:** Pake template admin `resources/views/docs/` sebagai **referensi style** (class CSS, struktur HTML). Tapi layout & view dibuat sendiri di `resources/views/admin/basic_layout/`. Jangan extends atau panggil file dari `docs/`.
4. **Log code:** Setiap ada perubahan penting (buat file, fix bug, rename, update aturan) catat di `basic-knowledge/log_code.md`. Format: tanggal | tipe | deskripsi | file. Biar riwayat proyek terlacak.
5. **Auth/Layout:** Login & auth middleware belakangan. Semua route admin dibuka dulu tanpa middleware. Admin view gak perlu login dulu.
6. **Folder structure — view, controller, model ikut layer yang sama:**
   - **View** (`resources/views/`):
     - `admin/` — CRUD biasa (stock, product, category, dll). Route prefix: `/admin/`
     - `sys_admin/` — system admin (company). Route prefix: `/sys_admin/`
     - `guest/` — tampilan pemesan/ordering. Folder doang, isi belakangan.
   - **Controller** (`app/Http/Controllers/`):
     - `Admin/` — StockController, ProductController, CategoryController, dll
     - `SysAdmin/` — CompanyController, UserController (kelola tenant)
     - `Guest/` — (ordering, nanti)
   - **Model** (`app/Models/`):
     - `Admin/` — Product, Stock, Category, dll (entitas bisnis)
     - `SysAdmin/` — Company, User (entitas level tenant)
     - `Guest/` — (ordering, nanti)
   - Route prefix & namespace ikut folder: `Route::prefix('sys_admin')->namespace('SysAdmin')->group(...)`

7. **Form & Table UX — loading feedback & pagination:**

   ### Form
   - Setiap form (create/edit) harus pakai:
     - **Input skeleton:** bungkus tiap input/select dalam `<div class="input-skeleton">` — otomatis shimmer saat submit
     - **Button loading:** tambah class `btn-loading` ke tombol submit — otomatis disabled + spinner saat diklik
   - **Min 400ms loading setelah submit** — pakai `setTimeout` biar user liat feedback.
   - Referensi: `resources/views/docs/forms.blade.php` (bagian "Input Skeleton & Button Loading Demo")

   ### Table
   - Setiap table index wajib pake **pagination** — referensi class `pagination-modern` dari docs (UI Components > Pagination).
   - Wajib ada **filter jumlah baris per page** (10 / 50 / 100) — berupa select/dropdown di atas table.
   - **AJAX** untuk semua interaksi table: ganti page, ganti filter per page, search, sort — jangan reload seluruh halaman.
   - Setiap perubahan data di table (load page, ganti per-page, dll) wajib **loading shimmer min 400ms** — skeleton per baris, **header tabel tetap muncul**, cuma isi row yang diganti skeleton.
   - Contoh struktur:
     ```html
     <!-- Filter per page -->
     <div class="d-flex align-items-center gap-2 mb-3">
       <label class="form-label-modern mb-0">Tampilkan</label>
       <select class="form-select-modern" id="perPage" style="width:auto;">
         <option value="10">10</option>
         <option value="50">50</option>
         <option value="100">100</option>
       </select>
       <span class="text-muted-c" style="font-size:0.85rem;">data</span>
     </div>

     <!-- Table body — dikasih id biar di-render ulang via AJAX -->
     <div id="table-body">
       <!-- isi table + skeleton loading di sini -->
     </div>

     <!-- Pagination — class pagination-modern -->
     <ul class="pagination-modern">
       <li class="disabled"><span>&laquo;</span></li>
       <li class="active"><a href="#">1</a></li>
       <li><a href="#">2</a></li>
       <li><a href="#">3</a></li>
       <li><a href="#">&raquo;</a></li>
     </ul>
     ```
   - Saat AJAX loading, table body diisi skeleton dulu (min 400ms):
     ```html
     <div class="table-responsive" id="table-body">
       <div class="p-3">
         <div class="skeleton skeleton-text mb-2"></div>
         <div class="skeleton skeleton-text mb-2"></div>
         <div class="skeleton skeleton-text mb-2"></div>
         <div class="skeleton skeleton-text"></div>
       </div>
     </div>
     ```
   - Referensi pagination: `resources/views/docs/ui-components.blade.php` (section 11. Pagination)

8. **Alert/notifikasi pakai NexoraToast (bukan session flash alert):**
   - Semua feedback ke user (after submit, error, sukses, warning, info) wajib paka **`NexoraToast()`** — bukan alert default browser, bukan session flash alert dari Laravel.
   - Signature:
     ```javascript
     NexoraToast('Pesan sukses', 'success');   // sukses — hijau
     NexoraToast('Pesan error', 'danger');      // error — merah
     NexoraToast('Pesan informasi');            // default — info (biru)
     ```
   - Untuk feedback setelah form submit (non-AJAX), panggil `NexoraToast` di Blade setelah redirect:
     ```blade
     @if(session('success'))
         <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('success') }}', 'success'); });</script>
     @endif
     @if(session('error'))
         <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
     @endif
     ```
   - Untuk AJAX response, langsung panggil di callback JS setelah loading selesai.
   - Referensi: `resources/views/docs/ui-components.blade.php` (section 33. Toast Demo)
