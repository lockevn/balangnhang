<?php

define("_UPGRADER_TITLE", "Actualización - Docebo 3.6.0.3 ");
define("_JUMP_TO_CONTENT", "Saltar al contenido");
define("_CHOOSE_LANG", "Escoger idioma");
define("_LANG_SELECTION", "Seleccionar idioma");

// choose begin
define("_TITLE_1OF2", "Paso 1 de 2 : Seleccione su versión de inicio");
define("_IS_PRESENT_DIRECTORIES","Ya están contenidos en la estructura de directorios que ya no será usada, le sugerimos borrarlos: ");
define("_LACKING_DIRECTORIES","Algunos directorios están perdidos, sin estos usted no podrá usar correctamente el sistema : ");
define("_CANT_CONNECT_WITH_DB", "Conexión a la base de datos fallida, verifique y corrija los parámetros en config.php");
define("_CANT_CONNECT_WITH_FTP","Conexión FTP fallida, verifique y corrija los parámetros en config.php");
define("_CHECKED_DIRECTORIES","Algún directorio para el almacenamiento de archivos no existe o no tiene los permisos de escritura correctos");
define("_EMPTY_DIRECTORIES","Algún directorio para el almacenamiento de archivos está vacío ¿Está seguro que no tiene ningún archivo para importar?");
define("_START_VERSION","Versión inicial");
define("_END_VERSION","Versión Final");
define("_DOUPGRADE", "Proceder con la actualización");

// result 
define("_TITLE_2OF2","Paso 2 de 2 : Actualización del sistema");
define("_UPGRADING_VERSION","Versión de actualización: ");
define("_FAILED_OPERATION","Operación fallida, código de error: ");
define("_SUCCESSFULL_OPERATION", "Operación exitosa para : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Error crítico en la actualización, la actualización ha sido detenida");
define("_TITLE_STEP3", "Actualización de idioma");
define("_LANG_INSTALLED", "Actualizando idioma");
define("_LANGUAGE", "");
define("_PLATFORM", " para la plataforma");
define("_LANGUAGE_NOT_FOUND", "El archivo de idioma no ha sido encontrado");
define("_NEXT", "Siguiente");
define("_NEXT_STEP", "Siguiente paso");
define("_ENDSTEP", "Fin");
define("_END_PHRASE", "La actualización ha sido completada exitosamente");
define("_CRITICAL_ERROR","Error crítico");
define("_NOTSCORM","Este servidor no soporta domxml o no es php5, usted no puede usar docebo, contacte a su proveedor para instalar la extensión domxml en este servidor");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","La función de sobrecarga no está activa, esto significa que usted debe tener una versión de php 4.3.0 o mayor. Linux mandriva está compilado sin sobrecarga, busque un paquete con un nombre similar a: php4-overload-xxxxx.mdk e instale el módulo, Linux fedora core 4 algunas veces presenta bugs con la sobrecarga, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">por favor actualícelo</a>. Si usted trabaja en una máquina con el sistema Windows instalado en ella, le sugerimos usar <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Proceder con la actualización de idiomas estándar (esta sobreescribirá todas las palabras)");
define("_NEXT_ONLY_ADD", "Proceder con la actualización de idioma, esta solo agregará nuevas palabras y no reescribirá las antiguas");
define("_CONVERT_TO_UTF", "conversión utf-8 en progreso ...");
define("_CONVERT_TO_UTF_COMMENT", "Estamos actualizando idiomas y contenido al método utf-8, no detenga esta operación");
?>