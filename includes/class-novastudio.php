<?php
/**
 * Clase principal del plugin NovaStudio
 *
 * @package NovaStudio
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Clase principal del plugin NovaStudio.
 */
class NovaStudio {
    
    /**
     * Inicializa el plugin.
     */
    public function init() {
        // Registrar scripts y estilos
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        
        // Registrar shortcodes
        add_shortcode( 'novastudio_dashboard', array( $this, 'dashboard_shortcode' ) );
        
        // Crear página de ejemplo al activar el plugin
        register_activation_hook( NOVASTUDIO_PLUGIN_BASENAME, array( $this, 'create_example_pages' ) );
        
        // Añadir funciones para personalización del tema
        add_action( 'after_setup_theme', array( $this, 'theme_support' ) );
        add_filter( 'body_class', array( $this, 'add_body_classes' ) );
        
        // Hook para crear la página de ejemplo aunque el plugin ya esté activado
        add_action( 'init', array( $this, 'maybe_create_example_pages' ) );
    }
    
    /**
     * Registra scripts y estilos necesarios.
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'novastudio-styles',
            NOVASTUDIO_PLUGIN_URL . 'assets/css/novastudio.css',
            array(),
            NOVASTUDIO_VERSION
        );
        
        wp_enqueue_script(
            'novastudio-scripts',
            NOVASTUDIO_PLUGIN_URL . 'assets/js/novastudio.js',
            array( 'jquery' ),
            NOVASTUDIO_VERSION,
            true
        );
        
        // Scripts específicos para el dashboard neo-brutalista
        if ( is_page( 'dashboard-ejemplo' ) ) {
            wp_enqueue_script(
                'novastudio-dashboard',
                NOVASTUDIO_PLUGIN_URL . 'assets/js/dashboard-neo.js',
                array( 'jquery' ),
                NOVASTUDIO_VERSION,
                true
            );
        }
    }
    
    /**
     * Shortcode para mostrar un dashboard de ejemplo.
     *
     * @param array $atts Atributos del shortcode.
     * @return string Contenido HTML del dashboard.
     */
    public function dashboard_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'type' => 'neo-brutalist', // Por defecto, estilo neo-brutalista
            ),
            $atts,
            'novastudio_dashboard'
        );
        
        ob_start();
        include NOVASTUDIO_PLUGIN_DIR . 'templates/dashboard-' . sanitize_key( $atts['type'] ) . '.php';
        return ob_get_clean();
    }
    
    /**
     * Añade soporte para funcionalidades del tema.
     */
    public function theme_support() {
        // Añadir soporte para personalización de tema oscuro/claro
        if ( current_theme_supports( 'dark-editor-style' ) ) {
            add_theme_support( 'editor-styles' );
            add_editor_style( 'assets/css/editor-dark.css' );
        }
    }
    
    /**
     * Añade clases al body según configuración.
     *
     * @param array $classes Clases actuales del body.
     * @return array Clases modificadas.
     */
    public function add_body_classes( $classes ) {
        $options = get_option( 'novastudio_options', array() );
        
        // Añadir clase si existe la configuración
        if ( isset( $options['theme']['custom_body_class'] ) && ! empty( $options['theme']['custom_body_class'] ) ) {
            $classes[] = sanitize_html_class( $options['theme']['custom_body_class'] );
        }
        
        // Añadir clase para estilos neo-brutalistas
        if ( is_page( 'dashboard-ejemplo' ) || is_page( 'ejemplo-random' ) ) {
            $classes[] = 'novastudio-neo-brutalist';
        }
        
        return $classes;
    }
    
    /**
     * Crea páginas de ejemplo al activar el plugin.
     */
    public function create_example_pages() {
        $this->create_dashboard_example_page();
    }
    
    /**
     * Crea las páginas de ejemplo si no existen, incluso después de activación.
     */
    public function maybe_create_example_pages() {
        // Comprobar si la opción indica que ya se han creado las páginas
        $created = get_option( 'novastudio_pages_created', false );
        
        if ( ! $created ) {
            $this->create_dashboard_example_page();
            update_option( 'novastudio_pages_created', true );
        }
    }
    
    /**
     * Crea una página de ejemplo de dashboard.
     */
    private function create_dashboard_example_page() {
        // Comprobar si ya existe la página
        $dashboard_page = get_page_by_path( 'dashboard-ejemplo' );
        
        if ( ! $dashboard_page ) {
            // Crear la página
            $page_id = wp_insert_post( array(
                'post_title'     => 'Dashboard Ejemplo',
                'post_name'      => 'dashboard-ejemplo',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_content'   => '<!-- wp:shortcode -->[novastudio_dashboard type="neo-brutalist"]<!-- /wp:shortcode -->',
                'comment_status' => 'closed',
                'page_template'  => 'templates/canvas.php',
            ) );
        }
    }
}
