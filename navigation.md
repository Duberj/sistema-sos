# SOS-UNI Navigation System

## Estructura de Navegación

### Navegación Principal (Bottom Navigation)
- **Inicio** - Pantalla principal con botón de denuncia
- **Recursos** - Información de ayuda y contacto
- **Configuración** - Opciones de privacidad y cuenta

### Navegación Secundaria (Contextual)
- **Denunciar** - Flujo de denuncia (multi-paso)
- **Panel Admin** - Acceso para personal autorizado
- **Historial** - Reportes previos (para usuarios registrados)

## Pantallas Detalladas

### 1. Pantalla de Inicio (index.html)
**Propósito**: Punto de entrada principal con acceso rápido a denuncia
**Contenido**:
- Botón grande "DENUNCIAR AHORA" (centro)
- Estadísticas anónimas (ej: "X reportes esta semana")
- Recursos de ayuda rápida
- Alertas importantes del campus
- Información de privacidad

**Interacciones**:
- Tap en botón principal → Formulario de denuncia
- Tap en recursos → Pantalla de ayuda
- Tap en configuración → Opciones de privacidad

### 2. Formulario de Denuncia (report.html)
**Propósito**: Flujo guiado para reportar incidentes
**Estructura multi-paso**:
1. **Tipo de incidente** - Grid de opciones visuales
2. **Descripción** - Texto corto (500 caracteres)
3. **Evidencia** - Adjuntar archivos con preview
4. **Ubicación** - Selector de campus/edificio
5. **Anonimato** - Opciones de privacidad
6. **Confirmación** - Resumen y envío

**Características**:
- Validación en cada paso
- Guardado automático (draft)
- Indicador de progreso
- Opción de cancelar en cualquier momento

### 3. Panel Administrativo (admin.html)
**Propósito**: Dashboard para personal autorizado
**Contenido**:
- Lista de reportes anónimos
- Filtros por tipo, fecha, ubicación
- Vista de detalles de reporte
- Herramientas de triage
- Estadísticas y gráficos
- Sistema de alertas

**Funcionalidades**:
- Ordenar por prioridad/urgencia
- Marcar como atendido
- Añadir notas internas
- Generar reportes agregados
- Configurar alertas automáticas

### 4. Recursos de Ayuda (resources.html)
**Propósito**: Información de apoyo y contacto
**Contenido**:
- Líneas de emergencia directas
- Servicios psicológicos
- Protocolos de actuación
- Información legal
- Preguntas frecuentes
- Testimonios anónimos

### 5. Configuración (settings.html)
**Propósito**: Gestión de privacidad y preferencias
**Opciones**:
- Nivel de anonimato
- Notificaciones
- Idioma
- Términos y condiciones
- Política de privacidad
- Eliminar datos

## Flujos de Navegación

### Flujo Principal (Usuario)
1. **Inicio** → **Denunciar** → **Formulario** → **Confirmación** → **Inicio**

### Flujo Administrativo
1. **Login** → **Panel Admin** → **Ver Reporte** → **Atender** → **Panel Admin**

### Flujo de Ayuda
1. **Inicio** → **Recursos** → **Contacto Directo** → **Confirmación**

## Elementos de UI

### Barra de Navegación Inferior
- Iconos claros y reconocibles
- Etiquetas descriptivas
- Indicador de ubicación actual
- Badges para notificaciones

### Navegación Superior (Cuando aplica)
- Botón de retroceso
- Título de pantalla
- Menú contextual (tres puntos)
- Búsqueda (donde corresponda)

### Sistema de Feedback
- Indicadores de carga
- Mensajes de confirmación
- Alertas de error
- Tooltips informativos

## Consideraciones de Accesibilidad

- Contraste mínimo 4.5:1
- Tamaños de fuente ajustables
- Navegación por voz
- Soporte para lectores de pantalla
- Indicadores visuales y táctiles
- Confirmaciones antes de acciones destructivas