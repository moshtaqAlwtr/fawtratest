<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromArray, WithHeadings, WithStyles
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }
    public function array(): array
    {
        $employees = Employee::get();

        return $employees->map(function ($employee) {
            $data = [];

            if (in_array('id', $this->fields)) {
                $data['id'] = $employee->id;
            }

            if (in_array('created_by', $this->fields)) {
                $data['created_by'] = $employee->created_by;
            }

            if (in_array('job_role_id', $this->fields)) {
                $data['job_role_id'] = isset($employee->job_role) ? $employee->job_role->name : 'غير محدد';
            }

            if (in_array('first_name', $this->fields)) {
                $data['first_name'] = $employee->first_name;
            }

            if (in_array('middle_name', $this->fields)) {
                $data['middle_name'] = $employee->middle_name;
            }

            if (in_array('full_name', $this->fields)) {
                $data['full_name'] = $employee->last_name;
            }

            if (in_array('nickname', $this->fields)) {
                $data['nickname'] = $employee->nickname;
            }

            if (in_array('phone_number', $this->fields)) {
                $data['phone_number'] = $employee->phone_number;
            }

            if (in_array('mobile_number', $this->fields)) {
                $data['mobile_number'] = $employee->mobile_number;
            }

            if (in_array('country', $this->fields)) {
                $data['country'] = $employee->country;
            }

            if (in_array('current_address_1', $this->fields)) {
                $data['current_address_1'] = $employee->current_address_1;
            }

            if (in_array('current_address_2', $this->fields)) {
                $data['current_address_2'] = $employee->current_address_2;
            }

            if (in_array('city', $this->fields)) {
                $data['city'] = $employee->city;
            }

            if (in_array('region', $this->fields)) {
                $data['region'] = $employee->region;
            }

            if (in_array('postal_code', $this->fields)) {
                $data['postal_code'] = $employee->postal_code;
            }

            if (in_array('email', $this->fields)) {
                $data['email'] = $employee->email;
            }

            if (in_array('status', $this->fields)) {
                $data['status'] = $employee->status == 0 ? 'مفعل' : 'غير مفعل';
            }

            if (in_array('created_at', $this->fields)) {
                $data['created_at'] = $employee->created_at;
            }

            if (in_array('branch_id', $this->fields)) {
                $data['branch_id'] = isset($employee->branch) ? $employee->branch->name : 'غير محدد';
            }

            if (in_array('gander', $this->fields)) {
                $data['gander'] = $employee->gander == 1 ? 'ذكر' : 'انثى';
            }

            if (in_array('nationality_status', $this->fields)) {
                $data['nationality_status'] = $employee->nationality_status
                    ? ($employee->nationality_status == 1 ? 'مواطن'
                        : ($employee->nationality_status == 2 ? 'مقيم'
                            : 'زائر'))
                    : 'غير معروف';
            }

            if (in_array('created_at', $this->fields)) {
                $data['created_at'] = $employee->created_at;
            }

            if (in_array('country', $this->fields)) {
                $data['country'] = $employee->country;
            }

            if (in_array('department_id', $this->fields)) {
                $data['department_id'] = isset($employee->department) ? $employee->department->name : 'غير محدد';
            }

            if (in_array('job_level_id', $this->fields)) {
                $data['job_level_id'] = isset($employee->job_level) ? $employee->job_level->name : 'غير محدد';
            }

            if (in_array('job_type_id', $this->fields)) {
                $data['job_type_id'] = isset($employee->job_type) ? $employee->job_type->name : 'غير محدد';
            }

            if (in_array('job_title_id', $this->fields)) {
                $data['job_title_id'] = isset($employee->job_title) ? $employee->job_title->name : 'غير محدد';
            }

            if (in_array('birth_date', $this->fields)) {
                $data['birth_date'] = $employee->birth_date;
            }

            if (in_array('personal_email', $this->fields)) {
                $data['personal_email'] = $employee->personal_email;
            }

            if (in_array('postal_code', $this->fields)) {
                $data['postal_code'] = $employee->postal_code;
            }

            if (in_array('shift_id', $this->fields)) {
                $data['shift_id'] = isset($employee->shift) ? $employee->shift->name : 'غير محدد';
            }

            if (in_array('leave_policy', $this->fields)) {
                $data['leave_policy'] = $employee->leave_policy;
            }

            if (in_array('direct_manager_id', $this->fields)) {
                $data['direct_manager_id'] = isset($employee->direct_manager) ? $employee->direct_manager->full_name : 'غير محدد';
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

        if (in_array('created_by', $this->fields)) {
            $headings[] = 'أنشئ بواسطة';
        }

        if (in_array('job_role_id', $this->fields)) {
            $headings[] = 'الدور الوظيفي';
        }

        if (in_array('first_name', $this->fields)) {
            $headings[] = 'الاسم الاول';
        }

        if (in_array('middle_name', $this->fields)) {
            $headings[] = 'اسم العائلة';
        }

        if (in_array('nickname', $this->fields)) {
            $headings[] = 'الاسم المستعار';
        }

        if (in_array('phone_number', $this->fields)) {
            $headings[] = 'رقم الهاتف';
        }

        if (in_array('mobile_number', $this->fields)) {
            $headings[] = 'رقم الجوال';
        }

        if (in_array('country', $this->fields)) {
            $headings[] = 'رمز الدولة';
        }

        if (in_array('current_address_1', $this->fields)) {
            $headings[] = 'العنوان الحالي 1';
        }

        if (in_array('current_address_2', $this->fields)) {
            $headings[] = 'العنوان الحالي 2';
        }

        if (in_array('city', $this->fields)) {
            $headings[] = 'المدينة';
        }

        if (in_array('region', $this->fields)) {
            $headings[] = 'المنطقة';
        }

        if (in_array('gender', $this->fields)) {
            $headings[] = 'النوع';
        }

        if (in_array('postal_code', $this->fields)) {
            $headings[] = 'الرمز البريدي';
        }

        if (in_array('nationality_status', $this->fields)) {
            $headings[] = 'حالة المواطنة';
        }

        if (in_array('created_at', $this->fields)) {
            $headings[] = 'تاريخ الانشاء';
        }

        if (in_array('country', $this->fields)) {
            $headings[] = 'رمز الدولة';
        }

        if (in_array('department_id', $this->fields)) {
            $headings[] = 'القسم';
        }

        if (in_array('job_level_id', $this->fields)) {
            $headings[] = 'المستوى الوظيفي';
        }

        if (in_array('job_type_id', $this->fields)) {
            $headings[] = 'نوع الوظيفة';
        }

        if (in_array('job_title_id', $this->fields)) {
            $headings[] = 'المسمى الوظيفي';
        }

        if (in_array('birth_date', $this->fields)) {
            $headings[] = 'تاريخ الميلاد';
        }

        if (in_array('personal_email', $this->fields)) {
            $headings[] = 'البريد الشخصي';
        }

        if (in_array('postal_code', $this->fields)) {
            $headings[] = 'الرمز البريدي';
        }

        if (in_array('shift_id', $this->fields)) {
            $headings[] = 'وردية الخضور';
        }

        if (in_array('leave_policy', $this->fields)) {
            $headings[] = 'سياسة الإجازات';
        }

        if (in_array('direct_manager_id', $this->fields)) {
            $headings[] = 'المدير المباشر';
        }

        return $headings;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

}

