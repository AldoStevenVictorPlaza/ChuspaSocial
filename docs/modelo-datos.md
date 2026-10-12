# Modelo de datos de ChuspaSocial

## Introducción

El presente documento describe de manera general el modelo de datos del sistema ChuspaSocial.

Su propósito es organizar y documentar las tablas que formarán parte de la base de datos antes de iniciar su implementación.

Esta documentación permitirá mantener una estructura clara, facilitar el desarrollo del sistema y establecer criterios comunes para la definición de las tablas.

## Tablas del modelo de datos

El modelo de datos contempla un total de siete tablas:

1. `local_chuspasocial_post`
2. `local_chuspasocial_comment`
3. `local_chuspasocial_reaction`
4. `local_chuspasocial_follow`
5. `local_chuspasocial_report`
6. `local_chuspasocial_listing`
7. `local_chuspasocial_lstcourse`

## Regla de nomenclatura de Moodle

Para mantener la compatibilidad con las restricciones de Moodle, los nombres de las tablas de la base de datos deben tener un máximo de 28 caracteres.

Esta regla debe respetarse al definir los nombres de las siete tablas del modelo de datos.

## Tabla `local_chuspasocial_report` (reportes de contenido)

Almacena los reportes que los usuarios hacen sobre publicaciones, comentarios y anuncios.

| Campo | Tipo XMLDB | Nulo | Descripción |
|---|---|---|---|
| `id` | INT(10), secuencia | NO | Clave primaria autoincremental del reporte. |
| `itemtype` | CHAR(20) | NO | Tipo de contenido reportado. Valores permitidos: `post`, `comment`, `listing`. |
| `itemid` | INT(10) | NO | ID del contenido reportado (se interpreta según `itemtype`). |
| `reporterid` | INT(10) | NO | ID del usuario que realiza el reporte (referencia a `user.id`). |
| `reason` | TEXT | NO | Motivo del reporte. |
| `status` | CHAR(20) | NO | Estado del reporte. Valores permitidos: `open`, `resolved`, `dismissed`. Por defecto: `open`. |
| `resolvedby` | INT(10) | SÍ | ID del usuario que resolvió el reporte (referencia a `user.id`). Nulo mientras no se resuelva. |
| `timecreated` | INT(10) | NO | Marca de tiempo Unix de creación. |
| `timeresolved` | INT(10) | SÍ | Marca de tiempo Unix de la resolución. Nulo mientras no se resuelva. |

### Valores permitidos de `itemtype`

| Valor | Significado |
|---|---|
| `post` | El reporte es sobre una publicación. |
| `comment` | El reporte es sobre un comentario. |
| `listing` | El reporte es sobre un anuncio de libro. |
## Tabla `local_chuspasocial_listing` (anuncios de libros)

Almacena los anuncios de libros publicados por los usuarios.

| Campo | Tipo XMLDB | Nulo | Descripción |
|---|---|---|---|
| `id` | INT(10), secuencia | NO | Clave primaria autoincremental del anuncio. |
| `userid` | INT(10) | NO | ID del usuario que publica el anuncio (referencia a `user.id`). |
| `title` | CHAR(255) | NO | Título del libro. |
| `author` | CHAR(255) | NO | Autor del libro. |
| `description` | TEXT | SÍ | Descripción del anuncio. |
| `descriptionformat` | INT(4) | NO | Formato de la descripción (constantes `FORMAT_*` de Moodle; por defecto `FORMAT_HTML` = 1). |
| `price` | NUMBER(10,2) | NO | Precio del libro. |
| `externalcontact` | CHAR(255) | SÍ | Contacto externo del vendedor (teléfono, correo u otro medio). |
| `status` | CHAR(20) | NO | Estado del anuncio. Valores permitidos: `available` (disponible), `sold` (vendido). Por defecto: `available`. |
| `timecreated` | INT(10) | NO | Marca de tiempo Unix de creación. |
| `timemodified` | INT(10) | NO | Marca de tiempo Unix de la última modificación. |

### Valores permitidos de `status`

| Valor | Significado |
|---|---|
| `open` | El reporte está pendiente de revisión. |
| `resolved` | El reporte fue atendido y se tomó una acción. |
| `dismissed` | El reporte fue revisado y descartado sin acción. |
| `available` | El libro está disponible para la venta. |
| `sold` | El libro ya fue vendido. |
