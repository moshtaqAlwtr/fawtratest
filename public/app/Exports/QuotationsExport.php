<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\PurchaseQuotation;

class QuotationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $purchaseQuotation;

    public function __construct(PurchaseQuotation $purchaseQuotation)
    {
        $this->purchaseQuotation = $purchaseQuotation;
    }

    public function collection()
    {
        return $this->purchaseQuotation->items;
    }

    public function headings(): array
    {
        return [
            'رقم الطلب',
            'المنتج',
            'الكمية',
            'معامل التحويل',
            'الوحدة الكبرى',
            'الوحدة الصغرى',
            'تاريخ الطلب',
            'تاريخ الاستحقاق',
            'الموردين',
            'الحالة'
        ];
    }

    public function map($item): array
    {
        $status = '';
        switch($this->purchaseQuotation->status) {
            case 1:
                $status = 'تحت المراجعة';
                break;
            case 2:
                $status = 'تمت الموافقة';
                break;
            case 3:
                $status = 'مرفوض';
                break;
        }

        return [
            $this->purchaseQuotation->code,
            $item->product->name,
            $item->quantity,
            $item->conversion_factor ?? '--',
            $item->unit->name ?? '--',
            $item->unit->name_small ?? '--',
            $this->purchaseQuotation->order_date,
            $this->purchaseQuotation->due_date,
            $this->purchaseQuotation->suppliers->pluck('trade_name')->implode(', '),
            $status
        ];
    }
}
