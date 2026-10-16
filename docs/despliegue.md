# Guía de Despliegue

## Instalación desde archivo ZIP (Producción)

Esta guía describe el procedimiento para instalar el plugin `local_chuspasocial` mediante la interfaz gráfica de Moodle para entornos de producción.

### Requisitos previos
- Cuenta con rol de **Administrador del sitio** en Moodle.
- Versión de Moodle 4.5 o superior.
- Archivo comprimido `local_chuspasocial.zip` descargado desde la sección de **Releases** del repositorio.

### Pasos de instalación
1. Inicie sesión en la plataforma Moodle como **Administrador del sitio**.
2. Navegue a **Administración del sitio** > **Plugins** > **Instalar plugins**.
3. En la sección **Cargar un archivo ZIP**, arrastre el archivo `local_chuspasocial.zip` o selecciónelo mediante el explorador de archivos.
4. En la casilla **Tipo de plugin**, asegúrese de seleccionar `Local plugin (local)`.
5. Presione el botón **Instalar plugin desde archivo ZIP**.
6. En la pantalla de verificación de requerimientos y compatibilidad, presione **Continuar**.
7. En la vista de actualización de la base de datos, haga clic en **Actualizar base de datos de Moodle ahora**.

---

## Ajustes del plugin

Una vez instalado, el administrador configura el plugin desde **Administración del sitio** > **Plugins** > **Plugins locales** > **ChuspaSocial**. Los ajustes se guardan en la base de datos de Moodle con el botón **Guardar cambios**; no hace falta editar `config.php` ni definir variables de entorno.

| Ajuste | Variable | Descripción | Valor por defecto |
| :--- | :--- | :--- | :--- |
| Habilitar marketplace | `enablemarketplace` | Activa o desactiva el marketplace del plugin. | `1` (activado) |
| Longitud máxima de publicación | `maxpostlength` | Cantidad máxima de caracteres por publicación. | `1000` |
| Máximo de imágenes por publicación | `maxpostimages` | Cantidad máxima de imágenes por publicación. | `5` |
| Publicaciones por página | `postsperpage` | Cantidad de publicaciones que se muestran por página. | `15` |
| Moneda | `currency` | Código de la moneda en que se expresan los precios. | `BOB` |
---

## Evidencia de validación en instalación limpia

Prueba realizada en una instalación limpia de Moodle 4.5.15, con el ZIP del
plugin generado desde la rama `dev` **sin ninguna modificación**.

| Componente | Versión |
| --- | --- |
| Moodle | 4.5.15 |
| PHP | 8.2.12 |
| MariaDB | 10.11.19 |

El ZIP se generó con `git archive --format=zip --prefix=chuspasocial/
upstream/dev:src` y se subió desde Administración del sitio > Plugins >
Instalar plugins.

Resultado: al continuar con la actualización de la base de datos, Moodle falla en `admin/upgradesettings.php` con este error:

```text
Exception - syntax error, unexpected variable "$settings", expecting ")"
```

Comprobación con PHP sobre el archivo instalado:

```text
PHP Parse error: syntax error, unexpected variable "$settings", expecting ")" in local\chuspasocial\settings.php on line 56
```

Causa: en `src/settings.php`, el ajuste `enablemarketplace` (líneas 51 a 55)
no se cierra con `));` antes de abrir el siguiente `$settings->add(` en la
línea 56. Es un defecto del plugin en `dev`, ajeno a esta guía, y se reporta
por separado. Hasta que se corrija, los pasos de ajustes de esta guía no se
pueden verificar en Moodle 4.5.
