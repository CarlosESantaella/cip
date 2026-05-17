<?php
/**
 * Template Name: CIP Landing Page
 * Template Post Type: page
 *
 * @package AstraChildCIP
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'cip-landing' ); ?>>
<?php wp_body_open(); ?>

<!-- ========== NAVBAR ========== -->
<nav class="cip-navbar" id="cipNavbar" role="navigation" aria-label="Navegación principal">
    <div class="cip-navbar__container">
        <!-- Logo -->
        <a href="#inicio" class="cip-navbar__logo" aria-label="CIP - Inicio">
            <img
                src="<?php echo esc_url( content_url( '/uploads/2026/02/logo-1_transparent.png' ) ); ?>"
                alt="CIP - Centro de Innovación Pública"
                class="cip-navbar__logo-img"
                width="180"
                height="50"
            >
        </a>

        <!-- Desktop Navigation -->
        <ul class="cip-navbar__links" id="cipNavLinks">
            <li><a href="#inicio" class="cip-navbar__link active">Inicio</a></li>
            <li><a href="#el-cip" class="cip-navbar__link">El CIP</a></li>
            <li><a href="#programa" class="cip-navbar__link">Programa</a></li>
            <li><a href="#nuestra-dinamica" class="cip-navbar__link">Nuestra Dinámica</a></li>
            <li><a href="#contacto" class="cip-navbar__link">Contacto</a></li>
        </ul>

        <!-- CTAs -->
        <div class="cip-navbar__ctas">
            <a href="#postularme" class="cip-btn cip-btn--primary cip-btn--sm">Postularme al Programa</a>
            <a href="#staff" class="cip-btn cip-btn--outline-light cip-btn--sm">Sumarme al Staff</a>
        </div>

        <!-- Hamburger -->
        <button
            class="cip-navbar__hamburger"
            id="cipHamburger"
            type="button"
            aria-label="Abrir menú de navegación"
            aria-expanded="false"
            aria-controls="cipMobileMenu"
        >
            <span class="cip-navbar__hamburger-line"></span>
            <span class="cip-navbar__hamburger-line"></span>
            <span class="cip-navbar__hamburger-line"></span>
        </button>
    </div>
</nav>

<!-- ========== MOBILE MENU ========== -->
<div class="cip-mobile-menu" id="cipMobileMenu" aria-hidden="true">
    <div class="cip-mobile-menu__content">
        <ul class="cip-mobile-menu__links">
            <li><a href="#inicio" class="cip-mobile-menu__link">Inicio</a></li>
            <li><a href="#el-cip" class="cip-mobile-menu__link">El CIP</a></li>
            <li><a href="#programa" class="cip-mobile-menu__link">Programa</a></li>
            <li><a href="#nuestra-dinamica" class="cip-mobile-menu__link">Nuestra Dinámica</a></li>
            <li><a href="#contacto" class="cip-mobile-menu__link">Contacto</a></li>
        </ul>
        <div class="cip-mobile-menu__ctas">
            <a href="#postularme" class="cip-btn cip-btn--primary">Postularme al Programa</a>
            <a href="#staff" class="cip-btn cip-btn--outline">Sumarme al Staff</a>
        </div>
    </div>
</div>

<!-- ========== HERO SECTION ========== -->
<section class="cip-hero" id="inicio">
    <div class="cip-hero__bg">
        <div class="cip-hero__gradient"></div>
        <div class="cip-hero__pattern"></div>
        <div class="cip-hero__circle cip-hero__circle--1"></div>
        <div class="cip-hero__circle cip-hero__circle--2"></div>
        <div class="cip-hero__circle cip-hero__circle--3"></div>
    </div>

    <div class="cip-hero__content">
        <p class="cip-hero__tag" data-animate="fade-up">Centro de Innovación Pública</p>
        <h1 class="cip-hero__title" data-animate="fade-up" data-delay="100">
            Ideas que <span class="cip-hero__title-accent">transforman.</span>
        </h1>
        <p class="cip-hero__subtitle" data-animate="fade-up" data-delay="200">
            Capacitación de excelencia para los futuros líderes de la gestión pública local.
        </p>
        <div class="cip-hero__ctas" data-animate="fade-up" data-delay="300">
            <a href="#postularme" class="cip-btn cip-btn--primary cip-btn--lg">
                Postularme al Programa
                <svg class="cip-btn__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.167 10h11.666M10.833 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <a href="#staff" class="cip-btn cip-btn--outline-light cip-btn--lg">Sumarme al Staff</a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="cip-hero__scroll" data-animate="fade-in" data-delay="600">
        <span class="cip-hero__scroll-text">Descubrí más</span>
        <div class="cip-hero__scroll-indicator">
            <div class="cip-hero__scroll-dot"></div>
        </div>
    </div>
</section>

<!-- ========== SOBRE NOSOTROS / EL CIP ========== -->
<section class="cip-section cip-section--light" id="el-cip">
    <div class="cip-about">
        <!-- Decorative geometric accent -->
        <div class="cip-about__decoration" aria-hidden="true">
            <div class="cip-about__geo cip-about__geo--1"></div>
            <div class="cip-about__geo cip-about__geo--2"></div>
        </div>

        <div class="cip-about__container">
            <!-- Section header -->
            <div class="cip-about__header" data-animate="fade-up">
                <span class="cip-about__label">Sobre Nosotros</span>
                <h2 class="cip-section__title">El CIP</h2>
                <div class="cip-about__accent-line" aria-hidden="true"></div>
            </div>

            <!-- Institutional description -->
            <div class="cip-about__intro" data-animate="fade-up" data-delay="100">
                <p class="cip-about__description">
                    Somos un centro de investigación y desarrollo de políticas públicas orientadas a la eficiencia y la modernización del Estado. Creemos en el rigor de la planificación técnica, la libertad de ideas y la gestión transparente como motores para potenciar nuestro entorno. En el CIP trascendemos el análisis teórico para diseñar soluciones concretas y viables que nuestra comunidad demanda. Nos constituimos como un punto de encuentro académico y práctico, con el propósito de agrupar a los mejores talentos y capacitar a la próxima generación de líderes en la gestión pública local.
                </p>
            </div>

            <!-- Misión y Visión cards -->
            <div class="cip-about__cards">
                <!-- Misión -->
                <div class="cip-about__card" data-animate="fade-up" data-delay="200">
                    <div class="cip-about__card-accent" aria-hidden="true"></div>
                    <div class="cip-about__card-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M16 2L16 6M16 26L16 30M30 16H26M6 16H2M25.9 6.1L23.07 8.93M8.93 23.07L6.1 25.9M25.9 25.9L23.07 23.07M8.93 8.93L6.1 6.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="16" cy="16" r="7" stroke="currentColor" stroke-width="2"/>
                            <circle cx="16" cy="16" r="3" fill="currentColor"/>
                        </svg>
                    </div>
                    <h3 class="cip-about__card-title">Nuestra Misión</h3>
                    <p class="cip-about__card-text">
                        Proveer formación técnica de excelencia a los futuros líderes locales y diseñar políticas públicas innovadoras que impacten de manera directa en la optimización de la gestión y el desarrollo integral de Canelones.
                    </p>
                </div>

                <!-- Visión -->
                <div class="cip-about__card" data-animate="fade-up" data-delay="300">
                    <div class="cip-about__card-accent" aria-hidden="true"></div>
                    <div class="cip-about__card-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.5 16C2.5 16 7.5 6 16 6C24.5 6 29.5 16 29.5 16C29.5 16 24.5 26 16 26C7.5 26 2.5 16 2.5 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="16" cy="16" r="4.5" stroke="currentColor" stroke-width="2"/>
                            <circle cx="16" cy="16" r="1.5" fill="currentColor"/>
                            <path d="M16 2V4M26 4L24.5 6M6 4L7.5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="cip-about__card-title">Nuestra Visión</h3>
                    <p class="cip-about__card-text">
                        Consolidarnos como el centro de innovación pública de referencia en el departamento, reconocidos por nuestro rigor analítico y por impulsar un modelo de gestión estatal ágil, transparente y enfocado en el progreso y el pleno ejercicio de las libertades ciudadanas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cip-section" id="programa">
    <div class="cip-section__container">
        <h2 class="cip-section__title" data-animate="fade-up">Programa</h2>
        <p class="cip-section__placeholder">Contenido próximamente...</p>
    </div>
</section>

<section class="cip-section cip-section--light" id="nuestra-dinamica">
    <div class="cip-section__container">
        <h2 class="cip-section__title" data-animate="fade-up">Nuestra Dinámica</h2>
        <p class="cip-section__placeholder">Contenido próximamente...</p>
    </div>
</section>

<section class="cip-section" id="contacto">
    <div class="cip-section__container">
        <h2 class="cip-section__title" data-animate="fade-up">Contacto</h2>
        <p class="cip-section__placeholder">Contenido próximamente...</p>
    </div>
</section>

<?php wp_footer(); ?>
</body>
</html>
