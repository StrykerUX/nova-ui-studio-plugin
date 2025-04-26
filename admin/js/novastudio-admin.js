/**
 * NovaStudio - Plugin de personalización para WordPress
 * JavaScript para el área de administración
 */

(function($) {
    'use strict';

    // Objeto principal del plugin de administración
    var NovaStudioAdmin = {
        
        /**
         * Inicializa el plugin
         */
        init: function() {
            var self = this;
            
            $(document).ready(function() {
                self.initColorPickers();
                self.initMediaUploaders();
                self.initSortable();
                self.setupTabNavigation();
                self.setupColorPresets();
                self.setupPreview();
                self.bindEvents();
            });
        },
        
        /**
         * Inicializa los selectores de color con wp-color-picker
         */
        initColorPickers: function() {
            $('.novastudio-color-field').wpColorPicker({
                change: function(event, ui) {
                    // Actualizar vista previa cuando se cambia un color
                    NovaStudioAdmin.updatePreview();
                }
            });
        },
        
        /**
         * Inicializa los selectores de medios de WordPress
         */
        initMediaUploaders: function() {
            // En cada botón de subida de imagen
            $('.novastudio-upload-button').each(function() {
                var $button = $(this);
                var $input = $button.prev('input');
                var $preview = $button.closest('.novastudio-media-uploader').find('.novastudio-image-preview');
                
                $button.on('click', function(e) {
                    e.preventDefault();
                    
                    // Si ya existe una instancia, la reutilizamos
                    if (wp.media.frames.novastudio) {
                        wp.media.frames.novastudio.open();
                        return;
                    }
                    
                    // Crear una nueva instancia
                    wp.media.frames.novastudio = wp.media({
                        title: 'Seleccionar Imagen',
                        button: { text: 'Usar esta imagen' },
                        multiple: false
                    });
                    
                    // Cuando se selecciona una imagen
                    wp.media.frames.novastudio.on('select', function() {
                        var attachment = wp.media.frames.novastudio.state().get('selection').first().toJSON();
                        $input.val(attachment.url);
                        
                        // Actualizar vista previa
                        $preview.html('<img src="' + attachment.url + '" alt="Preview" /><button class="button button-link novastudio-remove-image">Eliminar</button>');
                        
                        // Actualizar iframe de vista previa
                        NovaStudioAdmin.updatePreview();
                    });
                    
                    wp.media.frames.novastudio.open();
                });
            });
            
            // Eliminar imagen
            $(document).on('click', '.novastudio-remove-image', function(e) {
                e.preventDefault();
                var $preview = $(this).closest('.novastudio-image-preview');
                var $input = $preview.closest('.novastudio-media-uploader').find('input[type="text"]');
                
                $input.val('');
                $preview.empty();
                
                // Actualizar iframe de vista previa
                NovaStudioAdmin.updatePreview();
            });
        },
        
        /**
         * Inicializa los elementos ordenables (sortable)
         */
        initSortable: function() {
            // Sortable para elementos de menú
            if ($('.novastudio-menu-items').length) {
                $('.novastudio-menu-items').sortable({
                    handle: '.novastudio-menu-item-header',
                    placeholder: 'novastudio-menu-item-placeholder',
                    forcePlaceholderSize: true,
                    update: function(event, ui) {
                        // Reordenar los elementos
                        NovaStudioAdmin.updateMenuOrder();
                    }
                });
                
                // Sortable para submenús
                $('.novastudio-submenu-items').sortable({
                    handle: '.novastudio-menu-item-header',
                    placeholder: 'novastudio-menu-item-placeholder',
                    forcePlaceholderSize: true,
                    containment: 'parent',
                    update: function(event, ui) {
                        // Reordenar los subelementos
                        NovaStudioAdmin.updateMenuOrder();
                    }
                });
            }
        },
        
        /**
         * Configura la navegación por pestañas
         */
        setupTabNavigation: function() {
            // Tabs verticales si existen
            if ($('.novastudio-vertical-tabs').length) {
                $('.novastudio-tab-nav-item').on('click', function() {
                    var tabId = $(this).data('tab');
                    
                    // Activar tab seleccionado
                    $('.novastudio-tab-nav-item').removeClass('active');
                    $(this).addClass('active');
                    
                    // Mostrar contenido de tab
                    $('.novastudio-tab-panel').hide();
                    $('#' + tabId).show();
                });
                
                // Activar primer tab por defecto
                $('.novastudio-tab-nav-item:first').click();
            }
        },
        
        /**
         * Configura los presets de color
         */
        setupColorPresets: function() {
            $('.novastudio-preset-item').on('click', function() {
                var preset = $(this).data('preset');
                
                // Marcar preset como activo
                $('.novastudio-preset-item').removeClass('active');
                $(this).addClass('active');
                
                // Aplicar valores de preset
                NovaStudioAdmin.applyColorPreset(preset);
            });
        },
        
        /**
         * Aplica un preset de color a los campos
         * 
         * @param {string} preset Nombre del preset
         */
        applyColorPreset: function(preset) {
            var presets = {
                'default': {
                    'primary': '#FF6B6B',
                    'secondary': '#4ECDC4',
                    'accent': '#FFE66D',
                    'success': '#7BC950',
                    'warning': '#FFA552',
                    'error': '#F76F8E'
                },
                'calm': {
                    'primary': '#6B9BFF',
                    'secondary': '#4ECDC4',
                    'accent': '#C3E88D',
                    'success': '#7BC950',
                    'warning': '#FFA552',
                    'error': '#F76F8E'
                },
                'vibrant': {
                    'primary': '#FF6E9C',
                    'secondary': '#8A7AFF',
                    'accent': '#FFC53D',
                    'success': '#7BC950',
                    'warning': '#FFA552',
                    'error': '#F76F8E'
                },
                'earthy': {
                    'primary': '#E07A5F',
                    'secondary': '#81B29A',
                    'accent': '#F2CC8F',
                    'success': '#7BC950',
                    'warning': '#FFA552',
                    'error': '#F76F8E'
                }
            };
            
            if (!presets[preset]) {
                return;
            }
            
            // Actualizar cada campo de color con los valores del preset
            $.each(presets[preset], function(key, value) {
                var $input = $('#color_' + key);
                if ($input.length) {
                    $input.val(value).wpColorPicker('color', value);
                }
            });
            
            // Actualizar vista previa
            this.updatePreview();
        },
        
        /**
         * Configura el iframe de vista previa
         */
        setupPreview: function() {
            // Si no existe el iframe, no hacer nada
            if (!$('#novastudio-preview-iframe').length) {
                return;
            }
            
            // Esperar a que el iframe haya cargado
            $('#novastudio-preview-iframe').on('load', function() {
                NovaStudioAdmin.updatePreview();
            });
        },
        
        /**
         * Actualiza la vista previa con los valores actuales
         */
        updatePreview: function() {
            // Si no existe el iframe, no hacer nada
            if (!$('#novastudio-preview-iframe').length) {
                return;
            }
            
            var $iframe = $('#novastudio-preview-iframe');
            var iframeWindow = $iframe[0].contentWindow;
            
            // Generar CSS personalizado
            var customCSS = ':root {\n';
            
            // Colores
            $('.novastudio-color-field').each(function() {
                var $input = $(this);
                var name = $input.attr('id').replace('color_', '');
                var value = $input.val();
                
                if (value) {
                    customCSS += '  --color-' + name + ': ' + value + ';\n';
                }
            });
            
            // Tipografía
            var fontPrimary = $('#font_primary').val();
            var fontSecondary = $('#font_secondary').val();
            var baseSize = $('#font_size_base').val();
            
            if (fontPrimary) {
                customCSS += '  --font-primary: ' + fontPrimary + ';\n';
            }
            
            if (fontSecondary) {
                customCSS += '  --font-secondary: ' + fontSecondary + ';\n';
            }
            
            if (baseSize) {
                customCSS += '  --font-size-base: ' + baseSize + ';\n';
            }
            
            // Cerrar el bloque root
            customCSS += '}\n';
            
            // Agregar CSS personalizado si existe
            if ($('#custom_css').length) {
                customCSS += $('#custom_css').val();
            }
            
            // Inyectar CSS en el iframe
            try {
                var iframeDoc = iframeWindow.document;
                var styleId = 'novastudio-preview-style';
                
                // Eliminar style anterior si existe
                var oldStyle = iframeDoc.getElementById(styleId);
                if (oldStyle) {
                    oldStyle.remove();
                }
                
                // Crear nuevo style y agregar al head del iframe
                var style = iframeDoc.createElement('style');
                style.id = styleId;
                style.innerHTML = customCSS;
                iframeDoc.head.appendChild(style);
                
                // Forzar reflow
                iframeDoc.body.style.display = 'none';
                iframeDoc.body.offsetHeight; // Forzar reflow
                iframeDoc.body.style.display = '';
                
            } catch (e) {
                console.error('Error al actualizar vista previa:', e);
            }
        },
        
        /**
         * Actualiza el orden de los elementos del menú
         */
        updateMenuOrder: function() {
            // Actualizar los índices de los campos
            $('.novastudio-menu-items .novastudio-menu-item').each(function(index) {
                var $item = $(this);
                var itemId = $item.data('id');
                
                // Actualizar campo de orden
                $item.find('input[name$="[order]"]').val(index);
                
                // Actualizar los índices en los nombres de los campos
                $item.find('input, select, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/\[menu_items\]\[\d+\]/, '[menu_items][' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        },
        
        /**
         * Vincula eventos de UI
         */
        bindEvents: function() {
            var self = this;
            
            // Formulario principal
            $('#novastudio-options-form').on('submit', function(e) {
                // El submit normal es manejado por WordPress
            });
            
            // Botón para resetear a valores predeterminados
            $('#novastudio-reset-settings').on('click', function(e) {
                e.preventDefault();
                
                if (confirm(novaStudioAdmin.strings.resetConfirm)) {
                    self.resetSettings();
                }
            });
            
            // Insertar snippets de CSS
            $('.novastudio-insert-css').on('click', function(e) {
                e.preventDefault();
                
                var cssSnippet = $(this).prev('pre').text();
                var $cssEditor = $('#custom_css');
                
                // Si hay un editor CodeMirror activo
                if (wp.codeEditor && wp.codeEditor.defaultSettings) {
                    var editor = wp.codeEditor.initialize($cssEditor[0]);
                    var currentValue = editor.codemirror.getValue();
                    editor.codemirror.setValue(currentValue + '\n\n' + cssSnippet);
                } else {
                    var currentValue = $cssEditor.val();
                    $cssEditor.val(currentValue + '\n\n' + cssSnippet);
                }
            });
            
            // Expandir/colapsar elementos de menú
            $(document).on('click', '.novastudio-menu-item-toggle', function(e) {
                e.preventDefault();
                
                var $item = $(this).closest('.novastudio-menu-item');
                $item.toggleClass('expanded');
                
                // Cambiar icono
                var $icon = $(this).find('i');
                if ($item.hasClass('expanded')) {
                    $icon.removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
                } else {
                    $icon.removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
                }
            });
            
            // Eliminar elemento de menú
            $(document).on('click', '.novastudio-menu-item-delete', function(e) {
                e.preventDefault();
                
                if (confirm('¿Estás seguro de que deseas eliminar este elemento?')) {
                    $(this).closest('.novastudio-menu-item').remove();
                    self.updateMenuOrder();
                }
            });
            
            // Agregar nuevo elemento de menú
            $('.novastudio-add-menu-item').on('click', function(e) {
                e.preventDefault();
                
                var $container = $('.novastudio-menu-items');
                var itemCount = $container.find('.novastudio-menu-item').length;
                
                // Template para nuevo elemento (esto debería venir de un script en la página)
                var template = $('#novastudio-menu-item-template').html();
                if (!template) {
                    alert('Error: Template no encontrado');
                    return;
                }
                
                // Reemplazar placeholders con valores reales
                template = template.replace(/\{index\}/g, itemCount);
                
                // Agregar nuevo elemento al contenedor
                $container.append(template);
                
                // Inicializar nuevos controles si es necesario
                self.initColorPickers();
                self.initMediaUploaders();
                
                // Actualizar orden
                self.updateMenuOrder();
            });
            
            // Selector de iconos
            $(document).on('click', '.novastudio-icon-selector-button', function(e) {
                e.preventDefault();
                
                var $button = $(this);
                var $modal = $('#novastudio-icon-selector-modal');
                var $input = $button.siblings('input[type="hidden"]');
                
                // Mostrar modal
                $modal.show();
                
                // Marcar icono actualmente seleccionado
                var currentIcon = $input.val();
                if (currentIcon) {
                    $modal.find('.novastudio-icon-item[data-icon="' + currentIcon + '"]').addClass('selected');
                }
                
                // Configurar callback para cuando se selecciona un icono
                $modal.data('callback', function(icon) {
                    $input.val(icon);
                    $button.find('i').attr('class', 'dashicons dashicons-' + icon);
                    $modal.hide();
                });
            });
            
            // Seleccionar icono
            $(document).on('click', '.novastudio-icon-item', function(e) {
                e.preventDefault();
                
                var $item = $(this);
                var $modal = $item.closest('#novastudio-icon-selector-modal');
                var icon = $item.data('icon');
                var callback = $modal.data('callback');
                
                // Marcar como seleccionado
                $modal.find('.novastudio-icon-item').removeClass('selected');
                $item.addClass('selected');
                
                // Llamar al callback
                if (typeof callback === 'function') {
                    callback(icon);
                }
            });
            
            // Cerrar modal de iconos
            $(document).on('click', '.novastudio-icon-selector-close', function(e) {
                e.preventDefault();
                $('#novastudio-icon-selector-modal').hide();
            });
            
            // Cerrar modal al hacer clic fuera
            $(document).on('click', function(e) {
                var $modal = $('#novastudio-icon-selector-modal');
                if ($modal.is(':visible') && !$(e.target).closest('.novastudio-icon-selector-modal-content, .novastudio-icon-selector-button').length) {
                    $modal.hide();
                }
            });
        },
        
        /**
         * Resetea la configuración a valores predeterminados vía AJAX
         */
        resetSettings: function() {
            $.ajax({
                url: novaStudioAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'novastudio_reset_settings',
                    nonce: novaStudioAdmin.nonce
                },
                beforeSend: function() {
                    // Mostrar indicador de carga
                    $('body').addClass('novastudio-loading');
                },
                success: function(response) {
                    if (response.success) {
                        // Mostrar mensaje de éxito
                        alert(novaStudioAdmin.strings.resetSuccess);
                        
                        // Recargar la página para mostrar los valores por defecto
                        window.location.reload();
                    } else {
                        // Mostrar mensaje de error
                        alert(novaStudioAdmin.strings.resetError);
                    }
                },
                error: function() {
                    // Mostrar mensaje de error
                    alert(novaStudioAdmin.strings.resetError);
                },
                complete: function() {
                    // Ocultar indicador de carga
                    $('body').removeClass('novastudio-loading');
                }
            });
        }
    };
    
    // Inicializar el admin del plugin
    NovaStudioAdmin.init();
    
})(jQuery);
