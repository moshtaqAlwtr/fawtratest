<style>
    .success-partners-section {
        padding: 60px 0;
        background: #ffffff;
        position: relative;
    }

    .success-partners-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 2;
    }

    .section-header {
        text-align: center;
        margin-bottom: 40px;
        color: #333;
    }

    .section-title {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: 700;
        color: #2d3748;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #718096;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.5;
    }

    .partners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .partner-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        text-align: right;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .partner-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .partner-card:hover::before {
        transform: scaleX(1);
    }

    .partner-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .partner-icon {
        width: 50px;
        height: 50px;
        margin: 0 0 12px auto;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    .partner-icon::before {
        display: none;
    }

    .partner-card:hover .partner-icon::before {
        display: none;
    }

    .partner-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2d3748;
        text-align: right;
    }

    .partner-description {
        color: #718096;
        line-height: 1.5;
        font-size: 0.85rem;
        text-align: right;
    }

    .stats-section {
        background: #f7fafc;
        border-radius: 12px;
        padding: 30px;
        border: 1px solid #e2e8f0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 30px;
    }

    .stat-item {
        text-align: center;
        color: #2d3748;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #667eea;
        display: block;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
        font-weight: 500;
    }

    .cta-section {
        text-align: center;
        margin-top: 40px;
    }

    .cta-button {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Remove floating elements */
    .floating-element {
        display: none;
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 2.2rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
        }

        .partners-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .partner-card {
            padding: 30px 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .stat-number {
            font-size: 2.2rem;
        }
    }
</style>
<section class="success-partners-section">
    <!-- Floating background elements -->
    <div class="floating-element">
        <i class="fas fa-handshake" style="font-size: 3rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="fas fa-trophy" style="font-size: 2.5rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="fas fa-star" style="font-size: 2rem; color: white;"></i>
    </div>

    <div class="container">
        <!-- Header Section -->
        <div class="section-header">
            <h2 class="section-title">شركاء النجاح</h2>
            <p class="section-subtitle">
                شراكات موثوقة مع أفضل الشركات
            </p>
        </div>

        <!-- Partners Grid -->
        <div class="partners-grid">
            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="partner-title">الشركات الكبيرة</h3>
                <p class="partner-description">
                    حلول محاسبية متطورة للشركات الكبيرة.
                </p>
            </div>

            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="partner-title">المؤسسات الصغيرة والمتوسطة</h3>
                <p class="partner-description">
                    حلول مرنة للمؤسسات الصغيرة والمتوسطة.
                </p>
            </div>

            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="partner-title">المؤسسات التعليمية</h3>
                <p class="partner-description">
                    إدارة مالية حديثة للمؤسسات التعليمية.
                </p>
            </div>

            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <h3 class="partner-title">القطاع الصحي</h3>
                <p class="partner-description">
                    نظم فواتير ومصروفات للمراكز الطبية.
                </p>
            </div>

            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="partner-title">قطاع المطاعم والضيافة</h3>
                <p class="partner-description">
                    أنظمة نقاط بيع ومخزون للمطاعم والفنادق.
                </p>
            </div>

            <div class="partner-card">
                <div class="partner-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3 class="partner-title">شركات النقل واللوجستيات</h3>
                <p class="partner-description">
                    إدارة العمليات وتتبع الشحنات للنقل واللوجستيات.
                </p>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">5000+</span>
                    <div class="stat-label">شريك ناجح</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">15+</span>
                    <div class="stat-label">دولة حول العالم</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">99%</span>
                    <div class="stat-label">معدل رضا العملاء</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <div class="stat-label">دعم فني متواصل</div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-section">
            <a href="#" class="cta-button">
                <i class="fas fa-handshake" style="margin-left: 10px;"></i>
                انضم إلى شركاء النجاح
            </a>
        </div>
    </div>
</section>
