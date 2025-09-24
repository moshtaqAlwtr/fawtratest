@extends('master')

@section('title')
    الترقيم المتسلسل
@stop

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #f0f4f8, #e1f5fe);
        }

        .btn-save {
            background: linear-gradient(45deg, #28a745, #81c784);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .btn-save:hover {
            background: linear-gradient(45deg, #218838, #66bb6a);
        }

        .sidebar {
            background: linear-gradient(135deg, #ffffff, #e3f2fd);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 80px);
            position: sticky;
            /* Changed to sticky for better behavior */
            top: 80px;
            overflow-y: auto;
            width: 260px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #007bff;
            font-size: 15px;
            display: block;
            padding: 8px 15px;
            background-color: #f1f8e9;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #007bff;
            color: white;
        }

        .content {
            background: linear-gradient(135deg, #ffffff, #f9fbe7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
            /* Adjusted margin for spacing */
            min-height: 80vh;
        }

        .content h3 {
            color: #007bff;
            font-weight: bold;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-check-label {
            font-weight: bold;
            color: #555;
        }

        .btn-primary {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #3e4db3, #6e72c9);
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الترقيم المتسلسل</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">العرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- الشريط الجانبي -->
                <div class="col-md-3">
                    <div class="sidebar">
                        <ul class="list-unstyled">
                            @foreach ([
            'invoice' => 'فاتورة',
            'customer' => 'العميل',
            'quotation' => 'عروض الأسعار',
            'return-invoice' => 'فاتورة مرتجعة',
            'credit-note' => 'إشعار دائن',
            'reservation' => 'حجز',
            'purchase-invoice' => 'فاتورة شراء',
            'purchase-return' => 'مرتجع مشتريات',
            'supply-order' => 'أمر التوريد',
            'supplier' => 'المورد',
            'entry' => 'قيد',
            'expense' => 'مصروف',
            'receipt-voucher' => 'سندات القبض',
            'warehouse-add' => 'إذن إضافة مخزون',
            'warehouse-dispose' => 'إذن صرف مخزون',
            'transfer-request' => 'طلب تحويل',
            'branch' => 'الفروع',
            'inventory-report' => 'ورقة الجرد',
            'products' => 'المنتجات',
            'contracts' => 'العقود',
            'quotation-request' => 'طلب عرض أسعار',
            'purchase-quotation' => 'عرض سعر مشتريات',
            'purchase-order' => 'أمر شراء',
            'origin' => 'أصل',
            'invoice-payment' => 'مدفوعات الفواتير',
            'payment-return' => 'دفع مبلغ مرتجع',
            'purchase-refund' => 'دفع فاتورة شراء',
            'sales-debit' => 'إشعار مدين مبيعات',
            'products-custom' => 'المنتجات المخصصة',
            'purchase-refund-payment' => 'دفع مرتجع المشتريات',
            'sales-debit-notes' => 'إشعارات مدينة المبيعات',
            'purchase-credit-notes' => 'إشعارات دائنة المشتريات',
            'production-routes' => 'مسارات الإنتاج',
            'workstations' => 'محطات العمل',
            'production-material-lists' => 'قوائم مواد الإنتاج',
            'manufacturing-orders' => 'أوامر التصنيع',
            'production-plan' => 'خطة إنتاج',
        ] as $key => $value)
                                <li>
                                    <a
                                        href="{{ route('SequenceNumbering.current.number', ['section' => $key]) }}">{{ $value }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- المحتوى الرئيسي -->
                <div class="col-md-9">
                    <div class="content">
                        <h3 id="content-title" class="mb-4">تعديل تسلسل الترقيم - {{ $section }}</h3>
                        <form action="{{ route('SequenceNumbering.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="section" value="{{ $section }}">

                            <div class="form-group">
                                <label for="current-number">الرقم الحالي</label>
                                <input type="text" class="form-control" id="current-number" name="current_number" value="{{ $currentNumber }}">
                            </div>


                            <div class="form-group">
                                <label for="mode">النمط</label>
                                <select class="form-control" id="mode" name="mode">
                                    <option value="0">أرقام</option>
                                    <option value="1">أرقام سداسية بحروف صغيرة</option>
                                    <option value="2">أرقام سداسية بحروف كبيرة</option>
                                    <option value="3">أحرف صغيرة</option>
                                    <option value="4">أحرف كبيرة</option>
                                    <option value="5">أحرف صغيرة متبوعة بأرقام</option>
                                    <option value="6">أحرف كبيرة متبوعة بأرقام</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="current-number">عدد الارقام</label>
                                <input type="text" class="form-control" id="current-number" name="number_of_digits" value="">
                            </div>

                            <div class="form-group">
                                <label for="prefix" class="font-weight-bold">إضافة بادئة</label>
                                <input type="checkbox" id="prefix" name="prefix">
                            </div>


                            <!-- النمط -->
                            <div class="form-group">
                                <label for="mode" class="font-weight-bold">قيمة فريدة للتكرار</label>
                                <input type="checkbox" id="mode" name="mode">
                            </div>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection
