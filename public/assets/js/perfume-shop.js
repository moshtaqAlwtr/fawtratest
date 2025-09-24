

    // Scroll animations with enhanced performance
    const perfumeObserverOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const perfumeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('perfume-animated');
                perfumeObserver.unobserve(entry.target); // Stop observing once animated
            }
        });
    }, perfumeObserverOptions);

    // Observe all elements with perfume-animate-on-scroll class
    document.querySelectorAll('.perfume-animate-on-scroll').forEach(el => {
        perfumeObserver.observe(el);
    });

    // Enhanced hover effects for cards
    document.querySelectorAll('.perfume-feature-card, .perfume-benefit-card, .perfume-testimonial-card').forEach(
        card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.02)';
                this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 3px 5px -1px rgba(0, 0, 0, 0.1)';
            });
        });

    // Enhanced CTA button effects with ripple
    document.querySelectorAll('.perfume-cta-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(139, 90, 150, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: perfume-ripple 0.6s linear;
                    pointer-events: none;
                `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add CSS for ripple animation
    const perfumeStyle = document.createElement('style');
    perfumeStyle.textContent = `
            @keyframes perfume-ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
    document.head.appendChild(perfumeStyle);

    // Add smooth reveal animation for elements
    const perfumeRevealElements = () => {
        const reveals = document.querySelectorAll(
            '.perfume-section-text, .perfume-section-image, .perfume-feature-card, .perfume-benefit-card');

        reveals.forEach((element, index) => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            } else {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = `all 0.6s ease ${index * 0.1}s`;
            }
        });
    };

    window.addEventListener('scroll', perfumeRevealElements);
    perfumeRevealElements(); // Initial call

    // Performance optimization - throttle scroll events
    let perfumeTicking = false;

    function perfumeUpdateOnScroll() {
        perfumeRevealElements();
        perfumeTicking = false;
    }

    window.addEventListener('scroll', () => {
        if (!perfumeTicking) {
            requestAnimationFrame(perfumeUpdateOnScroll);
            perfumeTicking = true;
        }
    });
