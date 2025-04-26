<?php
/**
 * Clase de administración del plugin NovaStudio.
 *
 * Define toda la funcionalidad del área de administración del plugin.
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Clase de administración del plugin.
 */
class NovaStudio_Admin {

    /**
     * Opciones del plugin
     *
     * @var array
     */
    private $options;

    /**
     * Hook de la página del plugin
     *
     * @var string
     */
    private $plugin_page;

    /**
     * Constructor
     */
    public function __construct() {
        $this->options = get_option( 'novastudio_options', array() );
    }

    /**
     * Inicializar la clase
     */
    public function init() {
        // Agregar menú en el panel de administración
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Registrar las opciones del plugin
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Cargar hojas de estilo y scripts en el admin
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // Agregar enlaces de menú personalizables
        add_filter( 'novastudio_admin_tabs', array( $this, 'setup_admin_tabs' ), 10, 1 );
        
        // Registrar AJAX handlers
        add_action( 'wp_ajax_novastudio_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_novastudio_reset_settings', array( $this, 'ajax_reset_settings' ) );
    }

    /**
     * Agregar menú en el panel de administración
     */
    public function add_admin_menu() {
        $this->plugin_page = add_menu_page(
            __( 'NovaStudio', 'nova-ui-studio' ),
            __( 'NovaStudio', 'nova-ui-studio' ),
            'manage_options',
            'novastudio-options',
            array( $this, 'render_options_page' ),
            'dashicons-art',
            59
        );
        
        // Submenús
        add_submenu_page(
            'novastudio-options',
            __( 'General', 'nova-ui-studio' ),
            __( 'General', 'nova-ui-studio' ),
            'manage_options',
            'novastudio-options',
            array( $this, 'render_options_page' )
        );
        
        add_submenu_page(
            'novastudio-options',
            __( 'Colores', 'nova-ui-studio' ),
            __( 'Colores', 'nova-ui-studio' ),
            'manage_options',
            'admin.php?page=novastudio-options&tab=colors',
            ''
        );
        
        add_submenu_page(
            'novastudio-options',
            __( 'Tipografía', 'nova-ui-studio' ),
            __( 'Tipografía', 'nova-ui-studio' ),
            'manage_options',
            'admin.php?page=novastudio-options&tab=typography',
            ''
        );
        
        add_submenu_page(
            'novastudio-options',
            __( 'Sidebar', 'nova-ui-studio' ),
            __( 'Sidebar', 'nova-ui-studio' ),
            'manage_options',
            'admin.php?page=novastudio-options&tab=sidebar',
            ''
        );
        
        add_submenu_page(
            'novastudio-options',
            __( 'Header', 'nova-ui-studio' ),
            __( 'Header', 'nova-ui-studio' ),
            'manage_options',
            'admin.php?page=novastudio-options&tab=header',
            ''
        );
        
        add_submenu_page(
            'novastudio-options',
            __( 'CSS Personalizado', 'nova-ui-studio' ),
            __( 'CSS Personalizado', 'nova-ui-studio' ),
            'manage_options',
            'admin.php?page=novastudio-options&tab=custom_css',
            ''
        );
    }

    /**
     * Registrar las opciones del plugin
     */
    public function register_settings() {
        register_setting(
            'novastudio_options_group',
            'novastudio_options',
            array( $this, 'sanitize_options' )
        );
    }

    /**
     * Sanitiza las opciones antes de guardarlas
     *
     * @param array $options Las opciones a sanitizar.
     * @return array
     */
    public function sanitize_options( $options ) {
        // Sanitización de opciones
        if ( isset( $options['theme']['custom_css'] ) ) {
            $options['theme']['custom_css'] = wp_strip_all_tags( $options['theme']['custom_css'] );
        }
        
        // Devolver las opciones sanitizadas
        return $options;
    }

    /**
     * Cargar hojas de estilo y scripts para el admin
     *
     * @param string $hook Hook de la página actual.
     */
    public function enqueue_admin_assets( $hook ) {
        // Solo cargar en las páginas del plugin
        if ( $hook !== $this->plugin_page && strpos( $hook, 'novastudio' ) === false ) {
            return;
        }
        
        // Estilos para el admin
        wp_enqueue_style( 
            'novastudio-admin', 
            NOVASTUDIO_PLUGIN_URL . 'admin/css/novastudio-admin.css', 
            array(), 
            NOVASTUDIO_VERSION 
        );
        
        // Scripts para el admin
        wp_enqueue_script( 
            'novastudio-admin', 
            NOVASTUDIO_PLUGIN_URL . 'admin/js/novastudio-admin.js', 
            array( 'jquery', 'wp-color-picker', 'jquery-ui-tabs', 'jquery-ui-sortable' ), 
            NOVASTUDIO_VERSION, 
            true 
        );
        
        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        
        // Media uploader
        wp_enqueue_media();
        
        // Code editor para CSS
        if ( function_exists( 'wp_enqueue_code_editor' ) ) {
            $settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
            if ( ! empty( $settings ) ) {
                wp_add_inline_script(
                    'novastudio-admin',
                    sprintf( 'jQuery( function() { if(document.getElementById("custom_css")) { wp.codeEditor.initialize( "custom_css", %s ); } } );', wp_json_encode( $settings ) )
                );
            }
        }
        
        // Pasar variables al JavaScript
        wp_localize_script( 
            'novastudio-admin', 
            'novaStudioAdmin', 
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'novastudio-admin-nonce' ),
                'strings' => array(
                    'saveSuccess' => __( 'Configuración guardada correctamente.', 'nova-ui-studio' ),
                    'saveError' => __( 'Error al guardar la configuración.', 'nova-ui-studio' ),
                    'resetConfirm' => __( '¿Estás seguro de que deseas restablecer todas las configuraciones a sus valores predeterminados? Esta acción no se puede deshacer.', 'nova-ui-studio' ),
                    'resetSuccess' => __( 'Configuración restablecida correctamente.', 'nova-ui-studio' ),
                    'resetError' => __( 'Error al restablecer la configuración.', 'nova-ui-studio' ),
                ),
            )
        );
    }

    /**
     * Configurar las pestañas del panel de administración
     *
     * @param array $tabs Las pestañas actuales.
     * @return array
     */
    public function setup_admin_tabs( $tabs ) {
        $default_tabs = array(
            'general' => array(
                'label' => __( 'General', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_general_tab' ),
            ),
            'colors' => array(
                'label' => __( 'Colores', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_colors_tab' ),
            ),
            'typography' => array(
                'label' => __( 'Tipografía', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_typography_tab' ),
            ),
            'sidebar' => array(
                'label' => __( 'Sidebar', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_sidebar_tab' ),
            ),
            'header' => array(
                'label' => __( 'Header', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_header_tab' ),
            ),
            'custom_css' => array(
                'label' => __( 'CSS Personalizado', 'nova-ui-studio' ),
                'callback' => array( $this, 'render_custom_css_tab' ),
            ),
        );
        
        return array_merge( $default_tabs, $tabs );
    }

    /**
     * Renderizar la página de opciones
     */
    public function render_options_page() {
        // Verificar permisos
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Obtener la pestaña activa
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
        
        // Obtener todas las pestañas
        $tabs = apply_filters( 'novastudio_admin_tabs', array() );
        
        // Asegurarse de que la pestaña activa existe
        if ( ! isset( $tabs[ $active_tab ] ) ) {
            $active_tab = 'general';
        }
        
        // Iniciar la salida
        ?>
        <div class="wrap novastudio-options-page">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <div class="novastudio-header">
                <img src="<?php echo esc_url( NOVASTUDIO_PLUGIN_URL . 'assets/images/novastudio-logo.png' ); ?>" alt="NovaStudio" class="novastudio-logo">
                <div class="novastudio-version">
                    <?php printf( __( 'Versión %s', 'nova-ui-studio' ), NOVASTUDIO_VERSION ); ?>
                </div>
            </div>
            
            <h2 class="nav-tab-wrapper wp-clearfix">
                <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                    <a href="?page=novastudio-options&tab=<?php echo esc_attr( $tab_key ); ?>" class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $tab['label'] ); ?>
                    </a>
                <?php endforeach; ?>
            </h2>
            
            <div class="novastudio-tab-content">
                <form method="post" action="options.php" id="novastudio-options-form">
                    <?php settings_fields( 'novastudio_options_group' ); ?>
                    
                    <div class="novastudio-tab-panel" id="tab-<?php echo esc_attr( $active_tab ); ?>">
                        <?php
                        // Llamar al callback de la pestaña
                        if ( isset( $tabs[ $active_tab ]['callback'] ) && is_callable( $tabs[ $active_tab ]['callback'] ) ) {
                            call_user_func( $tabs[ $active_tab ]['callback'] );
                        }
                        ?>
                    </div>
                    
                    <div class="novastudio-actions">
                        <?php submit_button( __( 'Guardar Cambios', 'nova-ui-studio' ), 'primary', 'submit', false ); ?>
                        <button type="button" class="button button-secondary" id="novastudio-reset-settings">
                            <?php _e( 'Restablecer a Valores Predeterminados', 'nova-ui-studio' ); ?>
                        </button>
                    </div>
                </form>
                
                <div class="novastudio-preview">
                    <h3><?php _e( 'Vista Previa', 'nova-ui-studio' ); ?></h3>
                    <div class="novastudio-preview-frame">
                        <iframe id="novastudio-preview-iframe" src="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'Vista Previa', 'nova-ui-studio' ); ?>"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizar la pestaña de Configuración General
     */
    public function render_general_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'Configuración General', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Configure los ajustes generales del tema.', 'nova-ui-studio' ); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="theme_mode"><?php _e( 'Modo de Tema por Defecto', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[theme][default_mode]" id="theme_mode">
                        <option value="auto" <?php selected( isset( $this->options['theme']['default_mode'] ) ? $this->options['theme']['default_mode'] : 'auto', 'auto' ); ?>>
                            <?php _e( 'Automático (según preferencias del sistema)', 'nova-ui-studio' ); ?>
                        </option>
                        <option value="light" <?php selected( isset( $this->options['theme']['default_mode'] ) ? $this->options['theme']['default_mode'] : 'auto', 'light' ); ?>>
                            <?php _e( 'Claro', 'nova-ui-studio' ); ?>
                        </option>
                        <option value="dark" <?php selected( isset( $this->options['theme']['default_mode'] ) ? $this->options['theme']['default_mode'] : 'auto', 'dark' ); ?>>
                            <?php _e( 'Oscuro', 'nova-ui-studio' ); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione el modo de tema por defecto. El modo automático respeta las preferencias del sistema del usuario.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Renderizar la pestaña de Colores
     */
    public function render_colors_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'Configuración de Colores', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Personalice los colores principales del tema.', 'nova-ui-studio' ); ?></p>
        
        <div class="novastudio-color-presets">
            <h3><?php _e( 'Presets de Color', 'nova-ui-studio' ); ?></h3>
            <div class="novastudio-presets-grid">
                <div class="novastudio-preset-item" data-preset="default">
                    <div class="novastudio-preset-colors">
                        <span style="background-color: #FF6B6B;"></span>
                        <span style="background-color: #4ECDC4;"></span>
                        <span style="background-color: #FFE66D;"></span>
                    </div>
                    <div class="novastudio-preset-name"><?php _e( 'Por defecto', 'nova-ui-studio' ); ?></div>
                </div>
                <div class="novastudio-preset-item" data-preset="calm">
                    <div class="novastudio-preset-colors">
                        <span style="background-color: #6B9BFF;"></span>
                        <span style="background-color: #4ECDC4;"></span>
                        <span style="background-color: #C3E88D;"></span>
                    </div>
                    <div class="novastudio-preset-name"><?php _e( 'Calma', 'nova-ui-studio' ); ?></div>
                </div>
                <div class="novastudio-preset-item" data-preset="vibrant">
                    <div class="novastudio-preset-colors">
                        <span style="background-color: #FF6E9C;"></span>
                        <span style="background-color: #8A7AFF;"></span>
                        <span style="background-color: #FFC53D;"></span>
                    </div>
                    <div class="novastudio-preset-name"><?php _e( 'Vibrante', 'nova-ui-studio' ); ?></div>
                </div>
                <div class="novastudio-preset-item" data-preset="earthy">
                    <div class="novastudio-preset-colors">
                        <span style="background-color: #E07A5F;"></span>
                        <span style="background-color: #81B29A;"></span>
                        <span style="background-color: #F2CC8F;"></span>
                    </div>
                    <div class="novastudio-preset-name"><?php _e( 'Tierra', 'nova-ui-studio' ); ?></div>
                </div>
            </div>
        </div>
        
        <table class="form-table novastudio-colors-table">
            <tr>
                <th scope="row">
                    <label for="color_primary"><?php _e( 'Color Primario', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][primary]" id="color_primary" value="<?php echo isset( $this->options['colors']['primary'] ) ? esc_attr( $this->options['colors']['primary'] ) : '#FF6B6B'; ?>" data-default-color="#FF6B6B" />
                    <p class="description">
                        <?php _e( 'Color principal para botones de acción y elementos destacados.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="color_secondary"><?php _e( 'Color Secundario', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][secondary]" id="color_secondary" value="<?php echo isset( $this->options['colors']['secondary'] ) ? esc_attr( $this->options['colors']['secondary'] ) : '#4ECDC4'; ?>" data-default-color="#4ECDC4" />
                    <p class="description">
                        <?php _e( 'Color secundario para elementos informativos, IA y datos.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="color_accent"><?php _e( 'Color de Acento', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][accent]" id="color_accent" value="<?php echo isset( $this->options['colors']['accent'] ) ? esc_attr( $this->options['colors']['accent'] ) : '#FFE66D'; ?>" data-default-color="#FFE66D" />
                    <p class="description">
                        <?php _e( 'Color para destacar elementos, notificaciones y badges.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="color_success"><?php _e( 'Color de Éxito', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][success]" id="color_success" value="<?php echo isset( $this->options['colors']['success'] ) ? esc_attr( $this->options['colors']['success'] ) : '#7BC950'; ?>" data-default-color="#7BC950" />
                    <p class="description">
                        <?php _e( 'Color para mensajes de éxito, confirmaciones y métricas positivas.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="color_warning"><?php _e( 'Color de Advertencia', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][warning]" id="color_warning" value="<?php echo isset( $this->options['colors']['warning'] ) ? esc_attr( $this->options['colors']['warning'] ) : '#FFA552'; ?>" data-default-color="#FFA552" />
                    <p class="description">
                        <?php _e( 'Color para advertencias y elementos que requieren atención.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="color_error"><?php _e( 'Color de Error', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <input type="text" class="novastudio-color-field" name="novastudio_options[colors][error]" id="color_error" value="<?php echo isset( $this->options['colors']['error'] ) ? esc_attr( $this->options['colors']['error'] ) : '#F76F8E'; ?>" data-default-color="#F76F8E" />
                    <p class="description">
                        <?php _e( 'Color para errores, alertas y métricas negativas.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Renderizar la pestaña de Tipografía
     */
    public function render_typography_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'Configuración de Tipografía', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Personalice las fuentes y tamaños de texto del tema.', 'nova-ui-studio' ); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="font_primary"><?php _e( 'Fuente Principal', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[typography][font_primary]" id="font_primary">
                        <option value="'Jost', 'Quicksand', sans-serif" <?php selected( isset( $this->options['typography']['font_primary'] ) ? $this->options['typography']['font_primary'] : "'Jost', 'Quicksand', sans-serif", "'Jost', 'Quicksand', sans-serif" ); ?>>
                            Jost
                        </option>
                        <option value="'Quicksand', 'Jost', sans-serif" <?php selected( isset( $this->options['typography']['font_primary'] ) ? $this->options['typography']['font_primary'] : "'Jost', 'Quicksand', sans-serif", "'Quicksand', 'Jost', sans-serif" ); ?>>
                            Quicksand
                        </option>
                        <option value="'Montserrat', sans-serif" <?php selected( isset( $this->options['typography']['font_primary'] ) ? $this->options['typography']['font_primary'] : "'Jost', 'Quicksand', sans-serif", "'Montserrat', sans-serif" ); ?>>
                            Montserrat
                        </option>
                        <option value="'Poppins', sans-serif" <?php selected( isset( $this->options['typography']['font_primary'] ) ? $this->options['typography']['font_primary'] : "'Jost', 'Quicksand', sans-serif", "'Poppins', sans-serif" ); ?>>
                            Poppins
                        </option>
                        <option value="'Work Sans', sans-serif" <?php selected( isset( $this->options['typography']['font_primary'] ) ? $this->options['typography']['font_primary'] : "'Jost', 'Quicksand', sans-serif", "'Work Sans', sans-serif" ); ?>>
                            Work Sans
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione la fuente principal para todo el texto.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="font_secondary"><?php _e( 'Fuente Secundaria', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[typography][font_secondary]" id="font_secondary">
                        <option value="'Jost', 'Quicksand', sans-serif" <?php selected( isset( $this->options['typography']['font_secondary'] ) ? $this->options['typography']['font_secondary'] : "'Jost', 'Quicksand', sans-serif", "'Jost', 'Quicksand', sans-serif" ); ?>>
                            Jost
                        </option>
                        <option value="'Quicksand', 'Jost', sans-serif" <?php selected( isset( $this->options['typography']['font_secondary'] ) ? $this->options['typography']['font_secondary'] : "'Jost', 'Quicksand', sans-serif", "'Quicksand', 'Jost', sans-serif" ); ?>>
                            Quicksand
                        </option>
                        <option value="'Montserrat', sans-serif" <?php selected( isset( $this->options['typography']['font_secondary'] ) ? $this->options['typography']['font_secondary'] : "'Jost', 'Quicksand', sans-serif", "'Montserrat', sans-serif" ); ?>>
                            Montserrat
                        </option>
                        <option value="'Poppins', sans-serif" <?php selected( isset( $this->options['typography']['font_secondary'] ) ? $this->options['typography']['font_secondary'] : "'Jost', 'Quicksand', sans-serif", "'Poppins', sans-serif" ); ?>>
                            Poppins
                        </option>
                        <option value="'Work Sans', sans-serif" <?php selected( isset( $this->options['typography']['font_secondary'] ) ? $this->options['typography']['font_secondary'] : "'Jost', 'Quicksand', sans-serif", "'Work Sans', sans-serif" ); ?>>
                            Work Sans
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione la fuente secundaria para encabezados.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="font_size_base"><?php _e( 'Tamaño Base de Fuente', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[typography][base_size]" id="font_size_base">
                        <option value="14px" <?php selected( isset( $this->options['typography']['base_size'] ) ? $this->options['typography']['base_size'] : '16px', '14px' ); ?>>
                            14px (Pequeño)
                        </option>
                        <option value="16px" <?php selected( isset( $this->options['typography']['base_size'] ) ? $this->options['typography']['base_size'] : '16px', '16px' ); ?>>
                            16px (Mediano - Recomendado)
                        </option>
                        <option value="18px" <?php selected( isset( $this->options['typography']['base_size'] ) ? $this->options['typography']['base_size'] : '16px', '18px' ); ?>>
                            18px (Grande)
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione el tamaño base para todo el texto. Otros tamaños se escalarán proporcionalmente.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Renderizar la pestaña de Sidebar
     */
    public function render_sidebar_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'Configuración de Sidebar', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Personalice la apariencia y comportamiento de la barra lateral.', 'nova-ui-studio' ); ?></p>
        
        <h3><?php _e( 'Configuración de Logo', 'nova-ui-studio' ); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="sidebar_logo_expanded"><?php _e( 'Logo para Sidebar Expandido', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <div class="novastudio-media-uploader">
                        <input type="text" class="regular-text" name="novastudio_options[sidebar][logo_expanded]" id="sidebar_logo_expanded" value="<?php echo isset( $this->options['sidebar']['logo_expanded'] ) ? esc_attr( $this->options['sidebar']['logo_expanded'] ) : ''; ?>" />
                        <input type="button" class="button button-secondary novastudio-upload-button" value="<?php _e( 'Seleccionar Imagen', 'nova-ui-studio' ); ?>" />
                        
                        <div class="novastudio-image-preview">
                            <?php if ( ! empty( $this->options['sidebar']['logo_expanded'] ) ) : ?>
                                <img src="<?php echo esc_url( $this->options['sidebar']['logo_expanded'] ); ?>" alt="Logo Preview" />
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="description">
                        <?php _e( 'Suba o seleccione el logo para mostrar cuando la barra lateral está expandida.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="sidebar_logo_collapsed"><?php _e( 'Logo para Sidebar Colapsado (Icono)', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <div class="novastudio-media-uploader">
                        <input type="text" class="regular-text" name="novastudio_options[sidebar][logo_collapsed]" id="sidebar_logo_collapsed" value="<?php echo isset( $this->options['sidebar']['logo_collapsed'] ) ? esc_attr( $this->options['sidebar']['logo_collapsed'] ) : ''; ?>" />
                        <input type="button" class="button button-secondary novastudio-upload-button" value="<?php _e( 'Seleccionar Imagen', 'nova-ui-studio' ); ?>" />
                        
                        <div class="novastudio-image-preview">
                            <?php if ( ! empty( $this->options['sidebar']['logo_collapsed'] ) ) : ?>
                                <img src="<?php echo esc_url( $this->options['sidebar']['logo_collapsed'] ); ?>" alt="Icon Preview" />
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="description">
                        <?php _e( 'Suba o seleccione el icono para mostrar cuando la barra lateral está colapsada. Preferiblemente cuadrado.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <h3><?php _e( 'Dimensiones y Comportamiento', 'nova-ui-studio' ); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="sidebar_expanded_width"><?php _e( 'Ancho Expandido', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[sidebar][expanded_width]" id="sidebar_expanded_width">
                        <option value="200px" <?php selected( isset( $this->options['sidebar']['expanded_width'] ) ? $this->options['sidebar']['expanded_width'] : '250px', '200px' ); ?>>
                            200px (Estrecho)
                        </option>
                        <option value="250px" <?php selected( isset( $this->options['sidebar']['expanded_width'] ) ? $this->options['sidebar']['expanded_width'] : '250px', '250px' ); ?>>
                            250px (Medio - Recomendado)
                        </option>
                        <option value="300px" <?php selected( isset( $this->options['sidebar']['expanded_width'] ) ? $this->options['sidebar']['expanded_width'] : '250px', '300px' ); ?>>
                            300px (Ancho)
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione el ancho de la barra lateral cuando está expandida.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="sidebar_collapsed_width"><?php _e( 'Ancho Colapsado', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[sidebar][collapsed_width]" id="sidebar_collapsed_width">
                        <option value="50px" <?php selected( isset( $this->options['sidebar']['collapsed_width'] ) ? $this->options['sidebar']['collapsed_width'] : '60px', '50px' ); ?>>
                            50px (Muy Estrecho)
                        </option>
                        <option value="60px" <?php selected( isset( $this->options['sidebar']['collapsed_width'] ) ? $this->options['sidebar']['collapsed_width'] : '60px', '60px' ); ?>>
                            60px (Recomendado)
                        </option>
                        <option value="70px" <?php selected( isset( $this->options['sidebar']['collapsed_width'] ) ? $this->options['sidebar']['collapsed_width'] : '60px', '70px' ); ?>>
                            70px (Ancho)
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione el ancho de la barra lateral cuando está colapsada.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="sidebar_position"><?php _e( 'Posición', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[sidebar][position]" id="sidebar_position">
                        <option value="fixed" <?php selected( isset( $this->options['sidebar']['position'] ) ? $this->options['sidebar']['position'] : 'fixed', 'fixed' ); ?>>
                            <?php _e( 'Fija (siempre visible)', 'nova-ui-studio' ); ?>
                        </option>
                        <option value="static" <?php selected( isset( $this->options['sidebar']['position'] ) ? $this->options['sidebar']['position'] : 'fixed', 'static' ); ?>>
                            <?php _e( 'Estática (se desplaza con la página)', 'nova-ui-studio' ); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione cómo se comporta la barra lateral al desplazarse por la página.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Renderizar la pestaña de Header
     */
    public function render_header_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'Configuración de Header', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Personalice la apariencia y comportamiento del encabezado.', 'nova-ui-studio' ); ?></p>
        
        <h3><?php _e( 'Componentes Activables', 'nova-ui-studio' ); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <?php _e( 'Selector de Tema', 'nova-ui-studio' ); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="novastudio_options[header][show_theme_toggle]" value="1" <?php checked( isset( $this->options['header']['show_theme_toggle'] ) ? $this->options['header']['show_theme_toggle'] : true, true ); ?> />
                        <?php _e( 'Mostrar selector de tema claro/oscuro', 'nova-ui-studio' ); ?>
                    </label>
                    <p class="description">
                        <?php _e( 'Permite a los usuarios cambiar entre los modos claro y oscuro.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e( 'Barra de Búsqueda', 'nova-ui-studio' ); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="novastudio_options[header][show_search]" value="1" <?php checked( isset( $this->options['header']['show_search'] ) ? $this->options['header']['show_search'] : true, true ); ?> />
                        <?php _e( 'Mostrar barra de búsqueda', 'nova-ui-studio' ); ?>
                    </label>
                    <p class="description">
                        <?php _e( 'Muestra un campo de búsqueda en el encabezado.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e( 'Menú de Usuario', 'nova-ui-studio' ); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="novastudio_options[header][show_user_menu]" value="1" <?php checked( isset( $this->options['header']['show_user_menu'] ) ? $this->options['header']['show_user_menu'] : true, true ); ?> />
                        <?php _e( 'Mostrar menú de usuario', 'nova-ui-studio' ); ?>
                    </label>
                    <p class="description">
                        <?php _e( 'Muestra el avatar del usuario con un menú desplegable.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <h3><?php _e( 'Dimensiones y Comportamiento', 'nova-ui-studio' ); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="header_height"><?php _e( 'Altura del Header', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[header][height]" id="header_height">
                        <option value="50px" <?php selected( isset( $this->options['header']['height'] ) ? $this->options['header']['height'] : '60px', '50px' ); ?>>
                            50px (Bajo)
                        </option>
                        <option value="60px" <?php selected( isset( $this->options['header']['height'] ) ? $this->options['header']['height'] : '60px', '60px' ); ?>>
                            60px (Medio - Recomendado)
                        </option>
                        <option value="70px" <?php selected( isset( $this->options['header']['height'] ) ? $this->options['header']['height'] : '60px', '70px' ); ?>>
                            70px (Alto)
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione la altura del encabezado.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="header_position"><?php _e( 'Posición', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <select name="novastudio_options[header][position]" id="header_position">
                        <option value="sticky" <?php selected( isset( $this->options['header']['position'] ) ? $this->options['header']['position'] : 'sticky', 'sticky' ); ?>>
                            <?php _e( 'Sticky (se mantiene en la parte superior al desplazarse)', 'nova-ui-studio' ); ?>
                        </option>
                        <option value="static" <?php selected( isset( $this->options['header']['position'] ) ? $this->options['header']['position'] : 'sticky', 'static' ); ?>>
                            <?php _e( 'Estático (se desplaza con la página)', 'nova-ui-studio' ); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php _e( 'Seleccione cómo se comporta el encabezado al desplazarse por la página.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Renderizar la pestaña de CSS Personalizado
     */
    public function render_custom_css_tab() {
        // Se implementará el contenido real de la pestaña en próximas versiones
        ?>
        <h2><?php _e( 'CSS Personalizado', 'nova-ui-studio' ); ?></h2>
        <p><?php _e( 'Añada código CSS personalizado para personalizar aún más el tema.', 'nova-ui-studio' ); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="custom_css"><?php _e( 'CSS Personalizado', 'nova-ui-studio' ); ?></label>
                </th>
                <td>
                    <textarea name="novastudio_options[theme][custom_css]" id="custom_css" rows="20" class="large-text code"><?php echo isset( $this->options['theme']['custom_css'] ) ? esc_textarea( $this->options['theme']['custom_css'] ) : ''; ?></textarea>
                    <p class="description">
                        <?php _e( 'Añada su propio CSS para personalizar la apariencia del tema. Este código se aplicará a todas las páginas del sitio.', 'nova-ui-studio' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <h3><?php _e( 'Ejemplos', 'nova-ui-studio' ); ?></h3>
        <div class="novastudio-css-examples">
            <div class="novastudio-css-example">
                <h4><?php _e( 'Personalizar botones', 'nova-ui-studio' ); ?></h4>
                <pre>.btn-primary {
    border-width: 3px !important;
    text-transform: uppercase;
}</pre>
                <button class="button button-small novastudio-insert-css"><?php _e( 'Insertar', 'nova-ui-studio' ); ?></button>
            </div>
            
            <div class="novastudio-css-example">
                <h4><?php _e( 'Cambiar efectos hover', 'nova-ui-studio' ); ?></h4>
                <pre>.card:hover {
    transform: translateY(-8px);
    transition: transform 0.3s ease;
}</pre>
                <button class="button button-small novastudio-insert-css"><?php _e( 'Insertar', 'nova-ui-studio' ); ?></button>
            </div>
            
            <div class="novastudio-css-example">
                <h4><?php _e( 'Modificar espaciado', 'nova-ui-studio' ); ?></h4>
                <pre>.content-area {
    padding: 2rem;
}

.sidebar {
    margin-right: 1.5rem;
}</pre>
                <button class="button button-small novastudio-insert-css"><?php _e( 'Insertar', 'nova-ui-studio' ); ?></button>
            </div>
        </div>
        <?php
    }

    /**
     * Handler AJAX para guardar la configuración
     */
    public function ajax_save_settings() {
        // Verificar nonce y permisos
        if ( ! check_ajax_referer( 'novastudio-admin-nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'No tienes permisos para realizar esta acción.', 'nova-ui-studio' ) ) );
        }
        
        // Obtener y sanitizar datos
        $options = isset( $_POST['options'] ) ? wp_unslash( $_POST['options'] ) : array();
        
        // Sanitizar y guardar opciones
        $sanitized_options = $this->sanitize_options( $options );
        update_option( 'novastudio_options', $sanitized_options );
        
        wp_send_json_success( array( 'message' => __( 'Configuración guardada correctamente.', 'nova-ui-studio' ) ) );
    }

    /**
     * Handler AJAX para restablecer la configuración
     */
    public function ajax_reset_settings() {
        // Verificar nonce y permisos
        if ( ! check_ajax_referer( 'novastudio-admin-nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'No tienes permisos para realizar esta acción.', 'nova-ui-studio' ) ) );
        }
        
        // Eliminar la opción actual
        delete_option( 'novastudio_options' );
        
        // Crear las opciones por defecto
        $default_options = array(
            'sidebar' => array(
                'expanded_width' => '250px',
                'collapsed_width' => '60px',
                'position' => 'fixed',
                'show_logo' => true,
                'logo_expanded' => '',
                'logo_collapsed' => '',
            ),
            'header' => array(
                'height' => '60px',
                'position' => 'sticky',
                'show_search' => true,
                'show_theme_toggle' => true,
                'show_user_menu' => true,
            ),
            'theme' => array(
                'default_mode' => 'auto',
                'custom_css' => '',
            ),
            'colors' => array(
                'primary' => '#FF6B6B',
                'secondary' => '#4ECDC4',
                'accent' => '#FFE66D',
                'success' => '#7BC950',
                'warning' => '#FFA552',
                'error' => '#F76F8E',
            ),
            'typography' => array(
                'font_primary' => "'Jost', 'Quicksand', sans-serif",
                'font_secondary' => "'Jost', 'Quicksand', sans-serif",
                'base_size' => '16px',
            ),
        );
        
        add_option( 'novastudio_options', $default_options );
        
        wp_send_json_success( array( 
            'message' => __( 'Configuración restablecida correctamente.', 'nova-ui-studio' ),
            'options' => $default_options
        ) );
    }
}
