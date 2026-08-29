# Blueprint Hierarki Perusahaan & Cabang (`company_structure_plan.md`)

> **Dokumen Referensi**: Keputusan Arsitektur Database untuk Struktur Induk Perusahaan (Holding) vs Cabang/Outlet (`company_id` & `parent_company_id`).

---

## 1. Keputusan Arsitektur (Architectural Decision)

Untuk mendukung skala bisnis dari **UMKM 1 Toko (Basic)** hingga **Restoran Multi-Cabang / Holding (Premium)** tanpa merusak kode yang sudah ada, ditetapkan keputusan arsitektur berikut:

* **TIDAK MELAKUKAN RENAME `company_id` MENJADI `outlet_id`**.
* Seluruh tabel operasional (`orders`, `transactions`, `products`, `stocks`, `tables`, `taxes`, `service_charges`, `daily_closings`) **tetap menggunakan `company_id`**.
* Struktur Induk vs Cabang ditangani menggunakan **Self-Referencing Parent-Child** pada tabel `companies` dengan menambahkan kolom **`parent_company_id`**.

---

## 2. Alasan Keputusan (Rationale)

1. **Zero Risk & 100% Backward Compatible**: 
   Tidak perlu me-refactor 20+ file migration, model (`$fillable`), controller, seeder, dan view Blade yang sudah berjalan sukses 100%.
2. **Fleksibilitas SaaS Multi-Tier**:
   - **Paket Basic (1 Toko)**: Cukup 1 record `company_id` (`parent_company_id = NULL`).
   - **Paket Premium (Multi-Outlet)**: 1 Induk Perusahaan membawahi banyak record `company_id` cabang di bawahnya.
3. **Standar Industri ERP**:
   Pola *Parent-Child Hierarchy* pada entitas perusahaan adalah standar baku ERP Enterprise (seperti Odoo, SAP, Oracle) untuk memfasilitasi laporan konsolidasi holding.

---

## 3. Skema Tabel & Contoh Data (`companies`)

```sql
ALTER TABLE companies 
ADD COLUMN parent_company_id ULID NULL AFTER company_id,
ADD CONSTRAINT fk_companies_parent FOREIGN KEY (parent_company_id) REFERENCES companies(company_id) ON DELETE SET NULL;
```

### Contoh Record Data:

| `company_id` | `parent_company_id` | `company_name` | `company_branch` | Status Role |
| :--- | :--- | :--- | :--- | :--- |
| `COMP-001` | `NULL` | PT Geprek Gambos Indonesia | Head Office | **Induk / Holding (SysAdmin)** |
| `COMP-002` | `COMP-001` | Geprek Gambos | Jakarta Pusat | **Cabang / Outlet 1** |
| `COMP-003` | `COMP-001` | Geprek Gambos | Yogyakarta | **Cabang / Outlet 2** |
| `COMP-004` | `NULL` | Warung Makan Cak Udin | Surabaya | **Single Store (Basic UMKM)** |

---

## 4. Implikasi Query Laporan

### A. Laporan Spesifik Per Cabang (Misal: Cabang Jakarta)
```sql
SELECT * FROM orders 
WHERE company_id = 'COMP-002';
```

### B. Laporan Konsolidasi Gabungan Seluruh Cabang (Level Induk / Holding)
```sql
SELECT * FROM orders 
WHERE company_id IN (
    SELECT company_id FROM companies 
    WHERE company_id = 'COMP-001' OR parent_company_id = 'COMP-001'
);
```
