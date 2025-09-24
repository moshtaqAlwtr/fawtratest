@extends('master')

@section('title')
    تعديل امر تشغيل
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل امر تشغيل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('SupplyOrders.index') }}">أوامر التشغيل</a></li>
                            <li class="breadcrumb-item active">عرض </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')
    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('SupplyOrders.update', $supply_order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('SupplyOrders.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i> الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i> حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات اوامر التوريد</h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">
                                {{-- Basic Information Inputs --}}
                                <div class="form-group col-md-3">
                                    <label for="supply_order_name">مسمى</label>
                                    <input type="text" id="supply_order_name" name="name"
                                           class="form-control" placeholder="مسمى"
                                           value="{{ old('name', $supply_order->name) }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="supply_order_number">رقم امر</label>
                                    <input type="text" id="supply_order_number" name="supply_order_number"
                                           class="form-control" placeholder="رقم امر"
                                           value="{{ old('supply_order_number', $supply_order->order_number) }}"
                                           readonly>
                                    @error('supply_order_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="start_date">تاريخ البدء</label>
                                    <input type="date" id="start_date" name="start_date"
                                           class="form-control"
                                           value="{{ old('start_date', $supply_order->start_date ? $supply_order->start_date->format('Y-m-d') : '') }}"
                                           onchange="updateEndDate()">
                                    @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="end_date">تاريخ النهاية</label>
                                    <input type="date" id="end_date" name="end_date"
                                           class="form-control"
                                           value="{{ old('end_date', $supply_order->end_date ? $supply_order->end_date->format('Y-m-d') : '') }}">
                                    @error('end_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">الوصف</label>
                                <textarea id="description" name="description"
                                          class="form-control" rows="5"
                                          placeholder="الوصف">{{ old('description', $supply_order->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-body row">
                                {{-- Client Selection --}}
                                <div class="col-md-3">
                                    <label for="client_id">العميل</label>
                                    <select id="client_id" name="client_id" class="form-control">
                                        <option value="">اختر عميل</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $supply_order->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->trade_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('clients.create') }}" class="btn btn-success form-control">جديد</a>
                                </div>

                                {{-- Tag Selection --}}
                                <div class="form-group col-md-3">
                                    <label for="tag">الوسم</label>
                                    <select id="tag" name="tag" class="form-control">
                                        <option value="">اختر وسم</option>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag }}"
                                                {{ old('tag', $supply_order->tag) == $tag ? 'selected' : '' }}>
                                                {{ $tag }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tag')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Budget Input --}}
                                <div class="form-group col-md-2">
                                    <label for="budget">الميزانية</label>
                                    <input type="number" step="0.01" id="budget" name="budget"
                                           class="form-control" placeholder="الميزانية"
                                           value="{{ old('budget', $supply_order->budget) }}">
                                    @error('budget')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Currency Selection --}}
                                <div class="form-group col-md-2">
                                    <label for="currency">العملة</label>
                                    <select id="currency" name="currency" class="form-control">
                                        <option value="1" {{ old('currency', $supply_order->currency) == '1' ? 'selected' : '' }}>SAR</option>
                                        <option value="2" {{ old('currency', $supply_order->currency) == '2' ? 'selected' : '' }}>USD</option>
                                        <option value="3" {{ old('currency', $supply_order->currency) == '3' ? 'selected' : '' }}>EUR</option>
                                        <option value="4" {{ old('currency', $supply_order->currency) == '4' ? 'selected' : '' }}>GBP</option>
                                        <option value="5" {{ old('currency', $supply_order->currency) == '5' ? 'selected' : '' }}>CNY</option>
                                    </select>
                                    @error('currency')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Employee Selection --}}
                            <div class="form-body row">
                                <div class="col-md-3 d-flex gap-2 align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show-employee-input"
                                               style="width: 1.5em; height: 1.5em;" name="show_employee"
                                               {{ old('show_employee', $supply_order->show_employee) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show-employee-input"
                                               style="font-size: 1.2rem; margin-right: 10px;">
                                            تعيين الموظفين
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3" id="employee-input-container" style="{{ old('show_employee', $supply_order->show_employee) ? 'display: block;' : 'display: none;' }}">
                                    <label for="employee_id">اختر موظف</label>
                                    <select id="employee_id" name="employee_id" class="form-control">
                                        <option value="">اختر موظف</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id', $supply_order->employee_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Details --}}
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="product_details" class="form-label text-end d-block">بيانات المنتجات</label>
                                <textarea id="product_details" name="product_details"
                                          class="form-control" rows="4">{{ old('product_details', $supply_order->product_details) }}</textarea>
                                @error('product_details')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="shipping_address" class="form-label text-end d-block">عنوان الشحن</label>
                                <textarea id="shipping_address" name="shipping_address"
                                          class="form-control" rows="4">{{ old('shipping_address', $supply_order->shipping_address) }}</textarea>
                                @error('shipping_address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="tracking_number" class="form-label text-end d-block">رقم التتبع</label>
                                <input type="text" id="tracking_number" name="tracking_number"
                                       class="form-control" value="{{ old('tracking_number', $supply_order->tracking_number) }}">
                                @error('tracking_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="shipping_policy_file" class="form-label text-end d-block">بوليصة الشحن</label>
                                <input type="file" id="shipping_policy_file" name="shipping_policy_file"
                                       class="form-control">
                                @if($supply_order->shipping_policy_file)
                                    <a href="{{ asset('storage/' . $supply_order->shipping_policy_file) }}" target="_blank">عرض البوليصة الحالية</a>
                                @endif
                                @error('shipping_policy_file')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('sales::supplyOrders.custom_fields_modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/supply_orders.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeCheckbox = document.getElementById('show-employee-input');
            const employeeInputContainer = document.getElementById('employee-input-container');

            employeeCheckbox.addEventListener('change', function() {
                employeeInputContainer.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    document.getElementById('employee_id').value = '';
                }
            });
        });

        function updateEndDate() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + 1);

                // Format the end date to YYYY-MM-DD
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');

                endDateInput.value = `${year}-${month}-${day}`;
            }
        }

        // Run on page load to set initial end date
        document.addEventListener('DOMContentLoaded', updateEndDate);
    </script>
@endsection
