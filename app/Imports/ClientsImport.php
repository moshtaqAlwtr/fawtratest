<?php
namespace App\Imports;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $employeeId = $row['employee_id'] ?? null;
        if ($employeeId && !Employee::where('id', $employeeId)->exists()) {
            return null; // تجاوز السطر إذا لم يكن العميل موجودًا
        }
        $branchId = $row['branch_id'] ?? null;
        if ($branchId && !Branch::where('id', $branchId)->exists()) {
            return null; // تجاوز السطر إذا لم يكن العميل موجودًا
        }


        return new Client([
            'trade_name' => $row['trade_name'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'phone' => $row['phone'] ?? null,
            'mobile' => $row['mobile'] ?? null,
            'street1' => $row['street1'] ?? null,
            'street2' => $row['street2'] ?? null,
            'category' => $row['category'] ?? null,
            'city' => $row['city'] ?? null,
            'region' => $row['region'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'country' => $row['country'] ?? null,
            'tax_number' => $row['tax_number'] ?? null,
            'commercial_registration' => $row['commercial_registration'] ?? null,
            'credit_limit' => $row['credit_limit'] ?? null,
            'credit_period' => $row['credit_period'] ?? null,
            'printing_method' => $row['printing_method'] ?? null,
            'opening_balance' => $row['opening_balance'] ?? null,
            'opening_balance_date' => $row['opening_balance_date'] ?? null,
            'code' => $row['code'] ?? null,
            'currency' => $row['currency'] ?? null,
            'email' => $row['email'] ?? null,
            'client_type' => $row['client_type'] ?? null,
            'notes' => $row['notes'] ?? null,
            'employee_id' => $employeeId,
            'branch_id' => $branchId,
'status' => $row['status'] ?? null,
        ]);
    }
}
