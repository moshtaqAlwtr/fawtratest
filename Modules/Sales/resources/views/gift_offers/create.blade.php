@extends('master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($giftOffer) ? 'تعديل عرض هدية' : 'إضافة عرض هدية' }}</h4>
    </div>
    <div class="card-body">
        
            
       <form action="{{ isset($giftOffer) ? route('gift_offers.update', $giftOffer) : route('gift_offers.store') }}" method="POST">




            @csrf
            @if(isset($giftOffer))
                @method('PUT')
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name">اسم العرض</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $giftOffer->name ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label for="target_product_id">المنتج المستهدف</label>
                    <select name="target_product_id" class="form-control">
                        <option value="">-- اختر --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ isset($giftOffer) && $giftOffer->target_product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="min_quantity">الكمية المطلوبة</label>
                    <input type="number" name="min_quantity" class="form-control" value="{{ old('min_quantity', $giftOffer->min_quantity ?? 1) }}">
                </div>

                <div class="col-md-4">
                    <label for="gift_product_id">المنتج الهدية</label>
                    <select name="gift_product_id" class="form-control">
                        <option value="">-- اختر --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ isset($giftOffer) && $giftOffer->gift_product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="gift_quantity">عدد الوحدات المجانية</label>
                    <input type="number" name="gift_quantity" class="form-control" value="{{ old('gift_quantity', $giftOffer->gift_quantity ?? 1) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date">تاريخ البداية</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $giftOffer->start_date ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label for="end_date">تاريخ النهاية</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $giftOffer->end_date ?? '') }}">
                </div>
            </div>

            <!-- هل العرض لجميع العملاء؟ -->
<div class="mb-3">
    <label>هل العرض لجميع العملاء؟</label><br>
    <input type="radio" name="is_for_all_clients" value="1"
        {{ (old('is_for_all_clients', $giftOffer->is_for_all_clients ?? 1) == 1) ? 'checked' : '' }}> نعم

    <input type="radio" name="is_for_all_clients" value="0"
        {{ (old('is_for_all_clients', $giftOffer->is_for_all_clients ?? 1) == 0) ? 'checked' : '' }}> لا
</div>

<!-- اختيار العملاء (يُخفى عند اختيار "لكل العملاء") -->
<div class="mb-3" id="clients-select-section"
    style="{{ (old('is_for_all_clients', $giftOffer->is_for_all_clients ?? 1) == 1) ? 'display:none;' : '' }}">
    <label for="clients">اختر العملاء المستهدفين</label>
    <select name="clients[]" class="form-control select2" multiple>
        @foreach($clients as $client)
            <option value="{{ $client->id }}"
                {{ isset($giftOffer) && isset($giftOffer->clients) && $giftOffer->clients->contains($client->id) ? 'selected' : '' }}>
                {{ $client->trade_name ?? $client->first_name . ' ' . $client->last_name }}
            </option>
        @endforeach
    </select>
</div>
<!-- هل العرض لجميع الموظفين؟ -->
<div class="mb-3">
    <label>هل العرض لجميع الموظفين؟</label><br>
    <input type="radio" name="is_for_all_employees" value="1"
        {{ (old('is_for_all_employees', $giftOffer->is_for_all_employees ?? 1) == 1) ? 'checked' : '' }}> نعم

    <input type="radio" name="is_for_all_employees" value="0"
        {{ (old('is_for_all_employees', $giftOffer->is_for_all_employees ?? 1) == 0) ? 'checked' : '' }}> لا
</div>

<!-- اختيار الموظفين (دائمًا ظاهر) -->
<!-- اختيار الموظفين (يُخفى عند اختيار "لكل الموظفين") -->
<div class="mb-3" id="employees-select-section"
     style="{{ (old('is_for_all_employees', $giftOffer->is_for_all_employees ?? 1) == 1) ? 'display:none;' : '' }}">
    <label for="employees">اختر الموظفين المستهدفين</label>
    <select name="employees[]" class="form-control select2-employees" multiple>
        @foreach($users as $user)
            <option value="{{ $user->id }}"
                {{ isset($giftOffer) && isset($giftOffer->users) && $giftOffer->users->contains($user->id) ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
</div>

<!-- اختيار العملاء المستثنون (يمكن اختياره دائما) -->
<div class="mb-3">
    <label for="excluded_clients">اختر العملاء المستثنون (لا يطبق عليهم العرض)</label>
    <select name="excluded_clients[]" class="form-control select2-excluded" multiple>
        @foreach($clients as $client)
            <option value="{{ $client->id }}"
                {{ isset($giftOffer) && isset($giftOffer->excludedClients) && $giftOffer->excludedClients->contains($client->id) ? 'selected' : '' }}>
                {{ $client->trade_name ?? $client->first_name . ' ' . $client->last_name }}
            </option>
        @endforeach
    </select>
</div>



            <div class="text-end">
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            dir: 'rtl',
            placeholder: 'اختر العملاء'
        });

        $('.select2-employees').select2({
            width: '100%',
            dir: 'rtl',
            placeholder: 'اختر الموظفين'
        });

$('.select2-excluded').select2({
    width: '100%',
    dir: 'rtl',
    placeholder: 'اختر العملاء المستثنون'
});
        // العملاء
        $('input[name="is_for_all_clients"]').on('change', function () {
            if ($(this).val() == '0') {
                $('#clients-select-section').show();
            } else {
                $('#clients-select-section').hide();
                $('.select2').val(null).trigger('change');
            }
        });

        // الموظفين
        $('input[name="is_for_all_employees"]').on('change', function () {
            if ($(this).val() == '0') {
                $('#employees-select-section').show();
            } else {
                $('#employees-select-section').hide();
                $('.select2-employees').val(null).trigger('change');
            }
        });
    });
    
    

</script>

@endsection
