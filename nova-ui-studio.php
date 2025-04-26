<?php
/**
 * Plugin Name: NovaStudio
 * Plugin URI: https://github.com/StrykerUX/nova-ui-studio-plugin
 * Description: Plugin de personalización para WordPress que permite personalizar extensivamente el tema NovaUI con un enfoque en diseño Soft Neo-Brutalist.
 * Version: 1.0.0
 * Author: StrykerUX
 * Author URI: https://github.com/StrykerUX
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: nova-ui-studio
 * Domain Path: /languages
 * Requires at least: 5.5
 * Requires PHP: 7.4
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Definir constantes del plugin
define( 'NOVASTUDIO_VERSION', '1.0.0' );
define( 'NOVASTUDIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NOVASTUDIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NOVASTUDIO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Código que se ejecuta durante la activación del plugin.
 */
function activate_nova_ui_studio() {
    // Verificar si el tema NovaUI está instalado y activado
    $theme = wp_get_theme();
    if ( 'NovaUI' !== $theme->name && 'NovaUI' !== $theme->parent_theme ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 
            sprintf(
                __( 'Este plugin requiere que el tema %1$s esté instalado y activado. Por favor instala y activa el tema primero. <a href="%2$s">Volver a plugins</a>', 'nova-ui-studio' ),
                '<strong>NovaUI</strong>',
                admin_url( 'plugins.php' )
            )
        );
    }
    
    // Crear las opciones por defecto
    if ( ! get_option( 'novastudio_options' ) ) {
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
    }
}
register_activation_hook( __FILE__, 'activate_nova_ui_studio' );

/**
 * Código que se ejecuta durante la desactivación del plugin.
 */
function deactivate_nova_ui_studio() {
    // Acciones a realizar en la desactivación
}
register_deactivation_hook( __FILE__, 'deactivate_nova_ui_studio' );

/**
 * Código que se ejecuta durante la desinstalación del plugin.
 */
function uninstall_nova_ui_studio() {
    // Eliminar las opciones creadas por el plugin
    delete_option( 'novastudio_options' );
}
register_uninstall_hook( __FILE__, 'uninstall_nova_ui_studio' );

/**
 * Cargar clases e incluir archivos necesarios
 */
function novastudio_includes() {
    require_once NOVASTUDIO_PLUGIN_DIR . 'includes/helpers/helpers.php';
    require_once NOVASTUDIO_PLUGIN_DIR . 'includes/class-novastudio.php';
    require_once NOVASTUDIO_PLUGIN_DIR . 'admin/class-novastudio-admin.php';
}

/**
 * Iniciar el plugin
 */
function novastudio_init() {
    novastudio_includes();
    
    if ( is_admin() ) {
        $admin = new NovaStudio_Admin();
        $admin->init();
    }
    
    $plugin = new NovaStudio();
    $plugin->init();
}
add_action( 'plugins_loaded', 'novastudio_init' );

/**
 * Cargar el dominio de texto del plugin para traducción
 */
function novastudio_load_textdomain() {
    load_plugin_textdomain( 'nova-ui-studio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'novastudio_load_textdomain' );

/**
 * Agregar enlace a configuración en listado de plugins
 */
function novastudio_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=novastudio-options' ) . '">' . __( 'Configuración', 'nova-ui-studio' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'novastudio_settings_link' );
