@extends('layouts.app')

@section('title', 'SmartLOG - Logística RFID Industrial')
@section('page-title', 'Bem-vindo ao SmartLOG')
@section('breadcrumb', 'Início')

@section('content')
<div class="home-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-xxl">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="bi bi-lightning-charge-fill"></i>
                            Rastreamento em Tempo Real
                        </div>
                        <h1 class="hero-title">
                            Controle Total da sua
                            <span class="gradient-text">Logística Industrial</span>
                        </h1>
                        <p class="hero-description">
                            O <strong>SmartLOG</strong> revoluciona o rastreamento de produtos e ferramentas através de tecnologia RFID e ESP32, 
                            oferecendo monitoramento automatizado, segurança patrimonial e eficiência operacional em tempo real.
                        </p>
                        <div class="hero-stats">
                            <div class="stat-item">
                                <i class="bi bi-speedometer2"></i>
                                <div>
                                    <strong>2s</strong>
                                    <span>Tempo de resposta</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>99.8%</strong>
                                    <span>Precisão</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <i class="bi bi-clock-fill"></i>
                                <div>
                                    <strong>24/7</strong>
                                    <span>Monitoramento</span>
                                </div>
                            </div>
                        </div>
                        <div class="hero-buttons">
                            <a href="{{ url('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-play-circle-fill"></i>
                                Acessar Sistema
                            </a>
                            <a href="#recursos" class="btn btn-outline-primary">
                                <i class="bi bi-info-circle"></i>
                                Saiba Mais
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="rfid-animation">
                            <div class="rfid-waves"></div>
                            <div class="rfid-card-container">
                                <div class="rfid-reader">
                                    <i class="bi bi-broadcast"></i>
                                </div>
                                <div class="rfid-tag">
                                    <i class="bi bi-credit-card-2-front"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem & Solution Section -->
    <section class="problem-solution-section">
        <div class="container-xxl">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="problem-card">
                        <div class="problem-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h3>O Problema</h3>
                        <ul class="problem-list">
                            <li>
                                <i class="bi bi-x-circle"></i>
                                <span><strong>Falta de controle logístico</strong> - Dificuldade em rastrear materiais em tempo real</span>
                            </li>
                            <li>
                                <i class="bi bi-x-circle"></i>
                                <span><strong>Extravio de itens</strong> - Perda de pequenos produtos e ferramentas</span>
                            </li>
                            <li>
                                <i class="bi bi-x-circle"></i>
                                <span><strong>Processos manuais</strong> - Gestão ineficiente e propensa a erros</span>
                            </li>
                            <li>
                                <i class="bi bi-x-circle"></i>
                                <span><strong>Baixa segurança patrimonial</strong> - Dificuldade em identificar desvios</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="solution-card">
                        <div class="solution-icon">
                            <i class="bi bi-lightbulb-fill"></i>
                        </div>
                        <h3>Nossa Solução</h3>
                        <ul class="solution-list">
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Rastreamento automatizado</strong> - RFID identifica passagem em tempo real</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Controle total</strong> - Localização precisa de todos os itens</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Automação completa</strong> - ESP32 envia dados via Wi-Fi</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Segurança reforçada</strong> - Monitoramento 24/7 e alertas instantâneos</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="recursos">
        <div class="container-xxl">
            <div class="section-header">
                <span class="section-badge">Recursos</span>
                <h2 class="section-title">Por que escolher o SmartLOG?</h2>
                <p class="section-description">
                    Tecnologia de ponta para resolver os desafios da logística industrial moderna
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon red">
                            <i class="bi bi-broadcast-pin"></i>
                        </div>
                        <h3 class="feature-title">Rastreamento RFID</h3>
                        <p class="feature-description">
                            Identificação automática de pallets e produtos através de tags RFID em pontos estratégicos da fábrica.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="bi bi-speedometer"></i>
                        </div>
                        <h3 class="feature-title">Tempo Real</h3>
                        <p class="feature-description">
                            Dados transmitidos via Wi-Fi para base central, permitindo visualização instantânea do fluxo de materiais.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon yellow">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Segurança Total</h3>
                        <p class="feature-description">
                            Elimine extravios de ferramentas e pequenos itens com registro detalhado de localização e movimentação.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="feature-title">Eficiência</h3>
                        <p class="feature-description">
                            Otimize estoque e transporte interno com dados precisos sobre disponibilidade e movimentação de itens.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section class="how-works-section">
        <div class="container-xxl">
            <div class="section-header">
                <span class="section-badge">Como Funciona</span>
                <h2 class="section-title">Arquitetura do Sistema</h2>
                <p class="section-description">
                    Conheça a tecnologia por trás do SmartLOG
                </p>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="bi bi-1-circle-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Tags RFID nos Itens</h4>
                        <p>Cada pallet, produto ou ferramenta recebe uma etiqueta RFID única que permite identificação automática sem contato visual.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="bi bi-2-circle-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Leitores Estratégicos</h4>
                        <p>Módulos RFID instalados em pontos críticos da fábrica capturam automaticamente a passagem dos itens.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="bi bi-3-circle-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>ESP32 Processa Dados</h4>
                        <p>Microcontroladores ESP32 processam as leituras e transmitem informações via Wi-Fi para o servidor central.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="bi bi-4-circle-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Dashboard em Tempo Real</h4>
                        <p>Visualize, analise e gerencie todo o fluxo logístico através de uma interface web moderna e intuitiva.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology Stack Section -->
    <section class="tech-stack-section">
        <div class="container-xxl">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="tech-card">
                        <div class="tech-emoji">📡</div>
                        <h4>Módulos RFID</h4>
                        <p>Leitores de alta frequência instalados em pontos estratégicos para captura automática</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="tech-card">
                        <div class="tech-emoji">🔌</div>
                        <h4>ESP32</h4>
                        <p>Microcontrolador com Wi-Fi integrado para processamento e comunicação em tempo real</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="tech-card">
                        <div class="tech-emoji">☁️</div>
                        <h4>Base Central</h4>
                        <p>Servidor robusto para processamento, armazenamento e análise de dados</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="tech-card">
                        <div class="tech-emoji">📊</div>
                        <h4>Dashboard Web</h4>
                        <p>Interface intuitiva para visualização, controle e geração de relatórios</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section class="stats-banner">
        <div class="container-xxl">
            <div class="stats-content">
                <h2>SmartLOG em Números</h2>
                <div class="row g-4 mt-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-box">
                            <div class="stat-number">99.8%</div>
                            <div class="stat-label">Precisão de Leitura</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-box">
                            <div class="stat-number">&lt;2s</div>
                            <div class="stat-label">Tempo de Resposta</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-box">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Automação</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-box">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Monitoramento</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container-xxl">
            <div class="section-header">
                <span class="section-badge">Benefícios</span>
                <h2 class="section-title">Vantagens do SmartLOG</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-graph-up benefit-icon"></i>
                        <h4>Aumento de Produtividade</h4>
                        <p>Reduza tempo de busca por itens e otimize processos logísticos</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-piggy-bank benefit-icon"></i>
                        <h4>Redução de Custos</h4>
                        <p>Minimize perdas por extravio e melhore gestão de estoque</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-file-earmark-bar-graph benefit-icon"></i>
                        <h4>Relatórios Detalhados</h4>
                        <p>Análises completas para tomada de decisões estratégicas</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-bell benefit-icon"></i>
                        <h4>Alertas Inteligentes</h4>
                        <p>Notificações instantâneas sobre movimentações suspeitas</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-sliders benefit-icon"></i>
                        <h4>Fácil Integração</h4>
                        <p>Sistema flexível que se adapta à sua infraestrutura</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <i class="bi bi-phone benefit-icon"></i>
                        <h4>Acesso Remoto</h4>
                        <p>Monitore sua operação de qualquer lugar, a qualquer momento</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container-xxl">
            <div class="cta-card">
                <div class="cta-content">
                    <h2>Pronto para transformar sua logística?</h2>
                    <p>Implemente o SmartLOG e tenha controle total sobre seus ativos industriais</p>
                    <div class="cta-buttons">
                        <a href="{{ url('dashboard') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-rocket-takeoff"></i>
                            Começar Agora
                        </a>
                        <a href="{{ url('contato') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-chat-dots"></i>
                            Falar com Especialista
                        </a>
                    </div>
                </div>
                <div class="cta-visual">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('custom-js')
<script>
    // Scroll suave para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animação de entrada dos elementos
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observar elementos para animação
    document.querySelectorAll('.feature-card, .benefit-card, .tech-card, .timeline-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });

    // Contador animado para estatísticas
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Ativar contadores quando visível
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = 'true';
                const numbers = entry.target.querySelectorAll('.stat-number');
                numbers.forEach(num => {
                    const value = num.textContent.replace(/[^0-9.]/g, '');
                    if (value && !isNaN(value)) {
                        animateCounter(num, parseFloat(value));
                    }
                });
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stats-banner').forEach(el => {
        statsObserver.observe(el);
    });
</script>
@endsection