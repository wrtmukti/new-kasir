# 💰 Arsitektur Lengkap Alur Arus Kas (Cash Flow), Clock-In/Clock-Out, & Alur Uang Brankas

> **Dokumen**: `cash_flow_and_clock_in_out_architecture.md`  
> **Lokasi**: `basic-knowledge/deva-branch/cash_flow_and_clock_in_out_architecture.md`  
> **Branch**: `deva-branch`  
> **Tanggal Rilis**: 2026-09-01  
> **Format Utama**: JSON Schemas, Event Flow State Machines, & Formula Akuntansi Plan B

---

## 📑 Daftar Isi
1. [Ringkasan Eksekutif & Peta Perjalanan Uang (Money Journey)](#1-ringkasan-eksekutif--peta-perjalanan-uang)
2. [JSON Schema & State Machine: Siklus Shift Kasir (Clock-In ➔ Clock-Out)](#2-json-schema--state-machine-siklus-shift-kasir)
3. [JSON Blueprint: Arah Aliran Uang Fisik (Laci Kasir ➔ Brankas ➔ Holding)](#3-json-blueprint-arah-aliran-uang-fisik)
4. [JSON Komparasi: Arus Kas Riil vs Laba Rugi Akrual vs Buku Kas Laci](#4-json-komparasi-arus-kas-riil-vs-laba-rugi-akrual-vs-buku-kas-laci)
5. [JSON Schema: Monitoring Eksekutif Owner (Pusat Setoran & Hutang PO)](#5-json-schema-monitoring-eksekutif-owner)
6. [Tabel Formula Matematis & Validasi Presisi](#6-tabel-formula-matematis--validasi-presisi)

---

## 1. 🧭 Ringkasan Eksekutif & Peta Perjalanan Uang

Sistem kasir dan finansial Nexora POS membagi pergerakan uang menjadi **3 Zona Finansial yang Terisolasi**:

```text
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                  PETA PERJALANAN UANG NEXORA                                     │
├──────────────────────────────┬──────────────────────────────────┬────────────────────────────────┤
│    ZONA 1: LACI KASIR (POS)  │     ZONA 2: BRANKAS OUTLET       │    ZONA 3: REKENING / HOLDING  │
├──────────────────────────────┼──────────────────────────────────┼────────────────────────────────┤
│ • Modal Awal (Float)         │ • Amplop Setoran Tutup Shift     │ • Penjualan Non-Tunai (QRIS)   │
│ • Uang Tunai Pembeli         │   (cash_deposit_to_safe)         │ • Setoran Fisik Bank Harian    │
│ • Kas Masuk/Keluar Laci      │ • Saldo Disimpan Sementara di    │ • Pembayaran PO Supplier Tempo │
│ • Sisa Laci Shift Berikutnya │   Brankas Besi Outlet            │ • Laba Bersih Konsolidasi      │
└──────────────────────────────┴──────────────────────────────────┴────────────────────────────────┘
```

---

## 2. 🔄 JSON Schema & State Machine: Siklus Shift Kasir

Alur operasional kasir dari saat toko buka, transaksi harian, hingga cetak Z-Report saat toko tutup.

```json
{
  "shift_lifecycle": {
    "step_1_clock_in": {
      "endpoint": "POST /admin/keuangan/shift-operational/open",
      "controller_action": "ShiftOperationalController@openShift",
      "payload_request": {
        "starting_cash": 300000,
        "shift_name": "Shift Pagi (08:00 - 16:00)",
        "shift_number": 1
      },
      "business_date_logic": {
        "cutoff_time": "03:00:00",
        "rule": "Jika waktu buka < 03:00 subuh, business_date = kemarin. Jika >= 03:00, business_date = hari ini."
      },
      "database_record_created": {
        "table": "daily_closings",
        "state": {
          "id": 101,
          "outlet_id": "KS-JKT",
          "cashier_id": 4,
          "shift_name": "Shift Pagi",
          "business_date": "2026-09-01",
          "opened_at": "2026-09-01T08:00:00Z",
          "starting_cash": 300000.00,
          "system_cash_sales": 0.00,
          "system_non_cash_sales": 0.00,
          "cash_in_amount": 0.00,
          "cash_out_amount": 0.00,
          "system_expected_cash": 300000.00,
          "actual_cash_counted": 0.00,
          "status": "open"
        }
      }
    },
    
    "step_2_operational_transactions": {
      "cash_sales": {
        "description": "Pelanggan membayar pesanan di POS dengan uang tunai.",
        "db_binding": "orders & transactions otomatis mengisi kolom daily_closing_id = 101",
        "live_impact": "Menambah system_cash_sales dan system_expected_cash secara realtime."
      },
      "non_cash_sales": {
        "description": "Pelanggan membayar via QRIS, Transfer Bank, atau EDC.",
        "live_impact": "Menambah system_non_cash_sales (uang langsung masuk rekening bank, BUKAN laci kasir)."
      },
      "drawer_cash_in": {
        "endpoint": "POST /admin/keuangan/shift-operational/cash-in",
        "payload": {
          "amount": 100000,
          "category": "Top-up Uang Kembalian",
          "reason": "Tambah modal pecahan kecil Rp 2.000 & Rp 5.000"
        },
        "table_log": "cash_drawer_logs (type: 'in')",
        "live_impact": "daily_closings.cash_in_amount bertambah +Rp 100.000"
      },
      "drawer_cash_out": {
        "endpoint": "POST /admin/keuangan/shift-operational/cash-out",
        "payload": {
          "amount": 35000,
          "category": "Petty Cash",
          "reason": "Beli Es Batu Kristal 2 Karung & Air Galon"
        },
        "table_log": "cash_drawer_logs (type: 'out')",
        "live_impact": "daily_closings.cash_out_amount bertambah +Rp 35.000"
      }
    },

    "step_3_clock_out_and_z_report": {
      "endpoint": "POST /admin/keuangan/shift-operational/close",
      "controller_action": "ShiftOperationalController@closeShift",
      "blind_drop_protocol": "Kasir TIDAK DIBERITAHU angka expected sistem. Kasir wajib menghitung uang fisik di laci.",
      "payload_request": {
        "actual_cash_counted": 1865000,
        "retained_cash_float": 300000,
        "cashier_note": "Uang pas, semua struk petty cash terlampir."
      },
      "calculation_engine": {
        "system_expected_cash": "300.000 (modal) + 1.500.000 (tunai) + 100.000 (kas masuk) - 35.000 (kas keluar) = 1.865.000",
        "actual_cash_counted": 1865000,
        "cash_difference": "1.865.000 - 1.865.000 = 0 (Match / Tanpa Selisih)",
        "retained_cash_float": 300000,
        "cash_deposit_to_safe": "1.865.000 - 300.000 = 1.565.000 (Disetor ke Brankas)"
      },
      "database_record_closed": {
        "table": "daily_closings",
        "state": {
          "id": 101,
          "closed_at": "2026-09-01T16:00:00Z",
          "system_cash_sales": 1500000.00,
          "system_non_cash_sales": 2450000.00,
          "cash_in_amount": 100000.00,
          "cash_out_amount": 35000.00,
          "system_expected_cash": 1865000.00,
          "actual_cash_counted": 1865000.00,
          "retained_cash_float": 300000.00,
          "cash_deposit_to_safe": 1565000.00,
          "cash_difference": 0.00,
          "status": "closed"
        }
      },
      "z_report_thermal_output": {
        "print_view": "resources/views/admin/keuangan/shift-operational/z-report.blade.php",
        "width": "80mm Thermal Struk",
        "sections": [
          "Header Outlet & Shift Info",
          "Ringkasan Omzet (Cash vs Non-Cash)",
          "Rekonsiliasi Kas Laci (Starting + In - Out)",
          "Hasil Audit Fisik (Actual vs Expected vs Difference)",
          "Distribusi Uang (Sisa di Laci vs Setor Brankas)",
          "Tanda Tangan Kasir & Supervisor"
        ]
      }
    }
  }
}
```

---

## 3. 🏦 JSON Blueprint: Arah Aliran Uang Fisik (Money Path)

Bagaimana uang fisik mengalir dari tangan pembeli hingga masuk ke brankas outlet dan rekening perusahaan.

```json
{
  "money_flow_pipeline": {
    "source_1_customer_payment": {
      "jalur_a_uang_tunai": {
        "tujuan": "Laci Kasir (Cash Drawer)",
        "pencatat": "Kasir via POS Checkout",
        "nominal_contoh": 1500000
      },
      "jalur_b_digital_qris_edc": {
        "tujuan": "Rekening Bank / Escrow Payment Gateway",
        "pencatat": "Sistem Pembayaran Digital",
        "nominal_contoh": 2450000,
        "keterangan": "Uang ini TIDAK PERNAH masuk laci kasir fisik."
      }
    },

    "source_2_petty_cash_drawer": {
      "kas_keluar_operasional": {
        "nominal": 35000,
        "alokasi": "Beli bahan mendesak / perlengkapan toko",
        "syarat": "Wajib simpan nota/struk fisik di dalam laci"
      }
    },

    "checkpoint_shift_closing": {
      "total_uang_fisik_di_laci": 1865000,
      "pemecahan_uang_di_akhir_shift": {
        "bagian_1_modal_tinggal_di_laci": {
          "field": "retained_cash_float",
          "nominal": 300000,
          "arah_tujuan": "Tetap berada di dalam laci kasir.",
          "fungsi": "Menjadi modal awal (starting cash) untuk Kasir Shift Berikutnya (Shift 2)."
        },
        "bagian_2_setoran_brankas": {
          "field": "cash_deposit_to_safe",
          "nominal": 1565000,
          "arah_tujuan": "Dimasukkan ke dalam amplop tertutup ➔ Disimpan ke Brankas Besi Outlet (Safe Deposit).",
          "penanggung_jawab": "Kasir & Supervisor Shift",
          "bukti_fisik": "Struk Z-Report ditempel di depan amplop setoran."
        }
      }
    },

    "destination_outlet_safe_to_bank": {
      "aktivitas": "Setoran Tunai ke Rekening Bank Perusahaan",
      "frekuensi": "Harian / 2 Hari Sekali",
      "prosedur": [
        "1. Supervisor membuka brankas outlet dan mengambil seluruh amplop setoran harian.",
        "2. Membawa uang fisik ke Teller Bank / Mesin Setor Tunai (CDM).",
        "3. Uang fisik berubah menjadi Saldo Rekening Bank Holding."
      ]
    },

    "destination_bank_to_supplier": {
      "aktivitas": "Pelunasan Hutang PO Supplier Tempo",
      "sumber_dana": "Rekening Bank Perusahaan",
      "pemicu": "Kalender Jatuh Tempo Hutang PO di Portal Owner (admin.owner.cash-debt)"
    }
  }
}
```

---

## 4. ⚖️ JSON Komparasi: Arus Kas Riil vs Laba Rugi Akrual vs Buku Kas Laci

Mengapa Nexora memisahkan laporan keuangan menjadi **3 Pilar Plan B**:

```json
{
  "financial_architecture_plan_b": {
    "pillar_1_accrual_pnl": {
      "name": "Laporan Laba Rugi Akrual (P&L)",
      "filosofi": "Mengukur kinerja profitabilitas murni berdasarkan menu yang laku di kasir.",
      "formula": "Net Profit = Total Omzet - Theoretical COGS Resep Murni - Kerugian Waste - Beban Operasional",
      "contoh_perhitungan": {
        "total_revenue": 3950000,
        "theoretical_cogs_resep": 1420000,
        "gross_profit": 2530000,
        "waste_loss_dapur": 66000,
        "operating_expenses_overhead": 850000,
        "net_profit": 1614000
      },
      "aturan_emas": "Belanja PO bahan mentah BUKAN pengurang laba di sini (karena masih berupa aset persediaan di gudang)."
    },

    "pillar_2_real_cash_flow": {
      "name": "Laporan Arus Kas Riil (Cash Flow)",
      "filosofi": "Mengukur likuiditas uang nyata yang masuk dan keluar dari rekening & laci bisnis.",
      "formula": "Net Cash Flow = Total Inflow Riil - Total Outflow Riil",
      "contoh_perhitungan": {
        "inflow": {
          "penjualan_tunai_kasir": 1500000,
          "penjualan_non_tunai_qris_edc": 2450000,
          "topup_modal_laci_owner": 100000,
          "total_inflow": 4050000
        },
        "outflow": {
          "bayar_po_supplier_lunas": 1320000,
          "petty_cash_laci_kasir": 35000,
          "gaji_dan_sewa_operasional": 850000,
          "total_outflow": 2205000
        },
        "net_cash_flow": 1845000
      }
    },

    "pillar_3_cash_drawer_logs": {
      "name": "Buku Kas Laci Kasir (Cash Drawer Book)",
      "filosofi": "Mengontrol uang fisik di laci kasir agar tidak pernah terjadi kebocoran uang kembalian.",
      "aturan_emas": "Top-up modal laci dan petty cash kasir terisolasi di sini dan TIDAK PERNAH mencemari laporan laba rugi restoran."
    }
  }
}
```

---

## 5. 👑 JSON Schema: Monitoring Eksekutif Owner

Bagaimana Portal Owner menyatukan setoran brankas dan mengontrol hutang supplier seluruh cabang dalam satu layar terpusat.

```json
{
  "owner_portal_consolidation": {
    "dashboard_kpis": {
      "endpoint": "GET /admin/owner/dashboard",
      "controller_action": "OwnerDashboardController@index",
      "aggregated_output": {
        "total_revenue_holding": 12850000.00,
        "total_net_profit_holding": 4820000.00,
        "total_cash_inflow_holding": 13150000.00,
        "total_safe_deposit_holding": 4655000.00
      }
    },

    "safe_deposit_and_supplier_debt_hub": {
      "endpoint": "GET /admin/owner/cash-debt",
      "controller_action": "OwnerCashDebtController@index",
      "sub_module_1_safe_deposit_tracker": [
        {
          "outlet": "Kopi Senja - Jakarta (KS-JKT)",
          "shift_name": "Shift Pagi",
          "closed_at": "2026-09-01 16:00",
          "actual_counted": 1865000.00,
          "retained_in_drawer": 300000.00,
          "deposit_to_safe": 1565000.00,
          "difference": 0.00,
          "audit_status": "MATCH"
        },
        {
          "outlet": "Kopi Senja - Bandung (KS-BDG)",
          "shift_name": "Shift Siang",
          "closed_at": "2026-09-01 16:30",
          "actual_counted": 1225000.00,
          "retained_in_drawer": 300000.00,
          "deposit_to_safe": 925000.00,
          "difference": 0.00,
          "audit_status": "MATCH"
        },
        {
          "outlet": "Kopi Senja - Yogyakarta (KS-YOG)",
          "shift_name": "Shift Full Day",
          "closed_at": "2026-09-01 17:00",
          "actual_counted": 920000.00,
          "retained_in_drawer": 200000.00,
          "deposit_to_safe": 720000.00,
          "difference": -5000.00,
          "audit_status": "SHORTAGE (Kasir Kurang Rp 5.000)"
        }
      ],
      "sub_module_2_supplier_debt_calendar": [
        {
          "po_number": "PO-20260901-001",
          "supplier_name": "PT Sumber Biji Kopi Nusantara",
          "outlet": "Kopi Senja - Jakarta",
          "total_amount": 1320000.00,
          "due_date": "2026-09-03",
          "days_remaining": 2,
          "urgency_badge": "CRITICAL (Bayar dalam 48 Jam)"
        },
        {
          "po_number": "PO-20260901-002",
          "supplier_name": "Dairy Farm Fresh Milk",
          "outlet": "Kopi Senja - Bandung",
          "total_amount": 850000.00,
          "due_date": "2026-09-10",
          "days_remaining": 9,
          "urgency_badge": "SAFE (Aman)"
        }
      ]
    }
  }
}
```

---

## 6. 📐 Tabel Formula Matematis & Validasi Presisi

Berikut adalah rumus baku yang ditanam di seluruh controller Nexora POS:

| Variabel Finansial | Formula / Persamaan | Tujuan & Kegunaan |
|---|---|---|
| **System Expected Cash** | $$\text{Starting Cash} + \text{Cash Sales} + \text{Cash In} - \text{Cash Out}$$ | Menghitung saldo uang fisik yang *seharusnya ada* di laci kasir saat tutup shift. |
| **Cash Difference** | $$\text{Actual Cash Counted} - \text{System Expected Cash}$$ | Audit kejujuran kasir. Positif = *Overage* (Uang Lebih), Negatif = *Shortage* (Uang Hilang/Kurang). |
| **Cash Deposit to Safe** | $$\max(0, \text{Actual Cash Counted} - \text{Retained Cash Float})$$ | Nominal uang fisik yang wajib dimasukkan ke amplop setoran brankas outlet. |
| **Retained Cash Float** | Ditentukan kasir/kebijakan outlet (misal Rp 300.000) | Uang kembalian yang ditinggal di laci untuk modal kasir shift berikutnya. |
| **Gross Profit (Laba Kotor)** | $$\text{Omzet Penjualan} - \text{Theoretical COGS Resep}$$ | Mengukur efisiensi marjin resep makanan tanpa terdistorsi stok gudang. |
| **Net Profit (Laba Bersih)** | $$\text{Gross Profit} - \text{Waste Dapur} - \text{Biaya Operasional}$$ | Mengukur keuntungan bersih bisnis secara akrual. |
| **Net Cash Flow** | $$\text{Total Inflow Riil} - \text{Total Outflow Riil}$$ | Mengukur pertumbuhan kas nyata di rekening & brankas bisnis. |

---

> 📌 **Catatan Arsitektur**: Seluruh schema JSON dan formula di atas telah diterapkan pada controller `ShiftOperationalController.php`, `ConsolidatedFinancialService.php`, `OwnerCashDebtController.php`, serta view `shift-operational/index.blade.php` dan `owner/cash-debt.blade.php` di branch **`deva-branch`**.
