# SOS-UNI - Aplicación Móvil de Denuncias Anónimas Universitarias

## Descripción General

SOS-UNI es una aplicación móvil profesional diseñada para facilitar la denuncia anónima y la atención temprana de situaciones de maltrato, violencia, discriminación, consumo/venta de sustancias, presencia de armas u otras situaciones de riesgo dentro del entorno universitario.

## Características Principales

### 🔒 **Anonimato Garantizado**
- Sistema diseñado para el anonimato real
- No se recopilan datos personales identificables
- Eliminación automática de metadatos en archivos
- Sin rastreo de IP o ubicación exacta

### 📱 **Interfaz Móvil Intuitiva**
- Diseño mobile-first optimizado para todos los dispositivos
- Navegación por gestos y botones táctiles grandes
- Flujo de denuncia guiado paso a paso
- Animaciones suaves y feedback visual inmediato

### 🚨 **Sistema de Denuncia Rápida**
- Botón principal de "Denunciar Ahora" para acceso inmediato
- 10 categorías de incidentes predefinidas
- Formulario con validación en tiempo real
- Adjuntar evidencia (fotos, videos, documentos)
- Selección de ubicación aproximada (no exacta)

### 📊 **Panel Administrativo**
- Dashboard con estadísticas en tiempo real
- Sistema de triage y priorización automática
- Visualización de datos agregados por categoría y campus
- Gráficos interactivos con Plotly.js
- Gestión de estado de reportes

### 🆘 **Recursos de Ayuda Integrados**
- Contactos de emergencia directos (911, policía universitaria)
- Servicios de apoyo psicológico
- Asesoría legal y académica
- Preguntas frecuentes y guía de uso

## Arquitectura Técnica

### Frontend
- **HTML5/CSS3/JavaScript** vanilla para máximo rendimiento
- **Tailwind CSS** para estilos responsivos y modernos
- **Anime.js** para animaciones suaves y profesionales
- **Plotly.js** para visualización de datos en el panel admin

### Diseño y UX
- **Mobile-first approach** con diseño responsivo
- **Paleta de colores institucional** (azules profesionales)
- **Tipografía Inter** para máxima legibilidad
- **Accesibilidad WCAG 2.1 AA** incluida

### Características de Seguridad
- **Anonimato por diseño** - no hay forma de identificar usuarios
- **Cifrado de datos** en tránsito y reposo
- **Validación client-side** para integridad de datos
- **Protección contra XSS y CSRF**

## Estructura de la Aplicación

### Pantallas Principales

1. **index.html** - Pantalla de inicio con botón de denuncia rápida
   - Estadísticas anónimas de reportes
   - Acceso directo a recursos de ayuda
   - Información de privacidad destacada

2. **report.html** - Formulario de denuncia multi-paso
   - Selección de tipo de incidente
   - Descripción del evento (500 caracteres)
   - Adjuntar evidencia con validación
   - Selección de ubicación aproximada
   - Opciones de anonimato

3. **admin.html** - Panel administrativo
   - Dashboard con estadísticas
   - Lista de reportes con filtros
   - Visualización de datos agregados
   - Gestión de estado de casos

4. **resources.html** - Recursos de ayuda
   - Contactos de emergencia
   - Servicios de apoyo psicológico
   - Asesoría legal y académica
   - Preguntas frecuentes

5. **settings.html** - Configuración de usuario
   - Opciones de privacidad
   - Preferencias de notificación
   - Gestión de datos
   - Información de la aplicación

### Componentes Clave

- **Sistema de Navegación**: Bottom navigation para móvil
- **Formularios Validados**: Validación en tiempo real
- **Gestión de Archivos**: Upload seguro de evidencia
- **Modales Interactivos**: Diálogos contextuales
- **Gráficos Dinámicos**: Visualización de datos con Plotly.js

## Tipos de Incidentes Soportados

1. **Acoso Sexual** - Toqueteos, comentarios, mensajes no deseados
2. **Violencia Física** - Agresiones entre estudiantes
3. **Discriminación** - Racismo, xenofobia, homofobia
4. **Consumo/Venta de Drogas** - Dentro del campus
5. **Amenazas con Armas** - Presencia de armas u objetos peligrosos
6. **Acoso Académico** - Abuso de poder por profesores
7. **Ideación Suicida** - Señales de suicidio o depresión severa
8. **Extorsión Digital** - Sextorsión, chantaje
9. **Robo/Hurto** - En instalaciones universitarias
10. **Otro** - Cualquier otra situación de riesgo

## Características de Seguridad y Privacidad

### Anonimato Total
- No se requiere registro ni identificación
- Sin recopilación de datos personales
- Eliminación automática de metadatos
- Sin rastreo de ubicación exacta

### Seguridad de Datos
- Cifrado de extremo a extremo
- Validación de archivos adjuntos
- Límites de tamaño y tipo de archivo
- Protección contra malware

### Control del Usuario
- Opciones de anonimato flexibles
- Posibilidad de usar seudónimo
- Contacto seguro opcional
- Eliminación completa de datos

## Instalación y Uso

### Requisitos Previos
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Conexión a internet para funcionalidad completa
- No requiere instalación de software adicional

### Instrucciones de Uso

1. **Acceder a la aplicación**: Abrir `index.html` en un navegador
2. **Realizar una denuncia**: Presionar "Denunciar Ahora" y seguir el flujo
3. **Adjuntar evidencia**: Usar el sistema de upload seguro
4. **Revisar configuración**: Personalizar preferencias en Settings
5. **Acceder a recursos**: Usar la sección de ayuda para contactos

### Para Administradores
1. **Acceder al panel**: Navegar a `admin.html`
2. **Ver reportes**: Usar filtros para priorizar casos
3. **Actualizar estados**: Marcar casos como atendidos/resueltos
4. **Analizar datos**: Usar gráficos para identificar patrones

## Tecnologías Utilizadas

- **HTML5** - Estructura semántica
- **CSS3** - Estilos modernos con Tailwind
- **JavaScript ES6+** - Lógica de aplicación
- **Anime.js** - Animaciones profesionales
- **Plotly.js** - Visualización de datos
- **Web APIs** - Funcionalidades del navegador

## Mejores Prácticas Implementadas

### Diseño
- Mobile-first responsive design
- Accesibilidad WCAG 2.1 AA
- Contraste mínimo 4.5:1
- Tamaños de tacto adecuados (44px mínimo)

### Seguridad
- Anonimato por diseño
- Validación de entrada de usuario
- Protección contra inyección de código
- Gestión segura de archivos

### Rendimiento
- Código optimizado y minificado
- Imágenes optimizadas
- Animaciones eficientes
- Lazy loading donde corresponde

## Futuras Mejoras Planificadas

- **Notificaciones Push** para alertas importantes
- **Modo Offline** completo con sincronización
- **Multiidioma** (inglés, francés, portugués)
- **Integración con sistemas universitarios**
- **Chat en vivo** para emergencias
- **Reconocimiento de voz** para reportes rápidos

## Soporte y Mantenimiento

- **Documentación completa** incluida
- **Código comentado** y bien estructurado
- **Pruebas de seguridad** implementadas
- **Actualizaciones regulares** de dependencias

## Equipo de Desarrollo

Esta aplicación fue desarrollada siguiendo las mejores prácticas de:
- **Design Thinking** - Enfoque en el usuario
- **Lean Canvas** - Modelo de negocio eficiente
- **Metodologías Ágiles** - Desarrollo iterativo
- **Seguridad por Diseño** - Privacidad integrada

## Contacto

Para consultas sobre el proyecto:
- **Email**: soporte@sos-uni.edu
- **Web**: https://sos-uni.edu
- **Emergencias**: Siempre llamar al 911 primero

---

**⚠️ Importante**: SOS-UNI es una herramienta complementaria. En situaciones de emergencia inmediata, siempre contactar al 911 o a los servicios de emergencia locales primero.