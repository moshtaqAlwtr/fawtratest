@extends('master')

@section('title')
    تعديل العملات
@stop
@section('content')
    <x-layout.breadcrumb title="تعديل  العملات " :items="[['title' => 'عرض']]" />
    <div class="content-body">
        <x-layout.card>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="" class="btn btn-outline-primary">
                        <i class="fa fa-plus"></i> حفظ
                    </a>
                </div>
            </div>
        </x-layout.card>

        <x-layout.card title="بحث">
            <form class="form" method="GET" action="">
                <div class="form-body row">
                    <x-form.select label="من العملة" name="from_currency" id="from_currency" col="4">
                        <option value="">العملة</option>
                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                            <option value="{{ $code }}">{{ $code }} {{ $name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select label="الى العملة" name="to_currency" id="to_currency" col="4">
                        <option value="">العملة</option>
                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                            <option value="{{ $code }}">{{ $code }} {{ $name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input label="من (تاريخ)" name="created_from" type="date" col="4" />
                </div>
                <div class="form-body row">
                    <x-form.input label="سعر التحويل" name="conversion_rate" id="conversion_rate" type="text" readonly col="4" />
                    <div id="status-message" style="color: red; margin-top: 10px;"></div>
                </div>
            </form>
        </x-layout.card>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fromCurrency = document.getElementById('from_currency');
        const toCurrency = document.getElementById('to_currency');
        const conversionRate = document.getElementById('conversion_rate');
        const statusMessage = document.getElementById('status-message');

        async function fetchExchangeRate() {
            statusMessage.textContent = ''; // تفريغ الرسائل
            if (fromCurrency.value && toCurrency.value) {
                try {
                    const response = await fetch(`https://v6.exchangerate-api.com/v6/f83d4746a8551bb38ccd8b2e/pair/${fromCurrency.value}/${toCurrency.value}`);
                    const data = await response.json();

                    if (data.conversion_rate) {
                        const rate = data.conversion_rate.toFixed(4);
                        conversionRate.value = `1 ${fromCurrency.value} = ${rate} ${toCurrency.value}`;
                    } else {
                        conversionRate.value = 'سعر التحويل غير متوفر';
                        statusMessage.textContent = 'تعذر الحصول على سعر التحويل.';
                    }
                } catch (error) {
                    console.error('Error fetching exchange rate:', error);
                    statusMessage.textContent = 'حدث خطأ أثناء الاتصال بالخدمة.';
                }
            } else {
                conversionRate.value = '';
                statusMessage.textContent = 'يرجى اختيار العملات.';
            }
        }

        fromCurrency.addEventListener('change', fetchExchangeRate);
        toCurrency.addEventListener('change', fetchExchangeRate);
    });
</script>
@endsection
