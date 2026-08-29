# TODO & Task Tracker — Branch `deva-branch` (2026-08-27)

> **Tanggal**: 2026-08-27 (27 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: Ready for New Directives / Next Features  

---

## 📌 Status Terkini Sistem
- **Phase 1 (Decoupled COGS, HPP & Waste)**: COMPLETED 100%
- **Phase 2 (Master Pajak PB1, Service Charge, Shift Closing & Reports)**: COMPLETED 100%
- **Multi-Tenant SaaS (Database-per-Client & System Admin)**: COMPLETED 100%
- **Alur Pre-Payment & Post-Payment**: COMPLETED 100%

---

## 📋 Task Hari Ini (2026-08-27):
- [x] **Audit & Review**: Pengecekan menyeluruh kompatibilitas seluruh fitur buatan Deva dengan pembaruan arsitektur Mukti (100% Pass, No Conflict).
- [x] **Restrukturisasi Dokumentasi**: Pengelompokan file `basic-knowledge/deva-branch/` ke dalam subfolder berbasis tanggal (`2026-08-19/` dan `2026-08-27/`).
- [x] **Perancangan Plan A (USAR Standard Refactor)**: Dokumen blueprint arsitektur perbaikan kalkulasi Laba Rugi, COGS, Waste, Prime Cost & Cash Flow telah disimpan di [`plan-a.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-27/plan-a.md).
- [x] **Perancangan Plan A-v2 (Dedicated Cash Flow & P&L Separation)**: Dokumen perbaikan lanjutan yang memisahkan total Laba Rugi vs Arus Kas, integrasi Modal Awal Laci Kasir (*Float*), Kas Kecil (*Paid-out*), dan Selisih Shift Closing (*Over/Short*) telah disimpan di [`plan-a-v2.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-27/plan-a-v2.md).
- [x] **Perancangan Plan A-v3 (Theoretical Food Cost & Alert Waste Log)**: Dokumen blueprint final yang menerapkan model performa resep murni (Gross Profit = Net Sales - COGS Resep), Waste Log sebagai Alert Memo pengawasan (tanpa memotong rumus laba rugi), dan sistem Dedicated Cash Flow terpisah telah disimpan di [`plan-a-v3.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-27/plan-a-v3.md).
- [x] **Perancangan Plan B (Universal & Enterprise-Ready F&B Suite)**: Master blueprint yang merangkum 5 Pilar Arsitektur lengkap: Smart Cash Drawer & Shift Handover (`cash_drawer_logs`), Pembelian PO Fleksibel (Tunai vs Tempo Hutang AP), Refactor & Renaming `cogs_raw_materials` -> `raw_stock_materials`, Laba Rugi Resep Murni (P&L) + Radar Box, Dedicated Cash Flow dengan Audit Trail Drill-Down Matrix untuk skala cafe kecil hingga resto besar telah disimpan di [`plan-b.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-27/plan-b.md).
- [ ] **Next Task**: Diskusi perbandingan dan penetapan plan bersama user sebelum eksekusi coding/migration.
