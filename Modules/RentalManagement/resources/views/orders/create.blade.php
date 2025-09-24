@extends('master')

@section('title')
    أضافة أمر حجز
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أضافة أمر حجز</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">أمر الحجز
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="{{ route('rental_management.orders.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="form-section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form" action="" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- القسم الأول -->
                                    <div class="form-section" id="section-1">
                                        <h4 class="mb-3">تفاصيل الوحدة</h4>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="unit_type">نوع الوحدة</label>
                                                    <select name="unit_type" id="unit_type" class="form-control">
                                                        <option value="">اختر نوع الوحدة</option>
                                                        @foreach($unitTypes as $unitType)
                                                            <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="start_date">تاريخ البدء</label>
                                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="end_date">تاريخ الانتهاء</label>
                                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- القسم الثاني -->
                                    <div class="form-section d-none" id="section-2">
                                        <h4 class="mb-3">معلومات أمر الحجز</h4>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="client">العميل <span style="color: red">*</span></label>
                                                    <select id="client" name="client" class="form-control">
                                                        <option value="">اختر العميل</option>
                                                        <!-- خيارات العملاء -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="tags">وسوم</label>
                                                    <input type="text" id="tags" name="tags" class="form-control" placeholder="أدخل الوسوم...">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <p>السعر الكلي <hr id="total-price"> ريال</p>
                                                <p>تاريخ ووقت البدء <hr> 21/01/2025 - 12:00</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p>السعر دون الضريبة <hr id="price-without-tax"> ريال</p>
                                                <p>تاريخ ووقت الانتهاء <hr> 22/01/2025 - 12:00</p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">الملاحظات</label>
                                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="أضف ملاحظات هنا..."></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="attachments">المرفقات</label>
                                            <input type="file" name="attachments" id="attachments" class="d-none">
                                            <div class="upload-area border rounded p-3 text-center position-relative"
                                                onclick="document.getElementById('attachments').click()">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <i class="fas fa-cloud-upload-alt text-primary"></i>
                                                    <span class="text-primary">اضغط هنا</span>
                                                    <span>أو</span>
                                                    <span class="text-primary">اختر من جهازك</span>
                                                </div>
                                                <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                                    <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-outline-secondary prev-btn d-none">السابق</button>
                                        <button type="button" class="btn btn-outline-primary next-btn">التالي</button>
                                        <button type="submit" class="btn btn-primary submit-btn d-none">حفظ</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentSection = 0;
        const sections = document.querySelectorAll('.form-section');
        const nextBtn = document.querySelector('.next-btn');
        const prevBtn = document.querySelector('.prev-btn');
        const submitBtn = document.querySelector('.submit-btn');

        function showSection(index) {
            sections.forEach((section, i) => {
                section.classList.toggle('d-none', i !== index);
            });
            prevBtn.classList.toggle('d-none', index === 0);
            nextBtn.classList.toggle('d-none', index === sections.length - 1);
            submitBtn.classList.toggle('d-none', index !== sections.length - 1);
        }

        nextBtn.addEventListener('click', () => {
            if (currentSection < sections.length - 1) {
                currentSection++;
                showSection(currentSection);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
            }
        });

        showSection(currentSection);

        // تحديث الأسعار عند اختيار نوع الوحدة
        document.getElementById('unit_type').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const unitTypeId = selectedOption.value;

            if (unitTypeId) {
                fetch(`/api/unit-types/${unitTypeId}/pricing`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('total-price').textContent = `${data.price_with_tax} ريال`;
                        document.getElementById('price-without-tax').textContent = `${data.price_without_tax} ريال`;
                    })
                    .catch(error => console.error('Error fetching pricing data:', error));
            } else {
                document.getElementById('total-price').textContent = '';
                document.getElementById('price-without-tax').textContent = '';
            }
        });
    });
</script>
@endsection
