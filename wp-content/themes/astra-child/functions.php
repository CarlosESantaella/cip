<?php
/**
 * Astra Child - CIP
 *
 * @package AstraChildCIP
 */

// ──────────────────────────────────────────────
// 1. Enqueue parent + child styles
// ──────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'astra_child_cip_enqueue_styles' );
function astra_child_cip_enqueue_styles() {
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'astra' )->get( 'Version' )
    );

    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_uri(),
        array( 'astra-parent-style' ),
        wp_get_theme()->get( 'Version' )
    );
}

// ──────────────────────────────────────────────
// 1b. Register navigation menus
// ──────────────────────────────────────────────
add_action( 'after_setup_theme', 'astra_child_cip_register_menus' );
function astra_child_cip_register_menus() {
    register_nav_menus( array(
        'cip-landing-nav'  => 'CIP Landing — Navegación',
        'cip-landing-ctas' => 'CIP Landing — Botones CTA',
    ) );
}

/**
 * Walker for CIP Landing nav links.
 * Outputs clean <li><a class="..."> markup matching the existing CSS.
 */
class CIP_Nav_Walker extends Walker_Nav_Menu {

    private $link_class;

    public function __construct( $link_class = '' ) {
        $this->link_class = $link_class;
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $output .= '<li>';
        $output .= '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $this->link_class ) . '">';
        $output .= esc_html( $item->title );
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</a></li>';
    }
}

/**
 * Walker for CIP Landing CTA buttons.
 * Each menu item's CSS Classes field (admin) is used for button styling.
 * Fallback: first item gets primary style, rest get outline style.
 */
class CIP_CTA_Walker extends Walker_Nav_Menu {

    private $base_classes;
    private $counter = 0;
    private $styles;

    /**
     * @param array $styles Ordered button class sets, e.g. ['cip-btn cip-btn--primary', 'cip-btn cip-btn--outline-light']
     */
    public function __construct( $styles = array() ) {
        $this->styles = $styles;
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        // Use admin CSS classes if set, otherwise fall back to ordered styles
        $admin_classes = trim( implode( ' ', array_filter( (array) $item->classes ) ) );
        if ( ! empty( $admin_classes ) && strpos( $admin_classes, 'cip-btn' ) !== false ) {
            $classes = $admin_classes;
        } elseif ( isset( $this->styles[ $this->counter ] ) ) {
            $classes = $this->styles[ $this->counter ];
        } else {
            $classes = 'cip-btn cip-btn--primary';
        }
        $this->counter++;

        $output .= '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $classes ) . '">';
        $output .= esc_html( $item->title );
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</a>';
    }
}

// ──────────────────────────────────────────────
// 2. Detect if current page is the CIP Landing Page
// ──────────────────────────────────────────────
function astra_child_cip_is_landing_page() {
    $landing_page_id = 1563;

    // Elementor preview/editor context — detect early via query param
    if ( isset( $_GET['elementor-preview'] ) && (int) $_GET['elementor-preview'] === $landing_page_id ) {
        return true;
    }

    if ( ! is_page() ) {
        return false;
    }

    return ( get_the_ID() === $landing_page_id || get_queried_object_id() === $landing_page_id );
}

// ──────────────────────────────────────────────
// 3. Enqueue landing page assets (Google Fonts, CSS, JS)
//    Works with both old template-landing.php AND Elementor Canvas
// ──────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'astra_child_cip_landing_assets' );
function astra_child_cip_landing_assets() {
    // Support both legacy template and Elementor Canvas
    $is_landing = is_page_template( 'template-landing.php' ) || astra_child_cip_is_landing_page();

    if ( ! $is_landing ) {
        return;
    }

    // Google Fonts: Inter
    wp_enqueue_style(
        'cip-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Landing CSS (navbar + effects)
    wp_enqueue_style(
        'cip-landing-css',
        get_stylesheet_directory_uri() . '/assets/css/landing.css',
        array( 'cip-google-fonts' ),
        filemtime( get_stylesheet_directory() . '/assets/css/landing.css' )
    );

    // Landing JS (navbar scroll, mobile menu, smooth scroll, form toggle)
    wp_enqueue_script(
        'cip-landing-js',
        get_stylesheet_directory_uri() . '/assets/js/landing.js',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/js/landing.js' ),
        true
    );
}

// ──────────────────────────────────────────────
// 3b. Enqueue landing CSS inside Elementor editor & preview
//     wp_enqueue_scripts runs in the preview iframe but can miss
//     the page context — these hooks guarantee loading in the editor.
// ──────────────────────────────────────────────
add_action( 'elementor/preview/enqueue_styles', 'astra_child_cip_elementor_preview_assets' );
function astra_child_cip_elementor_preview_assets() {
    $page_id = 0;
    if ( isset( $_GET['elementor-preview'] ) ) {
        $page_id = (int) $_GET['elementor-preview'];
    }
    if ( $page_id !== 1563 ) {
        return;
    }

    wp_enqueue_style(
        'cip-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'cip-landing-css',
        get_stylesheet_directory_uri() . '/assets/css/landing.css',
        array( 'cip-google-fonts' ),
        filemtime( get_stylesheet_directory() . '/assets/css/landing.css' )
    );

    // Also load JS for toggle preview in editor
    wp_enqueue_script(
        'cip-landing-js',
        get_stylesheet_directory_uri() . '/assets/js/landing.js',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/js/landing.js' ),
        true
    );
}

add_action( 'elementor/editor/after_enqueue_styles', 'astra_child_cip_elementor_editor_styles' );
function astra_child_cip_elementor_editor_styles() {
    $post_id = 0;
    if ( isset( $_GET['post'] ) ) {
        $post_id = (int) $_GET['post'];
    }
    if ( $post_id !== 1563 ) {
        return;
    }

    wp_enqueue_style(
        'cip-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'cip-landing-css',
        get_stylesheet_directory_uri() . '/assets/css/landing.css',
        array( 'cip-google-fonts' ),
        filemtime( get_stylesheet_directory() . '/assets/css/landing.css' )
    );
}

// ──────────────────────────────────────────────
// 3c. Protect landing page Elementor data from accidental corruption
//     When Elementor can't load the structured data (e.g. missing CSS),
//     it falls back to post_content (plain HTML) and saves a flattened
//     version that destroys all sections. This hook blocks that.
// ──────────────────────────────────────────────
add_action( 'elementor/document/before_save', 'astra_child_cip_protect_elementor_data', 10, 2 );
function astra_child_cip_protect_elementor_data( $document ) {
    $page_id = $document->get_main_id();
    if ( (int) $page_id !== 1563 ) {
        return;
    }

    global $wpdb;

    // Get old data size directly from DB (bypass cache)
    $old_size = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT LENGTH(meta_value) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data' LIMIT 1",
        $page_id
    ) );

    // If old data is substantial (> 20KB = structured page with all sections)
    // and new data would be less than 30% of old size, block the save.
    if ( $old_size > 20000 ) {
        $new_data = $document->get_elements_data();
        $new_size = strlen( wp_json_encode( $new_data ) );

        if ( $new_size < $old_size * 0.3 ) {
            wp_die(
                '<h2>CIP — Protección de datos</h2>' .
                '<p>Se detectó que los datos de Elementor están por guardarse en un formato reducido ' .
                '(' . number_format( $new_size ) . ' bytes vs ' . number_format( $old_size ) . ' bytes original). ' .
                'Esto podría destruir la estructura de la landing page.</p>' .
                '<p><strong>El guardado fue bloqueado.</strong> Recargá el editor (Ctrl+Shift+R) e intentá de nuevo.</p>' .
                '<p><a href="' . admin_url() . '">Volver al Dashboard</a></p>',
                'Guardado bloqueado — CIP',
                array( 'back_link' => true, 'response' => 403 )
            );
        }
    }
}

// ──────────────────────────────────────────────
// 3d. Favicon for the landing page
// ──────────────────────────────────────────────
add_action( 'wp_head', 'astra_child_cip_landing_favicon' );
function astra_child_cip_landing_favicon() {
    if ( ! astra_child_cip_is_landing_page() ) {
        return;
    }

    $img_url = get_stylesheet_directory_uri() . '/assets/img';
    ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $img_url; ?>/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $img_url; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $img_url; ?>/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $img_url; ?>/apple-touch-icon.png">
    <?php
}

// ──────────────────────────────────────────────
// 4. Inject custom navbar on Elementor Canvas landing page
//    via wp_body_open hook (fires right after <body> tag)
// ──────────────────────────────────────────────
add_action( 'wp_body_open', 'astra_child_cip_inject_navbar' );
function astra_child_cip_inject_navbar() {
    // Only inject on Elementor Canvas landing page (not the old template)
    if ( is_page_template( 'template-landing.php' ) ) {
        return; // Old template has its own navbar HTML
    }

    if ( ! astra_child_cip_is_landing_page() ) {
        return;
    }

    $logo_url = esc_url( content_url( '/uploads/2026/02/logo-1_transparent.png' ) );
    ?>
    <!-- ========== CIP NAVBAR (injected via child theme) ========== -->
    <nav class="cip-navbar" id="cipNavbar" role="navigation" aria-label="Navegación principal">
        <div class="cip-navbar__container">
            <!-- Logo -->
            <a href="#inicio" class="cip-navbar__logo" aria-label="CIP - Inicio">
                <img
                    src="<?php echo $logo_url; ?>"
                    alt="CIP - Centro de Innovación Pública"
                    class="cip-navbar__logo-img"
                    width="180"
                    height="50"
                >
            </a>

            <!-- Desktop Navigation -->
            <?php
            wp_nav_menu( array(
                'theme_location' => 'cip-landing-nav',
                'container'      => false,
                'menu_class'     => 'cip-navbar__links',
                'menu_id'        => 'cipNavLinks',
                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'walker'         => new CIP_Nav_Walker( 'cip-navbar__link' ),
                'depth'          => 1,
                'fallback_cb'    => function() {
                    ?>
                    <ul class="cip-navbar__links" id="cipNavLinks">
                        <li><a href="#inicio" class="cip-navbar__link active">Inicio</a></li>
                        <li><a href="#el-cip" class="cip-navbar__link">El CIP</a></li>
                        <li><a href="#programa" class="cip-navbar__link">Programa</a></li>
                        <li><a href="#nuestra-dinamica" class="cip-navbar__link">Nuestra Dinámica</a></li>
                        <li><a href="#contacto" class="cip-navbar__link">Contacto</a></li>
                    </ul>
                    <?php
                },
            ) );
            ?>

            <!-- CTAs -->
            <div class="cip-navbar__ctas">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'cip-landing-ctas',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'walker'         => new CIP_CTA_Walker( array(
                        'cip-btn cip-btn--primary cip-btn--sm',
                        'cip-btn cip-btn--outline-light cip-btn--sm',
                    ) ),
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        ?>
                        <a href="#postularme" class="cip-btn cip-btn--primary cip-btn--sm">Postularme al Programa</a>
                        <a href="#staff" class="cip-btn cip-btn--outline-light cip-btn--sm">Sumarme al Staff</a>
                        <?php
                    },
                ) );
                ?>
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
            <?php
            wp_nav_menu( array(
                'theme_location' => 'cip-landing-nav',
                'container'      => false,
                'menu_class'     => 'cip-mobile-menu__links',
                'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                'walker'         => new CIP_Nav_Walker( 'cip-mobile-menu__link' ),
                'depth'          => 1,
                'fallback_cb'    => function() {
                    ?>
                    <ul class="cip-mobile-menu__links">
                        <li><a href="#inicio" class="cip-mobile-menu__link">Inicio</a></li>
                        <li><a href="#el-cip" class="cip-mobile-menu__link">El CIP</a></li>
                        <li><a href="#programa" class="cip-mobile-menu__link">Programa</a></li>
                        <li><a href="#nuestra-dinamica" class="cip-mobile-menu__link">Nuestra Dinámica</a></li>
                        <li><a href="#contacto" class="cip-mobile-menu__link">Contacto</a></li>
                    </ul>
                    <?php
                },
            ) );
            ?>
            <div class="cip-mobile-menu__ctas">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'cip-landing-ctas',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'walker'         => new CIP_CTA_Walker( array(
                        'cip-btn cip-btn--primary',
                        'cip-btn cip-btn--outline',
                    ) ),
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        ?>
                        <a href="#postularme" class="cip-btn cip-btn--primary">Postularme al Programa</a>
                        <a href="#staff" class="cip-btn cip-btn--outline">Sumarme al Staff</a>
                        <?php
                    },
                ) );
                ?>
            </div>
        </div>
    </div>
    <?php
}

// ──────────────────────────────────────────────
// 5. Add 'cip-landing' body class on the landing page
//    (needed for CSS scoping, even on Elementor Canvas)
// ──────────────────────────────────────────────
add_filter( 'body_class', 'astra_child_cip_body_class' );
function astra_child_cip_body_class( $classes ) {
    if ( astra_child_cip_is_landing_page() ) {
        $classes[] = 'cip-landing';
    }
    return $classes;
}

// ──────────────────────────────────────────────
// 6. Inject decorative elements via wp_footer
//    (floating circles + scroll indicator — outside Elementor
//     so they don't block editing)
// ──────────────────────────────────────────────
add_action( 'wp_footer', 'astra_child_cip_inject_decorations' );
function astra_child_cip_inject_decorations() {
    if ( is_page_template( 'template-landing.php' ) ) {
        return; // Legacy template has its own HTML
    }

    if ( ! astra_child_cip_is_landing_page() ) {
        return;
    }
    ?>
    <!-- CIP Hero Decorations (injected outside Elementor to avoid edit-blocking) -->
    <div class="cip-hero__decorations" aria-hidden="true">
        <div class="cip-hero__circle cip-hero__circle--2"></div>
        <div class="cip-hero__circle cip-hero__circle--3"></div>
    </div>
    <div class="cip-hero__scroll-wrap" aria-hidden="true">
        <div class="cip-hero__scroll">
            <span class="cip-hero__scroll-text">Descubrí más</span>
            <div class="cip-hero__scroll-indicator">
                <div class="cip-hero__scroll-dot"></div>
            </div>
        </div>
    </div>
    <style>
        /* Position decorations relative to the hero section */
        .cip-hero__decorations {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .cip-hero__scroll-wrap {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            pointer-events: none;
            z-index: 2;
        }
        /* Hide decorations when scrolled past the hero */
        .cip-hero__decorations,
        .cip-hero__scroll-wrap {
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .cip-hero__decorations.hidden,
        .cip-hero__scroll-wrap.hidden {
            opacity: 0;
        }
    </style>
    <script>
        (function() {
            var deco = document.querySelector('.cip-hero__decorations');
            var scroll = document.querySelector('.cip-hero__scroll-wrap');
            if (!deco) return;
            function checkHeroVisible() {
                var hero = document.querySelector('.cip-hero');
                if (!hero) return;
                var rect = hero.getBoundingClientRect();
                var pastHero = rect.bottom < 0;
                deco.classList.toggle('hidden', pastHero);
                if (scroll) scroll.classList.toggle('hidden', pastHero);
            }
            window.addEventListener('scroll', checkHeroVisible, { passive: true });
            checkHeroVisible();
        })();
    </script>
    <?php
}

