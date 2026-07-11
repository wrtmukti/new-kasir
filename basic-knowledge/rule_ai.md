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
6. **Folder structure view:**
   - `admin/` — CRUD biasa (stock, product, category, dll). Route prefix: `/admin/`
   - `sys_admin/` — system admin (company). Route prefix: `/sys_admin/`
   - `guest/` — tampilan pemesan/ordering. Folder doang, isi belakangan.
