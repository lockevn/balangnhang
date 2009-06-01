<?php

define("_INSTALLER_TITLE", "Instalación - Docebo 3.6.0.3 ");
define("_INSTALL", "Instalación");
define("_JUMP_TO_CONTENT", "Saltar al contenido");

// choose begin

define("_SELECT_LANGUAGE", "Seleccionar idioma");
define("_LANGUAGE", "Idioma");
define("_LANG_INSTALLED", "Language has been installed");
define("_PLATFORM", "Escoja las aplicaciones que necesita instalar");

define("_ITALIAN", "Italiano");
define("_ENGLISH", "Inglés");
define("_FRENCH", "Francés");
define("_SPANISH", "Español");
define("_GERMAN", "German");
define("_PORTUGUESE-BR", "Portuguese-br");
define("_TAMIL", "Tamil");
define("_CROATIAN", "Croatian");
define("_BOSNIAN", "Bosnian");

define("_TITLE_STEP1", "Paso 1: Seleccionar idioma");
define("_TITLE_STEP2", "Paso 2: Licencia");
define("_TITLE_STEP3", "Paso 3: Escoger aplicaciones para instalar");
define("_TITLE_STEP4", "Paso 4: Configuración");
define("_TITLE_STEP5", "Paso 5: Personalizar la instalación");
define("_TITLE_STEP6", "Paso 6: Importar base de datos");
define("_TITLE_STEP7", "Paso 7: Importar idiomas");
define("_TITLE_STEP8", "Paso 8: Instalación completada");

define("_IS_PRESENT_DIRECTORIES","Usted tiene algunos directorios que ya no serán usados, le sugerimos borrarlos:");
define("_LACKING_DIRECTORIES","Faltan algunos directorios, sin estos usted no podrá instalar ciertas partes de la aplicación o usar correctamente el sistema : ");
define("_CANT_CONNECT_WITH_DB", "No se puede conectar a la base de datos, por favor verifique los datos insertados");
define("_CANT_CONNECT_WITH_FTP","No se puede conectar al servidor ftp especificado, por favor verifique los parámetros");
define("_CHECKED_DIRECTORIES","Algún directorio para el almacenamiento de archivos no existe o no tiene los permisos de escritura correctos");
define("_CHECKED_FILES","Algunos archivos no tienen los permisos adecuados");
define("_EMPTY_DIRECTORIES","");
define("_SELECT_WHATINSTALL", "Seleccione que plataforma necesita instalar");


define("_WARNING_NOT_INSTALL", "<b>Atención</b>: si no marca alguna aplicación, usted no la podrá restaurar con el procedimiento automático.");

define("_FRAMEWORK", "Estructura General de Docebo");
define("_LMS", "Sistema de Gestión de Aprendizaje Docebo");
define("_ECOM", "Docebo E-Commerce");
define("_CMS", "Sistema de Gestión de Contenido Docebo");
define("_KMS", "Sistema de Gestión del Conocimiento");
define("_SCS", "Sistema de Colaboración Sincrónica Docebo");

define("_NEXT", "Siguiente paso");
define("_BACK", "Atrás");
define("_REFRESH", "Recargar");
define("_DOINSTALL", "Instalar");
define("_FINISH", "Fin");

define("_DATABASE_INFO", "Información de la base de datos");
define("_DB_HOST", "Dirección");
define("_DB_NAME", "Nombre de la base de datos");
define("_DB_USERNAME", "Usuario de la base de datos");
define("_DB_PASS", "Contraseña");
define("_DB_CONFPASS", "Confirmar contraseña");

define("_UPLOAD_METHOD", "Método de carga de archivo (se suguiere FTP, si usted trabaja bajo la plataforma Windows en casa use HTTP");
define("_HTTP_UPLOAD", "Método clásico (HTTP)");
define("_FTP_UPLOAD", "Cargar archivo usando FTP");
define("_NOTAVAILABLE", "No disponible");

define("_FTP_INFO", "Datos de acceso FTP");
define("_IF_FTP_SELECTED", "(Si usted seleccionó FTP como método de carga)");
define("_FTP_HOST", "Dirección del servidor");
define("_FTP_PORT", "Número de puerto (generalmente es correcto)");
define("_FTP_USERNAME", "Nombre de usuario");
define("_FTP_PASS", "Contraseña");
define("_FTP_CONFPASS", "Confirmar contraseña");
define("_FTP_PATH", "Parche FTP (es la raíz donde los archivos son almacenados. Ej. /htdocs/ /mainfile_html/");


define("_SOFTWARE_LICENSE", "Licencia del software para docebo");
define("_AGREE_LICENSE", "Acepto las condiciones de la licencia");
define("_MUST_ACCEPT_LICENSE", "Usted debe aceptar la licencia antes de continuar");

define("_DOMXML_REQUIRED", "Para instalar la suite Docebo usted debe tener instalado el módulo domxml o una versión de PHP mayor a 5");

define("_LANG_TO_INSTALL", "Idiomas a instalar");
define("_NUMBER_ESTIMATED_USERS", "Número de usuarios registrados que usarán el software");
define("_LESS_THAN50", "Menos de 50");
define("_LESS_THAN150", "Entre 50 y 150");
define("_MORE_THAN150", "mas de 150");
define("_MORE_THAN_ONE_BRANCH", "¿Su compañía o asociación tiene mas de una ubicación?");
define("_ANSWER_YES", "Si;");
define("_ANSWER_NO", "No");
define("_ADMINISTRATION_TYPE", "Tipo de Administración");
define("_ONE_ADMIN", "Un administrador");
define("_SUB_ADMINS", "Administrador y sub-administradores");

define("_REQUIRE_ACCESSIBILITY", "¿Usted necesita cumplir con estándares de accesibilidad para discapacidades?");
define("_REGISTRATION_TYPE", "Tipo de registro");
define("_REG_TYPE_FREE", "cualquier persona se puede registrar");
define("_REG_TYPE_MOD", "El moderador debe aprobar");
define("_REG_TYPE_ADMIN", "Únicamente el administrador puede crear usuarios");

define("_SIMPLIFIED_INTERFACE", "Opción para simplificar la interfase");
define("_ADMIN_USER_INFO", "Información sobre el administrador");
define("_ADMIN_USERNAME", "Usuario");
define("_ADMIN_PASS", "Contraseña");
define("_ADMIN_CONFPASS", "Confirmar contraseña");
define("_ADMIN_EMAIL", "e-mail");
define("_WEBSITE_INFO", "Información del sitio web");
define("_DEFAULT_PLATFORM", "Aplicación principal (Página de inicio)");
define("_SITE_DEFAULT_SENDER", "Dirección de email predeterminada");
define("_SITE_BASE_URL", "Url principal del sitio web (No lo cambie)");


define("_INVALID_USERNAME", "Usuario no válido.");
define("_INVALID_PASSWORD", "Contraseña no válida o no concuerda.");
define("_INVALID_EMAIL", "Dirección de E-Mail no válida.");
define("_INVALID_DEFAULTSENDEREMAIL", "Dirección de e-mail no es válida.");
define("_INVALID_SITEBASEURL", "Url principal no es válido, la dirección debe finalizar con \"/\".");


define("_DB_IMPORT_OK", "Base de datos correctamente cargada");
define("_DB_IMPORT_FAILED", "Usted a experimentado algún error en la base de datos, por favor reintente o use el procedimiento manual");
define("_NEXT_IMPORT_LANG", "Ahora se importarán los idiomas, esta operación puede tardar, (1 minuto o mas), por favor no cierre el navegador y haga click en \"siguiente paso\" ÚNICAMENTE cuando la página haya sido cargada correctamente. Si experimenta problemas por favor pídale a su administrador de sistema o proveedor de Internet configurar el timeout de PHP a un valor más alto o use el procedimiento manual");


define("_CONFIGURATION", "Configuración");
define("_INSTALLATION_COMPLETE", "La instalación ha sido completada");
define("_COPY_N_PASTE_CONFIG", "Copie y pegue el siguiente texto dentro del archivo config.php");


define("_TO_ADMIN", "Para la administración de la interfase haga click aquí");
define("_TO_WEBSITE", "Para ir a la página principal haga click en el siguiente enlace");
define("_INSTALLED_APPS", "Aplicación instalada");

define("_REMOVE_INSTALL_FOLDERS_AND_WRITE_PERM", "<b>Atención:</b> antes de proceder a borrar el archivo por favor borre la carpeta install/ del sitio web y baje el nivel de permisos de escritura del archivo config.php");


// result
define("_FAILED_OPERATION","Operación fallida, código de error: ");
define("_SUCCESSFULL_OPERATION", "Operación exitosa para : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Error crítico en la actualización, la actualización ha sido detenida");


// specific
define("_LMS_ENABLE_EVENT_UI", "Los usuarios pueden escoger que notificación recibir");
define("_LMS_ENABLE_GROUPSUB_UI", "Los usuarios pueden escoger suscribirse o no a otros grupos");

// diagnostic
define("_SERVERINFO","Información del servidor");
define("_SERVER_ADDR","Dirección del servidor : ");
define("_SERVER_PORT","Puerto del servidor : ");
define("_SERVER_NAME","Nombre del servidor : ");
define("_SERVER_ADMIN","Administrador del servidor : ");
define("_SERVER_SOFTWARE","Software del servidor : ");
define("_PHPINFO","Información de PHP : ");
define("_PHPVERSION","Versión de PHP : ");
define("_SAFEMODE","Modo seguro : ");
define("_REGISTER_GLOBAL","register_global : ");
define("_MAGIC_QUOTES_GPC","magic_quotes_gpc : ");
define("_UPLOAD_MAX_FILESIZE","upload_max_filsize : ");
define("_POST_MAX_SIZE","post_max_size : ");
define("_MAX_EXECUTION_TIME","max_execution_time : ");
define("_ALLOW_URL_INCLUDE","allow_url_include : ");
define("_DANGER","Peligro - Set to OFF");
define("_DOMXML","domxml(); : ");
define("_LDAP","Ldap : ");
define("_ON","Encendido ");
define("_OFF","Apagado ");
define("_NEXT_STEP","Siguiente paso ");
define("_ONLY_IF_YU_WANT_TO_USE_IT","Considere esta advertencia únicamente si usted necesita usar LDAP ");
define("_NOTSCORM","Este servidor no soporta domxml o no es php5, usted no puede usar docebo, contacte a su proveedor para instalar la extensión domxml en este servidor");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","La función de sobrecarga no está activa, esto significa que usted debe tener una versión de php 4.3.0 o mayor. Linux mandriva está compilado sin sobrecarga, busque un paquete con un nombre similar a: php4-overload-xxxxx.mdk e instale el módulo, Linux fedora core 4 algunas veces presenta bugs con la sobrecarga, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">por favor actualícelo</a>. Si usted trabaja en una máquina con el sistema Windows instalado en ella, le sugerimos usar <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_CRITICAL_ERROR","Error crítico ");

?>