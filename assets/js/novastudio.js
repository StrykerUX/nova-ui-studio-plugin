/**
 * NovaStudio - Script principal
 * 
 * Maneja las funcionalidades básicas del plugin de personalización
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM para personalización de temas
    const themeToggle = document.querySelector('.theme-toggle');
    const themePresets = document.querySelectorAll('.theme-preset-option');
    const colorPickers = document.querySelectorAll('.color-picker');
    const fontSelectors = document.querySelectorAll('.font-selector');
    
    // Toggle del tema claro/oscuro
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark-mode');
            
            // Guardar preferencia
            const isDarkMode = document.documentElement.classList.contains('dark-mode');
            localStorage.setItem('novaui_dark_mode', isDarkMode ? 'dark' : 'light');
            
            // Si está en un iframe para vista previa, enviar mensaje al padre
            if (window.parent && window !== window.parent) {
                window.parent.postMessage({
                    action: 'themeChange',
                    theme: isDarkMode ? 'dark' : 'light'
                }, '*');
            }
        });
    }
    
    // Selección de presets de tema
    if (themePresets && themePresets.length > 0) {
        themePresets.forEach(preset => {
            preset.addEventListener('click', function() {
                const presetId = this.dataset.preset;
                
                // Eliminar clase activa de todos los presets
                themePresets.forEach(p => p.classList.remove('active'));
                
                // Añadir clase activa al preset seleccionado
                this.classList.add('active');
                
                // Aplicar los valores del preset
                applyPreset(presetId);
            });
        });
    }
    
    // Función para aplicar un preset de tema
    function applyPreset(presetId) {
        let presetValues = {};
        
        // Presets predefinidos
        const presets = {
            'default': {
                '--color-primary': '#FF6B6B',
                '--color-secondary': '#4ECDC4',
                '--color-accent': '#FFE66D',
                '--font-primary': "'Jost', 'Quicksand', sans-serif",
            },
            'ocean': {
                '--color-primary': '#3498db',
                '--color-secondary': '#1abc9c',
                '--color-accent': '#f1c40f',
                '--font-primary': "'Montserrat', sans-serif",
            },
            'forest': {
                '--color-primary': '#27ae60',
                '--color-secondary': '#2ecc71',
                '--color-accent': '#f39c12',
                '--font-primary': "'Poppins', sans-serif",
            },
            'sunset': {
                '--color-primary': '#e74c3c',
                '--color-secondary': '#f39c12',
                '--color-accent': '#9b59b6',
                '--font-primary': "'Roboto', sans-serif",
            }
        };
        
        if (presets[presetId]) {
            presetValues = presets[presetId];
            
            // Aplicar valores de CSS
            for (const [property, value] of Object.entries(presetValues)) {
                document.documentElement.style.setProperty(property, value);
            }
            
            // Actualizar los controles de color
            updateColorControls(presetValues);
            
            // Si está en modo admin, guardar los cambios
            if (typeof saveCustomizationSettings === 'function') {
                saveCustomizationSettings(presetValues);
            }
        }
    }
    
    // Actualizar los controles de color según valores aplicados
    function updateColorControls(values) {
        if (colorPickers && colorPickers.length > 0) {
            colorPickers.forEach(picker => {
                const property = picker.dataset.cssProperty;
                if (values[property]) {
                    picker.value = convertToHex(values[property]);
                }
            });
        }
        
        if (fontSelectors && fontSelectors.length > 0) {
            fontSelectors.forEach(selector => {
                const property = selector.dataset.cssProperty;
                if (values[property]) {
                    // Extraer nombre de fuente principal de la cadena
                    const fontMatch = values[property].match(/'([^']+)'/);
                    if (fontMatch && fontMatch[1]) {
                        for (let i = 0; i < selector.options.length; i++) {
                            if (selector.options[i].value === fontMatch[1]) {
                                selector.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Convertir color a formato hexadecimal
    function convertToHex(color) {
        // Si ya es hex, devolverlo
        if (color.startsWith('#')) {
            return color;
        }
        
        // Si es rgb o rgba, convertir a hex
        const rgbMatch = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d.]+)?\)/);
        if (rgbMatch) {
            return '#' + 
                parseInt(rgbMatch[1]).toString(16).padStart(2, '0') +
                parseInt(rgbMatch[2]).toString(16).padStart(2, '0') +
                parseInt(rgbMatch[3]).toString(16).padStart(2, '0');
        }
        
        return color;
    }
    
    // Cambios en selectores de color
    if (colorPickers && colorPickers.length > 0) {
        colorPickers.forEach(picker => {
            picker.addEventListener('input', function() {
                const property = this.dataset.cssProperty;
                const value = this.value;
                
                // Aplicar el cambio de color
                document.documentElement.style.setProperty(property, value);
                
                // Si está en modo admin, guardar los cambios
                if (typeof saveCustomizationChanges === 'function') {
                    saveCustomizationChanges(property, value);
                }
            });
        });
    }
    
    // Cambios en selectores de fuente
    if (fontSelectors && fontSelectors.length > 0) {
        fontSelectors.forEach(selector => {
            selector.addEventListener('change', function() {
                const property = this.dataset.cssProperty;
                const fontName = this.value;
                let fontStack = '';
                
                // Establecer stack de fuentes según selección
                switch (fontName) {
                    case 'Jost':
                        fontStack = "'Jost', 'Quicksand', sans-serif";
                        break;
                    case 'Montserrat':
                        fontStack = "'Montserrat', 'Roboto', sans-serif";
                        break;
                    case 'Roboto':
                        fontStack = "'Roboto', 'Arial', sans-serif";
                        break;
                    case 'Poppins':
                        fontStack = "'Poppins', 'Open Sans', sans-serif";
                        break;
                    case 'Open Sans':
                        fontStack = "'Open Sans', 'Helvetica', sans-serif";
                        break;
                    default:
                        fontStack = `'${fontName}', sans-serif`;
                }
                
                // Aplicar el cambio de fuente
                document.documentElement.style.setProperty(property, fontStack);
                
                // Si está en modo admin, guardar los cambios
                if (typeof saveCustomizationChanges === 'function') {
                    saveCustomizationChanges(property, fontStack);
                }
            });
        });
    }
    
    // Vista previa en tiempo real
    const previewFrame = document.getElementById('novastudio-preview');
    if (previewFrame) {
        // Sincronizar cambios con el iframe de vista previa
        window.addEventListener('message', function(event) {
            if (event.data && event.data.action === 'ready') {
                const styleData = {
                    action: 'updateStyles',
                    styles: getCSSVariables()
                };
                previewFrame.contentWindow.postMessage(styleData, '*');
            }
        });
        
        // Obtener todas las variables CSS aplicadas actualmente
        function getCSSVariables() {
            const styles = {};
            const root = document.documentElement;
            const computedStyle = getComputedStyle(root);
            
            // Lista de propiedades a sincronizar
            const properties = [
                '--color-primary',
                '--color-secondary',
                '--color-accent',
                '--color-success',
                '--color-warning',
                '--color-error',
                '--font-primary',
                '--font-secondary',
                '--border-radius-lg',
                '--shadow-md'
            ];
            
            properties.forEach(prop => {
                styles[prop] = computedStyle.getPropertyValue(prop).trim();
            });
            
            // Añadir modo oscuro/claro
            styles['dark-mode'] = root.classList.contains('dark-mode');
            
            return styles;
        }
    }
    
    // Inicializar tema según preferencias guardadas
    function initTheme() {
        // Estado del tema
        const darkMode = localStorage.getItem('novaui_dark_mode');
        if (darkMode === 'dark') {
            document.documentElement.classList.add('dark-mode');
        } else if (darkMode === 'light') {
            document.documentElement.classList.remove('dark-mode');
        } else {
            // Si no hay preferencia guardada, usar preferencia del sistema
            const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDarkMode) {
                document.documentElement.classList.add('dark-mode');
            }
        }
    }
    
    // Iniciar
    initTheme();
});

// Función auxiliar para la Página de Ejemplo-random
function setupExamplePage() {
    // Solo ejecutar en la página Ejemplo-random
    if (!document.body.classList.contains('page-template-ejemplo-random') && 
        !document.body.classList.contains('page-id-ejemplo-random')) {
        return;
    }
    
    console.log('Inicializando página de ejemplo NovaUI...');
    
    // Añadir efectos visuales a los botones
    const buttons = document.querySelectorAll('.neo-button');
    buttons.forEach(button => {
        // Efecto de presión al hacer clic
        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '2px 2px 0 rgba(0, 0, 0, 0.1)';
        });
        
        // Restaurar al soltar
        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '6px 6px 0 rgba(0, 0, 0, 0.1)';
        });
        
        // También restaurar si el mouse sale del botón
        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    // Añadir efecto de hover a las tarjetas
    const cards = document.querySelectorAll('.neo-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '8px 8px 0 rgba(0, 0, 0, 0.1)';
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

// Ejecutar setupExamplePage cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', setupExamplePage);
