@extends('master')

@section('title')
   ضبط التطبيقات
@stop

@section('content')

<style>
    /* تحسين تصميم الكروت */
    .card {
        border: none !important;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    /* جعل الكروت بنفس الارتفاع */
    .card-body {
        min-height: 250px; /* يمكنك ضبط القيمة حسب الحاجة */
    }

    /* تحسين تصميم السويتش */
    .form-check-input {
        width: 50px !important; /* زيادة عرض السويتش */
        height: 25px !important; /* زيادة ارتفاع السويتش */
    }

    /* تحسين التباعد */
    .switch-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }

</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <form id="clientForm" action="{{ route('Application.update') }}" method="POST">
        @csrf
        <div class="card p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>

        <!-- الصف الأول -->
        <div class="row">
            <div class="col-md-6">                      


                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">إدارة المبيعات</div>
                    <div class="card-body text-dark">
                        @php
                            $sales_options = [
                                'sales' => 'المبيعات',
                                'pos' => 'نقاط البيع',
                                'target_sales_commissions' => 'المبيعات المستهدفة و العمولات',
                                'installments_management' => 'إدارة الأقساط',
                                'offers' => 'العروض',
                                'insurance' => 'التأمينات',
                                'customer_loyalty_points' => 'نقاط ولاء العملاء'
                            ];
                        @endphp

                        @foreach ($sales_options as $key => $label)
                            <div class="switch-container">
                                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="{{ $key }}" value="inactive">
                                    <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                        value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-light">إعدادات إدارة المبيعات</div>
                </div>
            </div>
    <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">إدارة المخزون والمشتريات</div>
                    <div class="card-body text-dark">
                     
                        @php
                            $sales_options = [
                                'inventory_management' => 'ادارة المخزون',
                                'manufacturing' => 'التصنيع',
                                'purchase_cycle' => 'دورة المشتريات',
                             
                            ];
                        @endphp

                        @foreach ($sales_options as $key => $label)
                            <div class="switch-container">
                                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="{{ $key }}" value="inactive">
                                    <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                        value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-light">إعدادات إدارة المخزون والمشتريات</div>
                </div>
         
        </div>

     
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">إدارة  الحسابات</div>
                <div class="card-body text-dark">
                 
                    @php
                        $sales_options = [
                            'finance' => ' المالية',
                            'general_accounts_journal_entries' => 'الحسابات العامة والقيود اليومية',
                            'cheque_cycle' => 'دورة الشيكات',
                         
                        ];
                    @endphp

                    @foreach ($sales_options as $key => $label)
                        <div class="switch-container">
                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $key }}" value="inactive">
                                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                    value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">إعدادات إدارة  الحسابات</div>
            </div>
            
        </div>
       
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">إدارة  العمليات</div>
                <div class="card-body text-dark">
                 
                    @php
                        $sales_options = [
                            'work_orders' => ' أوامر الشغل',
                            'rental_management' => 'ادارات الايجارات والوحدات',
                            'booking_management' => 'ادارة الحجوزات',
                            'time_tracking' => 'تتبع الوقت',
                            'workflow' => 'دورة العمل',
                         
                        ];
                    @endphp

                    @foreach ($sales_options as $key => $label)
                        <div class="switch-container">
                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $key }}" value="inactive">
                                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                    value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">إعدادات إدارة  العمليات</div>
            </div>
            
        </div>
        
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">إدارة  علاقة العملاء</div>
                <div class="card-body text-dark">
                 
                    @php
                        $sales_options = [
                            'customers' => ' العملاء ',
                            'customer_followup' => 'متابعة العميل'  ,
                            'points_balances' => 'النقاط والارصدة',
                            'membership' => 'العضوية',
                            'customer_attendance' => 'حضور العملاء',
                         
                        ];
                    @endphp

                    @foreach ($sales_options as $key => $label)
                        <div class="switch-container">
                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $key }}" value="inactive">
                                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                    value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">إعدادات إدارة  علاقة العملاء</div>
            </div>
            
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">إدارة الموارد البشرية</div>
                <div class="card-body text-dark">
                 
                    @php
                        $sales_options = [
                            'employees' => ' الموظفين ',
                            'organizational_structure' => 'الهيكل التنظيمي'  ,
                            'employee_attendance' => 'حضور الموظفين',
                            'salaries' => 'المرتبات',
                            'orders' => 'الطلبات',
                         
                        ];
                    @endphp

                    @foreach ($sales_options as $key => $label)
                        <div class="switch-container">
                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $key }}" value="inactive">
                                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                    value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">إعدادات إدارة  علاقة العملاء</div>
            </div>
            
        </div>
        

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">العامة</div>
                <div class="card-body text-dark">
                 
                    @php
                        $sales_options = [
                            'sms' => ' SMS ',
                            'ecommerce' => ' المتجر الالكتروني'  ,
                            'branches' => 'الفروع',
                          
                         
                        ];
                    @endphp

                    @foreach ($sales_options as $key => $label)
                        <div class="switch-container">
                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $key }}" value="inactive">
                                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}"
                                    value="active" {{ old($key, $settings[$key] ?? 'inactive') === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">إعدادات إدارة   العامة</div>
            </div>
            
        </div>
        <script>
            document.querySelectorAll('.form-check-input').forEach(switchInput => {
                switchInput.addEventListener('change', function() {
                    this.previousElementSibling.value = this.checked ? 'active' : 'inactive';
                });
            });
        </script>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
