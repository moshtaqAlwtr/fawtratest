<div class="business-area-page" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem; color: white;">
    <div class="business-area-content" style="max-width: 800px; margin: 0 auto; text-align: center;">
        <div class="mb-5">
            <h1 class="business-area-title" style="font-size: 2.5rem; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                <i class="fas fa-cog me-3"></i>
                {{ $pageTitle ?? 'مجال العمل' }}
            </h1>
            <p class="business-area-description" style="font-size: 1.2rem; opacity: 0.9;">
                {{ $pageDescription ?? 'صفحة مخصصة لهذا المجال' }}
            </p>
        </div>

        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
            <div class="feature-card" style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 10px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <div class="feature-icon" style="font-size: 2rem; margin-bottom: 1rem;">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>قيد التطوير</h3>
                <p>هذه الصفحة قيد التطوير وستكون متاحة قريباً مع جميع المزايا المطلوبة</p>
            </div>

            <div class="feature-card" style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 10px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <div class="feature-icon" style="font-size: 2rem; margin-bottom: 1rem;">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>مزايا متقدمة</h3>
                <p>سيتم إضافة مزايا متخصصة لهذا المجال لتلبية جميع احتياجاتك</p>
            </div>

            <div class="feature-card" style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 10px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <div class="feature-icon" style="font-size: 2rem; margin-bottom: 1rem;">
                    <i class="fas fa-support"></i>
                </div>
                <h3>دعم متخصص</h3>
                <p>فريق دعم متخصص في هذا المجال لمساعدتك في جميع الأوقات</p>
            </div>
        </div>

        <div class="action-buttons" style="margin-top: 3rem;">
            <button class="btn btn-light btn-lg me-3" style="padding: 1rem 2rem; border-radius: 50px;">
                <i class="fas fa-bell me-2"></i>
                اشترك للحصول على التحديثات
            </button>
            <button class="btn btn-outline-light btn-lg" style="padding: 1rem 2rem; border-radius: 50px;">
                <i class="fas fa-phone me-2"></i>
                تواصل معنا
            </button>
        </div>
    </div>
</div>