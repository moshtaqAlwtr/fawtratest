@extends('master')

@section('title')
    العروض والهدايا
@endsection

@section('content')

@include('layouts.alerts.error')
@include('layouts.alerts.success')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">العروض والهدايا</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active">العروض والهدايا</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- التبويبات -->
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" id="offersTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="gifts-tab" data-toggle="tab" href="#gifts" role="tab" aria-controls="gifts" aria-selected="true">الهدايا</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="offers-tab" data-toggle="tab" href="#offers" role="tab" aria-controls="offers" aria-selected="false">العروض</a>
            </li>
        </ul>

        <div class="tab-content pt-2" id="offersTabsContent">
            <!-- تبويب الهدايا -->
            <div class="tab-pane fade show active" id="gifts" role="tabpanel" aria-labelledby="gifts-tab">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                    <h4>الهدايا</h4>
                    <a href="{{ route('gift-offers.create') }}" class="btn btn-outline-primary">
                        <i class="fa fa-plus me-2"></i> إضافة هدية
                    </a>
                </div>

                <table class="table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th>الاسم</th>
                            <th>المنتج المستهدف</th>
                            <th>الكمية المطلوبة</th>
                            <th>الهدية</th>
                            <th>عدد الوحدات المجانية</th>
                            <th>التاريخ</th>
                            <th>خيارات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gift_offers as $offer)
                        <tr class="text-center">
                            <td>{{ $offer->name ?? '-' }}</td>
                            <td>{{ $offer->targetProduct->name ?? '-' }}</td>
                            <td>{{ $offer->min_quantity }}</td>
                            <td>{{ $offer->giftProduct->name ?? '-' }}</td>
                            <td>{{ $offer->gift_quantity }}</td>
                            <td>{{ $offer->start_date }} إلى {{ $offer->end_date }}</td>
                           <td>
    <a href="{{ route('gift-offers.edit', $offer->id) }}" class="btn btn-sm btn-primary">تعديل</a>

    @if ($offer->is_active)
        <a href="{{ route('gift-offers.status', $offer->id) }}" class="btn btn-sm btn-danger"
           onclick="return confirm('هل أنت متأكد أنك تريد إيقاف هذا العرض؟')">
            إيقاف
        </a>
    @else
        <a href="{{ route('gift-offers.status', $offer->id) }}" class="btn btn-sm btn-success"
           onclick="return confirm('هل تريد تفعيل هذا العرض؟')">
            تفعيل
        </a>
    @endif
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- تبويب العروض -->
            <div class="tab-pane fade" id="offers" role="tabpanel" aria-labelledby="offers-tab">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                    <h4>العروض</h4>
                    <a href="{{ route('Offers.create') }}" class="btn btn-outline-primary">
                        <i class="fa fa-plus me-2"></i> إضافة عرض
                    </a>
                </div>

                <table class="table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th>الرقم التعريفي</th>
                            <th>الاسم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offers as $offer)
                        <tr class="text-center">
                            <td>{{ $offer->id }}</td>
                            <td>{{ $offer->name }}</td>
                            <td>
                                @if ($offer->status == 1)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" data-toggle="dropdown"></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('Offers.show', $offer->id) }}">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                            </a>
                                            <a class="dropdown-item" href="{{ route('Offers.edit', $offer->id) }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                            <a class="dropdown-item text-danger" data-toggle="modal" data-target="#modal_DELETE{{ $offer->id }}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal الحذف -->
                                <div class="modal fade" id="modal_DELETE{{ $offer->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">حذف {{ $offer->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                هل أنت متأكد من الحذف؟
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                                                <a href="{{ route('Offers.destroy', $offer->id) }}" class="btn btn-danger">تأكيد</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- نهاية المودال -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
