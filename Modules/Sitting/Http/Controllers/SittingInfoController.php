<?php

namespace Modules\Sitting\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\AccountSetting;
use App\Models\BusinessData;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Revenue;
use App\Models\Supplier;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Response;

class SittingInfoController extends Controller
{
    public function index()
    {
        $client   = Client::where('user_id',auth()->user()->id)->first();
        $account_setting = AccountSetting::where('user_id',auth()->user()->id)->first();
        return view('sitting::accountInfo.index',compact('client','account_setting'));
    }
    public function create()
    {
        return view('sitting::accountInfo.create');
    }

    public function Backup()
    {

        return view('sitting::accountInfo.Backup');
    }
    public function download(Request $request)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'data_types' => 'required|array',
            'data_types.*' => 'in:invoices,clients,purchase_orders,products,expenses,revenues,employees,suppliers',
            'file_format' => 'required|in:xml,json,csv',
        ]);

        // إنشاء ملف ZIP
        $zip = new ZipArchive;
        $zipFileName = 'backup_' . now()->format('Ymd_His') . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($request->data_types as $dataType) {
                // جلب البيانات بناءً على الاختيار
                $data = [];
                switch ($dataType) {
                    case 'invoices':
                        $data = Invoice::all();
                        break;
                    case 'clients':
                        $data = Client::all();
                        break;
                    case 'purchase_orders':
                        $data = PurchaseOrder::all();
                        break;
                    case 'products':
                        $data = Product::all();
                        break;
                    case 'expenses':
                        $data = Expense::all();
                        break;
                    case 'revenues':
                        $data = Revenue::all();
                        break;
                    case 'employees':
                        $data = Employee::all();
                        break;
                    case 'suppliers':
                        $data = Supplier::all();
                        break;
                }

                // تحويل البيانات إلى التنسيق المطلوب
                $fileContent = '';
                $fileName = $dataType . '_' . now()->format('Ymd_His');
                switch ($request->file_format) {
                    case 'xml':
                        $fileContent = $this->arrayToXml($data->toArray());
                        $fileName .= '.xml';
                        break;
                    case 'json':
                        $fileContent = json_encode($data, JSON_PRETTY_PRINT);
                        $fileName .= '.json';
                        break;
                    case 'csv':
                        $fileContent = $this->arrayToCsv($data->toArray());
                        $fileName .= '.csv';
                        break;
                }

                  ModelsLog::create([
                'type' => 'setting',

                'type_log' => 'log', // نوع النشاط
                'description' => 'تم تنزيل نسخة احتياطية',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

                // إضافة الملف إلى الأرشيف
                $zip->addFromString($fileName, $fileContent);
            }

            $zip->close();
        }

        // تنزيل ملف ZIP
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    /**
     * تحويل المصفوفة إلى XML
     */
    private function arrayToXml(array $data, $rootNodeName = 'root', $xml = null)
    {
        if ($xml === null) {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootNodeName . '/>');
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $key = is_numeric($key) ? 'item' : $key;
                $this->arrayToXml($value, $key, $xml->addChild($key));
            } else {
                $key = is_numeric($key) ? 'item' : $key;
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml->asXML();
    }

    /**
     * تحويل المصفوفة إلى CSV
     */
    private function arrayToCsv(array $data)
    {
        if (count($data) === 0) {
            return '';
        }

        // فتح مخرجات CSV في الذاكرة
        $output = fopen('php://temp', 'w');

        // كتابة رأس CSV (الأعمدة)
        fputcsv($output, array_keys($data[0]));

        // كتابة البيانات
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        // إرجاع المحتوى كسلسلة نصية
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }




    public function store(Request $request)
    {



    }
}





