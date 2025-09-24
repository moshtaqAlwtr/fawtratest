@extends('master')

@section('title')
    فئات وكلاء التأمين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">فئات وكيل تأمين</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="card">
        <div class="card-content">
            <form action="{{ route('InsuranceAgentsClass.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <input type="hidden" name="insurance_agent_id" value="{{ $insurance_agent_id }}"> <!-- حقل مخفي للمعرف -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                            <div></div>
                            <div>
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa fa-plus me-2"></i>الغاء
                                </button>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group col-md-12">
                        <label for="minimum_import_points" class=""> الاسم </label>
                        <input type="text" id="minimum_import_points" class="form-control" placeholder="" name="name">
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>التصنيفات</th>
                                <th>الخصم %</th>
                                <th>Company copayment %</th>
                                <th>Client copayment %</th>
                                <th>الحد الأقصى للدفع المشترك $</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="statusTable">
                            <tr data-status-id="1">
                                <td>
                                    <div class="form-group">
                                        <select class="form-control" name="category_id">
                                            <option value="">اختر التصنيف</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="" name="discount">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control company-copayment" placeholder=""
                                            name="company_copayment" value="0">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control client-copayment" placeholder=""
                                            name="client_copayment" value="0">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <input name="" type="number" class="form-control numeric" step="any"
                                                    id="InsuranceAgentPricingGroup0MaxCopayment" />
                                            </div>
                                            <div class="col-6">
                                                <select name="type" class="form-control">
                                                    <option value="1">العميل</option>
                                                    <option value="2">شركه تأمين</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="delete-product-cell notEditable">
                                    <a href="#" class="removeItem delete-ico btn btn-circle btn-danger" tabindex="-1">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="btn btn-success mt-2" id="addNewStatus" type="button">
                        <i class="feather icon-plus"></i> إضافة سطر
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('addNewStatus').addEventListener('click', function() {
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <div class="form-group">
                    <select class="form-control" name="category_id[]">
                        <option value="">اختر التصنيف</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="" name="discount[]">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control company-copayment" placeholder="" name="company_copayment[]" value="0">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control client-copayment" placeholder="" name="client_copayment[]" value="0">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control numeric" step="any" name="max_copayment[]" />
                        </div>
                        <div class="col-6">
                            <select name="type[]" class="form-control">
                                <option value="1">العميل</option>
                                <option value="2">شركه تأمين</option>
                            </select>
                        </div>
                    </div>
                </div>
            </td>
            <td class="delete-product-cell notEditable">
                <a href="#" class="removeItem delete-ico btn btn-circle btn-danger" tabindex="-1">
                    <i class="fa fa-remove"></i>
                </a>
            </td>
        `;

        document.getElementById('statusTable').appendChild(newRow);
        addCopaymentEvents(newRow);
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;
        const discountInputs = document.querySelectorAll('input[name="discount[]"]');
        const companyCopaymentInputs = document.querySelectorAll('input[name="company_copayment[]"]');
        const clientCopaymentInputs = document.querySelectorAll('input[name="client_copayment[]"]');
        const maxCopaymentInputs = document.querySelectorAll('input[name="max_copayment[]"]');
        const typeInputs = document.querySelectorAll('select[name="type[]"]');

        discountInputs.forEach(input => {
            if (isNaN(input.value) || input.value.trim() === '') {
                valid = false;
                alert('The discount must be a number.');
            }
        });

        companyCopaymentInputs.forEach(input => {
            if (isNaN(input.value) || input.value.trim() === '') {
                valid = false;
                alert('The company copayment must be a number.');
            }
        });

        clientCopaymentInputs.forEach(input => {
            if (isNaN(input.value) || input.value.trim() === '') {
                valid = false;
                alert('The client copayment must be a number.');
            }
        });

        maxCopaymentInputs.forEach(input => {
            if (isNaN(input.value) || input.value.trim() === '') {
                valid = false;
                alert('The max copayment must be a number.');
            }
        });

        typeInputs.forEach(select => {
            if (!['1', '2'].includes(select.value)) {
                valid = false;
                alert('The type must be an integer (1 or 2).');
            }
        });

        if (!valid) {
            e.preventDefault(); // Prevent form submission
        }
    });
</script>
@endsection
