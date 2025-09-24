<!-- resources/views/sales/gift_offers/index.blade.php -->
@extends('master')
@section('content')

   
 
 <div class="content-body">
     <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> الهدايا </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>

                    </div>

                </div>
            </div>
        </div>
    </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('Offers.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-2"></i> اضافة هدية 
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card">
        <div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr class="text-center">
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
            @foreach ($offers as $offer)
                <tr class="text-center">
                    <td>{{ $offer->name ?? '-' }}</td>
                    <td>{{ $offer->targetProduct->name ?? '-' }}</td>
                    <td>{{ $offer->min_quantity }}</td>
                    <td>{{ $offer->giftProduct->name ?? '-' }}</td>
                    <td>{{ $offer->gift_quantity }}</td>
                    <td>{{ $offer->start_date }} إلى {{ $offer->end_date }}</td>
                    <td>
                        <a href="{{ route('gift-offers.edit', $offer->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                        <form action="{{ route('gift-offers.destroy', $offer->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
     </div>

@endsection
