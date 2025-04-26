# Guía de Desarrollo para NovaStudio

Este documento contiene pautas y mejores prácticas para desarrollar y extender el plugin NovaStudio.

## Estructura de Archivos

El plugin sigue esta estructura base:

```
nova-ui-studio-plugin/
├── admin/                  # Archivos de administración
│   ├── css-editor/         # Editor CSS avanzado
│   ├── theme-options/      # Panel de opciones del tema
│   └── templates/          # Templates de admin
├── assets/                 # Assets públicos
│   ├── css/                # Estilos del plugin
│   └── js/                 # Scripts del plugin
├── includes/               # Lógica principal
│   ├── shortcodes/         # Shortcodes del plugin
│   ├── widgets/            # Widgets personalizados
│   └── helpers/            # Funciones auxiliares
└── nova-ui-studio.php      # Archivo principal del plugin
```

## Convenciones de Nomenclatura

### Funciones

Para evitar conflictos, todas las funciones deben seguir estas pautas:

1. **Prefijo Único**: Usar `novastudio_` como prefijo para todas las funciones.
2. **Nombre Descriptivo**: El nombre debe indicar claramente qué hace la función.
3. **Sufijo Contextual**: Para funciones similares en diferentes contextos, añadir un sufijo descriptivo:
   - `novastudio_button_shortcode()` para el shortcode de botón genérico
   - `novastudio_section_button_shortcode()` para botones específicos de secciones

### Shortcodes

Los shortcodes registrados por el plugin utilizan el prefijo `saas_`:

- `[saas_section]`
- `[saas_row]`
- `[saas_column]`
- `[saas_heading]`
- `[saas_button]`
- etc.

### Clases CSS

Las clases CSS utilizan estos prefijos:

- `.saas-` para componentes generales
- `.saas-btn-` para variantes de botones
- `.saas-section-` para estilos de sección
- `.saas-text-` para estilos tipográficos

## Prevención de Conflictos

### Carga de Shortcodes

El plugin ahora detecta automáticamente posibles conflictos en las declaraciones de funciones. Sin embargo, sigue estas pautas para evitar problemas:

1. **Shortcodes Relacionados en un Solo Archivo**: Mantén los shortcodes relacionados en el mismo archivo.
2. **Nombres Específicos**: Usa nombres específicos con sufijos claros para funciones similares.
3. **Documentación**: Documenta claramente cada función y shortcode con DocBlocks completos.

### Orden de Carga

Los archivos de shortcodes se cargan en este orden:

1. Primero `saas-buttons.php` (tiene prioridad para evitar conflictos)
2. Luego el resto de archivos en orden alfabético

Si necesitas definir un nuevo orden de prioridad, modifica el array `$priority_files` en el método `init_shortcodes()` de la clase principal.

## Desarrollo de Shortcodes

### Estructura Recomendada

```php
/**
 * Registrar el shortcode [saas_ejemplo]
 *
 * @param array $atts Atributos del shortcode.
 * @param string $content Contenido del shortcode.
 * @return string HTML generado.
 */
function novastudio_ejemplo_shortcode( $atts, $content = null ) {
    // Normalizar atributos
    $atts = shortcode_atts(
        array(
            'id'    => '',
            'class' => '',
            // Más atributos...
        ),
        $atts,
        'saas_ejemplo'
    );
    
    // Lógica del shortcode
    
    return $output;
}
add_shortcode( 'saas_ejemplo', 'novastudio_ejemplo_shortcode' );
```

### Consejos para Shortcodes

1. **Validación**: Siempre valida y sanitiza todos los atributos.
2. **IDs Únicos**: Genera IDs únicos cuando no se proporcionen.
3. **Flexibilidad**: Permite personalización mediante clases y estilos adicionales.
4. **Documentación**: Documenta todos los atributos posibles del shortcode.

## Sistema de Variables CSS

El plugin define variables CSS disponibles para todos los componentes. Usa estas variables en lugar de valores codificados:

```css
/* Incorrecto */
.mi-componente {
    color: #FF6B6B;
    font-family: 'Jost', sans-serif;
}

/* Correcto */
.mi-componente {
    color: var(--color-primary);
    font-family: var(--font-primary);
}
```

## Extensión del Plugin

Para agregar nuevas funcionalidades al plugin:

1. Crea un nuevo archivo en la carpeta apropiada (`shortcodes/`, `widgets/`, etc.)
2. Sigue las convenciones de nomenclatura y prefijos
3. Documenta completamente tu código
4. Usa las funciones auxiliares existentes y sigue los patrones establecidos

## Solución de Problemas Comunes

### Declaraciones de Funciones Duplicadas

Si ves errores como "Cannot redeclare function..." durante el desarrollo:

1. Revisa si has definido la misma función en distintos archivos
2. Renombra la función con un sufijo contextual
3. Considera mover funciones similares al mismo archivo

### Conflictos de Shortcodes

Si un shortcode se está procesando incorrectamente:

1. Verifica si hay múltiples funciones registrando el mismo shortcode
2. Comprueba el orden de carga (prioridad) de los archivos
3. Utiliza los filtros de WordPress para modificar el resultado de shortcodes existentes en lugar de redefinirlos

## Pruebas

Antes de enviar cambios, asegúrate de probar:

1. Todos los shortcodes nuevos o modificados
2. Compatibilidad con el tema NovaUI
3. Comportamiento responsive
4. Compatibilidad con diferentes versiones de WordPress

## Documentación

Cuando implementes nuevas características:

1. Actualiza este documento si es necesario
2. Documenta todas las nuevas funciones y shortcodes
3. Agrega ejemplos de uso para los desarrolladores