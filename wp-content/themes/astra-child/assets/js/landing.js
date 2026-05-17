/**
 * CIP Landing Page — Interactivity
 * Centro de Innovación Pública
 *
 * Compatible with: template-landing.php (legacy) + Elementor Canvas
 */
(function () {
    'use strict';

    var SCROLL_THRESHOLD = 50;
    var NAVBAR_OFFSET = 80;
    var body = document.body;
    var isEditor = body.classList.contains('elementor-editor-active');


    // ================================================================
    //  NAVBAR MODULE — only runs when navbar exists
    // ================================================================
    var navbar = document.getElementById('cipNavbar');

    if (navbar) {
        var hamburger = document.getElementById('cipHamburger');
        var mobileMenu = document.getElementById('cipMobileMenu');
        var navLinks = document.querySelectorAll('.cip-navbar__link');

        /* ------------------------------------------------------------------
           1. Navbar scroll toggle (.scrolled)
           ------------------------------------------------------------------ */
        function handleScroll() {
            if (window.scrollY > SCROLL_THRESHOLD) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            updateActiveLink();
        }

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();

        /* ------------------------------------------------------------------
           2. Mobile menu toggle
           ------------------------------------------------------------------ */
        function openMobileMenu() {
            hamburger.classList.add('active');
            hamburger.setAttribute('aria-expanded', 'true');
            mobileMenu.classList.add('open');
            mobileMenu.setAttribute('aria-hidden', 'false');
            body.classList.add('menu-open');
        }

        function closeMobileMenu() {
            hamburger.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
            mobileMenu.classList.remove('open');
            mobileMenu.setAttribute('aria-hidden', 'true');
            body.classList.remove('menu-open');
        }

        if (hamburger && mobileMenu) {
            hamburger.addEventListener('click', function () {
                if (mobileMenu.classList.contains('open')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });

            var mobileLinks = mobileMenu.querySelectorAll('.cip-mobile-menu__link');
            mobileLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    closeMobileMenu();
                });
            });
        }

        /* ------------------------------------------------------------------
           3. Smooth scroll for anchor links
           Supports both legacy (#section-id) and Elementor (element IDs)
           ------------------------------------------------------------------ */
        var anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var targetId = this.getAttribute('href');
                if (targetId === '#' || targetId === '#!' ) return;

                // Try finding the target by ID directly
                var target = document.querySelector(targetId);

                // If not found, try Elementor's _element_id attribute
                if (!target) {
                    var cleanId = targetId.replace('#', '');
                    target = document.querySelector('[id="' + cleanId + '"]');
                }

                if (!target) return;

                e.preventDefault();

                var offsetTop = target.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET;

                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            });
        });

        /* ------------------------------------------------------------------
           4. Intersection Observer — entry animations
           ------------------------------------------------------------------ */
        var animatedElements = document.querySelectorAll('[data-animate]');

        if ('IntersectionObserver' in window && animatedElements.length > 0) {
            var observer = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            var el = entry.target;
                            var delay = parseInt(el.getAttribute('data-delay') || '0', 10);

                            setTimeout(function () {
                                el.classList.add('animated');
                            }, delay);

                            observer.unobserve(el);
                        }
                    });
                },
                {
                    threshold: 0.15,
                    rootMargin: '0px 0px -40px 0px'
                }
            );

            animatedElements.forEach(function (el) {
                observer.observe(el);
            });

            body.classList.add('cip-js');
        } else {
            animatedElements.forEach(function (el) {
                el.classList.add('animated');
            });
        }

        /* ------------------------------------------------------------------
           5. Active nav link based on visible section
           Supports both legacy sections and Elementor containers with IDs
           ------------------------------------------------------------------ */
        function getSections() {
            var legacySections = document.querySelectorAll('section[id]');
            if (legacySections.length > 0) return legacySections;

            var sectionIds = ['inicio', 'el-cip', 'programa', 'nuestra-dinamica', 'contacto'];
            var elementorSections = [];
            sectionIds.forEach(function (id) {
                var el = document.getElementById(id);
                if (el) elementorSections.push(el);
            });
            return elementorSections;
        }

        function updateActiveLink() {
            if (!navLinks || !navLinks.length) return;
            var sections = getSections();
            var scrollPos = window.scrollY + NAVBAR_OFFSET + 100;

            for (var i = 0; i < sections.length; i++) {
                var section = sections[i];
                var top = section.offsetTop;
                var height = section.offsetHeight;
                var id = section.getAttribute('id');

                if (scrollPos >= top && scrollPos < top + height) {
                    navLinks.forEach(function (link) {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) {
                            link.classList.add('active');
                        }
                    });
                }
            }
        }

        /* ------------------------------------------------------------------
           6. Close mobile menu on resize to desktop
           ------------------------------------------------------------------ */
        window.addEventListener('resize', function () {
            if (window.innerWidth > 991 && mobileMenu && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            }
        }, { passive: true });

        /* ------------------------------------------------------------------
           7. Escape key closes mobile menu
           ------------------------------------------------------------------ */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
                hamburger.focus();
            }
        });

    } // end navbar module

    // ================================================================
    //  FORM TOGGLE MODULE — runs independently of navbar
    //  Progressive enhancement: toggle is injected via JS.
    //  In Elementor editor: shows as non-interactive preview.
    //  Uses retry for Elementor async rendering.
    // ================================================================
    var toggleInitialized = false;

    function initFormToggle() {
        if (toggleInitialized) return true;

        var grid = document.querySelector('.cip-contacto__grid');
        var programa = document.querySelector('.cip-contacto__card--programa');
        var staff = document.querySelector('.cip-contacto__card--staff');
        var editorActive = document.body.classList.contains('elementor-editor-active');

        if (!grid || !programa || !staff) return false;

        toggleInitialized = true;

        // Build toggle HTML
        var toggleEl = document.createElement('div');
        toggleEl.className = 'cip-contacto__toggle';
        toggleEl.setAttribute('data-active', 'programa');
        if (editorActive) toggleEl.classList.add('cip-contacto__toggle--editor');
        toggleEl.innerHTML =
            '<div class="cip-contacto__toggle-track" role="tablist" aria-label="Tipo de formulario">' +
                '<button type="button" role="tab" aria-selected="true" class="cip-contacto__toggle-btn cip-contacto__toggle-btn--programa cip-contacto__toggle-btn--active" data-target="programa">' +
                    '<span class="cip-contacto__toggle-icon"><i class="fas fa-graduation-cap"></i></span>' +
                    '<span class="cip-contacto__toggle-label">' +
                        '<span class="cip-contacto__toggle-title">Postularme al Programa</span>' +
                        '<span class="cip-contacto__toggle-desc">Formaci\u00f3n profesional</span>' +
                    '</span>' +
                '</button>' +
                '<button type="button" role="tab" aria-selected="false" class="cip-contacto__toggle-btn cip-contacto__toggle-btn--staff" data-target="staff">' +
                    '<span class="cip-contacto__toggle-icon"><i class="fas fa-users"></i></span>' +
                    '<span class="cip-contacto__toggle-label">' +
                        '<span class="cip-contacto__toggle-title">Unirme al Staff</span>' +
                        '<span class="cip-contacto__toggle-desc">S\u00e9 parte del equipo</span>' +
                    '</span>' +
                '</button>' +
            '</div>';

        // Insert toggle before the grid
        grid.parentNode.insertBefore(toggleEl, grid);

        // In Elementor editor: show toggle as preview only, keep both cards visible
        if (editorActive) return true;

        var toggleBtns = toggleEl.querySelectorAll('.cip-contacto__toggle-btn');
        var currentForm = null;

        function showForm(target, animate) {
            if (target === currentForm) return;
            currentForm = target;

            var isPrograma = target === 'programa';
            var showCard = isPrograma ? programa : staff;
            var hideCard = isPrograma ? staff : programa;

            // Update toggle state
            toggleEl.setAttribute('data-active', target);
            toggleBtns.forEach(function (btn) {
                var isActive = btn.getAttribute('data-target') === target;
                btn.classList.toggle('cip-contacto__toggle-btn--active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            // Hide inactive card
            hideCard.classList.add('cip-contacto__card--hidden');
            hideCard.classList.remove('cip-contacto__card--entering');

            // Show active card
            showCard.classList.remove('cip-contacto__card--hidden');
            if (animate !== false) {
                showCard.classList.remove('cip-contacto__card--entering');
                void showCard.offsetWidth; // force reflow for re-trigger
                showCard.classList.add('cip-contacto__card--entering');
            }

            // Switch grid to single-column centered
            grid.classList.add('cip-contacto__grid--toggled');
        }

        // Toggle button clicks
        toggleBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                showForm(this.getAttribute('data-target'));
            });
        });

        // Intercept anchor clicks to #postularme / #staff (capture phase)
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href="#postularme"], a[href="#staff"]');
            if (!link) return;

            e.preventDefault();
            e.stopPropagation();

            var hash = link.getAttribute('href');
            showForm(hash === '#staff' ? 'staff' : 'programa');

            var contacto = document.querySelector('.cip-contacto');
            if (contacto) {
                var offsetTop = contacto.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET;
                window.scrollTo({ top: offsetTop, behavior: 'smooth' });
            }
        }, true);

        // Set initial state based on URL hash
        var initialTarget = window.location.hash === '#staff' ? 'staff' : 'programa';
        showForm(initialTarget, false);

        return true;
    }

    // Try immediately (works on normal frontend)
    if (!initFormToggle()) {
        // Elements not found yet — Elementor renders async, retry periodically
        var retries = 0;
        var retryTimer = setInterval(function () {
            retries++;
            if (initFormToggle() || retries >= 20) {
                clearInterval(retryTimer);
            }
        }, 500);
    }

    // ================================================================
    //  DECORATIVE SHAPES MODULE — injects geometric accents into sections
    //  Adds visual depth without blocking Elementor editing.
    //  Skipped entirely in the editor.
    // ================================================================
    if (!isEditor) {
        var decoConfigs = [
            {
                selector: '[id="el-cip"]',
                shapes: [
                    'cip-deco cip-deco--orb cip-deco--about-orb',
                    'cip-deco cip-deco--ring cip-deco--about-ring',
                    'cip-deco cip-deco--dots cip-deco--about-dots',
                    'cip-deco cip-deco--orb cip-deco--about-orb-2',
                    'cip-deco cip-deco--orb cip-deco--about-orb-3',
                    'cip-deco cip-deco--ring cip-deco--about-ring-2',
                    'cip-deco cip-deco--cross cip-deco--about-cross',
                    'cip-deco cip-deco--diamond cip-deco--about-diamond',
                    'cip-deco cip-deco--cross cip-deco--about-cross-2'
                ]
            },
            {
                selector: '.cip-dinamica',
                shapes: [
                    'cip-deco cip-deco--orb cip-deco--dinamica-orb',
                    'cip-deco cip-deco--ring cip-deco--dinamica-ring',
                    'cip-deco cip-deco--diamond cip-deco--dinamica-diamond',
                    'cip-deco cip-deco--orb cip-deco--dinamica-orb-2',
                    'cip-deco cip-deco--orb cip-deco--dinamica-orb-3',
                    'cip-deco cip-deco--ring cip-deco--dinamica-ring-2',
                    'cip-deco cip-deco--cross cip-deco--dinamica-cross',
                    'cip-deco cip-deco--dots cip-deco--dinamica-dots',
                    'cip-deco cip-deco--diamond cip-deco--dinamica-diamond-2'
                ]
            },
            {
                selector: '.cip-programa',
                shapes: [
                    'cip-deco cip-deco--orb cip-deco--programa-orb',
                    'cip-deco cip-deco--ring cip-deco--programa-ring',
                    'cip-deco cip-deco--cross cip-deco--programa-cross',
                    'cip-deco cip-deco--orb cip-deco--programa-orb-2',
                    'cip-deco cip-deco--orb cip-deco--programa-orb-3',
                    'cip-deco cip-deco--ring cip-deco--programa-ring-2',
                    'cip-deco cip-deco--diamond cip-deco--programa-diamond',
                    'cip-deco cip-deco--dots cip-deco--programa-dots',
                    'cip-deco cip-deco--cross cip-deco--programa-cross-2'
                ]
            },
            {
                selector: '.cip-contacto',
                shapes: [
                    'cip-deco cip-deco--orb cip-deco--contacto-orb',
                    'cip-deco cip-deco--ring cip-deco--ring-light cip-deco--contacto-ring',
                    'cip-deco cip-deco--dots cip-deco--dots-light cip-deco--contacto-dots',
                    'cip-deco cip-deco--orb cip-deco--contacto-orb-2',
                    'cip-deco cip-deco--orb cip-deco--contacto-orb-3',
                    'cip-deco cip-deco--ring cip-deco--ring-light cip-deco--contacto-ring-2',
                    'cip-deco cip-deco--cross cip-deco--contacto-cross',
                    'cip-deco cip-deco--diamond cip-deco--diamond-light cip-deco--contacto-diamond',
                    'cip-deco cip-deco--cross cip-deco--contacto-cross-2'
                ]
            }
        ];

        var decoInjected = false;

        function cipInjectDecorations() {
            if (decoInjected) return true;

            var found = 0;
            decoConfigs.forEach(function (cfg) {
                var section = document.querySelector(cfg.selector);
                if (!section) return;

                // Skip if already injected into this section
                if (section.querySelector('.cip-deco')) {
                    found++;
                    return;
                }

                cfg.shapes.forEach(function (classes) {
                    var el = document.createElement('div');
                    el.className = classes;
                    el.setAttribute('aria-hidden', 'true');
                    section.appendChild(el);
                });
                found++;
            });

            if (found === decoConfigs.length) {
                decoInjected = true;
                return true;
            }
            return false;
        }

        // Try immediately, retry for Elementor async rendering
        if (!cipInjectDecorations()) {
            var decoRetries = 0;
            var decoTimer = setInterval(function () {
                decoRetries++;
                if (cipInjectDecorations() || decoRetries >= 15) {
                    clearInterval(decoTimer);
                }
            }, 500);
        }
    }

})();
