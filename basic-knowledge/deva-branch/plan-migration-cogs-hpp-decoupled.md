# Rencana Migration — Modul COGS, HPP, & History Logging (V3.1)

> Status: **DRAFT RENCANA MIGRATION V3.1 (TERMASUK AUDIT TRAIL / HISTORY LOGGING)**
> Tanggal: 2026-08-09
> Pendekatan: 100% Terisolasi + Mengikuti Standar History Audit Trail `new-kasir` (`action_type`, `changed_by`, `effective_date`).

---

## 1. DAFTAR MIGRATION BARU (8 TABEL)

### A. Tabel Utama (5 Tabel)

#### 1. `2026_08_10_000001_create_cogs_raw_materials_table.php`
Tabel Master Bahan Mentah untuk estimasi modal HPP (`id`, `company_id`, `name`, `unit`, `price_per_unit`, `loss_percent`, `yield_percent`, `effective_price`, `notes`).

#### 2. `2026_08_10_000002_create_cogs_recipes_table.php`
Tabel Master Resep Standar Perkiraan per Menu (`id`, `company_id`, `product_id`, `recipe_name`, `target_food_cost`, `estimated_cogs`).

#### 3. `2026_08_10_000003_create_cogs_recipe_items_table.php`
Tabel Detail Takaran Bahan Perkiraan Resep (`id`, `cogs_recipe_id`, `cogs_raw_material_id`, `ingredient_qty`, `ingredient_cost`).

#### 4. `2026_08_10_000004_create_cogs_waste_logs_table.php`
Tabel Pencatatan Bahan Busuk / Rusak / Basi (*Rotten/Waste Log*) (`id`, `company_id`, `cogs_raw_material_id`, `qty_lost`, `waste_cost`, `reason`, `loss_date`).

#### 5. `2026_08_10_000005_create_hpp_financial_reports_table.php`
Tabel Laporan Keuangan Bulanan & Proyeksi Laba Rugi (`id`, `company_id`, `year`, `month`, `total_revenue`, `total_cogs_estimated`, `total_waste_cost`, `total_labor_cost`, `total_overhead_cost`, `net_profit_estimated`).

---

### B. Tabel Audit Trail / History Logging (3 Tabel Baru)

Mengikuti pola history pada `stock_histories`, `product_histories`, `discount_histories`, `voucher_histories`, dan `bundle_histories`:

#### 6. `2026_08_10_000006_create_cogs_raw_material_histories_table.php`
```sql
Schema::create('cogs_raw_material_histories', function (Blueprint $table) {
    $table->id('cogs_raw_material_history_id');
    $table->foreignId('cogs_raw_material_id')->constrained('cogs_raw_materials')->cascadeOnDelete();
    $table->string('company_id')->nullable();
    $table->string('name');
    $table->string('unit', 20);
    $table->decimal('price_per_unit', 15, 2);
    $table->decimal('loss_percent', 5, 2);
    $table->decimal('yield_percent', 5, 2);
    $table->decimal('effective_price', 15, 4);
    $table->string('action_type');                  // create / update / delete
    $table->string('changed_by', 50)->nullable();   // User yang mengubah
    $table->date('effective_date')->nullable();     // Tanggal perubahan berlaku
    $table->text('history_remark')->nullable();
    $table->timestamps();
});
```

#### 7. `2026_08_10_000007_create_cogs_recipe_histories_table.php`
```sql
Schema::create('cogs_recipe_histories', function (Blueprint $table) {
    $table->id('cogs_recipe_history_id');
    $table->foreignId('cogs_recipe_id')->constrained('cogs_recipes')->cascadeOnDelete();
    $table->string('company_id')->nullable();
    $table->string('recipe_name');
    $table->decimal('target_food_cost', 5, 2);
    $table->decimal('estimated_cogs', 15, 2);
    $table->json('snapshot_items_json')->nullable(); // Snapshot detail takaran saat diubah
    $table->string('action_type');                  // create / update / delete
    $table->string('changed_by', 50)->nullable();
    $table->date('effective_date')->nullable();
    $table->text('history_remark')->nullable();
    $table->timestamps();
});
```

#### 8. `2026_08_10_000008_create_cogs_waste_histories_table.php`
```sql
Schema::create('cogs_waste_histories', function (Blueprint $table) {
    $table->id('cogs_waste_history_id');
    $table->foreignId('cogs_waste_log_id')->constrained('cogs_waste_logs')->cascadeOnDelete();
    $table->string('company_id')->nullable();
    $table->foreignId('cogs_raw_material_id');
    $table->decimal('qty_lost', 15, 4);
    $table->decimal('waste_cost', 15, 2);
    $table->string('reason');
    $table->date('loss_date');
    $table->string('action_type');                  // create / update / delete
    $table->string('changed_by', 50)->nullable();
    $table->text('history_remark')->nullable();
    $table->timestamps();
});
```
