<?php

// File: database/migrations/xxxx_create_purchase_invoice_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePurchaseInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->string('setting_name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // إدراج البيانات الافتراضية
        DB::table('purchase_invoice_settings')->insert([
            [
                'setting_key' => 'total_discounts',
                'setting_name' => 'إجمالي الخصومات',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'update_product_prices',
                'setting_name' => 'تعديل أسعار المنتجات بعد فاتورة الشراء',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'auto_payment',
                'setting_name' => 'الدفع التلقائي لفواتير الشراء إذا كان لدى المورد رصيد صالح',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'default_paid_invoices',
                'setting_name' => 'جعل فواتير المشتريات مدفوعة بالكامل افتراضياً',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'default_received_invoices',
                'setting_name' => 'جعل فواتير المشتريات مستلمة بالكامل افتراضياً',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'manual_purchase_orders',
                'setting_name' => 'إعطاء طلبات الشراء حالات يدوية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'manual_quote_orders',
                'setting_name' => 'إعطاء طلبات عروض الأسعار حالات يدوية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'manual_purchase_quotes',
                'setting_name' => 'إعطاء عروض أسعار المشتريات حالات يدوية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'manual_purchase_orders_status',
                'setting_name' => 'إعطاء أوامر الشراء حالات يدوية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'manual_invoice_status',
                'setting_name' => 'إعطاء فواتير الشراء حالات يدوية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'enable_settlement',
                'setting_name' => 'تفعيل نظام التسوية للفواتير والمدفوعات',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'enable_debit_notice',
                'setting_name' => 'تفعيل الإشعار الدائن',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'custom_daily_entries',
                'setting_name' => 'وضع مخصص للقيود اليومية',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_invoice_settings');
    }
}
