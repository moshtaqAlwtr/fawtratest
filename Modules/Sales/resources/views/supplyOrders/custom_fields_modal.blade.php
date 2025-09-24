<div class="modal fade" id="customFieldsModal" tabindex="-1" aria-labelledby="customFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customFieldsModalLabel">إعدادات الحقول المخصصة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            يمكنك إضافة وتخصيص الحقول الإضافية لأوامر التشغيل هنا
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>اسم الحقل</label>
                            <input type="text" class="form-control" placeholder="أدخل اسم الحقل">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>نوع الحقل</label>
                            <select class="form-control">
                                <option>نص</option>
                                <option>رقم</option>
                                <option>تاريخ</option>
                                <option>قائمة اختيار</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary">حفظ التغييرات</button>
            </div>
        </div>
    </div>
</div>
<script >
document.addEventListener('DOMContentLoaded', function() {
    // Any specific JavaScript for supply orders can go here
    console.log('Supply Orders JavaScript Loaded');
});
</script>
