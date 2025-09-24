@extends('master')

@section('title')
إدارة المهام
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">إدارة المهام</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                        <li class="breadcrumb-item active">المهام</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <button type="button" class="btn btn-primary" id="btnAddTask">
            <i class="feather icon-plus"></i> إضافة مهمة جديدة
        </button>
    </div>
</div>

<div class="content-body">
    <!-- فلاتر البحث المحسنة -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>المشروع</label>
                    <select class="form-control select2" id="filterProject">
                        <option value="">جميع المشاريع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>الحالة</label>
                    <select class="form-control select2" id="filterStatus">
                        <option value="">جميع الحالات</option>
                        <option value="not_started">لم تبدأ</option>
                        <option value="in_progress">قيد التنفيذ</option>
                        <option value="completed">مكتملة</option>
                        <option value="overdue">متأخرة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>الأولوية</label>
                    <select class="form-control select2" id="filterPriority">
                        <option value="">جميع الأولويات</option>
                        <option value="low">منخفضة</option>
                        <option value="medium">متوسطة</option>
                        <option value="high">عالية</option>
                        <option value="urgent">عاجلة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>المستخدم المعين</label>
                    <select class="form-control select2" id="filterAssignee">
                        <option value="">الجميع</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary btn-block" id="btnFilterTasks">
                        <i class="feather icon-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- لوحة المهام بالسحب والإفلات -->
    <div class="row task-board">
        @foreach($statuses as $statusKey => $statusName)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $statusName }}</h4>
                        <span class="badge badge-pill task-count task-count-{{ $statusKey }}">
                            {{ isset($tasks[$statusKey]) ? $tasks[$statusKey]->count() : 0 }}
                        </span>
                    </div>
                    <div class="card-body task-column" data-status="{{ $statusKey }}">
                        @if(isset($tasks[$statusKey]))
                            @foreach($tasks[$statusKey] as $task)
                                @include('taskmanager::task.partial.task-card', ['task' => $task])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal إضافة/تعديل المهمة -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalTitle">إضافة مهمة جديدة</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="taskForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="task_id">

                <div class="modal-body">
                    <div class="alert alert-danger" id="formErrors" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المشروع <span class="text-danger">*</span></label>
                                <select name="project_id" id="project_id" class="form-control select2" required>
                                    <option value="">اختر المشروع</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المهمة الرئيسية</label>
                                <select name="parent_task_id" id="parent_task_id" class="form-control select2">
                                    <option value="">لا يوجد (مهمة رئيسية)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>عنوان المهمة <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>الوصف</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الحالة <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control select2" required>
                                    <option value="not_started">لم تبدأ</option>
                                    <option value="in_progress">قيد التنفيذ</option>
                                    <option value="completed">مكتملة</option>
                                    <option value="overdue">متأخرة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الأولوية <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low">منخفضة</option>
                                    <option value="medium">متوسطة</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجلة</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ البدء</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ الانتهاء</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>الميزانية</label>
                                <input type="number" name="budget" id="budget" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>الساعات المقدرة</label>
                                <input type="number" name="estimated_hours" id="estimated_hours" class="form-control" step="0.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>نسبة الإنجاز (%)</label>
                                <input type="number" name="completion_percentage" id="completion_percentage" class="form-control" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>تعيين المستخدمين</label>
                        <select name="assigned_users[]" id="assigned_users" class="form-control select2" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>إرفاق ملفات</label>
                        <input type="file" name="files[]" id="files" class="form-control" multiple>
                        <small class="text-muted">الحد الأقصى: 10 ميجابايت</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="send_notifications" name="send_notifications" value="1">
                            <label class="custom-control-label" for="send_notifications">إرسال إشعارات للمستخدمين</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="saveTaskBtn">
                        <i class="feather icon-save"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal عرض التفاصيل -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المهمة</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="taskDetailsContent"></div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/task-manager.css') }}">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- الملفات المنظمة -->
<script src="{{ asset('assets/js/task/utils.js') }}"></script>
<script src="{{ asset('assets/js/task/task-card.js') }}"></script>
<script src="{{ asset('assets/js/task/drag-drop.js') }}"></script>
<script src="{{ asset('assets/js/task/task-updater.js') }}"></script>
<script src="{{ asset('assets/js/task/task-modal.js') }}"></script>
<script src="{{ asset('assets/js/task/task-manager.js') }}"></script>
@endsection