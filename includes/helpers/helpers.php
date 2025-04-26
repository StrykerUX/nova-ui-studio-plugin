<?php
/**
 * Funciones auxiliares para el plugin NovaStudio.
 *
 * Contiene funciones de utilidad que se utilizan en todo el plugin.
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Obtiene las opciones del plugin.
 *
 * @return array Las opciones del plugin.
 */
function novastudio_get_options() {
    return get_option( 'novastudio_options', array() );
}

/**
 * Obtiene una opción específica del plugin.
 *
 * @param string $section Sección de la opción.
 * @param string $key     Clave de la opción.
 * @param mixed  $default Valor por defecto si la opción no existe.
 *
 * @return mixed El valor de la opción o el valor por defecto.
 */
function novastudio_get_option( $section, $key, $default = '' ) {
    $options = novastudio_get_options();
    
    if ( isset( $options[ $section ][ $key ] ) ) {
        return $options[ $section ][ $key ];
    }
    
    return $default;
}

/**
 * Verifica si el tema NovaUI está activo.
 *
 * @return boolean True si el tema está activo, false en caso contrario.
 */
function novastudio_is_novaui_active() {
    $theme = wp_get_theme();
    return ( 'NovaUI' === $theme->name || 'NovaUI' === $theme->parent_theme );
}

/**
 * Elimina espacios en blanco de un string y lo convierte a minúsculas.
 *
 * @param string $string El string a procesar.
 * @return string El string procesado.
 */
function novastudio_sanitize_key_string( $string ) {
    return strtolower( str_replace( ' ', '_', trim( $string ) ) );
}

/**
 * Genera un ID único para elementos en el DOM.
 *
 * @param string $prefix Prefijo para el ID.
 * @return string ID único.
 */
function novastudio_generate_id( $prefix = 'novastudio' ) {
    static $id_counter = 0;
    $id_counter++;
    
    return $prefix . '-' . $id_counter;
}

/**
 * Obtiene presets de color predefinidos.
 *
 * @return array Presets de color.
 */
function novastudio_get_color_presets() {
    return array(
        'default' => array(
            'primary' => '#FF6B6B',
            'secondary' => '#4ECDC4',
            'accent' => '#FFE66D',
            'success' => '#7BC950',
            'warning' => '#FFA552',
            'error' => '#F76F8E',
        ),
        'calm' => array(
            'primary' => '#6B9BFF',
            'secondary' => '#4ECDC4',
            'accent' => '#C3E88D',
            'success' => '#7BC950',
            'warning' => '#FFA552',
            'error' => '#F76F8E',
        ),
        'vibrant' => array(
            'primary' => '#FF6E9C',
            'secondary' => '#8A7AFF',
            'accent' => '#FFC53D',
            'success' => '#7BC950',
            'warning' => '#FFA552',
            'error' => '#F76F8E',
        ),
        'earthy' => array(
            'primary' => '#E07A5F',
            'secondary' => '#81B29A',
            'accent' => '#F2CC8F',
            'success' => '#7BC950',
            'warning' => '#FFA552',
            'error' => '#F76F8E',
        ),
    );
}

/**
 * Obtiene las opciones de fuentes disponibles.
 *
 * @return array Fuentes disponibles.
 */
function novastudio_get_font_options() {
    return array(
        "'Jost', 'Quicksand', sans-serif" => 'Jost',
        "'Quicksand', 'Jost', sans-serif" => 'Quicksand',
        "'Montserrat', sans-serif" => 'Montserrat',
        "'Poppins', sans-serif" => 'Poppins',
        "'Work Sans', sans-serif" => 'Work Sans',
    );
}

/**
 * Verifica el nivel de plan del usuario actual.
 *
 * @return string El nivel de plan ('basic', 'professional', 'business') o 'unknown' si no se puede determinar.
 */
function novastudio_get_user_plan_level() {
    // Si WooCommerce Memberships no está activo, devolver nivel básico
    if ( ! class_exists( 'WC_Memberships' ) ) {
        return 'basic';
    }
    
    // Obtener el usuario actual
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return 'basic';
    }
    
    // Verificar membresías del usuario
    if ( function_exists( 'wc_memberships_get_user_active_memberships' ) ) {
        $memberships = wc_memberships_get_user_active_memberships( $user_id );
        
        if ( empty( $memberships ) ) {
            return 'basic';
        }
        
        // Buscar el nivel más alto de membresía
        $has_business = false;
        $has_professional = false;
        
        foreach ( $memberships as $membership ) {
            $plan_id = $membership->get_plan_id();
            $plan_slug = get_post_field( 'post_name', $plan_id );
            
            // Ajustar esta lógica según los slugs reales de tus planes
            if ( strpos( $plan_slug, 'business' ) !== false || strpos( $plan_slug, 'empresa' ) !== false ) {
                $has_business = true;
            } elseif ( strpos( $plan_slug, 'professional' ) !== false || strpos( $plan_slug, 'profesional' ) !== false ) {
                $has_professional = true;
            }
        }
        
        if ( $has_business ) {
            return 'business';
        } elseif ( $has_professional ) {
            return 'professional';
        }
    }
    
    return 'basic';
}

/**
 * Comprueba si el usuario tiene acceso a una característica según su plan.
 *
 * @param string $feature_level Nivel de plan requerido para la característica ('basic', 'professional', 'business').
 * @return boolean True si el usuario tiene acceso, false en caso contrario.
 */
function novastudio_user_has_feature_access( $feature_level = 'basic' ) {
    $user_level = novastudio_get_user_plan_level();
    
    switch ( $feature_level ) {
        case 'basic':
            // Todos los niveles tienen acceso a características básicas
            return true;
        case 'professional':
            // Solo niveles profesional y empresa tienen acceso
            return in_array( $user_level, array( 'professional', 'business' ), true );
        case 'business':
            // Solo nivel empresa tiene acceso
            return $user_level === 'business';
        default:
            return false;
    }
}

/**
 * Obtiene la URL del logo del sitio (personalizable para cada plan).
 *
 * @param string $type Tipo de logo ('expanded' o 'collapsed').
 * @return string URL del logo o vacío si no existe.
 */
function novastudio_get_logo_url( $type = 'expanded' ) {
    $logo_url = '';
    
    // Obtener el logo personalizado si existe
    if ( $type === 'expanded' ) {
        $logo_url = novastudio_get_option( 'sidebar', 'logo_expanded', '' );
    } elseif ( $type === 'collapsed' ) {
        $logo_url = novastudio_get_option( 'sidebar', 'logo_collapsed', '' );
    }
    
    // Si no hay logo personalizado, usar el logo del sitio
    if ( empty( $logo_url ) && has_custom_logo() ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }
    
    return $logo_url;
}

/**
 * Obtiene el código hexadecimal de un color por su nombre.
 *
 * @param string $color_name Nombre del color.
 * @return string Código hexadecimal del color o el color por defecto.
 */
function novastudio_get_color( $color_name ) {
    $color_defaults = array(
        'primary' => '#FF6B6B',
        'secondary' => '#4ECDC4',
        'accent' => '#FFE66D',
        'success' => '#7BC950',
        'warning' => '#FFA552',
        'error' => '#F76F8E',
    );
    
    return novastudio_get_option( 'colors', $color_name, isset( $color_defaults[ $color_name ] ) ? $color_defaults[ $color_name ] : '#000000' );
}

/**
 * Añade un mensaje de notificación en el admin.
 *
 * @param string $message Mensaje a mostrar.
 * @param string $type    Tipo de notificación: 'success', 'warning', 'error', 'info'.
 */
function novastudio_add_admin_notice( $message, $type = 'info' ) {
    $notices = get_option( 'novastudio_admin_notices', array() );
    
    $notices[] = array(
        'message' => $message,
        'type' => $type,
    );
    
    update_option( 'novastudio_admin_notices', $notices );
}

/**
 * Muestra las notificaciones en el admin.
 */
function novastudio_display_admin_notices() {
    $notices = get_option( 'novastudio_admin_notices', array() );
    
    if ( empty( $notices ) ) {
        return;
    }
    
    foreach ( $notices as $notice ) {
        $class = 'notice notice-' . esc_attr( $notice['type'] );
        $message = wp_kses_post( $notice['message'] );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
    
    // Limpiar las notificaciones una vez mostradas
    delete_option( 'novastudio_admin_notices' );
}
add_action( 'admin_notices', 'novastudio_display_admin_notices' );

/**
 * Obtiene la URL de un asset del plugin.
 *
 * @param string $relative_path Ruta relativa al asset dentro de la carpeta 'assets'.
 * @return string URL completa del asset.
 */
function novastudio_get_asset_url( $relative_path ) {
    return NOVASTUDIO_PLUGIN_URL . 'assets/' . ltrim( $relative_path, '/' );
}

/**
 * Genera un bloque HTML con la vista previa de componentes UI.
 *
 * @param string $component_type Tipo de componente ('buttons', 'cards', 'forms', etc.).
 * @return string HTML de la vista previa.
 */
function novastudio_get_component_preview( $component_type ) {
    $output = '';
    
    switch ( $component_type ) {
        case 'buttons':
            $primary_color = novastudio_get_color( 'primary' );
            $secondary_color = novastudio_get_color( 'secondary' );
            
            $output .= '<div class="novastudio-preview-grid">';
            
            // Botón primario
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Botón Primario</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<button class="btn btn-primary" style="background-color: ' . esc_attr( $primary_color ) . ';">Botón Primario</button>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Botón secundario
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Botón Secundario</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<button class="btn btn-secondary" style="background-color: ' . esc_attr( $secondary_color ) . ';">Botón Secundario</button>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Botón outline
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Botón Outline</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<button class="btn btn-outline" style="border-color: ' . esc_attr( $primary_color ) . '; color: ' . esc_attr( $primary_color ) . ';">Botón Outline</button>';
            $output .= '</div>';
            $output .= '</div>';
            
            $output .= '</div>';
            break;
            
        case 'cards':
            $output .= '<div class="novastudio-preview-grid">';
            
            // Tarjeta simple
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Tarjeta Simple</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<div class="card">';
            $output .= '<div class="card-body">';
            $output .= '<h5 class="card-title">Título de Tarjeta</h5>';
            $output .= '<p class="card-text">Este es un ejemplo de tarjeta simple con texto y un botón.</p>';
            $output .= '<button class="btn btn-primary" style="background-color: ' . esc_attr( novastudio_get_color( 'primary' ) ) . ';">Acción</button>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Tarjeta con cabecera
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Tarjeta con Cabecera</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<div class="card">';
            $output .= '<div class="card-header" style="background-color: ' . esc_attr( novastudio_get_color( 'secondary' ) ) . ';">';
            $output .= '<h5 class="card-header-title">Cabecera</h5>';
            $output .= '</div>';
            $output .= '<div class="card-body">';
            $output .= '<p class="card-text">Contenido de la tarjeta con cabecera.</p>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            $output .= '</div>';
            break;
            
        case 'forms':
            $output .= '<div class="novastudio-preview-grid">';
            
            // Campo de texto
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Campo de Texto</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<div class="form-group">';
            $output .= '<label for="exampleInput">Etiqueta</label>';
            $output .= '<input type="text" class="form-control" id="exampleInput" placeholder="Placeholder">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Selector
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Selector</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<div class="form-group">';
            $output .= '<label for="exampleSelect">Selector</label>';
            $output .= '<select class="form-control" id="exampleSelect">';
            $output .= '<option>Opción 1</option>';
            $output .= '<option>Opción 2</option>';
            $output .= '</select>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Checkbox
            $output .= '<div class="novastudio-preview-item">';
            $output .= '<h4>Checkbox</h4>';
            $output .= '<div class="novastudio-preview-component">';
            $output .= '<div class="form-check">';
            $output .= '<input class="form-check-input" type="checkbox" id="exampleCheck">';
            $output .= '<label class="form-check-label" for="exampleCheck">Opción de Checkbox</label>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            $output .= '</div>';
            break;
            
        default:
            $output = '<p>' . __( 'No hay vista previa disponible para este tipo de componente.', 'nova-ui-studio' ) . '</p>';
    }
    
    return $output;
}

/**
 * Devuelve el HTML para un campo de tipo media uploader.
 *
 * @param string $field_name  Nombre del campo.
 * @param string $field_value Valor actual del campo.
 * @param string $label       Etiqueta del campo.
 * @param string $description Descripción del campo.
 * @return string HTML del campo.
 */
function novastudio_media_uploader_field( $field_name, $field_value, $label, $description = '' ) {
    $output = '<div class="novastudio-media-uploader">';
    
    $output .= '<label for="' . esc_attr( $field_name ) . '">' . esc_html( $label ) . '</label>';
    
    $output .= '<div class="novastudio-media-uploader-container">';
    $output .= '<input type="text" class="regular-text" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" />';
    $output .= '<input type="button" class="button button-secondary novastudio-upload-button" value="' . esc_attr__( 'Seleccionar Imagen', 'nova-ui-studio' ) . '" />';
    $output .= '</div>';
    
    $output .= '<div class="novastudio-image-preview">';
    if ( ! empty( $field_value ) ) {
        $output .= '<img src="' . esc_url( $field_value ) . '" alt="Preview" />';
        $output .= '<button class="button button-link novastudio-remove-image">' . esc_html__( 'Eliminar', 'nova-ui-studio' ) . '</button>';
    }
    $output .= '</div>';
    
    if ( ! empty( $description ) ) {
        $output .= '<p class="description">' . esc_html( $description ) . '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Devuelve el HTML para un campo de tipo color picker.
 *
 * @param string $field_name  Nombre del campo.
 * @param string $field_value Valor actual del campo.
 * @param string $label       Etiqueta del campo.
 * @param string $description Descripción del campo.
 * @param string $default     Valor por defecto del campo.
 * @return string HTML del campo.
 */
function novastudio_color_picker_field( $field_name, $field_value, $label, $description = '', $default = '#000000' ) {
    $output = '<div class="novastudio-color-picker">';
    
    $output .= '<label for="' . esc_attr( $field_name ) . '">' . esc_html( $label ) . '</label>';
    $output .= '<input type="text" class="novastudio-color-field" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    
    if ( ! empty( $description ) ) {
        $output .= '<p class="description">' . esc_html( $description ) . '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
