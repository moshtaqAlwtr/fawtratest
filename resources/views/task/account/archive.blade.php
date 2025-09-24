@extends('task.layouts.task_master')

@section('title', 'تفاصيل المهمة')

@section('content')
<div class="content-wrapper">
    <!-- Action Buttons -->
    <div class="d-flex">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <!-- يمكن إضافة أزرار إضافية هنا -->
        </div>

        <div class="btn-group" role="group">
            <a href="" class="btn btn-secondary f-14" data-toggle="tooltip" data-original-title="عرض جميع المهام">
                <i class="side-icon bi bi-list-ul"></i>
            </a>
            <a href="" class="btn btn-secondary f-14" data-toggle="tooltip" data-original-title="تعديل المهمة">
                <i class="side-icon bi bi-pencil"></i>
            </a>
        </div>
    </div>

    <!-- Task Details -->
    <div class="d-flex flex-column border-grey border-1 rounded mt-3 bg-white p-3">
        <div class="row">
            <div class="col-md-8">
                <h3 class="text-primary"></h3>
                <p class="text-muted"></p>

                <div class="task-meta mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>الحالة:</strong>
                                <span class="badge badge-">

                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>الأولوية:</strong>
                                <span class="badge badge-">

                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>تاريخ التسليم:</strong> </p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <p><strong>المشروع:</strong>
                                <a href=""></a>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>العميل:</strong>
                                <a href=""></a>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>تم الإنشاء بواسطة:</strong> نممم</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">فريق العمل</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">

                            <li class="mb-2 d-flex align-items-center">
                                <img src="" class="rounded-circle mr-2" width="30" height="30">

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: %;"
                         aria-valuenow="" aria-valuemin="0" aria-valuemax="100">

                    </div>
                </div>
            </div>
        </div>

        <!-- Task Comments Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">التعليقات</h5>
                    </div>
                    <div class="card-body">
                        <div class="task-comments">

                            <div class="comment mb-3">
                                <div class="d-flex">
                                    <img src="" class="rounded-circle mr-2" width="40" height="40">
                                    <div>
                                        <h6></h6>
                                        <small class="text-muted"></small>
                                        <p class="mt-1"></p>
                                    </div>
                                </div>
                            </div>


                            <form action="" method="POST" class="mt-3">

                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="3" placeholder="أضف تعليقًا..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Attachments -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">المرفقات</h5>
                    </div>
                    <div class="card-body">
                        <div class="task-attachments">

                            <div class="attachment mb-2 d-flex align-items-center">
                                <i class="bi bi-file-earmark mr-2"></i>
                                <a href="" target="_blank"></a>
                                <small class="text-muted ml-2"></small>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data" class="mt-3">

                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="attachment" name="attachment">
                                        <label class="custom-file-label" for="attachment">اختر ملف</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">رفع</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize file input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endsection
