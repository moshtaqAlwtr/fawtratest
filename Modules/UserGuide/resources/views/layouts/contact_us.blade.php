<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Cairo', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        direction: rtl;
        text-align: right;
    }

    .contact-page {
        min-height: 100vh;
        text-align: right;
    }
</style>
<div class="contact-page">
    <div class="contact-header">
        <div class="container">
            <h1 class="page-title">تواصل معنا</h1>
            <p class="page-subtitle">نحن هنا لمساعدتك في أي وقت - فريق الدعم والمبيعات</p>
        </div>
    </div>

    <div class="contact-content">
        <div class="container">
            <!-- قسم معلومات الاتصال -->
            @include('userguide::layouts.programs.additional.fawtura_agents')
            <!-- قسم نموذج التواصل -->
            <div class="contact-form-section">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="contact-form-card">
                            <h3 class="form-title">أرسل لنا رسالة</h3>
                            <p class="form-subtitle">سنقوم بالرد عليك في أقرب وقت ممكن</p>

                            <form class="contact-form" onsubmit="submitForm(event)">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">الاسم الكامل</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">البريد الإلكتروني</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">رقم الهاتف</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subject">الموضوع</label>
                                            <select class="form-control" id="subject" name="subject" required>
                                                <option value="">اختر الموضوع</option>
                                                <option value="sales">استفسار مبيعات</option>
                                                <option value="support">دعم فني</option>
                                                <option value="billing">استفسار فوترة</option>
                                                <option value="general">استفسار عام</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="message">الرسالة</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required placeholder="اكتب رسالتك هنا..."></textarea>
                                </div>

                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    إرسال الرسالة
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="contact-info-card">
                            <h4>معلومات إضافية</h4>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <h5>البريد الإلكتروني</h5>
                                    <p>support@foutrah.com</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="info-content">
                                    <h5>الموقع الإلكتروني</h5>
                                    <p>www.foutrah.com</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="info-content">
                                    <h5>الدعم الفني</h5>
                                    <p>متاح 24/7 لخدمتك</p>
                                </div>
                            </div>

                            <div class="social-links">
                                <h5>تابعنا على</h5>
                                <div class="ic-social-icons">
                                    <a href="#" class="ic-social-icon facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="ic-social-icon twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="ic-social-icon linkedin">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" class="ic-social-icon instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function submitForm(event) {
        event.preventDefault();

        const submitBtn = event.target.querySelector('.btn-primary');
        const originalText = submitBtn.innerHTML;

        // تأثير التحميل
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        submitBtn.disabled = true;

        // محاكاة إرسال النموذج
        setTimeout(() => {
            alert('تم إرسال رسالتك بنجاح! سنقوم بالرد عليك قريباً.');
            event.target.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 2000);
    }

    // تأثيرات تفاعلية للبطاقات
    document.querySelectorAll('.country-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px) scale(1.02)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });

    // تأثيرات للأيقونات الاجتماعية
    document.querySelectorAll('.ic-social-icon').forEach(icon => {
        icon.addEventListener('mouseenter', () => {
            icon.style.transform = 'translateY(-3px) rotate(5deg)';
        });

        icon.addEventListener('mouseleave', () => {
            icon.style.transform = 'translateY(0) rotate(0deg)';
        });
    });

    // تنسيق رقم الهاتف
    document.getElementById('phone').addEventListener('input', function() {
        this.value = this.value.replace(/[^\d+\-\s]/g, '');
    });

    // تأثير smooth scroll للروابط
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
