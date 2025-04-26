<?php
/**
 * La clase principal del plugin.
 *
 * Clase que define la funcionalidad principal del plugin NovaStudio.
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * La clase principal del plugin.
 */
class NovaStudio {

    /**
     * Opciones del plugin
     *
     * @var array
     */
    private $options;

    /**
     * Constructor
     */
    public function __construct() {
        $this->options = get_option( 'novastudio_options', array() );
    }

    /**
     * Inicializar el plugin
     */
    public function init() {
        // Cargar hojas de estilo y scripts en el frontend
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // Agregar las variables CSS personalizadas al header
        add_action( 'wp_head', array( $this, 'output_custom_css_variables' ) );
        
        // Agregar el CSS personalizado
        add_action( 'wp_head', array( $this, 'output_custom_css' ) );
        
        // Inicializar los shortcodes
        $this->init_shortcodes();
    }
    
    /**
     * Cargar hojas de estilo y scripts
     */
    public function enqueue_assets() {
        // Estilos principales del plugin
        wp_enqueue_style( 
            'novastudio-main', 
            NOVASTUDIO_PLUGIN_URL . 'assets/css/novastudio.css', 
            array(), 
            NOVASTUDIO_VERSION 
        );
        
        // Scripts principales del plugin
        wp_enqueue_script( 
            'novastudio-main', 
            NOVASTUDIO_PLUGIN_URL . 'assets/js/novastudio.js', 
            array( 'jquery' ), 
            NOVASTUDIO_VERSION, 
            true 
        );
        
        // Pasar variables al JavaScript
        wp_localize_script( 
            'novastudio-main', 
            'novaStudioData', 
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'novastudio-nonce' ),
                'themeMode' => isset( $this->options['theme']['default_mode'] ) ? $this->options['theme']['default_mode'] : 'auto',
            )
        );
    }
    
    /**
     * Generar variables CSS personalizadas
     */
    public function output_custom_css_variables() {
        $colors = isset( $this->options['colors'] ) ? $this->options['colors'] : array();
        $typography = isset( $this->options['typography'] ) ? $this->options['typography'] : array();
        
        // Si no hay opciones, no hacer nada
        if ( empty( $colors ) && empty( $typography ) ) {
            return;
        }
        
        // Iniciar el bloque de estilos
        echo "<style id='novastudio-custom-properties'>\n";
        echo ":root {\n";
        
        // Variables de colores
        if ( ! empty( $colors ) ) {
            foreach ( $colors as $key => $value ) {
                if ( empty( $value ) ) continue;
                echo "    --color-{$key}: {$value};\n";
            }
        }
        
        // Variables de tipografía
        if ( ! empty( $typography ) ) {
            if ( ! empty( $typography['font_primary'] ) ) {
                echo "    --font-primary: {$typography['font_primary']};\n";
            }
            
            if ( ! empty( $typography['font_secondary'] ) ) {
                echo "    --font-secondary: {$typography['font_secondary']};\n";
            }
            
            if ( ! empty( $typography['base_size'] ) ) {
                echo "    --font-size-base: {$typography['base_size']};\n";
            }
        }
        
        // Cerrar el bloque root
        echo "}\n";
        echo "</style>\n";
    }
    
    /**
     * Generar CSS personalizado
     */
    public function output_custom_css() {
        // Si no hay CSS personalizado, no hacer nada
        if ( empty( $this->options['theme']['custom_css'] ) ) {
            return;
        }
        
        // Sanitizar el CSS personalizado
        $custom_css = wp_strip_all_tags( $this->options['theme']['custom_css'] );
        
        // Generar el bloque de estilos
        echo "<style id='novastudio-custom-css'>\n";
        echo $custom_css . "\n";
        echo "</style>\n";
    }
    
    /**
     * Inicializar los shortcodes
     */
    private function init_shortcodes() {
        // Array para rastrear funciones ya declaradas
        $declared_functions = array();
        
        // Incluir el archivo de funciones helpers primero
        if (file_exists(NOVASTUDIO_PLUGIN_DIR . 'includes/helpers/helpers.php')) {
            require_once NOVASTUDIO_PLUGIN_DIR . 'includes/helpers/helpers.php';
        }
        
        // Definir orden de carga para archivos con prioridad
        $priority_files = array(
            'saas-buttons.php', // Cargar primero los botones para evitar conflictos
        );
        
        // Cargar primero los archivos prioritarios
        foreach ($priority_files as $priority_file) {
            $file_path = NOVASTUDIO_PLUGIN_DIR . 'includes/shortcodes/' . $priority_file;
            if (file_exists($file_path)) {
                $this->load_shortcode_file($file_path, $declared_functions);
            }
        }
        
        // Obtener todos los archivos de shortcodes
        $shortcode_files = glob(NOVASTUDIO_PLUGIN_DIR . 'includes/shortcodes/*.php');
        if (!empty($shortcode_files)) {
            foreach ($shortcode_files as $file) {
                // Omitir archivos de índice y los que ya se cargaron con prioridad
                $basename = basename($file);
                if ($basename !== 'index.php' && !in_array($basename, $priority_files)) {
                    $this->load_shortcode_file($file, $declared_functions);
                }
            }
        }
    }
    
    /**
     * Cargar un archivo de shortcode verificando funciones duplicadas
     *
     * @param string $file Ruta del archivo a cargar
     * @param array &$declared_functions Array de funciones ya declaradas
     */
    private function load_shortcode_file($file, &$declared_functions) {
        // Leer el contenido del archivo
        $file_content = file_get_contents($file);
        
        // Buscar todas las declaraciones de funciones
        preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/m', $file_content, $matches);
        
        if (!empty($matches[1])) {
            $potential_conflicts = false;
            
            foreach ($matches[1] as $function_name) {
                // Si la función ya está declarada, es un conflicto potencial
                if (in_array($function_name, $declared_functions)) {
                    error_log('NovaStudio: Posible conflicto de función - ' . $function_name . ' en ' . basename($file));
                    $potential_conflicts = true;
                } else {
                    // Registrar esta función para futuras comprobaciones
                    $declared_functions[] = $function_name;
                }
            }
            
            // Si hay conflictos, modificar el archivo (en un entorno de producción
            // esto podría ser más complejo o mostrar una advertencia en el admin)
            if ($potential_conflicts) {
                // En producción, aquí podría hacerse un manejo más sofisticado,
                // pero por ahora solo registramos un error para que el admin lo vea
                error_log('NovaStudio: Se encontraron posibles conflictos en ' . basename($file) . 
                          '. Considere usar nombres de función específicos para evitar colisiones.');
            }
        }
        
        // Cargar el archivo
        require_once $file;
    }
    
    /**
     * Obtener una opción específica del plugin
     *
     * @param string $section Sección de la opción.
     * @param string $key     Clave de la opción.
     * @param mixed  $default Valor por defecto si la opción no existe.
     *
     * @return mixed
     */
    public function get_option( $section, $key, $default = '' ) {
        if ( isset( $this->options[ $section ][ $key ] ) ) {
            return $this->options[ $section ][ $key ];
        }
        
        return $default;
    }
}