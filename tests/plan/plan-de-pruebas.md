# Plan de Pruebas

## 1. Objetivo

## 2. Alcance
### En alcance
-
### Fuera de alcance
-

## 3. Tipos de prueba
- [ ] Unitarias
- [ ] Integración
- [ ] Funcionales manuales
- [ ] Rendimiento
- [ ] Seguridad
- [ ] Regresión

## 4. Entornos

| Entorno | SO | Versión |
|---|---|---|
| Local | | |
| CI | Ubuntu latest | |

## 5. Responsables

| Rol | Responsable |
|---|---|
| Diseño de casos | |
| Ejecución manual | |
| Automatización | |
| Reporte | |

## 6. Criterios de salida
- [ ] Cobertura mínima de ___%
- [ ] Cero bugs críticos abiertos
- [ ] Todos los casos ejecutados

## 7. Riesgos

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| Incompatibilidad con Moodle 4.5 LTS o con versiones de PHP (8.1 a 8.3) | Media | Alta | Ejecutar CI con `moodle-plugin-ci` en varias versiones de PHP y probar sobre `MOODLE_405_STABLE` |
| Fallos de esquema o consultas en distintos motores de base de datos (MariaDB, MySQL, PostgreSQL) | Media | Alta | Validar el esquema XMLDB y las consultas DML en los tres motores antes de cada entrega |
| Fuga de publicaciones entre materias (un muro de curso lo deben ver solo los inscritos) | Media | Alta | Casos de prueba de permisos y capacidades por curso, con usuarios inscritos y no inscritos |
| Exposición de datos personales en perfiles, seguidores o mensajería | Media | Alta | Revisar capacidades y privacidad; documentar y verificar lo definido en `security/` |
| Vulnerabilidades web (XSS en publicaciones y comentarios, CSRF, subida de imágenes) | Media | Alta | Limpiar y validar entradas, probar con datos maliciosos y ejecutar pruebas de seguridad |
| Contenido inapropiado o abuso en publicaciones y comentarios | Alta | Media | Probar el flujo de reportes de contenido y de moderación de principio a fin |
| Datos inconsistentes en el marketplace (precio inválido, contacto externo incorrecto) | Media | Media | Pruebas de validación del formulario de anuncios (precio mayor o igual a 0, campos requeridos) |
| Bajo rendimiento del muro con muchas publicaciones, tags y notificaciones | Media | Media | Usar paginación, revisar consultas y ejecutar pruebas de carga con datos de ejemplo |
| Cobertura de pruebas insuficiente (PHPUnit y Behat) por tiempo y tamaño del equipo | Alta | Media | Priorizar los casos críticos y revisar la cobertura en CI antes de fusionar a `dev` |
| Diferencias entre entornos locales y CI (Windows, Linux, Docker) | Media | Baja | Documentar la instalación, fijar versiones de dependencias y usar `moodle-docker` como alternativa |