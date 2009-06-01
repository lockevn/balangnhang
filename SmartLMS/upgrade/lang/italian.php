<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - Aggiornamento");
define("_JUMP_TO_CONTENT", "Salta al contenuto");
define("_CHOOSE_LANG", "Scegli lingua");
define("_LANG_SELECTION", "Selezione lingua");

// choose begin
define("_TITLE_1OF2", "Passo 1 di 2 : Selezione della versione");
define("_IS_PRESENT_DIRECTORIES","Sono ancora presenti delle directory non più usate, ti consigliamo di cancellarle : ");
define("_LACKING_DIRECTORIES","Manacano alcune directory contenenti gli applicativi della suite, senza tali cartelle non sar&agrave; possibile usare il sistema : ");
define("_CANT_CONNECT_WITH_DB", "Non &egrave; stato possibile connettersi al database specificato nel file config.php, controlla i parametri inseriti");
define("_CANT_CONNECT_WITH_FTP","Non &egrave; stato possibile connettersi tramite ftp al server specificato nel file config.php, controlla i parametri inseriti");
define("_CHECKED_DIRECTORIES","Alcune delle directory per il salvataggio dei file non esistono oppure non hanno permessi adeguati");
define("_EMPTY_DIRECTORIES","Alcune delle directory per il salvataggio dei file sono vuote, sei sicuro di non avere vecchie file da importare in queste cartelle ?");
define("_START_VERSION","Seleziona la versione di partenza");
define("_END_VERSION","Versione finale");
define("_DOUPGRADE", "Aggiorna alla nuova versione");

// result
define("_TITLE_2OF2","Passo 2 di 2 : Aggiornamento dell'applicativo");
define("_UPGRADING_VERSION","Aggiorno la versione : ");
define("_FAILED_OPERATION","Operazione fallita per l'operazione, codice dell'errore : ");
define("_SUCCESSFULL_OPERATION", "Operazione avvenuta con successo per : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Errore critico nell'aggiornamento, l'aggiornamento &egrave; stato interrotto");
define("_TITLE_STEP3", "Caricamento lingue");
define("_LANG_INSTALLED", "Installo il linguaggio");
define("_LANGUAGE", "");
define("_PLATFORM", " per la piattaforma");
define("_LANGUAGE_NOT_FOUND", "File di linguaggio non trovato");
define("_NEXT", "Avanti");
define("_NEXTSTEP", "Prossimo passo");
define("_ENDSTEP", "Fine");
define("_END_PHRASE", "L'aggiornamento è stato completato con successo");
define("_CRITICAL_ERROR","Critical error ");
define("_NOTSCORM","Questo server non supporta domXML per php4 e non è php 5, non puoi installare docebo qui, chiedi al tuo provider di installare l'estensione domxml");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","la funzione overload non è attiva, per farla funzionare devi avere installato una versione di PHP uguale maggiore alla 4.3.0. Linux mandriva non è compilata con la funzione overload, cerca un file con un nome simile a php4-overload-xxxxx.mdk e installa tu il modulo, fedora core4 ha invece un bug <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">che va patchato</a>. Se sei su windows ti consigliamo di installare <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Procedi sovrascrivendo i linguaggi");
define("_NEXT_ONLY_ADD", "Prosegui, importa i linguaggi limitandoti ad aggiungere ciò che manca");
define("_CONVERT_TO_UTF", "Conversione in utf-8 in progress ...");
define("_CONVERT_TO_UTF_COMMENT", "Stiamo aggiornando i linguaggi e i contenuti installati precedentemente alla codifica utf-8, non bloccare questa operazione");

?>