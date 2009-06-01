<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - Ažuriranje");
define("_JUMP_TO_CONTENT", "Idi na sadržaj");
define("_CHOOSE_LANG", "Odaberi jezik");
define("_LANG_SELECTION", "Odaberi jezik");

// choose begin
define("_TITLE_1OF2", "Korak 1 od 2 : Odaberite početnu verziju");
define("_IS_PRESENT_DIRECTORIES","Imate direktorija koji nisu više u upotrebi, preporučamo Vam da ih obrišete: ");
define("_LACKING_DIRECTORIES","Neki direktoriji nedostaju, bez njih nije moguća potpuna instalacija i pravilan rad sistema : ");
define("_CANT_CONNECT_WITH_DB", "Neuspjela konekcija sa bazom podataka, provjerite podatke upisane u config.php");
define("_CANT_CONNECT_WITH_FTP","Neuspjela konekcija sa specificiranim ftp serverom, provjerite podatke upisane u config.php");
define("_CHECKED_DIRECTORIES","Neki direktoriji za snimanje fajlova ne postoje ili nemaju odgovarajuća prava pristupa");
define("_EMPTY_DIRECTORIES","Neki direktoriji za snimanje fajlova su prazni, da li ste sigurni da nemate neke starije fajlove za uvoz?");
define("_START_VERSION","Početna verzija");
define("_END_VERSION","Finalna verzija");
define("_DOUPGRADE", "Nastavi ažuriranje");

// result 
define("_TITLE_2OF2","Korak 2 od 2 : Ažuriranje sistema");
define("_UPGRADING_VERSION","Verzija : ");
define("_FAILED_OPERATION","Operacija nije uspjela, kod greške : ");
define("_SUCCESSFULL_OPERATION", "Uspjeh operacije : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Kritična greška tijekom ažuriranja, proces je prekinut");
define("_TITLE_STEP3", "Ažuriranje jezika");
define("_LANG_INSTALLED", "Ažuriranje jezika");
define("_LANGUAGE", "");
define("_PLATFORM", " za platformu");
define("_LANGUAGE_NOT_FOUND", "Fajl jezika nije nađen");
define("_NEXT", "Sljedeći");
define("_NEXTSTEP", "Sljedeći korak");
define("_ENDSTEP", "Kraj");
define("_END_PHRASE", "Ažuriranje uspješno");
define("_CRITICAL_ERROR","Kritična greška ");
define("_NOTSCORM","Ovaj server ne podržava domxml ili nema php5, ne možete ažurirati docebo, tražite od Vašeg provajdera da instalira domxml ekstenziju na server");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","overload funkcija nije aktivna, to znači da morate imati php verzije 4.3.0 ili više. Linux mandriva je kompajliran bez overload funkcije, potražite softver čije je ime u obliku: php4-overload-xxxxx.mdk i instalirajte modul, Linux fedora core 4 nekad doživljava bagove sa overload funkcijom, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">traže zakrpu</a>. Ako koristite Windows savjetuje se upotreba <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Nastavi standardno ažuriranje jezika (biće prepisano po starom)");
define("_NEXT_ONLY_ADD", "Nastavi ažuriranje jezika, ovim će se dodati neke nove riječi, a stare se neće brisati");
define("_CONVERT_TO_UTF", "utf-8 konverzija u toku ...");
define("_CONVERT_TO_UTF_COMMENT", "Ažuriramo sadržaj i jezike u utf-8, ne prekidajte ovaj proces");
?>