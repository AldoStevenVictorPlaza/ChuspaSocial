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

## Tabla `local_chuspasocial_follow`

Almacena los elementos que sigue cada usuario: otros usuarios y etiquetas (tags). Cada fila representa un seguimiento.

| Campo         | Tipo XMLDB | Nulo | Descripción                                                                 |
|---------------|------------|------|-----------------------------------------------------------------------------|
| `id`          | INT(10)    | No   | Clave primaria autoincremental.                                             |
| `userid`      | INT(10)    | No   | Id del usuario que sigue (referencia a `user.id`).                          |
| `itemtype`    | CHAR(10)   | No   | Tipo de elemento seguido. Valores permitidos: `user`, `tag`.                |
| `itemid`      | INT(10)    | No   | Id del elemento seguido: un `user.id` o un `tag.id`, según `itemtype`.      |
| `timecreated` | INT(10)    | No   | Marca de tiempo Unix del momento en que se creó el seguimiento.             |

### Valores permitidos de `itemtype`

XMLDB no tiene un tipo enum, por lo que `itemtype` se define como `CHAR` y sus valores se validan en la aplicación.

| Valor  | Significado                      | `itemid` apunta a |
|--------|----------------------------------|-------------------|
| `user` | El usuario sigue a otro usuario. | `user.id`         |
| `tag`  | El usuario sigue una etiqueta.   | `tag.id`          |