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
            
            // Verificar si el preset existe
            if (!presets[preset]) return;
            
            // Aplicar cada color del preset
            for (var color in presets[preset]) {
                if (presets[preset].hasOwnProperty(color)) {
                    var $colorField = $('#color_' + color);
                    $colorField.val(presets[preset][color]).trigger('change');
                }
            }
            
            // Actualizar vista previa
            this.updatePreview();
        },
        
        /**
         * Configura la vista previa en tiempo real
         */
        setupPreview: function() {
            // Configurar iframe de vista previa
            var $iframe = $('#novastudio-preview-iframe');
            
            // Cuando el iframe ha cargado
            $iframe.on('load', function() {
                // Inicializar eventos de refresco de vista previa
                NovaStudioAdmin.updatePreview();
            });
        },
        
        /**
         * Actualiza la vista previa en tiempo real
         */
        updatePreview: function() {
            var $iframe = $('#novastudio-preview-iframe');
            var iframeWindow = $iframe[0].contentWindow;
            
            // Verificar que el iframe esté cargado
            if (!iframeWindow) return;
            
            // Recopilar valores actuales de las opciones
            var cssVars = this.getCustomCSSVars();
            
            // Crear o actualizar hoja de estilos personalizada en el iframe
            try {
                var styleSheet = iframeWindow.document.getElementById('novastudio-preview-styles');
                
                if (!styleSheet) {
                    styleSheet = iframeWindow.document.createElement('style');
                    styleSheet.id = 'novastudio-preview-styles';
                    iframeWindow.document.head.appendChild(styleSheet);
                }
                
                // Aplicar estilos
                styleSheet.textContent = cssVars;
            } catch (e) {
                console.error('Error al actualizar vista previa:', e);
            }
        },
        
        /**
         * Genera las variables CSS personalizadas
         * 
         * @return {string} Variables CSS personalizadas
         */
        getCustomCSSVars: function() {
            var css = ':root {\n';
            
            // Colores
            $('.novastudio-color-field').each(function() {
                var $field = $(this);
                var id = $field.attr('id');
                var value = $field.val();
                
                if (id && value) {
                    var varName = '--color-' + id.replace('color_', '');
                    css += '    ' + varName + ': ' + value + ';\n';
                }
            });
            
            // Tipografía
            if ($('#font_primary').length) {
                css += '    --font-primary: ' + $('#font_primary').val() + ';\n';
            }
            
            if ($('#font_secondary').length) {
                css += '    --font-secondary: ' + $('#font_secondary').val() + ';\n';
            }
            
            if ($('#font_size_base').length) {
                css += '    --font-size-base: ' + $('#font_size_base').val() + ';\n';
            }
            
            // Sidebar
            if ($('#sidebar_expanded_width').length) {
                css += '    --sidebar-width-expanded: ' + $('#sidebar_expanded_width').val() + ';\n';
            }
            
            if ($('#sidebar_collapsed_width').length) {
                css += '    --sidebar-width-collapsed: ' + $('#sidebar_collapsed_width').val() + ';\n';
            }
            
            // Header
            if ($('#header_height').length) {
                css += '    --header-height: ' + $('#header_height').val() + ';\n';
            }
            
            css += '}\n';
            
            // Agregar CSS personalizado si existe
            if ($('#custom_css').length) {
                css += $('#custom_css').val();
            }
            
            return css;
        },
        
        /**
         * Actualiza el orden de los elementos de menú
         */
        updateMenuOrder: function() {
            var order = [];
            
            // Recorrer todos los elementos de menú
            $('.novastudio-menu-item').each(function(index) {
                var $item = $(this);
                var itemId = $item.data('id');
                
                // Actualizar orden en la interfaz
                $item.find('.novastudio-menu-item-order').val(index);
                
                // Agregar al array de orden
                order.push(itemId);
                
                // Procesar submenús si existen
                var subOrder = [];
                $item.find('.novastudio-submenu-item').each(function(subIndex) {
                    var $subItem = $(this);
                    var subItemId = $subItem.data('id');
                    
                    // Actualizar orden en la interfaz
                    $subItem.find('.novastudio-menu-item-order').val(subIndex);
                    
                    // Agregar al array de suborden
                    subOrder.push(subItemId);
                });
                
                // Guardar el orden de los submenús
                if (subOrder.length) {
                    $item.find('.novastudio-submenu-order').val(JSON.stringify(subOrder));
                }
            });
            
            // Guardar el orden en el campo oculto
            $('#novastudio_menu_order').val(JSON.stringify(order));
        },
        
        /**
         * Vincula eventos del formulario
         */
        bindEvents: function() {
            var self = this;
            
            // Envío del formulario
            $('#novastudio-options-form').on('submit', function(e) {
                e.preventDefault();
                
                // Mostrar indicador de carga
                self.showLoadingIndicator();
                
                // Recopilar datos del formulario
                var formData = new FormData(this);
                formData.append('action', 'novastudio_save_settings');
                formData.append('nonce', novaStudioAdmin.nonce);
                
                // Enviar solicitud AJAX
                $.ajax({
                    url: novaStudioAdmin.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        self.hideLoadingIndicator();
                        
                        if (response.success) {
                            self.showMessage(response.data.message, 'success');
                        } else {
                            self.showMessage(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        self.hideLoadingIndicator();
                        self.showMessage(novaStudioAdmin.strings.saveError, 'error');
                    }
                });
            });
            
            // Restablecimiento de valores predeterminados
            $('#novastudio-reset-settings').on('click', function(e) {
                e.preventDefault();
                
                if (confirm(novaStudioAdmin.strings.resetConfirm)) {
                    // Mostrar indicador de carga
                    self.showLoadingIndicator();
                    
                    // Enviar solicitud AJAX
                    $.ajax({
                        url: novaStudioAdmin.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'novastudio_reset_settings',
                            nonce: novaStudioAdmin.nonce
                        },
                        success: function(response) {
                            self.hideLoadingIndicator();
                            
                            if (response.success) {
                                self.showMessage(response.data.message, 'success');
                                
                                // Recargar la página para aplicar valores predeterminados
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                self.showMessage(response.data.message, 'error');
                            }
                        },
                        error: function() {
                            self.hideLoadingIndicator();
                            self.showMessage(novaStudioAdmin.strings.resetError, 'error');
                        }
                    });
                }
            });
            
            // Eventos para menús y submenús
            $(document).on('click', '.novastudio-menu-item-toggle', function(e) {
                e.preventDefault();
                $(this).closest('.novastudio-menu-item').toggleClass('expanded');
            });
            
            $(document).on('click', '.novastudio-add-menu-item', function(e) {
                e.preventDefault();
                self.addMenuItem();
            });
            
            $(document).on('click', '.novastudio-add-submenu-item', function(e) {
                e.preventDefault();
                var $parentItem = $(this).closest('.novastudio-menu-item');
                self.addSubmenuItem($parentItem);
            });
            
            $(document).on('click', '.novastudio-remove-menu-item', function(e) {
                e.preventDefault();
                $(this).closest('.novastudio-menu-item, .novastudio-submenu-item').remove();
                self.updateMenuOrder();
            });
            
            // Insertar ejemplos de CSS
            $('.novastudio-insert-css').on('click', function(e) {
                e.preventDefault();
                var css = $(this).prev('pre').text();
                var $customCss = $('#custom_css');
                
                // Obtener instancia de CodeMirror si está disponible
                var cm = $customCss.data('cm');
                
                if (cm) {
                    var doc = cm.getDoc();
                    var cursor = doc.getCursor();
                    doc.replaceRange(css, cursor);
                } else {
                    // Fallback si CodeMirror no está disponible
                    $customCss.val($customCss.val() + '\n' + css);
                }
                
                // Actualizar vista previa
                self.updatePreview();
            });
            
            // Cambio en campos de formulario
            $('#novastudio-options-form input, #novastudio-options-form select, #novastudio-options-form textarea').on('change', function() {
                self.updatePreview();
            });
        },
        
        /**
         * Muestra un indicador de carga
         */
        showLoadingIndicator: function() {
            // Si ya existe un indicador, no crear otro
            if ($('#novastudio-loading').length) return;
            
            $('body').append('<div id="novastudio-loading" class="novastudio-loading"><div class="novastudio-spinner"></div></div>');
        },
        
        /**
         * Oculta el indicador de carga
         */
        hideLoadingIndicator: function() {
            $('#novastudio-loading').remove();
        },
        
        /**
         * Muestra un mensaje de notificación
         * 
         * @param {string} message Mensaje a mostrar
         * @param {string} type    Tipo de mensaje ('success', 'error', 'warning', 'info')
         */
        showMessage: function(message, type) {
            var $notice = $('<div class="novastudio-notice novastudio-notice-' + type + '"><p>' + message + '</p></div>');
            
            // Agregar mensaje al inicio del formulario
            $('#novastudio-options-form').prepend($notice);
            
            // Eliminar mensaje después de 3 segundos
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },
        
        /**
         * Agrega un nuevo elemento de menú
         */
        addMenuItem: function() {
            var itemId = 'menu-item-' + Date.now();
            var itemIndex = $('.novastudio-menu-item').length;
            
            var itemTemplate = 
                '<div class="novastudio-menu-item" data-id="' + itemId + '">' +
                    '<div class="novastudio-menu-item-header">' +
                        '<span class="novastudio-menu-item-title">Nuevo Elemento</span>' +
                        '<div class="novastudio-menu-item-actions">' +
                            '<button type="button" class="button button-small novastudio-menu-item-toggle">Editar</button>' +
                            '<button type="button" class="button button-small novastudio-remove-menu-item">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="novastudio-menu-item-content">' +
                        '<input type="hidden" name="menu_items[' + itemIndex + '][id]" value="' + itemId + '">' +
                        '<input type="hidden" class="novastudio-menu-item-order" name="menu_items[' + itemIndex + '][order]" value="' + itemIndex + '">' +
                        '<div class="novastudio-form-group">' +
                            '<label>Título</label>' +
                            '<input type="text" name="menu_items[' + itemIndex + '][title]" value="Nuevo Elemento" class="regular-text">' +
                        '</div>' +
                        '<div class="novastudio-form-group">' +
                            '<label>URL</label>' +
                            '<input type="text" name="menu_items[' + itemIndex + '][url]" value="#" class="regular-text">' +
                        '</div>' +
                        '<div class="novastudio-form-group">' +
                            '<label>Icono</label>' +
                            '<input type="text" name="menu_items[' + itemIndex + '][icon]" value="dashicons-admin-generic" class="regular-text">' +
                        '</div>' +
                        '<div class="novastudio-submenu-container">' +
                            '<h4>Elementos de Submenú</h4>' +
                            '<div class="novastudio-submenu-items"></div>' +
                            '<input type="hidden" class="novastudio-submenu-order" name="menu_items[' + itemIndex + '][submenu_order]" value="[]">' +
                            '<button type="button" class="button novastudio-add-submenu-item">Agregar Elemento de Submenú</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            
            // Agregar el elemento al contenedor
            $('.novastudio-menu-items').append(itemTemplate);
            
            // Actualizar orden
            this.updateMenuOrder();
        },
        
        /**
         * Agrega un nuevo elemento de submenú
         * 
         * @param {jQuery} $parentItem Elemento padre
         */
        addSubmenuItem: function($parentItem) {
            var parentIndex = $parentItem.index();
            var itemId = 'submenu-item-' + Date.now();
            var itemIndex = $parentItem.find('.novastudio-submenu-item').length;
            
            var itemTemplate = 
                '<div class="novastudio-menu-item novastudio-submenu-item" data-id="' + itemId + '">' +
                    '<div class="novastudio-menu-item-header">' +
                        '<span class="novastudio-menu-item-title">Nuevo Subelemento</span>' +
                        '<div class="novastudio-menu-item-actions">' +
                            '<button type="button" class="button button-small novastudio-menu-item-toggle">Editar</button>' +
                            '<button type="button" class="button button-small novastudio-remove-menu-item">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="novastudio-menu-item-content">' +
                        '<input type="hidden" name="menu_items[' + parentIndex + '][submenu][' + itemIndex + '][id]" value="' + itemId + '">' +
                        '<input type="hidden" class="novastudio-menu-item-order" name="menu_items[' + parentIndex + '][submenu][' + itemIndex + '][order]" value="' + itemIndex + '">' +
                        '<div class="novastudio-form-group">' +
                            '<label>Título</label>' +
                            '<input type="text" name="menu_items[' + parentIndex + '][submenu][' + itemIndex + '][title]" value="Nuevo Subelemento" class="regular-text">' +
                        '</div>' +
                        '<div class="novastudio-form-group">' +
                            '<label>URL</label>' +
                            '<input type="text" name="menu_items[' + parentIndex + '][submenu][' + itemIndex + '][url]" value="#" class="regular-text">' +
                        '</div>' +
                    '</div>' +
                '</div>';
            
            // Agregar el elemento al contenedor de submenú
            $parentItem.find('.novastudio-submenu-items').append(itemTemplate);
            
            // Actualizar orden
            this.updateMenuOrder();
        }
    };
    
    // Inicializar el plugin
    NovaStudioAdmin.init();
    
})(jQuery);
