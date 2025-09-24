@extends('master')

@section('title')
    طلب أجازة
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">طلب أجازة</h2>
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
    <div class="content-body">
        <div class="card ">
            <div class="card-body">
                <div class="row justify-content-between align-items-center g-3">
                    <!-- القسم الأيمن -->
                    <div class="col-auto d-flex align-items-center flex-wrap gap-2">


                        <!-- قائمة الإجراءات -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-edit me-2"></i>تعديل
                                        المحدد</a></li>
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-trash me-2"></i>حذف
                                        المحدد</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item py-2" href="#"><i
                                            class="fas fa-file-export me-2"></i>تصدير</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- القسم الأيسر -->
                    <div class="col-auto d-flex align-items-center flex-wrap gap-2">
                        <!-- التنقل بين الصفحات -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-start" href="#" aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link border-0" href="#" aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                                <li class="page-item"><span class="page-link border-0">صفحة 1 من 10</span></li>
                                <li class="page-item">
                                    <a class="page-link border-0" href="#" aria-label="Next">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-end" href="#" aria-label="Last">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>


                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> إعدادات
                            </button>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item py-2" href="#">إعدادات 1</a></li>
                                <li><a class="dropdown-item py-2" href="#">إعدادات 2</a></li>
                            </ul>
                        </div>

                        <!-- زر إضافة طلب' -->
                        <a href="{{ route('orders.management.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle me-1"></i>
                            إضافة طلب
                        </a>
                    </div>
                </div>
            </div>
        </div>


      

               
                <script>
                    function toggleAdvancedSearch() {
                        let advancedSearch = document.getElementById('advancedSearch');
                        advancedSearch.classList.toggle('d-none');
                    }
                </script>
       
            
                <!-- 🔹 فورم الفلترة -->
                <div class="card p-3 mb-3">
                    <div class="row g-2">
                        <!-- 🔹 الصف الأول -->
                        <div class="col-md-3">
                            <label class="form-label">موظف</label>
                            <select class="form-control">
                                <option selected>اختر</option>
                                <option>موظف 1</option>
                                <option>موظف 2</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">تاريخ التقديم من</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">تاريخ التقديم إلى</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select class="form-control">
                                <option selected>الكل</option>
                                <option>نشط</option>
                                <option>تحت المراجعة</option>
                            </select>
                        </div>
            
                        <!-- 🔹 الصف الثاني -->
                        <div class="col-md-3">
                            <label class="form-label">تاريخ التنفيذ من</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">تاريخ التنفيذ إلى</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">القسم</label>
                            <select class="form-control">
                                <option selected>اختر</option>
                                <option>القسم 1</option>
                                <option>القسم 2</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">المستوى الوظيفي</label>
                            <select class="form-control">
                                <option selected>اختر</option>
                                <option>مستوى 1</option>
                                <option>مستوى 2</option>
                            </select>
                        </div>
                    </div>
            
                    <!-- 🔹 البحث المتقدم (مخفي افتراضيًا) -->
                    <div id="advancedSearch" class="d-none mt-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">نوع الوظيفة</label>
                                <select class="form-control">
                                    <option selected>اختر</option>
                                    <option>دوام كامل</option>
                                    <option>دوام جزئي</option>
                                    <option>مؤقت</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">المسمى الوظيفي</label>
                                <input type="text" class="form-control" placeholder="أدخل المسمى الوظيفي">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">البحث بواسطة</label>
                                <select class="form-control">
                                    <option selected>اختر</option>
                                    <option>اسم المدير</option>
                                    <option>الرقم التعريفي</option>
                                    <option>المدير المباشر</option>
                                </select>
                            </div>
                        </div>
                    </div>
            
                    <!-- 🔹 أزرار البحث -->
                    <div class="mt-3 d-flex justify-content-start">
                        <button class="btn btn-primary me-2">بحث</button>
                        <button class="btn btn-secondary me-2" onclick="toggleAdvancedSearch()">بحث متقدم</button>
                        <button class="btn btn-outline-secondary">إلغاء الفلتر</button>
                    </div>
                </div>
            
                <!-- 🔹 الجدول -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>موظف</th>
                                <th>تاريخ التنفيذ</th>
                                <th>تاريخ التقديم</th>
                                <th>الحالة</th>
                                <th>ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#2 راكان الحقباني</td>
                                <td>30/01/2025</td>
                                <td>01/01/2025</td>
                                <td><span class="text-warning">🟠 تحت المراجعة</span></td>
                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                aria-haspopup="true"aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item" href="">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="">
                                                            <i class="fa fa-pencil-alt me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#">
                                                            <i class="fa fa-trash-alt me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                  
                                                </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
           
            
          
            



    @endsection