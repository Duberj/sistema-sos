# SOS-UNI Mobile App Architecture

## Análisis de Requisitos

### Funcionalidades Principales
1. **Denuncia Anónima Rápida** - Botón principal para reportes inmediatos
2. **Formulario de Denuncia** - Selección de tipo de incidente, descripción, evidencia
3. **Sistema de Anonimato** - Opciones de anonimato completo o seudónimo
4. **Gestión de Evidencia** - Adjuntar fotos, audio, documentos con eliminación de metadatos
5. **Panel Administrativo** - Dashboard para personal autorizado
6. **Triage Automatizado** - Priorización de casos urgentes

### Tipos de Incidentes
- Acoso sexual y violencia de género
- Violencia física y psicológica
- Discriminación y racismo
- Consumo/venta de drogas
- Amenazas con armas
- Suicidio o ideación suicida
- Extorsión y chantaje digital
- Robo o hurto
- Corrupción y abuso de poder
- Otros (campo libre)

## Arquitectura Técnica

### Frontend Mobile
- Framework: HTML5/CSS3/JavaScript con diseño responsive
- Diseño: Mobile-first con enfoque en accesibilidad
- Navegación: Single Page Application (SPA)
- Almacenamiento: LocalStorage para datos temporales

### Backend (Simulado)
- API REST simulada con datos JSON
- Sistema de autenticación simulada
- Gestión de archivos con validación client-side

### Seguridad
- Anonimato por diseño
- Eliminación de metadatos en imágenes
- Sin rastreo de IP o identificación
- Cifrado de datos sensibles

## Estructura de Pantallas

1. **Pantalla de Inicio** - Botón de denuncia rápida y recursos de ayuda
2. **Formulario de Denuncia** - Paso a paso para reportar incidentes
3. **Gestión de Evidencia** - Adjuntar y gestionar archivos
4. **Panel Administrativo** - Dashboard para personal autorizado
5. **Recursos de Ayuda** - Información de contacto y apoyo
6. **Configuración** - Opciones de privacidad y anonimato

## Flujo de Usuario

1. Usuario abre la app → Pantalla de inicio con botón de denuncia
2. Presiona denunciar → Selección de tipo de incidente
3. Completa descripción → Opción de adjuntar evidencia
4. Selecciona ubicación → Opción de anonimato
5. Envía reporte → Confirmación y recursos de ayuda
6. Personal autorizado recibe alerta → Revisa en panel administrativo

## Características Especiales

- **Diseño profesional** con paleta de colores institucional
- **Animaciones suaves** para mejorar la experiencia
- **Modo oscuro** para accesibilidad
- **Sin conexión** - funcionalidad básica offline
- **Multiidioma** - preparado para internacionalización