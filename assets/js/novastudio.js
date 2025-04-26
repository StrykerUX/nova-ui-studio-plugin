/**
 * NovaStudio - Plugin de personalización para WordPress
 * JavaScript para el frontend
 */

(function($) {
    'use strict';

    // Objeto principal del plugin
    var NovaStudio = {
        
        /**
         * Inicializa el plugin
         */
        init: function() {
            // Guardar referencia para el uso en callbacks
            var self = this;
            
            // Inicializar cuando el DOM esté listo
            $(document).ready(function() {
                self.setupThemeToggle();
                self.setupSidebar();
                self.bindEvents();
                self.applyCustomizations();
                
                // Inicializar funcionalidades específicas
                if ($('.saas-chat-container').length) {
                    self.initChatFeatures();
                }
                
                if ($('.saas-quicklinks-container').length) {
                    self.initQuickLinksFeatures();
                }
            });
        },
        
        /**
         * Configurar el toggle de tema claro/oscuro
         */
        setupThemeToggle: function() {
            var defaultMode = novaStudioData.themeMode || 'auto';
            var currentMode = localStorage.getItem('novaui-theme-mode') || defaultMode;
            
            // Detectar preferencia del sistema si está en modo auto
            if (currentMode === 'auto') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    currentMode = 'dark';
                } else {
                    currentMode = 'light';
                }
            }
            
            // Aplicar modo inicial
            this.setThemeMode(currentMode);
            
            // Configurar eventos para cambios de preferencia del sistema
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    if (localStorage.getItem('novaui-theme-mode') === 'auto') {
                        NovaStudio.setThemeMode(e.matches ? 'dark' : 'light');
                    }
                });
            }
        },
        
        /**
         * Establece el modo de tema
         * 
         * @param {string} mode Modo de tema ('light' o 'dark')
         */
        setThemeMode: function(mode) {
            if (mode === 'dark') {
                $('html').removeClass('light-mode').addClass('dark-mode');
                $('.saas-theme-toggle').html('<i class="sun-icon"></i>');
            } else {
                $('html').removeClass('dark-mode').addClass('light-mode');
                $('.saas-theme-toggle').html('<i class="moon-icon"></i>');
            }
        },
        
        /**
         * Configurar la barra lateral
         */
        setupSidebar: function() {
            var sidebarState = localStorage.getItem('novaui-sidebar-state') || 'expanded';
            
            if (sidebarState === 'collapsed') {
                $('.saas-sidebar').addClass('collapsed');
                $('.saas-main-content').addClass('sidebar-collapsed');
            }
        },
        
        /**
         * Vincular eventos de UI
         */
        bindEvents: function() {
            // Toggle de tema claro/oscuro
            $(document).on('click', '.saas-theme-toggle', function(e) {
                e.preventDefault();
                
                var currentMode = $('html').hasClass('dark-mode') ? 'dark' : 'light';
                var newMode = currentMode === 'dark' ? 'light' : 'dark';
                
                NovaStudio.setThemeMode(newMode);
                localStorage.setItem('novaui-theme-mode', newMode);
            });
            
            // Toggle de sidebar
            $(document).on('click', '.saas-sidebar-toggle', function(e) {
                e.preventDefault();
                
                $('.saas-sidebar').toggleClass('collapsed');
                $('.saas-main-content').toggleClass('sidebar-collapsed');
                
                var sidebarState = $('.saas-sidebar').hasClass('collapsed') ? 'collapsed' : 'expanded';
                localStorage.setItem('novaui-sidebar-state', sidebarState);
            });
            
            // Dropdown de usuario
            $(document).on('click', '.saas-user-menu-toggle', function(e) {
                e.preventDefault();
                $('.saas-user-menu').toggleClass('active');
            });
            
            // Cerrar dropdown al hacer clic en cualquier otro lugar
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.saas-user-menu-toggle, .saas-user-menu').length) {
                    $('.saas-user-menu').removeClass('active');
                }
            });
        },
        
        /**
         * Aplicar personalizaciones específicas del usuario
         */
        applyCustomizations: function() {
            // Aplicar cualquier personalización dinámica adicional
            // Esta función se puede extender según se necesite
        },
        
        /**
         * Inicializar características específicas del Chat IA
         */
        initChatFeatures: function() {
            // Animación de burbujas de chat
            $('.saas-chat-message').each(function(index) {
                var $this = $(this);
                setTimeout(function() {
                    $this.addClass('animated fadeIn');
                }, index * 100);
            });
            
            // Simulación de escritura de IA
            $('.saas-chat-ai.typing').each(function() {
                var $this = $(this);
                var $dots = $this.find('.typing-dots');
                
                setInterval(function() {
                    var dotsText = $dots.text();
                    if (dotsText.length >= 3) {
                        $dots.text('.');
                    } else {
                        $dots.text(dotsText + '.');
                    }
                }, 500);
            });
        },
        
        /**
         * Inicializar características específicas de Quick Links
         */
        initQuickLinksFeatures: function() {
            // Animación de entrada para tarjetas
            $('.saas-quicklink-card').each(function(index) {
                var $this = $(this);
                setTimeout(function() {
                    $this.addClass('animated slideUp');
                }, index * 100);
            });
            
            // Copiar al portapapeles para enlaces
            $('.saas-quicklink-copy').on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var linkUrl = $this.data('url');
                var $message = $this.siblings('.saas-quicklink-copy-message');
                
                // Copiar al portapapeles
                var tempInput = document.createElement('input');
                tempInput.value = linkUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                // Mostrar mensaje
                $message.fadeIn(200);
                setTimeout(function() {
                    $message.fadeOut(200);
                }, 2000);
            });
        },
        
        /**
         * Carga dinámica de CSS personalizado
         * 
         * @param {string} css Código CSS personalizado
         */
        loadCustomCSS: function(css) {
            if (!css) return;
            
            // Eliminar estilos personalizados anteriores
            $('#novastudio-custom-css').remove();
            
            // Agregar nuevos estilos personalizados
            $('head').append('<style id="novastudio-custom-css">' + css + '</style>');
        }
    };
    
    // Inicializar el plugin
    NovaStudio.init();
    
})(jQuery);
