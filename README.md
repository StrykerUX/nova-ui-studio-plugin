# NovaStudio Plugin

Plugin de personalización para WordPress que permite personalizar extensivamente el tema NovaUI con un enfoque en diseño Soft Neo-Brutalist.

## Características

- Panel de administración para personalización visual completa
- Editor CSS avanzado para personalizaciones específicas
- Sistema de presets para configuración rápida
- Vista previa en tiempo real de los cambios
- Opciones para tema claro/oscuro
- Personalización por plan de membresía
- Personalización extensiva de sidebar/menú lateral
- Editor de header y menú superior
- Personalización de dashboard de usuario
- Editor de plantillas y páginas
- Gestor de componentes UI
- Personalización avanzada para plugins propios (Chat IA, Quick Links)
- Editor de tipografía avanzado
- Configuración de transiciones y animaciones
- Sistema de exportación/importación de configuraciones

## Áreas de Personalización

- **Colores**: Esquemas de color completos con paletas predefinidas
- **Tipografía**: Fuentes, tamaños, estilos y jerarquía tipográfica
- **Componentes**: Estilos de tarjetas, botones, formularios, tablas, etc.
- **Layout**: Anchos, espaciados, disposición de elementos
- **Efectos**: Transiciones, animaciones, sombras, bordes
- **WooCommerce**: Personalización de páginas de producto, checkout, etc.

## Estructura del Plugin

```
nova-ui-studio-plugin/
├── admin/
│   ├── css-editor/             # Editor CSS avanzado
│   ├── theme-options/          # Panel de opciones del tema
│   └── templates/              # Templates de admin
├── assets/
│   ├── css/                    # Estilos del plugin
│   └── js/                     # Scripts del plugin
├── includes/
│   ├── shortcodes/             # Shortcodes adicionales
│   ├── widgets/                # Widgets personalizados
│   └── helpers/                # Funciones auxiliares
└── nova-ui-studio.php          # Archivo principal del plugin
```

## Niveles de Personalización

NovaStudio ofrece tres niveles de personalización adaptados a diferentes planes de membresía:

### Nivel Básico (Para Todos los Usuarios)
- Selección de tema claro/oscuro
- Opciones básicas de colores primarios
- Personalización de avatar y perfil

### Nivel Intermedio (Planes Profesionales)
- Personalización de componentes específicos
- Selección entre múltiples presets
- Ajustes de tipografía y espaciado

### Nivel Avanzado (Planes Empresa)
- Editor CSS completo
- Personalización completa del dashboard
- Posibilidad de CSS personalizado por componente
- Branding completo (logos, colores, fuentes)

## Gestor de Sidebar/Menú Lateral

- **Configuración de Logo**:
  - Carga de imagen para logo completo (sidebar expandido)
  - Carga de imagen para icono (sidebar colapsado)
  - Opción para ajustar dimensiones y alineación

- **Gestor de Elementos del Menú**:
  - Tipos de elementos: Enlaces, separadores, encabezados, submenús
  - Propiedades por elemento: Título, URL, icono, visibilidad condicional
  - Organización mediante drag-and-drop
  - Vista previa en tiempo real

## Requisitos

- WordPress 5.5 o superior
- PHP 7.4 o superior
- Tema NovaUI instalado y activado
- Navegadores modernos (2 últimas versiones)

## Instalación

1. Descarga el archivo zip del plugin
2. Ve a tu panel de administración de WordPress > Plugins > Añadir nuevo > Subir plugin
3. Selecciona el archivo zip y haz clic en "Instalar ahora"
4. Activa el plugin
5. Accede a "NovaStudio" en el menú lateral para comenzar la personalización

## Desarrollo

### Requisitos para desarrollo

- Node.js 14.x o superior
- npm 6.x o superior

### Instrucciones para desarrollo

1. Clona este repositorio:
   ```bash
   git clone https://github.com/StrykerUX/nova-ui-studio-plugin.git
   cd nova-ui-studio-plugin
   ```

2. Instala las dependencias (cuando se implemente el sistema de build):
   ```bash
   npm install
   ```

3. Para desarrollo (watches y compilación automática):
   ```bash
   npm run dev
   ```

4. Para compilar para producción:
   ```bash
   npm run build
   ```

## Licencia

Este plugin está licenciado bajo la [GNU General Public License v2 o posterior](https://www.gnu.org/licenses/gpl-2.0.html).

## Créditos

- Diseñado y desarrollado por StrykerUX
