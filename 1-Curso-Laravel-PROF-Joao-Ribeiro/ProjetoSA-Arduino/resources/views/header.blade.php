<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmartLOG - Sistema de Logística RFID')</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
</head>
<body>

    <!-- Main Header Navigation -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                    <div class="logo-icon">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <h1 class="logo-text">SmartLOG</h1>
                    <span class="logo-badge">RFID</span>
                </a>
            </div>
            
            <nav class="main-nav">
                <a href="{{ url('/') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    Início
                </a>
                <a href="{{ url('dashboard') }}" class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
                <a href="{{ url('rastreamento') }}" class="nav-link {{ Request::is('rastreamento*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i>
                    Rastreamento
                </a>
                <a href="{{ url('relatorios') }}" class="nav-link {{ Request::is('relatorios*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    Relatórios
                </a>
                
                <!-- NOVO MENU DROPDOWN GERENCIAR -->
                <div class="nav-dropdown">
                    <button class="nav-link dropdown-toggle {{ Request::is('gerenciar*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        Gerenciar
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <div class="dropdown-menu-nav">
                        <a href="{{ route('gerenciar.readers.index') }}" class="dropdown-item-nav">
                            <i class="bi bi-router"></i>
                            Leitores RFID
                        </a>
                        <a href="{{ route('gerenciar.tags.index') }}" class="dropdown-item-nav">
                            <i class="bi bi-tags"></i>
                            Tags RFID
                        </a>
                    </div>
                </div>
            </nav>

            <div class="header-actions">
                <button class="notification-btn" title="Notificações">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <div class="user-menu">
                    <button class="user-menu-btn">
                        <img src="{{ asset('assets/media/avatars/blank.png') }}" alt="User" class="user-avatar">
                        <span class="user-name">Admin</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="user-dropdown">
                        <a href="{{ url('perfil') }}" class="dropdown-item">
                            <i class="bi bi-person"></i>
                            Meu Perfil
                        </a>
                        <a href="{{ url('configuracoes') }}" class="dropdown-item">
                            <i class="bi bi-gear"></i>
                            Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ url('sair') }}" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container-xxl mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="container-xxl mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container-xxl">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted">
                    &copy; {{ date('Y') }} SmartLOG. Todos os direitos reservados.
                </div>
                <div class="d-flex gap-3 mt-3 mt-md-0">
                    <a href="{{ url('sobre') }}" class="text-muted text-hover-primary">Sobre</a>
                    <a href="{{ url('suporte') }}" class="text-muted text-hover-primary">Suporte</a>
                    <a href="{{ url('privacidade') }}" class="text-muted text-hover-primary">Privacidade</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Dropdown
            const userMenuBtn = document.querySelector('.user-menu-btn');
            const userDropdown = document.querySelector('.user-dropdown');
            
            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!userMenuBtn.contains(e.target)) {
                        userDropdown.classList.remove('show');
                    }
                });
            }
            
            // Nav Dropdown (Gerenciar)
            const navDropdowns = document.querySelectorAll('.nav-dropdown');
            navDropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu-nav');
                
                if (toggle && menu) {
                    toggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        menu.classList.toggle('show');
                    });
                    
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            menu.classList.remove('show');
                        }
                    });
                }
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @yield('custom-js')
</body>
</html>