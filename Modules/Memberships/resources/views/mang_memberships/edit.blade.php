@extends('master')

@section('title')
    تعديل عضوية
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> تعديل عضوية </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('Memberships.index') }}">العضويات</a></li>
                                <li class="breadcrumb-item active">تعديل</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('Memberships.update', $membership->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="content-body">
                <div class="card" style="max-width: 90%; margin: 0 auto;">
                    <div class="card-header">
                        <h1>تعديل تفاصيل العضوية</h1>
                    </div>
                    <div class="card-body">
                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="client_id">العميل <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-control @error('client_id') is-invalid @enderror">
                                    <option value="">اختر العميل</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ $membership->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->first_name }} {{ $client->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label for="package_id">الباقة <span class="text-danger">*</span></label>
                                <select name="package_id" class="form-control @error('package_id') is-invalid @enderror">
                                    <option value="">اختر الباقة</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}" {{ $membership->package_id == $package->id ? 'selected' : '' }}>
                                            {{ $package->commission_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="join_date">تاريخ الالتحاق <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('join_date') is-invalid @enderror" 
                                       name="join_date" value="{{ $membership->join_date }}">
                                @error('join_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label for="end_date">تاريخ الانتهاء<span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" value="{{ $membership->end_date }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="description">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description">{{ $membership->description }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <a href="{{ route('Memberships.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i> حفظ التعديلات
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
