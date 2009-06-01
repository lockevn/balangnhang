/************************************************************************/
/* DOCEBO - Framework							*/
/* ===================================================================  */
/*									*/
/* Copyright (c) 2004 - 2005 - 2006					*/
/* http://www.docebo.com						*/
/*									*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.	*/
/************************************************************************/

SPECIFICHE:

Server: Linux, Windows, MAcos, Unix, Sun with 
- Apache 1.3.x o superiore, IIS6
- PHP 5.2.x o superiori con funzione overload() abilitata; e domxml(); (doxmxml solo se php4) abilitata (Linux mandriva non è compilata per default con la funzione overload, Fc4 ha un bug che potrebbe dover essere corretto)
- Mysql 4.1 o superiore
- Non importa se safe mode o register_global sono on o off ;-)
- Se devi testare sul pc windows di casa suggeriamo easyphp 1.8 o XAMPP

Installazione:

- Assicuratevi di avere parametri ftp (host, user, password) e parametri del database ((user, password dbname) a disposizione

- Se sei sul tuo pc windows di casa con easyphp 1.8 create manualmente un database tramite http://localhost/mysql/, ricordate che l'utente per la connessione al DB è root e la password deve essere lasciata vuota
- caricate tutti i file nella directory principale
- Lanciate http://www.yoursite.com/install/
- Seguite le istruzioni di installazione
- Una vola arrivati in fondo siete OK

Nota: Il sistema caricherà i file xml di linguaggio, non cliccate finché le pagine non saranno completamente caricate!

Procedura di aggiornamento (da docebo 2.0.x a docebo 3.x)

Procedura di aggiornamento (da docebo 3.x ad una versione superiore):

- Sovrascrivete tutti i vecchi file (non eliminate/sovrascrivete il config.php!!!)
- Lanciate www.yourwebsite.com/upgrade
- Seguite le istruzioni

Aggiornare linguaggi (Senza procedura di upgrade)

- Andare nell'area di amministrazione
- Andare in configurazione
- Andare in importa/esporta linguaggi
- Importa i file XML

Nota: Questa azione sovrascriverà *tutte* le traduzioni, anche le vostre personalizzazioni (Esempio: se sovrascriverete la lingua inglese perderete tutte le modifiche alla lingua inglese)

Ulteriori informazioni sui manuali o wiki:

http://www.docebo.org

