// JavaScript لصفحة محلات الموبايل - فوترة

// انيميشن محسن مع تأثيرات بصرية
const mobileObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('mobile-animated');

            // تأثير خاص للميزات
            if (entry.target.classList.contains('mobile-features-section')) {
                const cards = entry.target.querySelectorAll('.mobile-feature-card');
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 150);
                });
            }

            mobileObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
});

// مراقبة جميع العناصر
document.querySelectorAll('.mobile-animate-on-scroll').forEach(el => {
    mobileObserver.observe(el);
});

// تأثير متقدم للأزرار
document.querySelectorAll('.mobile-cta-button').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        // تأثير موجات متتالية
        for (let i = 0; i < 3; i++) {
            setTimeout(() => {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 1.5;
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, ${0.4 - i * 0.1});
                    border-radius: 50%;
                    transform: scale(0);
                    animation: mobile-ripple-wave 1.2s ease-out;
                    pointer-events: none;
                `;

                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 1200);
            }, i * 100);
        }
    });

    // تأثير hover محسن
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.08)';
        this.style.boxShadow = '0 15px 40px rgba(245, 158, 11, 0.6)';
    });

    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
        this.style.boxShadow = '0 8px 25px rgba(245, 158, 11, 0.4)';
    });
});

// إضافة CSS للانيميشن المتقدم
function addMobileAdvancedStyles() {
    const mobileAdvancedStyle = document.createElement('style');
    mobileAdvancedStyle.textContent = `
        @keyframes mobile-ripple-wave {
            0% {
                transform: scale(0);
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
            100% {
                transform: scale(1);
                opacity: 0;
            }
        }

        .mobile-section-image img {
            will-change: transform;
        }

        .mobile-feature-card {
            will-change: transform, opacity;
        }

        .mobile-cta-button {
            will-change: transform, box-shadow;
        }
    `;
    document.head.appendChild(mobileAdvancedStyle);
}

// تأثيرات متقدمة للكروت
function initMobileFeatureCards() {
    document.querySelectorAll('.mobile-feature-card').forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            // تأثير الظل المتحرك
            this.style.boxShadow = `
                0 25px 60px rgba(0, 0, 0, 0.15),
                0 5px 20px rgba(102, 126, 234, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9)
            `;

            // تحريك الأيقونة
            const icon = this.querySelector('.mobile-feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotateY(15deg)';
                icon.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.4)';
            }
        });

        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
            const icon = this.querySelector('.mobile-feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotateY(0deg)';
                icon.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3)';
            }
        });
    });
}

// تأثير parallax للخلفية
let mobileParallaxTicking = false;

function updateMobileParallax() {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.mobile-hero-section');

    if (heroSection) {
        const rate = scrolled * -0.2;
        heroSection.style.transform = `translateY(${rate}px)`;
    }

    mobileParallaxTicking = false;
}

function initMobileParallax() {
    window.addEventListener('scroll', () => {
        if (!mobileParallaxTicking) {
            requestAnimationFrame(updateMobileParallax);
            mobileParallaxTicking = true;
        }
    });
}

// تحسين الوصولية
function initMobileAccessibility() {
    document.querySelectorAll('.mobile-cta-button, .mobile-feature-card').forEach(element => {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });

        if (!element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '0');
        }
    });
}

// تأثير الكتابة المتحركة للعنوان
function mobileTypeWriter(element, text, speed = 120) {
    let i = 0;
    element.innerHTML = '';

    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    type();
}

// تشغيل تأثير الكتابة عند تحميل الصفحة
function initMobileTypeWriter() {
    document.addEventListener('DOMContentLoaded', () => {
        const title = document.querySelector('.mobile-hero-title');
        if (title) {
            const originalText = title.textContent;
            setTimeout(() => {
                mobileTypeWriter(title, originalText, 100);
            }, 800);
        }
    });
}

// تحسين تجربة المستخدم على الأجهزة اللمسية
function initMobileTouchEvents() {
    if ('ontouchstart' in window) {
        document.querySelectorAll('.mobile-feature-card').forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-5px) scale(0.98)';
            });

            card.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = 'translateY(-10px) scale(1)';
                }, 100);
            });
        });
    }
}

// تأثيرات تفاعلية للصور
function initMobileImageEffects() {
    document.querySelectorAll('.mobile-section-image').forEach(imageContainer => {
        const img = imageContainer.querySelector('img');
        if (img) {
            imageContainer.addEventListener('mouseenter', function() {
                img.style.transform = 'translateY(-10px) scale(1.02)';
                img.style.boxShadow = '0 30px 50px rgba(0, 0, 0, 0.2)';
            });

            imageContainer.addEventListener('mouseleave', function() {
                img.style.transform = 'translateY(0) scale(1)';
                img.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });
        }
    });
}

// تأثيرات للميزات في Hero Section
function initMobileHeroFeatures() {
    document.querySelectorAll('.mobile-hero-feature').forEach((feature, index) => {
        feature.style.animationDelay = `${index * 0.1}s`;

        feature.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(255, 255, 255, 0.3)';
            this.style.transform = 'translateX(-5px) scale(1.02)';
        });

        feature.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(255, 255, 255, 0.15)';
            this.style.transform = 'translateX(0) scale(1)';
        });
    });
}

// تأثير تموج للخلفية
function createMobileBackgroundWaves() {
    const heroSection = document.querySelector('.mobile-hero-section');
    if (heroSection) {
        const wave = document.createElement('div');
        wave.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: mobile-wave-pulse 4s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        `;

        heroSection.appendChild(wave);

        // إضافة CSS للتموج
        const waveStyle = document.createElement('style');
        waveStyle.textContent = `
            @keyframes mobile-wave-pulse {
                0%, 100% {
                    transform: translate(-50%, -50%) scale(1);
                    opacity: 0.3;
                }
                50% {
                    transform: translate(-50%, -50%) scale(1.5);
                    opacity: 0.1;
                }
            }
        `;
        document.head.appendChild(waveStyle);
    }
}

// تحسين الأداء مع Intersection Observer
function initMobilePerformanceOptimization() {
    // تأجيل تحميل الصور
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    // مراقبة الصور للتحميل الكسول
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// تأثير حركة السحب والإفلات (اختياري)
function initMobileDragEffects() {
    document.querySelectorAll('.mobile-feature-card').forEach(card => {
        let isDragging = false;
        let startX, startY, initialX, initialY;

        card.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            initialX = card.offsetLeft;
            initialY = card.offsetTop;
            card.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;

            // تحديد حركة طفيفة فقط للتأثير البصري
            card.style.transform = `translate(${deltaX * 0.1}px, ${deltaY * 0.1}px)`;
        });

        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                card.style.cursor = 'pointer';
                card.style.transform = '';
            }
        });
    });
}

// دالة التهيئة الرئيسية
function initMobileShopPage() {
    // تشغيل جميع الوظائف
    addMobileAdvancedStyles();
    initMobileFeatureCards();
    initMobileParallax();
    initMobileAccessibility();
    initMobileTypeWriter();
    initMobileTouchEvents();
    initMobileImageEffects();
    initMobileHeroFeatures();
    createMobileBackgroundWaves();
    initMobilePerformanceOptimization();

    // تأثيرات اختيارية (يمكن تعطيلها)
    // initMobileDragEffects();

    console.log('✅ تم تحميل JavaScript لصفحة محلات الموبايل بنجاح');
}

// تشغيل التهيئة عند تحميل DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileShopPage);
} else {
    initMobileShopPage();
}

// تصدير الوظائف للاستخدام الخارجي (اختياري)
window.MobileShopJS = {
    init: initMobileShopPage,
    typeWriter: mobileTypeWriter,
    updateParallax: updateMobileParallax
};
