<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - Update");
define("_JUMP_TO_CONTENT", "Gehe zum Inhalt");
define("_CHOOSE_LANG", "Sprache auswählen");
define("_LANG_SELECTION", "Sprache auswählen");

// choose begin
define("_TITLE_1OF2", "Step 1 von 2 : Select start version");
define("_IS_PRESENT_DIRECTORIES","Es gibt nicht mehr verwendete Verzeichnisse. Wir empfehlen diese zu löschen: ");
define("_LACKING_DIRECTORIES","Es fehlen Verzeichnisse, ohne diese Verzeichnisse können Teile der Applikation nicht installiert bzw. genutzt werden : ");
define("_CANT_CONNECT_WITH_DB", "Verbindung zur DB kann nicht hergestellt werden, bitte die angegebenen Daten in config.php prüfen");
define("_CANT_CONNECT_WITH_FTP","FTP Verbindung zum angegebenen Server ist nicht möglich, bitte die angegebenen Daten in config.php prüfen");
define("_CHECKED_DIRECTORIES","Verzeichnisse für gespeicherte Dateien existieren nicht, oder haben falsche Berechtigungen gesetzt");
define("_EMPTY_DIRECTORIES","Verzeichnisse oder Dateien zur Datenspeicherung sind leer. Haben Sie sicher keine Daten mehr zu importieren?");
define("_START_VERSION","Start Version");
define("_END_VERSION","End Version");
define("_DOUPGRADE", "Mit Upgrade fortfahren");

// result 
define("_TITLE_2OF2","Step 2 of 2 : System Update");
define("_UPGRADING_VERSION","Update Version : ");
define("_FAILED_OPERATION","Operation fehlgeschlagen, Errorcode : ");
define("_SUCCESSFULL_OPERATION", "Operation erfolgreich für : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Kritischer Updatefahler, Update wurde gestoppt");
define("_TITLE_STEP3", "Sprachupdate");
define("_LANG_INSTALLED", "Update der Sprachen");
define("_LANGUAGE", "");
define("_PLATFORM", " für Plattform");
define("_LANGUAGE_NOT_FOUND", "Sprachdatei nicht gefunden");
define("_NEXT", "Weiter");
define("_NEXTSTEP", "Nächster Schritt");
define("_ENDSTEP", "Ende");
define("_END_PHRASE", "Update wurde erfolgreich abgeschlossen");
define("_CRITICAL_ERROR","Kritischer Fehler ");
define("_NOTSCORM","Dieser Server unterstützt DOMXML nicht, oder wird nicht unter PHP5 betrieben, daher können Sie DOCEBO nicht installieren. Bitte fragen Sie ihren Provider um Installation von DOMXML.");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","Overload Funktion ist nicht aktiv. Das bedeutet, dass Sie eine PHP Version 4.3.0 oder höher benötigen. Linux mandriva ist ohne overload kompiliert, suchen Sie ein Pakt mit einem Namen ähnlich: php4-overload-xxxxx.mdk und installieren Sie das Modul, Linux Fedora Core 4 verursucht manchmal Bugs mit overload, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">bitte den Patch installieren</a>. Auf einem Windows-Server empfehlen wir: <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Mit Standard Sprachupdate fortfahren (überschreibt alles)");
define("_NEXT_ONLY_ADD", "Mit Sprachupdate fortfahren, die fügt nur neue Daten hinzu, überschreibt aber keine bestehenden Einträge");
define("_CONVERT_TO_UTF", "utf-8 Konvertierung im Gange ...");
define("_CONVERT_TO_UTF_COMMENT", "Sprachen und Inhalt werden auf utf-8 Format umgestellt, diese Operation nicht unterbrechen!");
?>