# Sistema de Gestión de Proyectos Laravel

Un sistema robusto y completo de gestión de proyectos desarrollado con Laravel, Bootstrap y MySQL. Incluye funcionalidades avanzadas como tableros Kanban, gráficos Gantt, gestión de tickets, multimedia, reuniones con Calendly y base de datos pública de contenido.

## 🚀 Características Principales

### 📊 Gestión de Proyectos
- **Dashboard Completo**: Vista general con estadísticas y actividad reciente
- **CRUD de Proyectos**: Crear, leer, actualizar y eliminar proyectos
- **Estados de Proyecto**: Activo, Completado, En Pausa, Cancelado
- **Seguimiento de Progreso**: Cálculo automático basado en tareas completadas

### 📋 Tablero Kanban
- **Drag & Drop**: Mover tareas entre columnas intuitivamente
- **Columnas Personalizables**: To Do, In Progress, Review, Done
- **Tarjetas Interactivas**: Con información de prioridad, fechas y archivos
- **Actualización en Tiempo Real**: Cambios reflejados inmediatamente

### 📈 Gráfico Gantt
- **Planificación Visual**: Timeline de tareas con fechas de inicio y fin
- **Barras de Progreso**: Representación visual del avance
- **Zoom y Navegación**: Vistas mensual, semanal y diaria
- **Línea de Tiempo Actual**: Indicador visual del día presente

### 🎫 Sistema de Tickets
- **Gestión Completa**: Crear, asignar y resolver tickets
- **Prioridades**: Baja, Media, Alta, Crítica
- **Estados**: Abierto, En Progreso, Resuelto, Cerrado
- **Vinculación**: Tickets asociados a proyectos específicos

### 📁 Gestión Multimedia
- **Carga de Archivos**: Soporte para imágenes, documentos y videos
- **Organización**: Archivos asociados a proyectos, tareas o tickets
- **Validación**: Tipos de archivo y tamaños permitidos
- **Almacenamiento Seguro**: Sistema de archivos Laravel

### 📅 Reuniones con Calendly
- **Integración Calendly**: Programación de reuniones embebida
- **Gestión de Reuniones**: Crear, editar y seguir reuniones
- **Estados**: Programada, En Progreso, Completada, Cancelada
- **Vinculación**: Reuniones asociadas a proyectos

### 📚 Base de Datos Pública
- **Contenido Compartido**: Artículos, guías, tutoriales, FAQ
- **Tipos de Contenido**: Múltiples categorías organizadas
- **Búsqueda**: Sistema de búsqueda en contenido público
- **Estadísticas**: Contador de visualizaciones y popularidad

## 🎨 Diseño y UI

### Colores Principales
- **Rojo Principal**: #FB0009
- **Blanco**: #FFFFFF  
- **Gris**: #BBBBBB

### Características de Diseño
- **Bootstrap 5**: Framework CSS moderno y responsivo
- **Google Fonts**: Tipografía Inter para mejor legibilidad
- **Sin Iconos**: Diseño limpio basado en tipografía y emojis
- **Responsive**: Adaptable a todos los dispositivos
- **Animaciones Suaves**: Transiciones y efectos visuales

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8.0+, Laravel 9
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Base de Datos**: MySQL 8.0+
- **Dependencias**: jQuery, SortableJS (para drag & drop)
- **Almacenamiento**: Sistema de archivos Laravel

## 📦 Instalación

### Requisitos Previos
- PHP 8.0 o superior
- Composer
- MySQL 8.0 o superior
- Node.js y NPM (opcional)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd project-management-system
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar el entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar la base de datos**
Editar el archivo `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. **Configurar Calendly (opcional)**
```env
CALENDLY_EMBED_URL=https://calendly.com/tu-usuario-calendly
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Crear enlace de almacenamiento**
```bash
php artisan storage:link
```

8. **Iniciar el servidor**
```bash
php artisan serve --port=8000
```

## 🗄️ Estructura de la Base de Datos

### Tablas Principales

#### Projects
- `id`, `name`, `description`
- `start_date`, `end_date`, `status`
- `created_at`, `updated_at`

#### Tasks
- `id`, `project_id`, `name`, `description`
- `start_date`, `due_date`, `priority`
- `kanban_column`, `position`
- `created_at`, `updated_at`

#### Tickets
- `id`, `project_id`, `title`, `description`
- `user_id`, `priority`, `status`
- `created_at`, `updated_at`

#### Media
- `id`, `model_type`, `model_id`
- `file_path`, `file_type`, `file_name`, `file_size`
- `created_at`, `updated_at`

#### Meetings
- `id`, `project_id`, `title`, `description`
- `scheduled_time`, `calendly_url`, `status`
- `created_at`, `updated_at`

#### Contents
- `id`, `title`, `description`, `content_type`
- `file_path`, `user_id`, `is_public`, `views_count`
- `created_at`, `updated_at`

## 🔧 Configuración Avanzada

### Personalización de Colores
Editar `resources/views/layouts/app.blade.php` en la sección de CSS variables:
```css
:root {
    --primary-red: #FB0009;
    --white: #FFFFFF;
    --gray: #BBBBBB;
}
```

### Configuración de Archivos
Modificar `config/filesystems.php` para cambiar configuraciones de almacenamiento.

### Middleware Personalizado
El sistema incluye middleware para:
- Encriptación de cookies
- Verificación CSRF
- Limpieza de strings
- Prevención durante mantenimiento

## 📱 Funcionalidades por Módulo

### Dashboard
- Estadísticas generales del sistema
- Proyectos recientes con progreso
- Tareas próximas a vencer
- Tickets críticos pendientes
- Reuniones programadas
- Actividad reciente del sistema

### Proyectos
- Lista con filtros y búsqueda
- Vista detallada con pestañas
- Estadísticas de progreso
- Gestión de archivos adjuntos
- Acciones rápidas

### Kanban
- 4 columnas predefinidas
- Drag & drop funcional
- Creación rápida de tareas
- Edición inline
- Contadores automáticos

### Gantt
- Timeline visual de tareas
- Barras de progreso por prioridad
- Zoom y navegación temporal
- Línea de tiempo actual
- Lista detallada de tareas

### Tickets
- Sistema completo de tickets
- Filtros por estado y prioridad
- Cambio rápido de estados
- Archivos adjuntos
- Historial de cambios

### Reuniones
- Integración con Calendly
- Vista de calendario
- Estados de reunión
- Notificaciones de proximidad
- Vinculación con proyectos

### Contenido Público
- Base de conocimiento
- Múltiples tipos de contenido
- Sistema de búsqueda
- Estadísticas de visualización
- Gestión de visibilidad

## 🔒 Seguridad

- Protección CSRF en todos los formularios
- Validación de archivos subidos
- Sanitización de entradas
- Middleware de seguridad
- Encriptación de cookies sensibles

## 🚀 Rendimiento

- Consultas optimizadas con Eloquent
- Carga lazy de relaciones
- Paginación en listados
- Índices de base de datos
- Caching de configuración

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o consultas:
- Crear un issue en GitHub
- Documentación en línea
- Comunidad de desarrolladores

## 🔄 Actualizaciones

### Versión 1.0.0
- Sistema base completo
- Todas las funcionalidades principales
- Interfaz responsive
- Integración con Calendly
- Sistema de archivos multimedia

### Próximas Funcionalidades
- Sistema de notificaciones en tiempo real
- API REST completa
- Aplicación móvil
- Integración con más servicios externos
- Sistema de reportes avanzados

---

**Desarrollado con ❤️ usando Laravel y Bootstrap**
