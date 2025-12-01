<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOS-UNI - Denuncias Anónimas Universitarias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .hero-section { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .particle { position: absolute; width: 4px; height: 4px; background: rgba(59, 130, 246, 0.3); border-radius: 50%; animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .main-button { background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .alert-banner { background: #3b82f6; }
        .resource-card { background: white; border: 1px solid #e5e7eb; }
        .stats-card { background: white; border: 1px solid #e5e7eb; }
        .bottom-nav { background: white; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Alert Banner -->
    <div class="alert-banner text-white text-center py-2 text-sm font-medium">
        ⚠️ Tu seguridad es nuestra prioridad - Denuncias 100% anónimas
    </div>
    
    <!-- Hero Section -->
    <div class="hero-section min-h-screen flex flex-col justify-center items-center px-4 relative">
        <!-- Floating Particles -->
        <div class="floating-particles absolute inset-0 overflow-hidden">
            <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
            <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
            <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
            <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
            <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
            <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
            <div class="particle" style="left: 70%; animation-delay: 1.5s;"></div>
            <div class="particle" style="left: 80%; animation-delay: 2.5s;"></div>
            <div class="particle" style="left: 90%; animation-delay: 3.5s;"></div>
        </div>
        
        <!-- App Logo -->
        <div class="mb-8 z-10">
            <img src="resources/app-logo.png" alt="SOS-UNI Logo" class="w-20 h-20 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 text-center">SOS-UNI</h1>
            <p class="text-gray-600 text-center mt-2">Denuncias Anónimas Universitarias</p>

            <!-- Main Action Button -->
            <button id="mainReportBtn" class="main-button text-white px-12 py-6 rounded-2xl text-xl font-bold mb-4 mt-6">
                🚨 DENUNCIAR AHORA
            </button>
            
            <!-- Login Access -->
            <div class="mt-4">
                <button onclick="goToLogin()" class="bg-white bg-opacity-10 text-purple-600 px-8 py-4 rounded-lg text-sm font-medium backdrop-blur-sm">
                    🔐 Login / Registro 
                    <p class="text-gray-600 text-center mt-2 text-xs">Si deseas hacer seguimiento a tu denuncia</p>
                </button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4 w-full max-w-sm mb-6 z-10">
            <div class="stats-card rounded-2xl p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-blue-600" id="reportsCount">--</div>
                <div class="text-sm text-gray-600">Reportes esta semana</div>
            </div>
            <div class="stats-card rounded-2xl p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-green-600" id="responseTime">--</div>
                <div class="text-sm text-gray-600">Tiempo de respuesta</div>
            </div>
        </div>
        
        <!-- Quick Resources -->
        <div class="w-full max-w-sm space-y-3">
            <div class="resource-card rounded-xl p-4 flex items-center cursor-pointer" onclick="showResources()">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600">📞</span>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-900">Líneas de Emergencia</div>
                    <div class="text-sm text-gray-600">Contactos directos de ayuda</div>
                </div>
                <div class="text-gray-400">›</div>
            </div>
            
            <div class="resource-card rounded-xl p-4 flex items-center cursor-pointer" onclick="showHelp()">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-green-600">❓</span>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-900">¿Cómo funciona?</div>
                    <div class="text-sm text-gray-600">Guía de uso anónimo</div>
                </div>
                <div class="text-gray-400">›</div>
            </div>
            
            <div class="resource-card rounded-xl p-4 flex items-center cursor-pointer" onclick="showPrivacy()">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-purple-600">🔒</span>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-900">Privacidad Garantizada</div>
                    <div class="text-sm text-gray-600">Tu seguridad es primero</div>
                </div>
                <div class="text-gray-400">›</div>
            </div>
        </div>
        
        <!-- Advertising Space -->
        <div class="w-full max-w-sm mt-8 mb-24">
            <div class="bg-gradient-to-r from-purple-700 to-pink-700 rounded-xl p-4 text-white text-center">
                <div class="text-2xl mb-2">📢</div>
                <div class="font-semibold mb-2">Espacio Publicitario</div>
                <div class="text-sm opacity-90">Tu marca puede ir aquí</div>
                <button onclick="contactAdvertising()" class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-lg text-xs font-medium mt-2">
                    Contactar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation -->
    <div class="bottom-nav fixed bottom-0 left-0 right-0 px-4 py-2 z-50">
        <div class="flex justify-around items-center">
            <div class="nav-item flex flex-col items-center py-2 cursor-pointer" onclick="goHome()">
                <span class="text-2xl mb-1">🏠</span>
                <span class="text-xs">Inicio</span>
            </div>
            <div class="nav-item flex flex-col items-center py-2 cursor-pointer" onclick="showResources()">
                <span class="text-2xl mb-1">📞</span>
                <span class="text-xs">Recursos</span>
            </div>
            <div class="nav-item flex flex-col items-center py-2 cursor-pointer" onclick="showSettings()">
                <span class="text-2xl mb-1">⚙️</span>
                <span class="text-xs">Configuración</span>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <div id="privacyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4 text-gray-900">🔒 Privacidad Garantizada</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <p>✅ No recopilamos datos personales</p>
                <p>✅ No rastreamos tu ubicación exacta</p>
                <p>✅ No almacenamos IP ni identificadores</p>
                <p>✅ Encriptación de extremo a extremo</p>
                <p>✅ Eliminación automática de metadatos</p>
            </div>
            <button onclick="closeModal('privacyModal')" class="w-full mt-6 bg-blue-600 text-white py-3 rounded-xl font-semibold">
                Entendido
            </button>
        </div>
    </div>
    
    <div id="helpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4 text-gray-900">❓ ¿Cómo funciona?</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <p><strong>1.</strong> Presiona "Denunciar Ahora"</p>
                <p><strong>2.</strong> Selecciona el tipo de incidente</p>
                <p><strong>3.</strong> Describe lo ocurrido</p>
                <p><strong>4.</strong> Adjunta evidencia si lo deseas</p>
                <p><strong>5.</strong> Selecciona ubicación aproximada</p>
                <p><strong>6.</strong> Elige nivel de anonimato</p>
                <p><strong>7.</strong> Envía tu reporte seguro</p>
            </div>
            <button onclick="closeModal('helpModal')" class="w-full mt-6 bg-blue-600 text-white py-3 rounded-xl font-semibold">
                Comenzar
            </button>
        </div>
    </div>

    <script>
        // ========================================
        // FUNCIONES FUNCIONALES (del primer HTML)
        // ========================================
        
        async function loadStats() {
            try {
                const response = await fetch('api/get_public_stats.php');
                const stats = await response.json();
                document.getElementById('reportsCount').textContent = stats.weekly || 0;
                document.getElementById('responseTime').textContent = '<24h';
            } catch (error) {
                document.getElementById('reportsCount').textContent = '--';
                document.getElementById('responseTime').textContent = '--';
            }
        }

        // ========================================
        // FUNCIONES DE DISEÑO (del segundo HTML)
        // ========================================
        
        function animateCounter(elementId, target) {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 40);
        }

        function goHome() {
            // Ya estamos en home
            window.location.href = 'index.php';
        }

        function goToLogin() {
            window.location.href = 'login.html';
        }

        function contactAdvertising() {
            alert('📢 Información de Publicidad:\n\n📧 Email: publi@uni.edu\n📞 Tel: 555-1234\n\nContáctanos para más información.');
        }

        function showResources() {
            alert('📞 Líneas de Emergencia:\n\n• Psicología UNI: 555-1000\n• Seguridad: 555-2000\n• Rectoría: 555-3000');
        }

        function showSettings() {
            alert('⚙️ Configuración:\n\nFuncionalidad en desarrollo');
        }

        function showPrivacy() {
            document.getElementById('privacyModal').classList.remove('hidden');
            document.getElementById('privacyModal').classList.add('flex');
        }

        function showHelp() {
            document.getElementById('helpModal').classList.remove('hidden');
            document.getElementById('helpModal').classList.add('flex');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ========================================
        // INICIALIZACIÓN
        // ========================================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidad principal: cargar estadísticas
            loadStats();
            
            // Animaciones del botón principal
            document.getElementById('mainReportBtn').addEventListener('click', function() {
                anime({
                    targets: this,
                    scale: [1, 0.95, 1],
                    duration: 200,
                    easing: 'easeInOutQuad',
                    complete: function() {
                        window.location.href = 'report.html';
                    }
                });
            });
            
            // Animaciones de la UI
            anime({
                targets: '#mainReportBtn',
                scale: [0.8, 1],
                opacity: [0, 1],
                duration: 800,
                delay: 500,
                easing: 'easeOutElastic(1, .8)'
            });
            
            anime({
                targets: '.resource-card',
                translateY: [50, 0],
                opacity: [0, 1],
                duration: 600,
                delay: anime.stagger(100, {start: 800}),
                easing: 'easeOutCubic'
            });
        });
    </script>
<!-- Añade esto al final de index.html, antes de </body> -->
<div class="fixed bottom-20 right-4 text-xs opacity-50 hover:opacity-100 transition">
    <a href="admin_login.php" class="text-gray-500 hover:text-blue-600">
        🔐 Acceso Admin
    </a>
</div>


</body>
</html>