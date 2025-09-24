@extends('master')

@section('title')
    اعدادت الحساب
@stop

@section('content')
    <x-layout.breadcrumb title="اعدادت الحساب" :items="[['title' => 'عرض']]" />
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')
        @if ($errors->has('email'))
        <div class="text-danger">{{ $errors->first('email') }}</div>
         @endif
    <div class="content-body">
        <form id="clientForm" action="{{ route('SittingAccount.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        

            <x-layout.card>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                       
                        <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#emailleModal" data-whatever="@mdo">   <i class="fa fa-envelope"></i> تغيير البريد الإلكتروني</button>
                        
                        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#passwordleModal" data-whatever="@mdo">    <i class="fa fa-key"></i> تغيير كلمة المرور</button>
                        <button type="submit" class="btn btn-outline-success">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </x-layout.card>

            <div class="row">
                <div class="col-md-6 col-12">
                    <x-layout.card title="بيانات العميل">
                        <div class="row">
                            <x-form.input label="الاسم التجاري" value="{!! old('trade_name', $client->trade_name ?? '') !!}" name="trade_name" icon="briefcase" required="true"
                                col="12" />

                            <x-form.input label="الاسم الأول" value="{!! old('first_name', $client->first_name ?? '') !!}" name="first_name" icon="user" col="6" />
                            <x-form.input label="الاسم الأخير" value="{!! old('last_name', $client->last_name ?? '') !!}"  name="last_name" icon="user" col="6" />

                            <x-form.input label="الهاتف" value="{!! old('phone', $client->phone ?? '') !!}" name="phone" icon="phone" col="6" />
                            <x-form.input label="جوال" name="mobile" icon="smartphone" col="6" />

                            <x-form.input label="عنوان الشارع 1" value="{!! old('street1', $client->street1 ?? '') !!}" name="street1" icon="map-pin" col="6" />
                            <x-form.input label="عنوان الشارع 2" value="{!! old('street2', $client->street2 ?? '') !!}" name="street2" icon="map-pin" col="6" />

                            <x-form.input label="المدينة" value="{!! old('city', $client->city ?? '') !!}" name="city" icon="map" col="4" />
                            <x-form.input label="المنطقة" value="{!! old('region', $client->region ?? '') !!}" name="region" icon="map" col="4" />
                            <x-form.input label="الرمز البريدي" value="{!! old('postal_code', $client->postal_code ?? '') !!}" name="postal_code" icon="mail" col="4" />

                            <x-form.select label="البلد" name="country" icon="globe" col="12">
                                <option value="SA" selected>المملكة العربية السعودية (SA)</option>
                            </x-form.select>

                            <x-form.input label="الرقم الضريبي (اختياري)" value="{!! old('tax_number', $client->tax_number ?? '') !!}" name="tax_number" icon="file-text"
                                col="6" />
                            <x-form.input label="سجل تجاري (اختياري)" value="{!! old('commercial_registration', $client->commercial_registration ?? '') !!}" name="commercial_registration" icon="file"
                                col="6" />
                        </div>
                    </x-layout.card>
                </div>

                <div class="col-md-6">
                    <x-layout.card title="إعدادات الحساب">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a href="{{ route('CurrencyRates.index') }}" class="text-primary">

                                        <i class="feather icon-external-link"></i> أسعار العملات

                                    </a>
                                    <x-form.select label="العملات" name="currency"  col="12">
                                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                                          <option value="{{ $code }}" 
                                        {{ (isset($client) && $client->currency == $code) ? 'selected' : '' }}>
                                        {{ $code }} {{ $name }}
                                       </option>
                                      @endforeach

                                    </x-form.select>
                                   
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <x-form.timezone-select />
                            </div>

                            <x-form.select label="تنسيقات العملات السالبة" value="{!! old('negative_currency_format', $client->negative_currency_format ?? '') !!}" name="negative_currency_formats"
                                icon="minus-circle" col="6">
                                <option value="standard" selected>-19.5</option>
                                <option value="standard" selected>(19.5)</option>
                            </x-form.select>

                            <x-form.select label="صيغة الوقت" name="time_formula" icon="calendar" col="6">
                                @php
                                    // تعيين قيمة افتراضية إذا لم تكن موجودة
                                    $defaultTimeFormula = 'd/m/Y';
                                @endphp
                            
                                @foreach (['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'M d, Y', 'F d, Y'] as $format)
                                    <option value="{{ $format }}" 
                                        {{ old('time_formula', $account_setting->time_formula ?? $defaultTimeFormula) == $format ? 'selected' : '' }}>
                                        {{ $format }} ({{ now()->format($format) }})
                                    </option>
                                @endforeach
                            </x-form.select>
                            
                            

                            <x-form.select label="اللغة" name="language" icon="globe" col="12">
                                <option value="ar" selected>العربية (AR)</option>
                            </x-form.select>

                            <x-form.select label="أنت تبيع" name="business_type" icon="shopping-bag" col="12">
                                <option value="products">المنتجات</option>
                                <option value="services">الخدمات</option>
                                <option value="both">الخدمات والمنتجات</option>
                               
                            </x-form.select>

                        <x-form.select label="طريقة الطباعة" name="printing_method" icon="printer" col="12">
                                <option value="browser" selected>متصفح</option>
                                <option value="pdf">PDF</option>
                            </x-form.select>
                        </div>
                    </x-layout.card>

                    <x-layout.card>
                        <x-form.file label="المرفقات" name="attachments" />
                    </x-layout.card>
                </div>
            </div>
        
    </div>

    <!-- Modal أسعار العملات -->
    <div class="modal fade" id="currencyRatesModal" tabindex="-1" role="dialog"
        aria-labelledby="currencyRatesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="currencyRatesModalLabel">أسعار العملات</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>العملة</th>
                                    <th>الرمز</th>
                                    <th>السعر مقابل الريال السعودي</th>
                                </tr>
                            </thead>
                            <tbody id="currencyRatesTableBody">
                                <!-- سيتم ملء هذا الجزء عن طريق JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>

 
 <!-- تغيير البريد الالركتروني-->
<div class="modal fade" id="emailleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تغيير البريد الالكتروني </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="clientForm" action="{{ route('SittingAccount.Change_email') }}" method="POST" enctype="multipart/form-data">
            @csrf
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">البريد الالكتروني</label>
            <input type="text" value="{{$user->email ?? ""}}" class="form-control" name="email" id="recipient-name">
                 @if ($errors->has('email'))
               <div class="text-danger">{{ $errors->first('email') }}</div>
                @endif
          </div>
         
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
        <button type="submit" class="btn btn-primary">حفظ</button>

    </form>
      </div>
    </div>
  </div>
</div>

 <!-- تغيير  كلمة المرور-->
 <div class="modal fade" id="passwordleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">تغيير كلمة المرور</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('SittingAccount.change_password') }}" method="POST">
                    @csrf
                    <!-- حقل كلمة المرور القديمة -->
                    <div class="form-group">
                        <label for="current_password" class="col-form-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control" name="current_password" id="current_password" required>
                        @error('current_password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- حقل كلمة المرور الجديدة -->
                    <div class="form-group">
                        <label for="password" class="col-form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- حقل تأكيد كلمة المرور -->
                    <div class="form-group">
                        <label for="password_confirmation" class="col-form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
            </form>
        </div>
    </div>
</div>



@endsection

@section('scripts')
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        document.getElementById('clientForm').addEventListener('submit', function(e) {
            // e.preventDefault();
            console.log('تم تقديم النموذج');
            const formData = new FormData(this);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
        });
    </script>
    <script>
    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var recipient = button.data('whatever') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-title').text('New message to ' + recipient)
        modal.find('.modal-body input').val(recipient)
      })
      </script>
@endsection
