# ESTADO DEL PROYECTO

## Entorno
- Laravel: [pendiente de confirmar]
- PHP: [pendiente de confirmar]
- Base de datos: MySQL (portal_seguro)
- Frontend: Blade + Tailwind + Vite

## Módulos cerrados
- [ ] Fase 0 - Base
- [ ] Login seguro
- [ ] Banner
- [ ] Servicios
- [ ] Nosotros
- [ ] Categorías
- [ ] Publicaciones
- [ ] Multimedia
- [ ] Videos
- [ ] Equipo
- [ ] Testimonios
- [ ] Contacto
- [ ] Redes sociales
- [ ] SEO
- [ ] Usuarios
- [ ] Roles y permisos
- [ ] SMTP
- [ ] OTP
- [ ] Auditoría
- [ ] Dashboard
- [ ] Hardening
- [ ] Testing
- [ ] Despliegue

## Módulo actual
Fase 0 - Arquitectura base

## Decisiones técnicas vigentes
- Se usa Blade + Tailwind CSS + Vite (sin frameworks JS adicionales por ahora).
- Estructura de layouts: layouts/public.blade.php, layouts/admin.blade.php, layouts/auth.blade.php.

## Rutas importantes
- / → home
- /login → login (vista visual, sin lógica todavía)
- /admin → admin.dashboard (protegida con middleware auth)

## Servicios compartidos
- Ninguno todavía.

## Pendientes
- Confirmar versión de Laravel y PHP.
- Confirmar si Tailwind ya está instalado en el proyecto.
- Implementar login funcional (Fase 0.1).

## Última prueba exitosa
- Fecha: [pendiente]
- Comando: php artisan serve
- Resultado: [pendiente]