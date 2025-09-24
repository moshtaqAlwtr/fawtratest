<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentsExport implements FromArray, WithHeadings, WithStyles
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }
    public function array(): array
    {
        $departments = Department::with('employees')->get();

        return $departments->map(function ($department) {
            $data = [];

            if (in_array('id', $this->fields)) {
                $data['id'] = $department->id;
            }

            if (in_array('name', $this->fields)) {
                $data['name'] = $department->name;
            }

            if (in_array('short_name', $this->fields)) {
                $data['short_name'] = $department->short_name;
            }

            if (in_array('status', $this->fields)) {
                $data['status'] = $department->status == 0 ? 'نشط' : 'غير نشط';
            }

            if (in_array('description', $this->fields)) {
                $data['description'] = $department->description;
            }

            if (in_array('managers', $this->fields)) {
                $data['managers'] = $department->employees->pluck('full_name')->join(', ');
            }

            if (in_array('created_at', $this->fields)) {
                $data['created_at'] = $department->created_at;
            }

            if (in_array('updated_at', $this->fields)) {

            }

            return $data;

        })->toArray();
    }

    public function headings(): array
    {
        $headings = [];

        if (in_array('id', $this->fields)) {
            $headings[] = 'المعرف';
        }

        if (in_array('name', $this->fields)) {
            $headings[] = 'اسم القسم';
        }

        if (in_array('short_name', $this->fields)) {
            $headings[] = 'الاختصار';
        }

        if (in_array('status', $this->fields)) {
            $headings[] = 'الحالة';
        }

        if (in_array('description', $this->fields)) {
            $headings[] = 'الوصف';
        }

        if (in_array('managers', $this->fields)) {
            $headings[] = 'المديرون';
        }

        if (in_array('created_at', $this->fields)) {
            $headings[] = 'تاريخ الانشاء';
        }

        if (in_array('updated_at', $this->fields)) {
            $headings[] = 'تاريخ التعديل';
        }

        return $headings;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A' => ['width' => 5],
            'B' => ['width' => 10],
            'C' => ['width' => 10],
            'D' => ['width' => 10],
            'E' => ['width' => 40],
            'F' => ['width' => 30],
            'G' => ['width' => 10],
            'H' => ['width' => 10],
        ];
    }

}

