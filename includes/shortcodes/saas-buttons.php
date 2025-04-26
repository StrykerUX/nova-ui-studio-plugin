<?php
/**
 * Shortcodes para botones personalizados.
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Shortcode para crear botones personalizados.
 *
 * @param array $atts Atributos del shortcode.
 * @param string $content Contenido del shortcode.
 * @return string HTML del botón.
 */
function novastudio_button_shortcode( $atts, $content = null ) {
    // Normalizar atributos
    $atts = shortcode_atts(
        array(
            'style' => 'primary',    // primary, secondary, outline
            'size'  => 'md',         // sm, md, lg
            'url'   => '#',          // URL de destino
            'class' => '',           // Clases adicionales
            'target' => '_self',     // Target (_blank, _self, etc.)
            'icon'  => '',           // Icono opcional (nombre del icono)
            'align' => '',           // left, center, right
            'rel'   => '',           // Atributo rel
            'download' => '',        // Atributo download
            'id'    => '',           // ID del botón
        ),
        $atts,
        'saas_button'
    );
    
    // Generar clases CSS
    $classes = array( 'saas-btn' );
    
    // Estilo del botón
    switch ( $atts['style'] ) {
        case 'secondary':
            $classes[] = 'saas-btn-secondary';
            break;
        case 'outline':
            $classes[] = 'saas-btn-outline';
            break;
        case 'primary':
        default:
            $classes[] = 'saas-btn-primary';
            break;
    }
    
    // Tamaño del botón
    switch ( $atts['size'] ) {
        case 'sm':
            $classes[] = 'saas-btn-sm';
            break;
        case 'lg':
            $classes[] = 'saas-btn-lg';
            break;
    }
    
    // Alineación del botón
    if ( ! empty( $atts['align'] ) ) {
        $classes[] = 'saas-align-' . $atts['align'];
    }
    
    // Clases adicionales
    if ( ! empty( $atts['class'] ) ) {
        $classes[] = $atts['class'];
    }
    
    // Sanitizar contenido
    $content = do_shortcode( $content );
    
    // Generar HTML del botón
    $button = '<a href="' . esc_url( $atts['url'] ) . '" ';
    
    // Agregar clases
    $button .= 'class="' . esc_attr( implode( ' ', $classes ) ) . '" ';
    
    // Agregar target si es necesario
    if ( '_self' !== $atts['target'] ) {
        $button .= 'target="' . esc_attr( $atts['target'] ) . '" ';
    }
    
    // Agregar rel si es necesario
    if ( ! empty( $atts['rel'] ) ) {
        $button .= 'rel="' . esc_attr( $atts['rel'] ) . '" ';
    }
    
    // Agregar download si es necesario
    if ( ! empty( $atts['download'] ) ) {
        $button .= 'download="' . esc_attr( $atts['download'] ) . '" ';
    }
    
    // Agregar ID si es necesario
    if ( ! empty( $atts['id'] ) ) {
        $button .= 'id="' . esc_attr( $atts['id'] ) . '" ';
    }
    
    $button .= '>';
    
    // Agregar icono si es necesario
    if ( ! empty( $atts['icon'] ) ) {
        $button .= '<span class="saas-btn-icon ' . esc_attr( $atts['icon'] ) . '"></span> ';
    }
    
    // Agregar contenido del botón
    $button .= $content;
    
    $button .= '</a>';
    
    // Aplicar filtro para que otros desarrolladores puedan modificar el resultado
    $button = apply_filters( 'novastudio_button_shortcode_html', $button, $atts, $content );
    
    return $button;
}
add_shortcode( 'saas_button', 'novastudio_button_shortcode' );

/**
 * Shortcode para crear grupos de botones.
 *
 * @param array $atts Atributos del shortcode.
 * @param string $content Contenido del shortcode.
 * @return string HTML del grupo de botones.
 */
function novastudio_button_group_shortcode( $atts, $content = null ) {
    // Normalizar atributos
    $atts = shortcode_atts(
        array(
            'align'  => '',     // left, center, right
            'class'  => '',     // Clases adicionales
            'gap'    => 'md',   // sm, md, lg
            'id'     => '',     // ID del grupo
        ),
        $atts,
        'saas_button_group'
    );
    
    // Generar clases CSS
    $classes = array( 'saas-btn-group' );
    
    // Alineación del grupo
    if ( ! empty( $atts['align'] ) ) {
        $classes[] = 'saas-align-' . $atts['align'];
    }
    
    // Tamaño del espacio entre botones
    switch ( $atts['gap'] ) {
        case 'sm':
            $classes[] = 'saas-btn-group-gap-sm';
            break;
        case 'lg':
            $classes[] = 'saas-btn-group-gap-lg';
            break;
        case 'md':
        default:
            $classes[] = 'saas-btn-group-gap-md';
            break;
    }
    
    // Clases adicionales
    if ( ! empty( $atts['class'] ) ) {
        $classes[] = $atts['class'];
    }
    
    // Generar HTML del grupo de botones
    $button_group = '<div ';
    
    // Agregar clases
    $button_group .= 'class="' . esc_attr( implode( ' ', $classes ) ) . '" ';
    
    // Agregar ID si es necesario
    if ( ! empty( $atts['id'] ) ) {
        $button_group .= 'id="' . esc_attr( $atts['id'] ) . '" ';
    }
    
    $button_group .= '>';
    
    // Agregar contenido del grupo (otros botones)
    $button_group .= do_shortcode( $content );
    
    $button_group .= '</div>';
    
    // Aplicar filtro para que otros desarrolladores puedan modificar el resultado
    $button_group = apply_filters( 'novastudio_button_group_shortcode_html', $button_group, $atts, $content );
    
    return $button_group;
}
add_shortcode( 'saas_button_group', 'novastudio_button_group_shortcode' );
