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

El procedimiento de instalación desde el archivo `.zip` se probó en un sitio Moodle recién instalado, sin el plugin.

### Registro de instalación
- **Entorno:** Moodle 4.3.11+ (Build: 20250411) | PHP 8.2.12 | MariaDB 10.4.32 (XAMPP en Windows).
- **Método de prueba:** carga del archivo `.zip` en `/admin/tool/installaddon/index.php`, con **Tipo de plugin** = `Local plugin (local)`.
- **Validación:** `Validating local_chuspasocial ... OK`, con la advertencia informativa `Declared maturity level [MATURITY_ALPHA]` porque el plugin se declara en fase alfa.
- **Pantalla Plugins check:** `local_chuspasocial` aparece como `To be installed`.
- **Resultado:** tras **Actualizar base de datos de Moodle ahora**, la instalación terminó con éxito y el plugin quedó disponible en **Administración del sitio** > **Plugins** > **Plugins locales** > **ChuspaSocial**.
- **Nota:** el plugin declara Moodle 4.5 como versión mínima. Esta prueba se hizo en Moodle 4.3.11, por lo que el ZIP de prueba tenía `requires` y `supported` rebajados en `version.php`; ese cambio no forma parte del repositorio.
