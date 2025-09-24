@extends('master')

@section('title')
    إعدادات الـ SMTP
@stop

@section('content')
    <x-layout.breadcrumb title="إعدادات SMTP" />
    <div class="content-body">
        <x-layout.card>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="" class="btn btn-outline-black ">
                        <i class="fa fa-redo"></i> اعادة تعيينٍٍ SMPT
                    </a>
                    <a href="" class="btn btn-outline-primary">
                        <i class="fa fa-plus"></i> حفظ
                    </a>
                </div>
            </div>
        </x-layout.card>

        <x-layout.card title="إعدادات SMTP">
            <form class="form" method="POST" action="">
                <div class="form-body">
                    <div class="d-flex align-items-center mb-4">
                        <input type="checkbox" name="use_smtp" id="use_smtp" class="form-check-input me-2"
                            style="width: 20px; height: 20px;" onclick="toggleSmtpSettings()">
                        <label for="use_smtp" class="form-label mb-0"
                            style="font-size: 16px; font-weight: 500; margin-right: 10px;">أرسل البريد بإستعمال حساب
                            SMTP</label>
                    </div>

                    <div id="smtp-settings" style="display: none;">
                        <div class="row">
                            <x-form.form-input label="اسم المرسل" name="sender_name" type="text" icon="user"
                                col="4" />

                            <x-form.form-input label="البريد الإلكتروني" name="email" type="email" icon="mail"
                                col="4" />

                            <x-form.form-input label="كلمة المرور" name="password" type="password" icon="lock"
                                col="4" />
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <a href="#" class="text-primary" onclick="toggleAdvancedOptions(event)" style="text-decoration: none;">
                                    <h6 class="mb-0">خيارات متقدمة</h6>
                                </a>
                            </div>
                        </div>

                        {{-- <div id="advanced-options" style="display: none;">
                            <div class="row">
                                <x-form.form-input label="اسم مستخدم SMTP" name="smtp_username" type="text" icon="user"
                                    col="4" />

                                <x-form.form-input label="خادم الاستضافة SMTP" name="smtp_host" type="text" icon="server"
                                    col="4" />

                                <x-form.form-input label="منفذ SMTP" name="smtp_port" type="text" icon="hash"
                                    value="25" col="4" />
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <input type="checkbox" name="ssl_required" id="ssl_required" class="form-check-input me-2"
                                    style="width: 20px; height: 20px;">
                                <label for="ssl_required" class="form-label mb-0"
                                    style="font-size: 16px; font-weight: 500; margin-right: 10px;">
                                    يتطلب هذا الاتصال SSL</label>
                            </div>
                        </div> --}}

                        <div class="row mt-3">
                            <div class="col-12">
                                {{-- <button type="button" class="btn btn-primary me-2">
                                    <i class="fas fa-envelope"></i> Outlook
                                </button> --}}
                                <button type="button" class="btn btn-danger">
                                    <i class="fab fa-google"></i> اضافة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </x-layout.card>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleSmtpSettings() {
            var checkbox = document.getElementById('use_smtp');
            var settings = document.getElementById('smtp-settings');

            if (checkbox.checked) {
                settings.style.display = 'block';
            } else {
                settings.style.display = 'none';
            }
        }

        function toggleAdvancedOptions(event) {
            event.preventDefault();
            var advancedOptions = document.getElementById('advanced-options');
            if (advancedOptions.style.display === 'none') {
                advancedOptions.style.display = 'block';
            } else {
                advancedOptions.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var checkbox = document.getElementById('use_smtp');
            var settings = document.getElementById('smtp-settings');

            if (checkbox.checked) {
                settings.style.display = 'block';
            } else {
                settings.style.display = 'none';
            }
        });
    </script>
@endsection
