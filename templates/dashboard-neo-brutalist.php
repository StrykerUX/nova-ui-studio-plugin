<?php
/**
 * Template para mostrar un dashboard con estilo neo-brutalista
 *
 * @package NovaStudio
 */

// Asegurarse de no permitir acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="dashboard-layout">
    <!-- Sidebar del Dashboard -->
    <div class="dashboard-sidebar" id="dashboardSidebar">
        <div class="dashboard-sidebar-header">
            <div class="dashboard-logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="dashboard-logo-full">
                    Nova<span style="color: var(--color-primary);">UI</span>
                </a>
                <div class="dashboard-logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle" aria-expanded="true" aria-label="Toggle Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
        </div>

        <div class="dashboard-sidebar-content">
            <nav class="dashboard-navigation">
                <ul class="dashboard-menu">
                    <li class="dashboard-menu-item active">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </span>
                            <span class="dashboard-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="dashboard-menu-item">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </span>
                            <span class="dashboard-menu-text">Analytics</span>
                        </a>
                    </li>
                    <li class="dashboard-menu-item">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                            <span class="dashboard-menu-text">Chat IA</span>
                        </a>
                    </li>
                    <li class="dashboard-menu-item">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </span>
                            <span class="dashboard-menu-text">Quick Links</span>
                        </a>
                    </li>
                    <li class="dashboard-menu-item">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                            <span class="dashboard-menu-text">Documents</span>
                        </a>
                    </li>
                    <li class="dashboard-menu-item">
                        <a href="#">
                            <span class="dashboard-menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            </span>
                            <span class="dashboard-menu-text">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="dashboard-sidebar-footer">
            <button id="dark-mode-toggle" class="saas-dark-mode-toggle" aria-label="Toggle Dark Mode">
                <span class="saas-dark-mode-toggle__icon icon-moon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </span>
                <span class="saas-dark-mode-toggle__icon icon-sun">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </span>
            </button>
        </div>
    </div>

    <!-- Contenido principal del Dashboard -->
    <div class="dashboard-main">
        <!-- Header del Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-header-start">
                <button class="dashboard-mobile-menu-toggle" id="mobileSidebarToggle" aria-label="Toggle Mobile Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                
                <div class="dashboard-search">
                    <form role="search" method="get" class="search-form" action="#">
                        <div class="search-input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="search" class="search-field" placeholder="Search..." value="" name="s" />
                            <div class="search-keyboard-shortcut">⌘K</div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dashboard-header-end">
                <button class="dashboard-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                
                <button id="theme-toggle" class="dashboard-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
                
                <div class="dashboard-user-menu">
                    <button class="dashboard-user-toggle" id="userMenuToggle">
                        <div class="dashboard-user-avatar">M</div>
                        <span class="dashboard-user-name">Miguel R.</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Contenido del Dashboard -->
        <div class="dashboard-content">
            <div class="dashboard-content-inner">
                <!-- Breadcrumbs -->
                <div class="breadcrumbs">
                    <span class="breadcrumbs-item"><a href="#">Home</a></span>
                    <span class="breadcrumbs-separator">/</span>
                    <span class="breadcrumbs-item active">Dashboard</span>
                </div>

                <!-- Page Title and Actions -->
                <div class="dashboard-page-header">
                    <h1 class="dashboard-page-title">Dashboard</h1>
                    <div class="dashboard-page-actions">
                        <button class="button button-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Export</span>
                        </button>
                        <button class="button button-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>New Report</span>
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <div>
                                <p class="stats-card-title">Total Revenue</p>
                                <h3 class="stats-card-value">$124,592.40</h3>
                            </div>
                            <div class="stats-card-icon primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                        </div>
                        <div class="stats-card-change">
                            <span class="stats-card-badge positive">+12.4%</span>
                            <span class="stats-card-period">vs last month</span>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <div>
                                <p class="stats-card-title">Active Users</p>
                                <h3 class="stats-card-value">4,893</h3>
                            </div>
                            <div class="stats-card-icon success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                        </div>
                        <div class="stats-card-change">
                            <span class="stats-card-badge positive">+17.2%</span>
                            <span class="stats-card-period">vs last month</span>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <div>
                                <p class="stats-card-title">Conversion Rate</p>
                                <h3 class="stats-card-value">3.42%</h3>
                            </div>
                            <div class="stats-card-icon error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            </div>
                        </div>
                        <div class="stats-card-change">
                            <span class="stats-card-badge negative">-2.1%</span>
                            <span class="stats-card-period">vs last month</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Widgets -->
                <div class="main-widgets">
                    <!-- Chat IA Widget -->
                    <div class="widget-chat">
                        <div class="widget-header">
                            <h2 class="widget-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                Chat IA
                            </h2>
                            <span class="widget-badge">ACTIVE</span>
                        </div>
                        
                        <div class="widget-content">
                            <div class="chat-container">
                                <!-- AI Message -->
                                <div class="chat-message">
                                    <div class="chat-avatar ai">AI</div>
                                    <div class="chat-bubble ai">
                                        <p class="chat-text">I can analyze your recent sales data and provide insights on trends. Your revenue has increased 12.4% compared to last month. Would you like a detailed breakdown?</p>
                                    </div>
                                </div>
                                
                                <!-- User Message -->
                                <div class="chat-message chat-user">
                                    <div class="chat-avatar user">M</div>
                                    <div class="chat-bubble user">
                                        <p class="chat-text">Yes, show me the breakdown by product category and highlight the best performers.</p>
                                    </div>
                                </div>
                            </div>

                            <form class="chat-form">
                                <input type="text" class="chat-input" placeholder="Ask AI assistant..." />
                                <button type="submit" class="chat-submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                    Send
                                </button>
                            </form>

                            <div class="chat-footer">
                                <span class="chat-tokens">
                                    <span class="chat-tokens-value">500</span> tokens remaining
                                </span>
                                <a href="#" class="chat-view-all">
                                    View all conversations →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links Widget -->
                    <div class="widget-quicklinks">
                        <div class="widget-header">
                            <h2 class="widget-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                Quick Links
                            </h2>
                            <button class="widget-badge">+ New</button>
                        </div>
                        
                        <div class="widget-content">
                            <div class="quicklink-list">
                                <div class="quicklink-item">
                                    <div class="quicklink-info">
                                        <h3>Product Portfolio</h3>
                                        <div class="quicklink-views">1243 views</div>
                                    </div>
                                    <button class="quicklink-action">Edit</button>
                                </div>
                                
                                <div class="quicklink-item">
                                    <div class="quicklink-info">
                                        <h3>Company Dashboard</h3>
                                        <div class="quicklink-views">842 views</div>
                                    </div>
                                    <button class="quicklink-action">Edit</button>
                                </div>
                                
                                <div class="quicklink-item">
                                    <div class="quicklink-info">
                                        <h3>Support Resources</h3>
                                        <div class="quicklink-views">568 views</div>
                                    </div>
                                    <button class="quicklink-action">Edit</button>
                                </div>
                            </div>
                            
                            <a href="#" class="view-all-links">View All Links</a>
                        </div>
                    </div>
                </div>
                
                <!-- Secondary Widgets -->
                <div class="secondary-widgets">
                    <!-- Tasks Widget -->
                    <div class="widget-tasks">
                        <div class="widget-header">
                            <h2 class="widget-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Upcoming Tasks
                            </h2>
                            <select class="widget-header-actions">
                                <option>Today</option>
                                <option>This Week</option>
                                <option>This Month</option>
                            </select>
                        </div>
                        
                        <div class="widget-content">
                            <div class="task-list">
                                <div class="task-item">
                                    <div class="task-check">
                                        <div class="task-checkbox"></div>
                                        <div class="task-info">
                                            <p class="task-title">Update Quick Links interface</p>
                                            <div class="task-time">10:00 AM</div>
                                        </div>
                                    </div>
                                    <span class="task-priority high">High</span>
                                </div>
                                
                                <div class="task-item">
                                    <div class="task-check">
                                        <div class="task-checkbox"></div>
                                        <div class="task-info">
                                            <p class="task-title">Review Chat IA performance</p>
                                            <div class="task-time">1:30 PM</div>
                                        </div>
                                    </div>
                                    <span class="task-priority medium">Medium</span>
                                </div>
                                
                                <div class="task-item">
                                    <div class="task-check">
                                        <div class="task-checkbox"></div>
                                        <div class="task-info">
                                            <p class="task-title">Team meeting - Sprint planning</p>
                                            <div class="task-time">3:00 PM</div>
                                        </div>
                                    </div>
                                    <span class="task-priority high">High</span>
                                </div>
                                
                                <div class="task-item">
                                    <div class="task-check">
                                        <div class="task-checkbox"></div>
                                        <div class="task-info">
                                            <p class="task-title">Prepare monthly report</p>
                                            <div class="task-time">5:00 PM</div>
                                        </div>
                                    </div>
                                    <span class="task-priority low">Low</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Membership Widget -->
                    <div class="widget-membership">
                        <div class="membership-progress-bar"></div>
                        
                        <div class="widget-content">
                            <div class="membership-header">
                                <div class="membership-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </div>
                                <div class="membership-info">
                                    <h2 class="membership-plan">Professional Plan</h2>
                                    <p class="membership-expires">Active until May 26, 2025</p>
                                </div>
                            </div>
                            
                            <div class="membership-resources">
                                <div class="resource-header">
                                    <span class="resource-name">IA Tokens</span>
                                    <span class="resource-value">500/2000</span>
                                </div>
                                <div class="resource-progress">
                                    <div class="resource-bar" style="width: 25%"></div>
                                </div>
                                <p class="resource-info">Tokens reset in 16 days</p>
                            </div>
                            
                            <div class="membership-stats">
                                <div class="stat-card">
                                    <p class="stat-label">Quick Links</p>
                                    <p class="stat-value">3<span class="stat-max">/10</span></p>
                                </div>
                                <div class="stat-card">
                                    <p class="stat-label">Users</p>
                                    <p class="stat-value">2<span class="stat-max">/5</span></p>
                                </div>
                            </div>
                            
                            <button class="upgrade-button">Upgrade Plan</button>
                        </div>

                        <!-- Game UI decorative elements -->
                        <div class="membership-decorative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="membership-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="membership-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="membership-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
