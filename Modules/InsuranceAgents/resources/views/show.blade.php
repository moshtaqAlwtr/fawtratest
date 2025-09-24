@extends('master')

@section('title')
    عرض وكلاء التامين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض وكيل تأمين</h2>
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
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="row" id="client-additional-data">
                    <div class="col-md-6">
                        <div class="client-profile">
                            <h3 class="media-heading">{{ $insuranceAgent->name }}</h3>
                            <h4 class="text-muted">#{{ $insuranceAgent->id }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="text-end">
                                        <a href="mailto:{{ $insuranceAgent->email }}"
                                            class="btn btn-outline-primary btn-block">
                                            <i class="fa fa-envelope"></i> {{ $insuranceAgent->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        <a href="tel:{{ $insuranceAgent->phone }}"
                                            class="btn btn-outline-success btn-block">
                                            <i class="fa fa-phone"></i> {{ $insuranceAgent->phone }}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <div class="vr"></div>
            <a href="{{ route('Insurance_Agents.edit', $insuranceAgent->id) }}"
                class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>

            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE{{ $insuranceAgent->id }}">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
            <a href="{{ route('InsuranceAgentsClass.create', ['insurance_agent_id' => $insuranceAgent->id]) }}"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                اضافة فئة <i class="fa fa-plus ms-1"></i>
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>تفاصيل الوكيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <span> الاسم :</span>
                                    <a href="#">{{ $insuranceAgent->name }}</a>
                                    <a href="#">{{ $insuranceAgent->id }}</a>
                                </div>

                                <div class="col-6 text-center">
                                    <h6>{{ $insuranceAgent->location }}</h6>
                                    <small>الموقع </small>
                                </div>

                            </div>

                            <div class="row mt-3">
                                <div class="col-6 text-end">
                                    <h6>{{ $insuranceAgent->phone }}</h6>
                                    <small>هاتف</small>
                                </div>

                                <div class="col-6 text-center">
                                    <h6><a href="#">{{ $insuranceAgent->email }}</a></h6>
                                    <small>البريد الإلكتروني</small>
                                </div>

                            </div>

                            <hr>
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card p-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>التصنيفات</th>
                    <th>الدفع المشترك%</th>
                    <th>الخصم%</th>
<th>الحد الأقصى للدفع المشترك</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $insuranceAgentCategory)
                    <tr>
                        <td>{{ $insuranceAgentCategory->name?? 'غير متوفر' }}</td>
                        <td>{{ $insuranceAgentCategory->category->name ?? 'غير متوفر' }}</td>
                        <td>{{ $insuranceAgentCategory->client_copayment?? 'غير متوفر' }}%</td>
                        <td>{{ $insuranceAgentCategory->discount?? 'غير متوفر' }}%</td>
                        <td>{{ $insuranceAgentCategory->max_copayment?? 'غير متوفر' }}/
                            @if($insuranceAgentCategory->type=1)
                            شركة تامين
                            @else
                            عميل
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                        id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">


                                        <li>
                                            <a class="dropdown-item" href="{{ route('InsuranceAgentsClass.edit', $insuranceAgentCategory->id) }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $insuranceAgentCategory->id }}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </li>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <div class="modal fade" id="modal_DELETE{{ $insuranceAgentCategory->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">حذف وكيل تأمين</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    هل أنت متأكد من حذف الوكيل "{{ $insuranceAgentCategory->name }}"��
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    <form action="{{ route('InsuranceAgentsClass.destroy', $insuranceAgentCategory->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">تأكيد</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($categories->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">لا توجد فئات متاحة</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Modal DELETE -->
    <div class="modal fade" id="modal_DELETE{{ $insuranceAgent->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">حذف وكيل تأمين</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من حذف الوكيل "{{ $insuranceAgent->name }}"��
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <form action="{{ route('Insurance_Agents.destroy', $insuranceAgent->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">تأكيد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
