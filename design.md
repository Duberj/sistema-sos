# SOS-UNI Design System

## Filosofía de Diseño

### Principios Fundamentales
- **Seguridad Visual**: Colores y elementos que transmiten confianza y seriedad
- **Simplicidad Funcional**: Interfaz intuitiva que reduce la fricción en momentos de crisis
- **Accesibilidad Total**: Diseño inclusivo para todos los usuarios
- **Profesionalismo Institucional**: Apariencia seria y confiable que refleje el compromiso universitario

### Paleta de Colores
**Colores Primarios**:
- **Azul Profundo** (#1e3a8a) - Principal, transmite confianza y estabilidad
- **Azul Claro** (#3b82f6) - Secundario, para elementos interactivos
- **Blanco** (#ffffff) - Fondo principal y texto en colores oscuros

**Colores de Estado**:
- **Rojo Alerta** (#dc2626) - Para incidentes críticos y urgencias
- **Amarillo Advertencia** (#f59e0b) - Para casos que requieren atención
- **Verde Éxito** (#059669) - Para confirmaciones y estados positivos
- **Gris Neutral** (#6b7280) - Para texto secundario y bordes

**Colores de Fondo**:
- **Fondo Principal** (#f8fafc) - Blanco suave para reducir fatiga visual
- **Fondo Secundario** (#e2e8f0) - Para secciones diferenciadas
- **Fondo Oscuro** (#0f172a) - Para modo oscuro y elementos premium

### Tipografía
**Fuente Principal**: Inter (sans-serif)
- **Encabezados**: Inter Bold (700) - 24px-32px
- **Subtítulos**: Inter SemiBold (600) - 18px-22px
- **Cuerpo**: Inter Regular (400) - 14px-16px
- **Pequeño**: Inter Medium (500) - 12px-14px

**Fuente Secundaria**: JetBrains Mono (monospace)
- **Código/Citas**: Para elementos técnicos o legales

### Sistema de Espaciado
- **Base**: 8px
- **Micro**: 4px (para elementos muy juntos)
- **Pequeño**: 8px (espacio estándar entre elementos)
- **Medio**: 16px (separación de secciones)
- **Grande**: 24px (separación de bloques)
- **XL**: 32px (separación de secciones principales)

## Componentes de UI

### Botones
**Botón Primario**:
- Fondo: Azul Profundo (#1e3a8a)
- Texto: Blanco
- Border-radius: 12px
- Padding: 16px 24px
- Sombra suave para profundidad

**Botón Secundario**:
- Fondo: Transparente
- Borde: 2px sólido Azul Profundo
- Texto: Azul Profundo
- Border-radius: 12px
- Padding: 14px 22px

**Botón de Peligro**:
- Fondo: Rojo Alerta (#dc2626)
- Texto: Blanco
- Uso exclusivo para acciones críticas

### Tarjetas (Cards)
- Fondo: Blanco
- Border-radius: 16px
- Sombra: 0 4px 6px -1px rgba(0, 0, 0, 0.1)
- Padding: 24px
- Borde: 1px sólido #e2e8f0

### Formularios
**Inputs**:
- Border-radius: 8px
- Border: 2px sólido #e2e8f0
- Padding: 12px 16px
- Focus: Border azul claro
- Placeholder: Gris neutral

**Selectores**:
- Border-radius: 8px
- Padding: 12px 16px
- Icono de flecha personalizado

### Navegación
**Bottom Navigation**:
- Altura: 80px
- Fondo: Blanco con transparencia
- Border-top: 1px sólido #e2e8f0
- Iconos: 24px
- Texto: 12px

**Top Bar**:
- Altura: 56px
- Fondo: Blanco
- Sombra sutil
- Título centrado
- Botones laterales según contexto

## Efectos Visuales

### Animaciones
**Transiciones**:
- Duración: 300ms
- Easing: cubic-bezier(0.4, 0, 0.2, 1)
- Propiedades: opacity, transform

**Micro-interacciones**:
- Botones: Escala 0.95 al presionar
- Tarjetas: Sombra aumenta al hacer hover
- Formularios: Focus con glow azul

### Efectos Especiales
**Fondo con Gradient**:
- Degradado sutil azul a blanco
- Ángulo: 135deg
- Opacidad: 0.05

**Partículas** (para pantalla principal):
- Círculos pequeños flotando
- Color: Azul claro con opacidad 0.1
- Movimiento: Animación suave y lenta

## Responsive Design

### Breakpoints
- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px
- **Desktop**: 1024px+

### Adaptaciones
- **Mobile**: Bottom navigation, botones grandes
- **Tablet**: Sidebar navigation opcional
- **Desktop**: Layout de dos columnas

## Accesibilidad

### Contraste
- Texto normal: 4.5:1 mínimo
- Texto grande: 3:1 mínimo
- Elementos interactivos: 3:1 mínimo

### Tamaños de Tacto
- Botones: Mínimo 44px x 44px
- Espacio entre elementos táctiles: Mínimo 8px

### Navegación
- Focus visible con outline azul
- Orden lógico de tabulación
- Skip links para lectores de pantalla

## Marca y Personalización

### Logo Conceptual
- Símbolo de escudo o protección
- Colores institucionales
- Versión simplificada para iconos

### Iconografía
- Estilo: Outline (contorno)
- Grosor: 2px
- Tamaño: 24px estándar
- Consistencia en estilo visual

### Ilustraciones
- Estilo: Flat design con sombras suaves
- Colores: Palette institucional
- Temas: Seguridad, confidencialidad, apoyo

## Implementación

### CSS Custom Properties
```css
:root {
  --color-primary: #1e3a8a;
  --color-secondary: #3b82f6;
  --color-danger: #dc2626;
  --color-success: #059669;
  --color-warning: #f59e0b;
  --color-neutral: #6b7280;
  --spacing-xs: 4px;
  --spacing-sm: 8px;
  --spacing-md: 16px;
  --spacing-lg: 24px;
  --spacing-xl: 32px;
  --border-radius-sm: 8px;
  --border-radius-md: 12px;
  --border-radius-lg: 16px;
}
```

### Componentes Reutilizables
- Botones con variantes
- Tarjetas con diferentes estados
- Formularios con validación visual
- Navegación adaptable
- Modales y overlays consistentes