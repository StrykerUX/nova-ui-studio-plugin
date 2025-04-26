/**
 * NovaStudio - Plugin JavaScript principal
 * Script para el frontend del plugin de personalización
 */
(function() {
    'use strict';
    
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar funcionalidad del plugin
        initNovaStudio();
    });
    
    /**
     * Inicializar todas las funcionalidades del plugin
     */
    function initNovaStudio() {
        // Detectar si el tema NovaUI está activo
        const isNovaUIActive = document.body.classList.contains('theme-nova-ui') || 
                              document.querySelector('style[id^="nova-ui"]') !== null;
        
        if (!isNovaUIActive) {
            console.warn('NovaStudio: El tema NovaUI no parece estar activo. Algunas funcionalidades pueden no estar disponibles.');
        }
        
        // Inicializar componentes del plugin
        initThemeToggle();
        initPresetSelectors();
        initLivePreview();
        
        // Si estamos en una página de administración
        if (document.body.classList.contains('wp-admin')) {
            initAdminFeatures();
        }
    }
    
    /**
     * Inicializar el toggle de tema claro/oscuro
     */
    function initThemeToggle() {
        const toggles = document.querySelectorAll('.novastudio-theme-toggle, .saas-dark-mode-toggle');
        
        toggles.forEach(toggle => {
            // Si ya tiene listener, no agregar otro
            if (toggle.getAttribute('data-initialized') === 'true') return;
            
            toggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark-mode');
                
                // Guardar preferencia en localStorage
                const isDarkMode = document.documentElement.classList.contains('dark-mode');
                localStorage.setItem('novauiDarkMode', isDarkMode ? 'dark' : 'light');
                
                // Si window.novaUIDarkMode existe (definido en el tema), usarlo
                if (window.novaUIDarkMode && typeof window.novaUIDarkMode.toggle === 'function') {
                    window.novaUIDarkMode.toggle();
                }
            });
            
            // Marcar como inicializado
            toggle.setAttribute('data-initialized', 'true');
        });
    }
    
    /**
     * Inicializar selectores de presets de tema
     */
    function initPresetSelectors() {
        const presetItems = document.querySelectorAll('.novastudio-preset-card, .novastudio-preset-item');
        
        presetItems.forEach(item => {
            item.addEventListener('click', function() {
                const presetName = this.getAttribute('data-preset');
                if (!presetName) return;
                
                // Aplicar el preset seleccionado
                applyPreset(presetName);
                
                // Marcar este preset como activo
                document.querySelectorAll('.novastudio-preset-card.active, .novastudio-preset-item.active')
                    .forEach(activeItem => activeItem.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
    
    /**
     * Aplicar un preset de tema
     * @param {string} presetName - Nombre del preset a aplicar
     */
    function applyPreset(presetName) {
        // Valores de presets predefinidos
        const presets = {
            'default': {
                primary: '#FF6B6B',
                secondary: '#4ECDC4',
                accent: '#FFE66D',
                success: '#7BC950',
                warning: '#FFA552',
                error: '#F76F8E'
            },
            'calm': {
                primary: '#6B9BFF',
                secondary: '#4ECDC4',
                accent: '#C3E88D',
                success: '#7BC950',
                warning: '#FFA552',
                error: '#F76F8E'
            },
            'vibrant': {
                primary: '#FF6E9C',
                secondary: '#8A7AFF',
                accent: '#FFC53D',
                success: '#7BC950',
                warning: '#FFA552',
                error: '#F76F8E'
            },
            'earthy': {
                primary: '#E07A5F',
                secondary: '#81B29A',
                accent: '#F2CC8F',
                success: '#7BC950',
                warning: '#FFA552',
                error: '#F76F8E'
            }
        };
        
        // Si el preset no existe, no hacer nada
        if (!presets[presetName]) {
            console.warn(`NovaStudio: Preset "${presetName}" no encontrado.`);
            return;
        }
        
        // Aplicar valores del preset a los campos del formulario
        const preset = presets[presetName];
        Object.keys(preset).forEach(key => {
            const input = document.getElementById(`color_${key}`);
            if (input) {
                input.value = preset[key];
                // Si es un color picker, actualizar la visualización
                if (input.classList.contains('novastudio-color-field') && window.wpColorPicker) {
                    jQuery(input).wpColorPicker('color', preset[key]);
                }
            }
        });
        
        // Actualizar variable de preset activo
        document.documentElement.style.setProperty('--current-preset', presetName);
        
        // Si hay vista previa en tiempo real, actualizar
        updateLivePreview(preset);
    }
    
    /**
     * Inicializar vista previa en tiempo real
     */
    function initLivePreview() {
        // Seleccionar todos los inputs de color
        const colorInputs = document.querySelectorAll('.novastudio-color-field, [id^="color_"]');
        
        // Agregar listeners para actualizar la vista previa
        colorInputs.forEach(input => {
            // Para inputs normales
            input.addEventListener('input', function() {
                const colorType = this.id.replace('color_', '');
                const color = this.value;
                
                // Actualizar CSS variable directamente
                document.documentElement.style.setProperty(`--color-${colorType}`, color);
                
                // Actualizar vista previa
                const previewElements = document.querySelectorAll(`.preview-${colorType}`);
                previewElements.forEach(el => {
                    el.style.backgroundColor = color;
                });
            });
            
            // Para WP Color Picker (si está disponible)
            if (window.wpColorPicker && jQuery) {
                jQuery(input).on('wpColorPicker:change', function(event, ui) {
                    const colorType = event.target.id.replace('color_', '');
                    const color = ui.color.toString();
                    
                    // Actualizar CSS variable directamente
                    document.documentElement.style.setProperty(`--color-${colorType}`, color);
                    
                    // Actualizar vista previa
                    const previewElements = document.querySelectorAll(`.preview-${colorType}`);
                    previewElements.forEach(el => {
                        el.style.backgroundColor = color;
                    });
                });
            }
        });
        
        // Inicializar iframe de vista previa
        const previewIframe = document.getElementById('novastudio-preview-iframe');
        if (previewIframe) {
            previewIframe.addEventListener('load', function() {
                // Cuando el iframe se carga, crear una función para actualizar sus estilos
                window.updatePreviewIframe = function(styles) {
                    try {
                        const iframeDoc = previewIframe.contentDocument || previewIframe.contentWindow.document;
                        
                        // Buscar si ya existe un estilo para novastudio
                        let styleEl = iframeDoc.getElementById('novastudio-preview-styles');
                        
                        if (!styleEl) {
                            // Si no existe, crear uno nuevo
                            styleEl = iframeDoc.createElement('style');
                            styleEl.id = 'novastudio-preview-styles';
                            iframeDoc.head.appendChild(styleEl);
                        }
                        
                        // Actualizar los estilos
                        styleEl.textContent = styles;
                    } catch (error) {
                        console.error('Error al actualizar el iframe de vista previa:', error);
                    }
                };
            });
        }
    }
    
    /**
     * Actualizar la vista previa en tiempo real
     * @param {Object} styles - Objeto con los estilos a aplicar
     */
    function updateLivePreview(styles) {
        if (!styles) return;
        
        // Crear CSS para aplicar al documento principal y al iframe
        let cssText = `:root {\n`;
        
        // Agregar cada variable CSS
        Object.keys(styles).forEach(key => {
            cssText += `  --color-${key}: ${styles[key]};\n`;
        });
        
        cssText += `}\n`;
        
        // Aplicar al iframe si existe la función
        if (window.updatePreviewIframe) {
            window.updatePreviewIframe(cssText);
        }
        
        // Aplicar al documento principal
        Object.keys(styles).forEach(key => {
            document.documentElement.style.setProperty(`--color-${key}`, styles[key]);
        });
    }
    
    /**
     * Inicializar características específicas de administración
     */
    function initAdminFeatures() {
        // Media Uploader para logos
        initMediaUploader();
        
        // Editor de CSS
        initCSSEditor();
        
        // Gestión de menús
        initMenuManager();
        
        // Submit del formulario con AJAX
        initAjaxForm();
    }
    
    /**
     * Inicializar Media Uploader para selección de imágenes
     */
    function initMediaUploader() {
        // Verificar si están las dependencias de WordPress
        if (!window.wp || !window.wp.media || !jQuery) return;
        
        // Seleccionar todos los botones de upload
        const uploadButtons = document.querySelectorAll('.novastudio-upload-button');
        
        uploadButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Obtener el input y la vista previa relacionados
                const wrapper = this.closest('.novastudio-media-uploader');
                if (!wrapper) return;
                
                const input = wrapper.querySelector('input[type="text"]');
                const preview = wrapper.querySelector('.novastudio-image-preview');
                
                // Crear frame de media
                const frame = wp.media({
                    title: 'Seleccionar o subir imagen',
                    button: {
                        text: 'Usar esta imagen'
                    },
                    multiple: false
                });
                
                // Cuando se selecciona una imagen
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    
                    // Actualizar input con la URL
                    if (input) {
                        input.value = attachment.url;
                    }
                    
                    // Actualizar vista previa
                    if (preview) {
                        preview.innerHTML = `<img src="${attachment.url}" alt="Preview" />`;
                    }
                });
                
                // Abrir el selector de media
                frame.open();
            });
        });
    }
    
    /**
     * Inicializar editor de CSS personalizado
     */
    function initCSSEditor() {
        // Verificar si existe el editor de código de WordPress
        if (!window.wp || !window.wp.codeEditor) return;
        
        // Seleccionar el textarea de CSS personalizado
        const customCSSField = document.getElementById('custom_css');
        if (!customCSSField) return;
        
        // Verificar si ya está inicializado
        if (customCSSField.getAttribute('data-editor-initialized') === 'true') return;
        
        // Inicializar editor
        const editorSettings = wp.codeEditor.defaultSettings ? 
            Object.assign({}, wp.codeEditor.defaultSettings) : 
            { codemirror: { mode: 'css' } };
            
        editorSettings.codemirror.mode = 'css';
        editorSettings.codemirror.theme = document.documentElement.classList.contains('dark-mode') ? 
            'dark' : 'default';
            
        const editor = wp.codeEditor.initialize(customCSSField, editorSettings);
        
        // Marcar como inicializado
        customCSSField.setAttribute('data-editor-initialized', 'true');
        
        // Ejemplo de código para insertarse
        const codeExamples = document.querySelectorAll('.novastudio-insert-css');
        codeExamples.forEach(button => {
            button.addEventListener('click', function() {
                const example = this.previousElementSibling;
                if (!example || !example.textContent) return;
                
                // Insertar el ejemplo en el editor
                editor.codemirror.replaceSelection(example.textContent);
                editor.codemirror.focus();
            });
        });
    }
    
    /**
     * Inicializar gestor de menús y elementos de navegación
     */
    function initMenuManager() {
        // Verificar si jQuery y jQuery UI están disponibles
        if (!jQuery || !jQuery.ui || !jQuery.ui.sortable) return;
        
        // Inicializar sortable para arrastrar y soltar elementos
        jQuery('.novastudio-menu-items').sortable({
            items: '> .novastudio-menu-item',
            handle: '.novastudio-menu-item-handle',
            update: function() {
                // Actualizar números de orden
                jQuery(this).find('.novastudio-menu-item').each(function(index) {
                    jQuery(this).find('input[name*="[order]"]').val(index);
                });
            }
        });
        
        // Botón para agregar nuevo elemento
        const addButton = document.getElementById('novastudio-add-menu-item');
        if (addButton) {
            addButton.addEventListener('click', function() {
                const template = document.getElementById('novastudio-menu-item-template');
                if (!template) return;
                
                const container = document.querySelector('.novastudio-menu-items');
                if (!container) return;
                
                // Clonar la plantilla
                const newItem = template.content.cloneNode(true);
                
                // Generar ID único
                const uniqueId = 'item_' + Date.now();
                const inputs = newItem.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace('[TEMPLATE]', `[${uniqueId}]`));
                    }
                });
                
                // Asignar número de orden
                const orderInput = newItem.querySelector('input[name*="[order]"]');
                if (orderInput) {
                    orderInput.value = container.children.length;
                }
                
                // Agregar a la lista
                container.appendChild(newItem);
                
                // Reinicializar sortable
                jQuery('.novastudio-menu-items').sortable('refresh');
            });
        }
        
        // Botones para eliminar elementos
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('novastudio-remove-menu-item')) {
                const item = e.target.closest('.novastudio-menu-item');
                if (item) {
                    item.remove();
                    
                    // Actualizar números de orden
                    jQuery('.novastudio-menu-items .novastudio-menu-item').each(function(index) {
                        jQuery(this).find('input[name*="[order]"]').val(index);
                    });
                }
            }
        });
        
        // Toggle para mostrar/ocultar opciones del elemento
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('novastudio-toggle-item-options')) {
                const item = e.target.closest('.novastudio-menu-item');
                if (item) {
                    const content = item.querySelector('.novastudio-menu-item-content');
                    if (content) {
                        content.classList.toggle('open');
                        e.target.setAttribute('aria-expanded', content.classList.contains('open'));
                    }
                }
            }
        });
    }
    
    /**
     * Inicializar formulario con envío AJAX
     */
    function initAjaxForm() {
        // Verificar si jQuery está disponible
        if (!jQuery) return;
        
        // Seleccionar el formulario
        const form = document.getElementById('novastudio-options-form');
        if (!form) return;
        
        // Botón para restablecer configuración
        const resetButton = document.getElementById('novastudio-reset-settings');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                if (!confirm('¿Estás seguro de que deseas restablecer todas las configuraciones a sus valores predeterminados? Esta acción no se puede deshacer.')) {
                    return;
                }
                
                // Enviar solicitud AJAX para restablecer
                jQuery.ajax({
                    url: novaStudioAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'novastudio_reset_settings',
                        nonce: novaStudioAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            // Recargar la página
                            location.reload();
                        } else {
                            alert(response.data.message || 'Error al restablecer la configuración.');
                        }
                    },
                    error: function() {
                        alert('Error de conexión al intentar restablecer la configuración.');
                    }
                });
            });
        }
        
        // Submit del formulario con AJAX
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar indicador de carga
            form.classList.add('is-submitting');
            
            // Enviar formulario con AJAX
            jQuery.ajax({
                url: novaStudioAdmin.ajaxUrl,
                type: 'POST',
                data: jQuery(form).serialize() + '&action=novastudio_save_settings',
                success: function(response) {
                    form.classList.remove('is-submitting');
                    
                    if (response.success) {
                        // Mostrar mensaje de éxito
                        const successMessage = document.createElement('div');
                        successMessage.className = 'notice notice-success is-dismissible';
                        successMessage.innerHTML = `<p>${response.data.message}</p>`;
                        
                        const heading = form.querySelector('h2');
                        if (heading) {
                            heading.parentNode.insertBefore(successMessage, heading.nextSibling);
                        } else {
                            form.parentNode.insertBefore(successMessage, form);
                        }
                        
                        // Actualizar iframe de vista previa
                        const previewIframe = document.getElementById('novastudio-preview-iframe');
                        if (previewIframe) {
                            previewIframe.contentWindow.location.reload();
                        }
                    } else {
                        alert(response.data.message || 'Error al guardar la configuración.');
                    }
                },
                error: function() {
                    form.classList.remove('is-submitting');
                    alert('Error de conexión al intentar guardar la configuración.');
                }
            });
        });
    }
})();
