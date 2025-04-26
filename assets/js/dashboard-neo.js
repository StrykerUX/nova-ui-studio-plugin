/**
 * Dashboard Neo-Brutalista Script
 * 
 * Maneja las interacciones y animaciones del dashboard con estilo neo-brutalista
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.getElementById('dashboardSidebar');
    const userMenuToggle = document.getElementById('userMenuToggle');
    const themeToggle = document.getElementById('theme-toggle');
    const dashboardMain = document.querySelector('.dashboard-main');
    
    // Toggle del sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Actualizar el aria-expanded
            const isExpanded = !sidebar.classList.contains('collapsed');
            sidebarToggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            
            // Guardar preferencia con localStorage
            localStorage.setItem('novastudio_sidebar_collapsed', sidebar.classList.contains('collapsed') ? 'true' : 'false');
        });
    }
    
    // Toggle del sidebar en móvil
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Cerrar sidebar en móvil al hacer clic fuera
    document.addEventListener('click', function(event) {
        const isMobile = window.innerWidth < 1024;
        if (isMobile && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(event.target) && 
            event.target !== mobileSidebarToggle) {
            sidebar.classList.remove('active');
        }
    });
    
    // Toggle del menú de usuario
    if (userMenuToggle) {
        // Crear el menú dropdown si no existe
        if (!document.querySelector('.dashboard-user-dropdown')) {
            const dropdown = document.createElement('div');
            dropdown.className = 'dashboard-user-dropdown';
            dropdown.innerHTML = `
                <div class="dashboard-user-dropdown-header">
                    <div class="dashboard-user-avatar">M</div>
                    <div class="dashboard-user-info">
                        <div class="dashboard-user-name">Miguel R.</div>
                        <div class="dashboard-user-email">miguel@example.com</div>
                    </div>
                </div>
                <ul class="dashboard-user-menu-list">
                    <li>
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Log Out
                        </a>
                    </li>
                </ul>
            `;
            userMenuToggle.parentNode.appendChild(dropdown);
        }
        
        const userDropdown = document.querySelector('.dashboard-user-dropdown');
        
        userMenuToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!userMenuToggle.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }
    
    // Toggle del tema claro/oscuro
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark-mode');
            
            // Guardar preferencia
            const isDarkMode = document.documentElement.classList.contains('dark-mode');
            localStorage.setItem('novastudio_dark_mode', isDarkMode ? 'dark' : 'light');
        });
    }
    
    // Inicializar según preferencias guardadas
    function initDashboard() {
        // Estado del sidebar
        const sidebarCollapsed = localStorage.getItem('novastudio_sidebar_collapsed');
        if (sidebarCollapsed === 'true' && sidebar) {
            sidebar.classList.add('collapsed');
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', 'false');
            }
        }
        
        // Estado del tema
        const darkMode = localStorage.getItem('novastudio_dark_mode');
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
    
    // Efecto de hover para los elementos del menú
    const menuItems = document.querySelectorAll('.dashboard-menu-item:not(.active) a');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '4px 4px 0 rgba(0, 0, 0, 0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    // Efecto para las tarjetas de estadísticas
    const statsCards = document.querySelectorAll('.stats-card, .neo-card');
    statsCards.forEach(card => {
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
    
    // Iniciar
    initDashboard();
    
    // Efecto para el botón de upgrade
    const upgradeButton = document.querySelector('.upgrade-button');
    if (upgradeButton) {
        upgradeButton.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(2px)';
            this.style.boxShadow = '2px 2px 0 rgba(0, 0, 0, 0.1)';
        });
        
        upgradeButton.addEventListener('mouseup', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
        
        upgradeButton.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    }
    
    // Efecto para los checkboxes de tareas
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            this.classList.toggle('checked');
            const taskItem = this.closest('.task-item');
            if (this.classList.contains('checked')) {
                taskItem.style.opacity = '0.6';
                taskItem.style.textDecoration = 'line-through';
            } else {
                taskItem.style.opacity = '';
                taskItem.style.textDecoration = '';
            }
        });
    });
    
    // Simulación de envío de chat
    const chatForm = document.querySelector('.chat-form');
    const chatInput = document.querySelector('.chat-input');
    const chatContainer = document.querySelector('.chat-container');
    
    if (chatForm && chatInput && chatContainer) {
        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const message = chatInput.value.trim();
            if (message !== '') {
                // Añadir mensaje del usuario
                const userMessage = document.createElement('div');
                userMessage.className = 'chat-message chat-user';
                userMessage.innerHTML = `
                    <div class="chat-avatar user">M</div>
                    <div class="chat-bubble user">
                        <p class="chat-text">${message}</p>
                    </div>
                `;
                chatContainer.appendChild(userMessage);
                
                // Simular respuesta de IA después de un breve retraso
                setTimeout(() => {
                    const aiMessage = document.createElement('div');
                    aiMessage.className = 'chat-message';
                    aiMessage.innerHTML = `
                        <div class="chat-avatar ai">AI</div>
                        <div class="chat-bubble ai">
                            <p class="chat-text">I'm analyzing the data for product categories. The top performers are Electronics (32%), Home Goods (24%), and Apparel (18%). Would you like more detailed analysis?</p>
                        </div>
                    `;
                    chatContainer.appendChild(aiMessage);
                    
                    // Scroll al final de la conversación
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 1000);
                
                // Limpiar input y scroll al final
                chatInput.value = '';
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    }
});
