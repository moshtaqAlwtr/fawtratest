<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $table = 'clients';
    protected $dates = ['last_note_at'];
    protected $casts = [
        'force_show' => 'boolean',
    ];

    protected $primaryKey = 'id';
    public $timestamps = true;
    // الحقول القابلة للتعبئة
    protected $fillable = ['trade_name', 'category_id', 'force_show', 'last_note_at', 'first_name', 'last_name', 'phone', 'mobile', 'cat', 'street1', 'street2', 'category', 'city', 'region', 'visit_type', 'postal_code', 'country', 'tax_number', 'commercial_registration', 'credit_limit', 'credit_period', 'printing_method', 'opening_balance', 'opening_balance_date', 'code', 'currency', 'email', 'client_type', 'notes', 'attachments', 'employee_id', 'status_id' => 5, 'branch_id'];

    // العلاقة مع المواعيد
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // في ملف Client.php
    public function latestStatus()
    {
        return $this->hasOne(ClientRelation::class, 'client_id')->latest();
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function Neighborhoodname()
    {
        return $this->hasOne(Neighborhood::class, 'client_id');
    }

    public function Balance()
    {
        return Account::where('client_id', $this->id)->sum('balance');
    }
    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->street1) {
            $address[] = $this->street1;
        }
        if ($this->street2) {
            $address[] = $this->street2;
        }
        if ($this->city) {
            $address[] = $this->city;
        }
        if ($this->region) {
            $address[] = $this->region;
        }
        if ($this->postal_code) {
            $address[] = $this->postal_code;
        }
        if ($this->country) {
            $address[] = $this->country;
        }

        return implode(', ', $address);
    }

    // العلاقة مع ملاحظات المواعيد
public function appointmentNotes()
{
    return $this->hasMany(ClientRelation::class, 'client_id');
}


    // العلاقات
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'client_id', 'id');
    }

    public function cheques()
    {
        return $this->hasMany(ChequesCycle::class, 'client_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // العلاقة مع CreditNotification
    public function creditNotifications()
    {
        return $this->hasMany(CreditNotification::class, 'client_id');
    }

    // العلاقة مع JournalEntry
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'client_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    // Accessors
    public function getBalanceAttribute()
    {
        $invoicesTotal = $this->invoices()->sum('grand_total') ?? 0;
        $paymentsTotal = $this->payments()->sum('amount') ?? 0;
        return $invoicesTotal - $paymentsTotal;
    }
    public function supplyOrders()
    {
        return $this->hasMany(SupplyOrder::class, 'client_id');
    }

    public function getTotalInvoicesAttribute()
    {
        return $this->invoices()->sum('grand_total') ?? 0;
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->payments()->sum('amount') ?? 0;
    }

    public function getStatusAttribute()
    {
        // التحقق من حالة العميل باستخدام حقل deleted_at
        // إذا كان deleted_at فارغ فالعميل نشط
        return $this->deleted_at === null;
    }

    public function notes()
    {
        return $this->hasMany(AppointmentNote::class);
    }

    // دالة لجلب حركة الحساب
    public function getTransactionsAttribute()
    {
        $transactions = collect();

        // إضافة الفواتير
        $this->invoices->each(function ($invoice) use ($transactions) {
            $transactions->push([
                'date' => $invoice->invoice_date,
                'type' => 'فاتورة',
                'number' => $invoice->invoice_number,
                'amount' => $invoice->grand_total,
                'balance' => 0, // سيتم حسابه لاحقاً
                'notes' => $invoice->notes,
            ]);
        });

        // إضافة المدفوعات
        $this->payments->each(function ($payment) use ($transactions) {
            $transactions->push([
                'date' => $payment->payment_date,
                'type' => 'دفعة',
                'number' => $payment->payment_number,
                'amount' => -$payment->amount, // سالب لأنها دفعة
                'balance' => 0, // سيتم حسابه لاحقاً
                'notes' => $payment->notes,
            ]);
        });

        // ترتيب المعاملات حسب التاريخ
        $transactions = $transactions->sortBy('date');

        // حساب الرصيد التراكمي
        $balance = $this->opening_balance ?? 0;
        $transactions->transform(function ($transaction) use (&$balance) {
            $balance += $transaction['amount'];
            $transaction['balance'] = $balance;
            return $transaction;
        });

        return $transactions;
    }

    // Boot method to handle cascading deletes
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($client) {
            // حذف الفواتير المرتبطة
            $client->invoices()->delete();

            // حذف السندات المرتبطة
            // $client->receipts()->delete();

            // حذف المدفوعات المرتبطة
            $client->payments()->delete();

            // حذف الشيكات المرتبطة

            // حذف إشعارات الائتمان المرتبطة
            $client->creditNotifications()->delete();

            // حذف مدخلات المجلة المرتبطة
            $client->journalEntries()->delete();
        });
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
  public function categoriesClient()
{
    return $this->belongsTo(CategoriesClient::class, 'category_id'); // وضح المفتاح الأجنبي
}

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class, // النموذج المرتبط
            'client_employee', // اسم الجدول الوسيط
            'client_id', // المفتاح الأجنبي للنموذج الحالي
            'employee_id', // المفتاح الأجنبي للنموذج المرتبط
        )->withTimestamps(); // إضافة طوابع زمنية
    }
    public function payments()
    {
        return $this->hasManyThrough(PaymentsProcess::class, Invoice::class);
    }
    // في Client model
    public function clientEmployees()
    {
        return $this->hasMany(ClientEmployee::class);
    }


    public function locations()
    {
        return $this->hasMany(Location::class, 'client_id');
    }

public function neighborhood()
{
    return $this->hasOne(Neighborhood::class, 'client_id');
}
    public function visits()
    {
        return $this->hasMany(Visit::class, 'client_id');
    }

    public function status_client()
    {
        return $this->belongsTo(Statuses::class, 'status_id');
    }
    // في ملف app/Models/Client.php
    public function status()
    {
        return $this->belongsTo(Statuses::class);
    }
    public function fullLocation(): Attribute
    {
        return Attribute::get(function () {
            $location = [];

            if (!empty($this->street1)) {
                $location['street1'] = $this->street1;
            }

            if (!empty($this->street2)) {
                $location['street2'] = $this->street2;
            }

            if (!empty($this->country)) {
                $location['country'] = $this->country;
            }

            if (!empty($this->city)) {
                $location['city'] = $this->city;
            }

            if (!empty($this->region)) {
                $location['region'] = $this->region;
            }

            if (!empty($this->postal_code)) {
                $location['postal_code'] = $this->postal_code;
            }

            if ($this->relationLoaded('neighborhood') && $this->neighborhood) {
                $location['neighborhood'] = $this->neighborhood->name;
            } elseif (!empty($this->neighborhood_id)) {
                $location['neighborhood'] = Neighborhood::find($this->neighborhood_id)?->name;
            }

            if ($this->relationLoaded('locations') && $this->locations) {
                $location['latitude'] = $this->locations->latitude;
                $location['longitude'] = $this->locations->longitude;
                $location['map_url'] = $this->locations->map_url;
            }

            $formattedAddress = [];
            if (!empty($location['street1'])) {
                $formattedAddress[] = $location['street1'];
            }
            if (!empty($location['street2'])) {
                $formattedAddress[] = $location['street2'];
            }
            if (!empty($location['neighborhood'])) {
                $formattedAddress[] = $location['neighborhood'];
            }
            if (!empty($location['city'])) {
                $formattedAddress[] = $location['city'];
            }
            if (!empty($location['region'])) {
                $formattedAddress[] = $location['region'];
            }
            if (!empty($location['postal_code'])) {
                $formattedAddress[] = $location['postal_code'];
            }
            if (!empty($location['country'])) {
                $formattedAddress[] = $location['country'];
            }

            $location['formatted_address'] = implode('، ', $formattedAddress);

            return $location;
        });
    }
    public function formattedAddress(): Attribute
    {
        return Attribute::get(function () {
            return $this->full_location['formatted_address'] ?? '';
        });
    }
    public function group()
    {
        return $this->belongsTo(Region_groub::class, 'group_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'client_id');
    }


    public function clientRelations()
    {
        return $this->hasMany(ClientRelation::class, 'client_id');
    }


}
