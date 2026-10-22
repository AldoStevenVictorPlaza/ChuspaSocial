-- =========================================================
-- Consulta para medir la evolución mensual de la actividad del plugin
-- Para cada mes (año y mes de timecreated) devuelve el total de
-- publicaciones (total_publicaciones), comentarios (total_comentarios)
-- y reacciones (total_reacciones) creados, ordenado cronológicamente.
-- Solo devuelve agregados por mes: no expone userid, nombres, correos,
-- direcciones IP ni contenido de publicaciones o comentarios.
--
-- Tablas (con prefijo de Moodle entre llaves):
--   {local_chuspasocial_post}, {local_chuspasocial_comment},
--   {local_chuspasocial_reaction}.
-- Campo usado en todas: timecreated (marca de tiempo Unix, en segundos).
--
-- Notas:
--   * Se cuentan todas las publicaciones, incluidas las ocultas (hidden).
--   * Año y mes se obtienen con EXTRACT y TO_TIMESTAMP, igual que
--     tags_mas_usados.sql y publicaciones_por_materia.sql. Esta sintaxis
--     es de PostgreSQL.
--   * Los meses sin actividad no aparecen en el resultado.
-- =========================================================

SELECT
    anio,
    mes,
    SUM(es_publicacion) AS total_publicaciones,
    SUM(es_comentario) AS total_comentarios,
    SUM(es_reaccion) AS total_reacciones
FROM (
    SELECT
        EXTRACT(YEAR FROM TO_TIMESTAMP(timecreated)) AS anio,
        EXTRACT(MONTH FROM TO_TIMESTAMP(timecreated)) AS mes,
        1 AS es_publicacion,
        0 AS es_comentario,
        0 AS es_reaccion
    FROM {local_chuspasocial_post}

    UNION ALL

    SELECT
        EXTRACT(YEAR FROM TO_TIMESTAMP(timecreated)) AS anio,
        EXTRACT(MONTH FROM TO_TIMESTAMP(timecreated)) AS mes,
        0 AS es_publicacion,
        1 AS es_comentario,
        0 AS es_reaccion
    FROM {local_chuspasocial_comment}

    UNION ALL

    SELECT
        EXTRACT(YEAR FROM TO_TIMESTAMP(timecreated)) AS anio,
        EXTRACT(MONTH FROM TO_TIMESTAMP(timecreated)) AS mes,
        0 AS es_publicacion,
        0 AS es_comentario,
        1 AS es_reaccion
    FROM {local_chuspasocial_reaction}
) AS actividad
GROUP BY
    anio,
    mes
ORDER BY
    anio ASC,
    mes ASC;
