<?php
/**
 * Shortcode para secciones personalizables.
 *
 * Permite crear secciones con diferentes layouts y estilos.
 *
 * @package NovaStudio
 */

// Si este archivo se llama directamente, abortar.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Registrar el shortcode [saas_section]
 */
function novastudio_section_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'layout'     => 'default',
        'padding'    => 'md',
        'background' => '',
        'color'      => '',
        'align'      => 'left',
        'width'      => '100%',
        'border'     => 'none',
        'shadow'     => 'none',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_section' );
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-section-' . novastudio_generate_id();
    }
    
    // Padding classes
    $padding_class = 'saas-padding-' . $atts['padding'];
    
    // Shadow class
    $shadow_class = '';
    if ( $atts['shadow'] !== 'none' ) {
        $shadow_class = 'saas-shadow-' . $atts['shadow'];
    }
    
    // Border class
    $border_class = '';
    if ( $atts['border'] !== 'none' ) {
        $border_class = 'saas-border-' . $atts['border'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Layout class
    $layout_class = 'saas-section-' . $atts['layout'];
    
    // Build classes
    $classes = array(
        'saas-section',
        $layout_class,
        $padding_class,
        $shadow_class,
        $border_class,
        $animation_class,
        'saas-align-' . $atts['align'],
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['background'] ) ) {
        $styles[] = 'background-color: ' . esc_attr( $atts['background'] ) . ';';
    }
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    if ( ! empty( $atts['width'] ) && $atts['width'] !== '100%' ) {
        $styles[] = 'width: ' . esc_attr( $atts['width'] ) . ';';
        $styles[] = 'margin-left: auto;';
        $styles[] = 'margin-right: auto;';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Initialize output
    $output = '<section id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    
    // Wrapper if layout requires it
    if ( $atts['layout'] === 'container' || $atts['layout'] === 'full-width' ) {
        $output .= '<div class="saas-container">';
    }
    
    // Add content
    $output .= do_shortcode( $content );
    
    // Close wrapper if needed
    if ( $atts['layout'] === 'container' || $atts['layout'] === 'full-width' ) {
        $output .= '</div>';
    }
    
    $output .= '</section>';
    
    return $output;
}
add_shortcode( 'saas_section', 'novastudio_section_shortcode' );

/**
 * Registrar el shortcode [saas_row]
 */
function novastudio_row_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'align'      => 'start',
        'valign'     => 'start',
        'gap'        => 'md',
        'padding'    => 'none',
        'background' => '',
        'color'      => '',
        'border'     => 'none',
        'shadow'     => 'none',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_row' );
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-row-' . novastudio_generate_id();
    }
    
    // Gap class
    $gap_class = 'saas-gap-' . $atts['gap'];
    
    // Padding class
    $padding_class = '';
    if ( $atts['padding'] !== 'none' ) {
        $padding_class = 'saas-padding-' . $atts['padding'];
    }
    
    // Shadow class
    $shadow_class = '';
    if ( $atts['shadow'] !== 'none' ) {
        $shadow_class = 'saas-shadow-' . $atts['shadow'];
    }
    
    // Border class
    $border_class = '';
    if ( $atts['border'] !== 'none' ) {
        $border_class = 'saas-border-' . $atts['border'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-row',
        $gap_class,
        $padding_class,
        $shadow_class,
        $border_class,
        $animation_class,
        'saas-justify-' . $atts['align'],
        'saas-align-' . $atts['valign'],
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['background'] ) ) {
        $styles[] = 'background-color: ' . esc_attr( $atts['background'] ) . ';';
    }
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Initialize output
    $output = '<div id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    
    // Add content
    $output .= do_shortcode( $content );
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode( 'saas_row', 'novastudio_row_shortcode' );

/**
 * Registrar el shortcode [saas_column]
 */
function novastudio_column_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'width'      => '',
        'md_width'   => '',
        'sm_width'   => '100%',
        'padding'    => 'none',
        'background' => '',
        'color'      => '',
        'align'      => 'left',
        'valign'     => 'top',
        'border'     => 'none',
        'shadow'     => 'none',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_column' );
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-column-' . novastudio_generate_id();
    }
    
    // Padding class
    $padding_class = '';
    if ( $atts['padding'] !== 'none' ) {
        $padding_class = 'saas-padding-' . $atts['padding'];
    }
    
    // Shadow class
    $shadow_class = '';
    if ( $atts['shadow'] !== 'none' ) {
        $shadow_class = 'saas-shadow-' . $atts['shadow'];
    }
    
    // Border class
    $border_class = '';
    if ( $atts['border'] !== 'none' ) {
        $border_class = 'saas-border-' . $atts['border'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-column',
        $padding_class,
        $shadow_class,
        $border_class,
        $animation_class,
        'saas-text-' . $atts['align'],
        'saas-valign-' . $atts['valign'],
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['width'] ) ) {
        $styles[] = 'width: ' . esc_attr( $atts['width'] ) . ';';
    }
    
    if ( ! empty( $atts['background'] ) ) {
        $styles[] = 'background-color: ' . esc_attr( $atts['background'] ) . ';';
    }
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Responsive data attributes
    $data_attrs = '';
    
    if ( ! empty( $atts['md_width'] ) ) {
        $data_attrs .= ' data-md-width="' . esc_attr( $atts['md_width'] ) . '"';
    }
    
    if ( ! empty( $atts['sm_width'] ) ) {
        $data_attrs .= ' data-sm-width="' . esc_attr( $atts['sm_width'] ) . '"';
    }
    
    // Initialize output
    $output = '<div id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . $data_attrs . '>';
    
    // Add content
    $output .= do_shortcode( $content );
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode( 'saas_column', 'novastudio_column_shortcode' );

/**
 * Registrar el shortcode [saas_heading]
 */
function novastudio_heading_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'size'       => 'xl',
        'tag'        => 'h2',
        'color'      => '',
        'align'      => '',
        'margin'     => 'mb-md',
        'font'       => '',
        'weight'     => '',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_heading' );
    
    // Tamaño
    $size_class = 'saas-text-' . $atts['size'];
    
    // Margin
    $margin_class = $atts['margin'];
    
    // Alineación
    $align_class = '';
    if ( ! empty( $atts['align'] ) ) {
        $align_class = 'saas-text-' . $atts['align'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-heading',
        $size_class,
        $margin_class,
        $align_class,
        $animation_class,
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    if ( ! empty( $atts['font'] ) ) {
        $styles[] = 'font-family: ' . esc_attr( $atts['font'] ) . ';';
    }
    
    if ( ! empty( $atts['weight'] ) ) {
        $styles[] = 'font-weight: ' . esc_attr( $atts['weight'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Sanitize tag
    $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $tag = in_array( $atts['tag'], $allowed_tags ) ? $atts['tag'] : 'h2';
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-heading-' . novastudio_generate_id();
    }
    
    // Initialize output
    $output = '<' . $tag . ' id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    $output .= do_shortcode( $content );
    $output .= '</' . $tag . '>';
    
    return $output;
}
add_shortcode( 'saas_heading', 'novastudio_heading_shortcode' );

/**
 * Registrar el shortcode [saas_text]
 */
function novastudio_text_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'size'       => 'md',
        'color'      => '',
        'align'      => '',
        'margin'     => 'mb-md',
        'font'       => '',
        'weight'     => '',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_text' );
    
    // Tamaño
    $size_class = 'saas-text-' . $atts['size'];
    
    // Margin
    $margin_class = $atts['margin'];
    
    // Alineación
    $align_class = '';
    if ( ! empty( $atts['align'] ) ) {
        $align_class = 'saas-text-' . $atts['align'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-text',
        $size_class,
        $margin_class,
        $align_class,
        $animation_class,
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    if ( ! empty( $atts['font'] ) ) {
        $styles[] = 'font-family: ' . esc_attr( $atts['font'] ) . ';';
    }
    
    if ( ! empty( $atts['weight'] ) ) {
        $styles[] = 'font-weight: ' . esc_attr( $atts['weight'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-text-' . novastudio_generate_id();
    }
    
    // Initialize output
    $output = '<p id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    $output .= do_shortcode( $content );
    $output .= '</p>';
    
    return $output;
}
add_shortcode( 'saas_text', 'novastudio_text_shortcode' );

/**
 * Registrar el shortcode [saas_button] en el contexto de secciones
 * 
 * Esta función se ha renombrado para evitar conflictos con la función
 * novastudio_button_shortcode definida en saas-buttons.php
 */
function novastudio_section_button_shortcode( $atts, $content = null ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'url'        => '#',
        'target'     => '_self',
        'style'      => 'primary',
        'size'       => 'md',
        'width'      => 'auto',
        'align'      => '',
        'margin'     => 'mb-md',
        'icon'       => '',
        'icon_pos'   => 'right',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_button' );
    
    // Estilo
    $style_class = 'saas-btn-' . $atts['style'];
    
    // Tamaño
    $size_class = 'saas-btn-' . $atts['size'];
    
    // Margin
    $margin_class = $atts['margin'];
    
    // Alineación
    $align_class = '';
    if ( ! empty( $atts['align'] ) ) {
        $align_class = 'saas-text-' . $atts['align'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-btn',
        $style_class,
        $size_class,
        $margin_class,
        $align_class,
        $animation_class,
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['width'] ) && $atts['width'] !== 'auto' ) {
        $styles[] = 'width: ' . esc_attr( $atts['width'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-btn-' . novastudio_generate_id();
    }
    
    // Icono
    $icon_html = '';
    if ( ! empty( $atts['icon'] ) ) {
        $icon_html = '<span class="saas-btn-icon saas-icon-' . esc_attr( $atts['icon'] ) . '"></span>';
    }
    
    // Initialize output
    $output = '<a id="' . esc_attr( $atts['id'] ) . '" href="' . esc_url( $atts['url'] ) . '" target="' . esc_attr( $atts['target'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    
    if ( ! empty( $atts['icon'] ) && $atts['icon_pos'] === 'left' ) {
        $output .= $icon_html . ' ';
    }
    
    $output .= do_shortcode( $content );
    
    if ( ! empty( $atts['icon'] ) && $atts['icon_pos'] === 'right' ) {
        $output .= ' ' . $icon_html;
    }
    
    $output .= '</a>';
    
    return $output;
}
// Note: No registramos este shortcode aquí porque ya está registrado en saas-buttons.php
// add_shortcode( 'saas_button', 'novastudio_section_button_shortcode' );

/**
 * Registrar el shortcode [saas_image]
 */
function novastudio_image_shortcode( $atts ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'src'        => '',
        'alt'        => '',
        'width'      => '',
        'height'     => '',
        'align'      => '',
        'margin'     => 'mb-md',
        'shadow'     => 'none',
        'border'     => 'none',
        'shape'      => 'default',
        'animation'  => '',
        'link'       => '',
        'target'     => '_self',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_image' );
    
    // Verificar si hay imagen
    if ( empty( $atts['src'] ) ) {
        return '';
    }
    
    // Margin
    $margin_class = $atts['margin'];
    
    // Shadow class
    $shadow_class = '';
    if ( $atts['shadow'] !== 'none' ) {
        $shadow_class = 'saas-shadow-' . $atts['shadow'];
    }
    
    // Border class
    $border_class = '';
    if ( $atts['border'] !== 'none' ) {
        $border_class = 'saas-border-' . $atts['border'];
    }
    
    // Shape class
    $shape_class = '';
    if ( $atts['shape'] !== 'default' ) {
        $shape_class = 'saas-shape-' . $atts['shape'];
    }
    
    // Alineación
    $align_class = '';
    if ( ! empty( $atts['align'] ) ) {
        $align_class = 'saas-align-' . $atts['align'];
    }
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-image',
        $margin_class,
        $shadow_class,
        $border_class,
        $shape_class,
        $align_class,
        $animation_class,
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Dimensiones
    $width_attr = ! empty( $atts['width'] ) ? ' width="' . esc_attr( $atts['width'] ) . '"' : '';
    $height_attr = ! empty( $atts['height'] ) ? ' height="' . esc_attr( $atts['height'] ) . '"' : '';
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-image-' . novastudio_generate_id();
    }
    
    // Initialize output
    $output = '';
    
    // Si hay link, agregar etiqueta <a>
    if ( ! empty( $atts['link'] ) ) {
        $output .= '<a href="' . esc_url( $atts['link'] ) . '" target="' . esc_attr( $atts['target'] ) . '">';
    }
    
    $output .= '<img id="' . esc_attr( $atts['id'] ) . '" src="' . esc_url( $atts['src'] ) . '" alt="' . esc_attr( $atts['alt'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $width_attr . $height_attr . '>';
    
    // Cerrar etiqueta <a> si hay link
    if ( ! empty( $atts['link'] ) ) {
        $output .= '</a>';
    }
    
    return $output;
}
add_shortcode( 'saas_image', 'novastudio_image_shortcode' );

/**
 * Registrar el shortcode [saas_divider]
 */
function novastudio_divider_shortcode( $atts ) {
    $defaults = array(
        'id'         => '',
        'class'      => '',
        'height'     => '1px',
        'width'      => '100%',
        'color'      => '',
        'margin'     => 'my-md',
        'style'      => 'solid',
        'animation'  => '',
    );
    
    $atts = shortcode_atts( $defaults, $atts, 'saas_divider' );
    
    // Margin
    $margin_class = $atts['margin'];
    
    // Style class
    $style_class = 'saas-divider-' . $atts['style'];
    
    // Animation class
    $animation_class = '';
    if ( ! empty( $atts['animation'] ) ) {
        $animation_class = 'saas-animation-' . $atts['animation'];
    }
    
    // Build classes
    $classes = array(
        'saas-divider',
        $margin_class,
        $style_class,
        $animation_class,
        $atts['class']
    );
    
    $classes = array_filter( $classes ); // Remove empty values
    $class_attr = implode( ' ', $classes );
    
    // Inline styles
    $styles = array();
    
    if ( ! empty( $atts['height'] ) ) {
        $styles[] = 'height: ' . esc_attr( $atts['height'] ) . ';';
    }
    
    if ( ! empty( $atts['width'] ) && $atts['width'] !== '100%' ) {
        $styles[] = 'width: ' . esc_attr( $atts['width'] ) . ';';
        $styles[] = 'margin-left: auto;';
        $styles[] = 'margin-right: auto;';
    }
    
    if ( ! empty( $atts['color'] ) ) {
        $styles[] = 'background-color: ' . esc_attr( $atts['color'] ) . ';';
    }
    
    $style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';
    
    // Generar un ID único si no se proporciona uno
    if ( empty( $atts['id'] ) ) {
        $atts['id'] = 'saas-divider-' . novastudio_generate_id();
    }
    
    // Initialize output
    $output = '<hr id="' . esc_attr( $atts['id'] ) . '" class="' . esc_attr( $class_attr ) . '"' . $style_attr . '>';
    
    return $output;
}
add_shortcode( 'saas_divider', 'novastudio_divider_shortcode' );