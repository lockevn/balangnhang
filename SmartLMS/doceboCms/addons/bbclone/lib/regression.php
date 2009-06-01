<?php
/* This file is part of BBClone (The PHP web counter on steroids)
 *
 * $Header: /cvs/bbclone/lib/regression.php,v 1.12 2006/12/27 17:01:44 christoph Exp $
 *
 * Copyright (C) 2001-2007, the BBClone Team (see file doc/authors.txt
 * distributed with this library)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * See doc/copying.txt for details
 */

$regression = array(
  "Science Traveller International 1X/1.0" => array(
    "robot" => NULL,
    "browser" => "1X",
    "os" => "other",
    "description" => "1X on Windows"
  ),
  "amaya/9.51 libwww/5.4.0" => array(
    "robot" => NULL,
    "browser" => "amaya",
    "os" => "other",
    "description" => "Amaya 9.51 on linux"
  ),
  "amaya/9.1 libwww/5.4.0" => array(
    "robot" => NULL,
    "browser" => "amaya",
    "os" => "other",
    "description" => "Amaya 9.1"
  ),
  "amaya/6.2 libwww/5.3.1" => array(
    "robot" => NULL,
    "browser" => "amaya",
    "os" => "other",
    "description" => "Amaya 6.2"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; America Online Browser 1.1; rev1.1; Windows NT 5.1;)" => array(
    "robot" => NULL,
    "browser" => "aol",
    "os" => "windowsxp",
    "description" => "AOL 1.1 on Windows XP"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; America Online Browser 1.1; rev1.2; Windows NT 5.1;)" => array(
    "robot" => NULL,
    "browser" => "aol",
    "os" => "windowsxp",
    "description" => "AOL 1.2 on Windows XP"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; America Online Browser 1.1; rev1.5; Windows NT 5.1;)" => array(
    "robot" => NULL,
    "browser" => "aol",
    "os" => "windowsxp",
    "description" => "AOL 1.5 on Windows XP"
  ),
//  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; FDM)" => array(
//    "robot" => NULL,
//    "browser" => "avantbrowser",
//    "os" => "windowsxp",
//    "description" => "Avant Browser (MSIE 6 clone) on XP with SP2 and .NET framework"
//  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Avant Browser [avantbrowser.com]; Hotbar 4.4.5.0)" => array(
    "robot" => NULL,
    "browser" => "avantbrowser",
    "os" => "windows2k",
    "description" => "Avant Browser (MSIE 6 clone) on Win 2K",
  ),
  "Advanced Browser (http://www.avantbrowser.com)" => array(
    "robot" => NULL,
    "browser" => "avantbrowser",
    "os" => "other",
    "description" => "First version",
  ),
  "Avant Browser (http://www.avantbrowser.com)" => array(
    "robot" => NULL,
    "browser" => "avantbrowser",
    "os" => "other",
    "description" => "Old version",
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Avant Browser [avantbrowser.com]; iOpus-I-M; QXW03416; .NET CLR 1.1.4322)" => array(
    "robot" => NULL,
    "browser" => "avantbrowser",
    "os" => "windowsxp",
    "description" => "Avant Browser (MSIE 6 clone) on XP with SP2 and .NET framework"
  ),
  "Mozilla/3.0 (compatible; AvantGo 3.2)" => array(
    "robot" => NULL,
    "browser" => "avantgo",
    "os" => "other",
    "description" => "AvantGo v3.2 under PalmOS 3.0 on Treo 180"
  ),
  "Mozilla/5.0 (compatible; AvantGo 3.2; ProxiNet; Danger hiptop 1.0)" => array(
    "robot" => NULL,
    "browser" => "avantgo",
    "os" => "other",
    "description" => "AvantGo v3.2 on Danger HipTop 1.0 (ProxiNet' seems to be a proxy service used by 'hiptop')"
  ),
  "Amiga-AWeb/3.5.07 beta" => array(
    "robot" => NULL,
    "browser" => "aweb",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/6.0; (Spoofed by Amiga-AWeb/3.5.07 beta)" => array(
    "robot" => NULL,
    "browser" => "aweb",
    "os" => "amiga",
    "description" => ""
  ),
  "MSIE/6.0; (Spoofed by Amiga-AWeb/3.4APL)" => array(
    "robot" => NULL,
    "browser" => "aweb",
    "os" => "amiga",
    "description" => ""
  ),
  "bluefish 0.6 HTML editor" => array(
    "robot" => NULL,
    "browser" => "bluefish",
    "os" => "other",
    "description" => ""
  ),
  "noxtrumbot/1.0 (crawler@noxtrum.com)" => array(
    "robot" => "noxtrum",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "HiddenMarket-1.0-beta www.hiddenmarket.net/crawler.php" => array(
    "robot" => "hiddenmarket",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (ReactOS; U; ReactOS 0.3; en-US; rv:1.8) Gecko/20051107" => array(
    "robot" => NULL,
    "browser" => "mozilla",
    "os" => "reactos",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322) Babya Discoverer 8.0:" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windowsxp",
    "description" => ""
  ),
  "FDM 2.x" => array(
    "robot" => "fdm",
    "browser" => "other",
    "os" => "other",
    "description" => "Free Download Manager"
  ),
  "Mozilla/4.01 (Compatible; Acorn Phoenix 2.08 [intermediate]; RISC OS 4.39) Acorn-HTTP/0.84" => array(
    "robot" => NULL,
    "browser" => "acorn",
    "os" => "risc",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; .NET CLR 2.0.50727; Media Center PC 4.0)" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windowsmc",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20040913 Waterunicorn/0.10 StumbleUpon/1.998" => array(
    "robot" => NULL,
    "browser" => "mozilla",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Yoono; http://www.yoono.com/)" => array(
    "robot" => "yoono",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "CazoodleBot/Nutch-0.9-dev (CazoodleBot Crawler; http://www.cazoodle.com; mqbot@cazoodle.com)" => array(
    "robot" => "cazoodle",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0; .NET CLR 2.0.50727)" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windowsmc",
    "description" => "Internet Explorer 6.0 on Media Center Edition 2005 with .NET Framework 1.0, 1.1, and 2.0 installed"
  ),
  "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windowsvista",
    "description" => "7.0 beta on Windows Vista"
  ),
  "Microsoft Internet Explorer/4.0b1 (Windows 95)" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windows95",
    "description" => "Internet Explorer 1.0 on Windows 95"
  ),
  "Mozilla/1.22 (compatible; MSIE 2.0; Windows 95)" => array(
    "robot" => NULL,
    "browser" => "explorer",
    "os" => "windows95",
    "description" => "Internet Explorer 2.0 on Windows 95"
  ),
  "fmII URL validator/1.0" => array(
    "robot" => "freshmeat",
    "browser" => "other",
    "os" => "other",
    "description" => "freshmeat crawler"
  ),
  "GameSpyHTTP/1.0" => array(
    "robot" => "gamespy",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; grub-client-1.4.3; Crawl your own stuff with http://grub.org)" => array(
    "robot" => "grub",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; grub-client-2.3)" => array(
    "robot" => "grub",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "GurujiBot/1.0 (+http://www.guruji.com/WebmasterFAQ.html)" => array(
    "robot" => "guruji",
    "browser" => "other",
    "os" => "other",
    "description" => "guruji : the Indian search engine robot"
  ),
  "Mozilla/5.0 (compatible; iaskspider/1.0; MSIE 6.0)" => array(
    "robot" => "iask",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "InetURL:/1.0" => array(
    "robot" => "ineturl",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ICRA_label_generator/1.0" => array(
    "robot" => "irca",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ICRA_label_generator/0.9" => array(
    "robot" => "irca",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "\"iVia Site Checker\"/1.0" => array(
    "robot" => "ivia",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "lanshanbot/1.0" => array(
    "robot" => "lanshan",
    "browser" => "other",
    "os" => "other",
    "description" => "Unknown robot from Easten Network China"
  ),
  "Mozilla/4.0 (compatible; Mavicanet robot; www.mavicanet.org)" => array(
    "robot" => "mavicanet",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "NextopiaBOT (+http://www.nextopia.com) distributed crawler client beta v0.8" => array(
    "robot" => "nextopia",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "OutfoxMelonBot/0.5 (for internet experiments; http://; outfoxbot@gmail.com)" => array(
    "robot" => "outfox",
    "browser" => "other",
    "os" => "other",
    "description" => "Unknown robot from Chinanet (60.191.80.1)"
  ),
  "SanszBot_International_Tender_Search_Engine_Ver1.5_(WWW.SANSZ.ORG) sheridan@sansz.org" => array(
    "robot" => "sanszbot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  " Syndic8/1.0 (http://www.syndic8.com/)" => array(
    "robot" => "syndic8",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "thumbshots-de-Bot (Version: 1.02, powered by www.thumbshots.de)" => array(
    "robot" => "thumbshot",
    "browser" => "other",
    "os" => "other",
    "description" => "ThumbShots website thumbnail service (Germany) robot"
  ),
   "UltraSpider3000/1.0 (+http://www.search.ch/rim.html)" => array(
    "robot" => "ultraspider",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Waggr_Fetcher)" => array(
    "robot" => "wagger",
    "browser" => "other",
    "os" => "other",
    "description" => "Online RSS Aggregator"
  ),
  "WeBoX/0.99" => array(
    "robot" => "webox",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Sqworm/2.9.85-BETA (beta_release; 20011115-775; i686-pc-linux" => array(
    "robot" => "websense",
    "browser" => "other",
    "os" => "linux",
    "description" => "a crawler from Sqworm.com"
  ),
  "NetSprint -- 2.0" => array(
    "robot" => "wp",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "AtlocalBot/1.1 +(http://www.atlocal.com/local-web-site-owner.html)" => array(
    "robot" => "atlocal",
    "browser" => "other",
    "os" => "other",
    "description" => "Atlocal local business search robot"
  ),
  "AutoMapIt Bot (http://www.automapit.com/bot.html)" => array(
    "robot" => "automapit",
    "browser" => "other",
    "os" => "other",
    "description" => "Automapit spider which builds a site map - should only be present if you have signed up to use the service"
  ),
  "Bigsearch.ca/Nutch-0.9-dev (Bigsearch.ca Internet Spider; http://www.bigsearch.ca/; info@enhancededge.com)" => array(
    "robot" => "bigsearch",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Blogshares.com Friends (Ricardo Blessed V1.34.0.1)" => array(
    "robot" => "blogshares",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Butch__2.1.1 agdm79@mail.ru" => array(
    "robot" => "butch",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "CFNetwork/129.18" => array(
    "robot" => "cfnetwork",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE is not me; EDI/1.6.6; Edacious & Intelligent Web Robot; Daum Communications Corp., Korea)" => array(
    "robot" => "daum",
    "browser" => "explorer",
    "os" => "other",
    "description" => ""
  ),
  "ES.NET Crawler/2.0 (http://search.innerprise.net/)" => array(
    "robot" => "esnet",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Snoopy v1.2" => array(
    "robot" => "snoopy",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Opera/9.02 (Windows NT 5.1; U; ru)" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windowsxp",
    "description" => ""
  ),
  "msnbot/1.0 (+http://search.msn.com/msnbot.htm)" => array(
    "robot" => "msnbot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" => array(
    "robot" => "google",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "macppc",
    "description" => "Internet Explorer 5.2.3 on Mac OS"
  ),
  "Mozilla/4.0 (compatible; MSIE 5.0; SunOS 5.9 sun4u; X11)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "sun",
    "description" => "Internet Explorer 5.0 on SunOS"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows2003",
    "description" => "Internet Explorer 6.0 on Windows Server 2003 SP1 with .NET Framework 1.1 installed"
  ),
  "Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows2k",
    "description" => "Internet Explorer 5.5 on Windows 2000"
  ),
  "Microsoft Internet Explorer/4.0b1 (Windows 95)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows95",
    "description" => "Internet Explorer 1.0 on Windows 95"
  ),
  "Mozilla/1.22 (compatible; MSIE 2.0; Windows 95)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows95",
    "description" => "Internet Explorer 2.0 on Windows 95"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windowsxp",
    "description" => "Internet Explorer 6.0 on Windows XP SP 2"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windowsxp",
    "description" => "Internet Explorer 6.0 on Windows XP SP 2 with .NET Framework 1.1 installed"
  ),
  "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windowsxp",
    "description" => "Internet Explorer 7.0 beta on Windows XP"
  ),
  "Mozilla/4.0 (compatible; MSIE 7.0b; Win32)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows",
    "description" => "Internet Explorer 7.0 beta 1 on Windows XP"
  ),
  "Mozilla/4.0 (compatible; MSIE 5.17; Mac_PowerPC)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "macppc",
    "description" => "Internet Explorer 5.1.7 on Mac OS 9"
  ),
  "Mozilla/1.22 (compatible; MSIE 1.5; Windows NT)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windowsnt",
    "description" => "Internet Explorer 1.5 on Windows NT"
  ),
  "Mozilla/2.0 (compatible; MSIE 3.01; Windows 98)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows98",
    "description" => "Internet Explorer 3.01 on Windows 98"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; MSN 2.5; Windows 98)" => array(
    "robot" => "other",
    "browser" => "msn",
    "os" => "windows98",
    "description" => "Internet Explorer 6.0 in MSN 2.5 on Windows 98"
  ),
  "Mozilla/4.61 [en] (X11; U; ) - BrowseX (2.0.0 Windows)" => array(
    "robot" => "other",
    "browser" => "browsex",
    "os" => "windows",
    "description" => ""
  ),
  "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.0.1) Gecko/20060118 Camino/1.0b2+" => array(
    "robot" => "other",
    "browser" => "camino",
    "os" => "macosx",
    "description" => "Camino nightly build on Mac OS X"
  ),
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.5b) Gecko/20030917 Camino/0.7+" => array(
    "robot" => "other",
    "browser" => "camino",
    "os" => "macosx",
    "description" => "Camino (formerly Chimera) nightly build on Mac OS X"
  ),
  "Mozilla/2.0 compatible; Check&Get 1.14 (Windows NT)" => array(
    "robot" => "other",
    "browser" => "checkandget",
    "os" => "windowsnt",
    "description" => "Check&Get Version 1.14 on NT 4.0"
  ),
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US; rv:1.0.1) Gecko/20021104 Chimera/0.6"=> array(
    "robot" => "other",
    "browser" => "chimera",
    "os" => "macosx",
    "description" => "Chimera nightly build on Mac OS X"
  ),
  "Chimera/2.0alpha" => array(
    "robot" => "other",
    "browser" => "chimera",
    "os" => "other",
    "description" => "Chimera on Mac OS X"
  ),
  "Contiki/1.0 (Commodore 64; http://dunkels.com/adam/contiki/)" => array(
    "robot" => "other",
    "browser" => "contiki",
    "os" => "c64",
    "description" => "Contiki on Commodore 64"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Crazy Browser 1.0.5)" => array(
    "robot" => "other",
    "browser" => "crazybrowser",
    "os" => "windowsxp",
    "description" => "Crazy Browser 1.0.5 on Windows XP"
  ),
  "curl/7.7.2 (powerpc-apple-darwin6.0) libcurl 7.7.2 (OpenSSL 0.9.6b)" => array(
    "robot" => "other",
    "browser" => "curl",
    "os" => "darwin",
    "description" => "cURL 7.10.5 on Mac OS X 10.2.6 with Darwin kernel 6.6"
  ),
  "\"CIS TE/1.0\" - Cute FTP Pro 3.3" => array(
    "robot" => "other",
    "browser" => "cuteftp",
    "os" => "other",
    "description" => "Cute FTP 3.3"
  ),
  "Democracy/0.8.1 (http://www.participatoryculture.org)" => array(
    "robot" => "other",
    "browser" => "democracy",
    "os" => "other",
    "description" => "Democracy TV Viewer"
  ),
  "Dillo/0.8.5-i18n-misc" => array(
    "robot" => "other",
    "browser" => "dillo",
    "os" => "other",
    "description" => "Dillo on a DSL linux distro with QEMU",
  ),
  "Dillo/0.8.5-pre" => array(
    "robot" => "other",
    "browser" => "dillo",
    "os" => "other",
    "description" => "",
  ),
  "Dillo/0.8.3" => array(
    "robot" => "other",
    "browser" => "dillo",
    "os" => "other",
    "description" => "Dillo on Mandrake 10.1 with kernel 2.6.7",
  ),
  "Dillo/0.8.2" => array(
    "robot" => "other",
    "browser" => "dillo",
    "os" => "other",
    "description" => "Dillo under NetBSD",
  ),
  "Dillo/0.6.6" => array(
    "robot" => "other",
    "browser" => "dillo",
    "os" => "other",
    "description" => "",
  ),
  "DivX Player 2.0" => array(
    "robot" => "other",
    "browser" => "divx",
    "os" => "other",
    "description" => ""
  ),
  "DocZilla/1.0 (Windows; U; WinNT4.0; en-US; rv:1.0.0) Gecko/20020804" => array(
    "robot" => "other",
    "browser" => "doczilla",
    "os" => "windowsnt",
    "description" => "DocZilla 1.0 RC1 on Windows NT"
  ),
  "edbrowse/2.2.10" => array(
    "robot" => "other",
    "browser" => "edbrowse",
    "os" => "other",
    "description" => ""
  ),
  "ELinks/0.10.4-7ubuntu1-debian (textmode; Linux 2.6.12-10-k7-smp i686; 80x24-2)" => array(
    "robot" => "other",
    "browser" => "elinks",
    "os" => "ubuntu",
    "description" => "ELinks 0.10.4 on ubuntu oldstable, linux 2.6 k7 SMP, standard terminal"
  ),
  "ELinks/0.10.5 (textmode; CYGWIN_NT-5.0 1.5.18(0.132/4/2) i686; 143x51-2)" => array(
    "robot" => "other",
    "browser" => "elinks",
    "os" => "windows",
    "description" => "Elinks 0.10.5 on Windows 2000 (SP4) using CYGWIN",
  ),
  "ELinks (0.4.2; Linux; )" => array(
    "robot" => "other",
    "browser" => "elinks",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; FunWebProducts; (R1 1.5))" => array(
    "robot" => "other",
    "browser" => "realplayer",
    "os" => "windowsxp",
    "description" => "Internet Explorer 6.0 on Windows XP with Spyware 'FunWebProducts' installed"
  ),
  "SquidClamAV_Redirector 1.8.2" => array(
    "robot" => "other",
    "browser" => "squid",
    "os" => "other",
    "description" => "SCAVR - Squid helper script for scanning download URLs for viruses"
  ),
  "Cafi/1.02 (OSIX; 128-bit)" => array(
    "robot" => "other",
    "browser" => "squid",
    "os" => "other",
    "description" => "Faked user agent by squid proxy"
  ),
  "West Wind Internet Protocols 4.55" => array(
    "robot" => "westwind",
    "browser" => "other",
    "os" => "other",
    "description" => "A library for Visual Fox Pro for webaccess, often used for crappy spiders"
  ),
  "NASA Search 1.0" => array(
    "robot" => "SPAM",
    "browser" => "other",
    "os" => "other",
    "description" => "A spam haverster"
  ),
  "page_verifier http://www.securecomputing.com/goto/pv" => array(
    "robot" => "securecomputing",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "DataCha0s/2.0" => array(
    "robot" => "SPAM",
    "browser" => "other",
    "os" => "other",
    "description" => "robot trying to crash AWStats"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Nitro) Opera 8.50 [ja]" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "other",
    "description" => "8.50 on Nintendo DS (Japanese)"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Nitro) Opera 8.50 [en]" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "other",
    "description" => "8.50 on Nintendo DS (European)"
  ),
  "Accoona-AI-Agent/1.1.2 (aicrawler at accoonabot dot com)" => array(
    "robot" => "accoona",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ia_archiver" => array(
    "robot" => "alexa",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/2.0 (compatible; Ask Jeeves/Teoma)" => array(
    "robot" => "ask",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Baiduspider ( http://www.baidu.com/search/spider.htm)" => array(
    "robot" => "baidu",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "curl/7.13.1 (powerpc-apple-darwin8.0) libcurl/7.13.1 OpenSSL/0.9.7b zlib/1.2.2" => array(
    "robot" => "curl",
    "browser" => "curl",
    "os" => "darwin",
    "description" => ""
  ),
  "Gigabot/2.0" => array(
    "robot" => "gigabot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Googlebot/2.1 (+http://www.google.com/bot.html)" => array(
    "robot" => "google",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Googlebot-Image/1.0" => array(
    "robot" => "google",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; grub-client-1.4.3; Crawl your own stuff with http://grub.org) Mozilla/4.0 (compatible; grub-client-2.3)" => array(
    "robot" => "grub",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/3.0 (Slurp/si; slurp@inktomi.com; http://www.inktomi.com/slurp.html)" => array(
    "robot" => "inktomi",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "OmniExplorer_Bot/6.70 (+http://www.omni-explorer.com) WorldIndexer" => array(
    "robot" => "omni",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Scooter-3.2.EX" => array(
    "robot" => "altavista",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "W3C_Validator/1.432.2.10" => array(
    "robot" => "validator",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Jigsaw/2.2.5 W3C_CSS_Validator_JFouffa/2.0" => array(
    "robot" => "csscheck",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Wget/1.9" => array(
    "robot" => "wget",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Yahoo! Slurp;http://help.yahoo.com/help/us/ysearch/slurp)" => array(
    "robot" => "yahoo",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Yahoo! Slurp China; http://misc.yahoo.com.cn/help.html)" => array(
    "robot" => "yahoo",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (ReactOS; U; ReactOS 0.3; en-US; rv:1.8) Gecko/20051107 Firefox/1.5" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "reactos",
    "description" => ""
  ),
  "Opera/9.02 (Windows 98; U; en)" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windows98",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; FreeBSD i386; en-US; rv:1.8.1) Gecko/20061101 Firefox/2.0" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "freebsd",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.7) Gecko/20060405 SeaMonkey/1.0.5" => array(
    "robot" => "other",
    "browser" => "seamonkey",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; pl) Opera 9.02" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows NT 5.1; U; pl) Opera 9.02" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Opera/9.02 (Windows NT 5.1; U; pl)" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.8.0.6) Gecko/20060728 Firefox/1.5.0.6 (Debian-1.5.dfsg+1.5.0.6-2)" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "debian",
    "description" => ""
  ),
  "Opera/8.01 (J2ME/MIDP; Opera Mini/3.0.6307/1528; en; U; ssr)" => array(
    "robot" => "other",
    "browser" => "operamini",
    "os" => "java",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.5 (like Gecko) (Kubuntu)" => array(
    "robot" => "other",
    "browser" => "konqueror",
    "os" => "ubuntu",
    "description" => ""
  ),
  "Sony-HTTPClient/1.0 [PS3 test]" => array(
    "robot" => "httpclient",
    "browser" => "other",
    "os" => "other",
    "description" => "PS3 in testing"
  ),
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.7) Gecko/20060917 K-Ninja/2.0.4" => array(
    "robot" => "other",
    "browser" => "k-ninja",
    "os" => "windowsxp",
    "description" => ""
  ),
  "qtver=7.5.0;os=Windows 98" => array(
    "robot" => "other",
    "browser" => "quicktime",
    "os" => "windows98",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Charlotte/1.0b; http://www.betaspider.com/)" => array(
    "robot" => "charlotte",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux ppc; en-US; rv:1.8.1) Gecko/20061024 Iceweasel/2.0 (Debian-2.0+dfsg-1)" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "debian",
    "description" => "Unbranded Firefox 2.0, GNU compatible, on Debian Linux"
  ),
  "Mozilla/5.0 (SymbianOS/9.1; U; [en-us]; Series60/3.0 NokiaE61/2.0618.06.05) AppleWebKit/413 (KHTML, like Gecko) Safari/413" => array(
    "robot" => "other",
    "browser" => "safari",
    "os" => "symbian",
    "description" => ""
  ),
  "Szukacz/1.5 (robot; www.szukacz.pl/html/jak_dziala_robot.html; info@szukacz.pl)" => array(
    "robot" => "szukacz",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0; Girafabot; girafabot at girafa dot com; http://www.girafa.com)" => array(
    "robot" => "girafabot",
    "browser" => "explorer",
    "os" => "windowsnt",
    "description" => ""
  ),
  "QuickTime (qtver=6.3;os=Windows NT 5.1Service Pack 1)" => array(
    "robot" => "other",
    "browser" => "quicktime",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.5) Gecko/20060727 Galeon/2.0.1 Firefox/1.5.0.5" => array(
    "robot" => "other",
    "browser" => "galeon",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.4) Gecko/20060608 Firefox/1.5.0.4 Flock/0.7.0.16.2" => array(
    "robot" => "other",
    "browser" => "flock",
    "os" => "linux",
    "description" => ""
  ),
  "Lynx/2.8.5rel.1 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.7i" => array(
    "robot" => "other",
    "browser" => "lynx",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.7 [en] (X11; I; SunOS 5.8 i86pc)" => array(
    "robot" => "other",
    "browser" => "netscape",
    "os" => "sun",
    "description" => ""
  ),
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.8.0.1) Gecko/20060214 Camino/1.0" => array(
    "robot" => "other",
    "browser" => "camino",
    "os" => "macosx",
    "description" => ""
  ),
  "Links (2.1pre20; Linux 2.6.16-gentoo-r11 x86_64; x)" => array(
    "robot" => "other",
    "browser" => "links",
    "os" => "gentoo",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.5) Gecko/20031007 Firebird/0.7" => array(
    "robot" => "other",
    "browser" => "firebird",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.3a) Gecko/20021207 Phoenix/0.5" => array(
    "robot" => "other",
    "browser" => "phoenix",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/5.0 (BeOS; U; BeOS 5 PE Max Edition v3b1 BePC; pl-PL; rv:1.3) Firebird/0.8" => array(
    "robot" => "other",
    "browser" => "firebird",
    "os" => "beos",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; OpenBSD i386; en-US; rv:1.8.0.4) Gecko/20060717 Firefox/1.5.0.4" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "openbsd",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.8.0.5) Gecko/20060706 K-Meleon/1.0" => array(
    "robot" => "other",
    "browser" => "k-meleon",
    "os" => "windows98",
    "description" => ""
  ),
  "SonyEricssonV630iv/R1CE Browser/NetFront/3.3Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "mobile",
    "description" => ""
  ),
  "Space Bison/0.02 [fu] (Win67; X; SK)" => array(
    "robot" => "other",
    "browser" => "proxomitron",
    "os" => "other",
    "description" => ""
  ),
  "Space Bison/0.02 [fu] (Mindows; X; SK; en-GB)" => array(
    "robot" => "other",
    "browser" => "proxomitron",
    "os" => "other",
    "description" => ""
  ),
  "MOT-V300/0B.09.2ER MIB/2.2 Profile/MIDP-2.0 Configuration/CLDC-1.0" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "Nexus (W11; Multics GE-645; U; en)" => array(
    "robot" => "other",
    "browser" => "nexus",
    "os" => "NexT",
    "description" => "The first browser ... written by Tim Berners-Lee"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Maxthon; .NET CLR 2.0.50727)" => array(
    "robot" => "other",
    "browser" => "maxthon",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.0.6) Gecko/20060728 SUSE/1.5.0.6-0.1 Firefox/1.5.0.6" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "suse",
    "description" => ""
  ),
  "SoftBank/1.0/E02NK/NKJ001 Series60/3.0 NokiaE61/2.0618.06.05 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; BecomeBot/3.0; MSIE 6.0 compatible; +http://www.become.com/site_owners.html)" => array(
    "robot" => "become",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.5) Gecko/20060731 Ubuntu/dapper-security Turboyak/0.9.0.3" => array(
    "robot" => "other",
    "browser" => "mozilla",
    "os" => "ubuntu",
    "description" => ""
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.8) Gecko/20050524 Fedora/1.0.4-4 Firefox/1.0.4" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "fedora",
    "description" => ""
  ),
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US) AppleWebKit/125.4 (KHTML, like Gecko, Safari) OmniWeb/v563.15" => array(
    "robot" => "other",
    "browser" => "omniweb",
    "os" => "macosx",
    "description" => ""
  ),
  "Mozilla/5.0 (Nintendo DS v4; U; M3 Adapter CF + PassMe2; en-US; rv:1.8.0.6 ) Gecko/20060728 Firefox/1.5.0.6 (firefox.gba.ds)" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "nintendods",
    "description" => ""
  ),
  "Emacs-W3/4.0pre.46 URL/p4.0pre.46 (i686-pc-linux; X11)" => array(
    "robot" => "other",
    "browser" => "emacs",
    "os" => "linux",
    "description" => "Emacs/W3 on X-windows Linux"
  ),
  "Mozilla/1.22 (compatible; MSIE 5.01; PalmOS 3.0) EudoraWeb 2.1" => array(
    "robot" => "other",
    "browser" => "eudoraweb",
    "os" => "palm",
    "description" => "Eudora 2.1 under PalmOS 3.0 on Treo 180"
  ),
  "Microsoft Pocket Internet Explorer/0.6" => array(
    "robot" => "other",
    "browser" => "iexplorepocket",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/1.1 (compatible; MSPIE 2.0; Windows CE)" => array(
    "robot" => "other",
    "browser" => "iexplorepocket",
    "os" => "windowsce",
    "description" => ""
  ),
  "iSiloX/4.01 Windows/32" => array(
    "robot" => "other",
    "browser" => "isilox",
    "os" => "windows",
    "description" => "iSiloX HTML Reader on Pockect PC"
  ),
  "Mozilla/5.0 (compatible; Konqueror/3.1-rc3; i686 Linux; 20020515)" => array(
    "robot" => "websense",
    "browser" => "konqueror",
    "os" => "linux",
    "description" => "Websense with fakes user agent as Knoqueror 3.1 RC 3 on Linux"
  ),
  "Mozilla/5.0 (compatible; Konqueror/3.1; Linux 2.4.22-10mdk; X11; i686; fr, fr_FR)" => array(
    "robot" => "other",
    "browser" => "konqueror",
    "os" => "linux",
    "description" => "Knoqueror 3.1 on Mandrake Linux (french)"
  ),
  "NSPlayer/9.0.0.2980 WMFSDK/9.0" => array(
    "robot" => "other",
    "browser" => "mediaplayer",
    "os" => "windows",
    "description" => "Media Player 9"
  ),
  "Mozilla/5.0 (Windows; U; Windows CE 5.1; rv:1.8.1a3) Gecko/20060610 Minimo/0.016" => array(
    "robot" => "other",
    "browser" => "minimo",
    "os" => "windowsce",
    "description" => "Minimo under Windows CE Mobile 5.x on Pocket PC"
  ),
  "Mozilla/5.0 (Windows; U; Windows CE 4.21; rv:1.8b4) Gecko/20050720 Minimo/0.007" => array(
    "robot" => "other",
    "browser" => "minimo",
    "os" => "windowsce",
    "description" => "Minimo under Windows Mobile 2003"
  ),
  "Media Player Classic" => array(
    "robot" => "other",
    "browser" => "mpc",
    "os" => "other",
    "description" => ""
  ),
  "MPlayer/1.0rc1-4.1.1" => array(
    "robot" => "other",
    "browser" => "mplayer",
    "os" => "other",
    "description" => "MPlayer 1.0 RC1 build with GCC 4.1.1"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; MSN 6.1; MSNbMSFT; MSNmen-us; MSNc00)" => array(
    "robot" => "other",
    "browser" => "msn",
    "os" => "windowsxp",
    "description" => "MSN Explorer 6.1"
  ),
  "OutfoxBot/0.5 (for internet experiments; http://; outfoxbot@gmail.com)" => array(
    "robot" => "outfox",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "OutfoxBot/0.3 (For internet experiments; http://; outfox.agent@gmail.com)" => array(
    "robot" => "outfox",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "OutfoxBot/0.1 (For internet experiments; http://www.outfox.com; outfoxbot@gmail.com)" => array(
    "robot" => "outfox",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "iaskspider2 iask@staff.sina.com.cn" => array(
    "robot" => "iask",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "gnome-vfs/2.16.2 neon/0.25.4" => array(
    "robot" => "other",
    "browser" => "nautilus",
    "os" => "other",
    "description" => "Nautilus from 2.16.2"
  ),
  "Mozilla/4.08 (Windows; Mobile Content Viewer/1.0) NetFront/3.2" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "windowsce",
    "description" => "NetFront simulator on XP SP2"
  ),
  "Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "playstation",
    "description" => "NetFront on Japanese PS 2"
  ),
  "Mozilla/4.0 (PDA; PalmOS/sony/model crdb/Revision:1.1.36(de)) NetFront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "palm",
    "description" => "NetFront on CLie(Palm 5.0)"
  ),
  "Mozilla/4.0 (PDA; PalmOS/sony/model prmr/Revision:1.1.54 (en)) NetFront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "palm",
    "description" => "NetFront on CLie(Palm 5.0) ?"
  ),
  "Mozilla/4.0 (PDA; Windows CE/0.9.3) NetFront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "windowsce",
    "description" => "NetFront under Widows CE 2003"
  ),
  "Mozilla/4.0 (PDA; Windows CE/1.0.1) NetFront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "windowsce",
    "description" => "NetFront under Widows CE 2003"
  ),
  "Mozilla/4.0 (PDA; SL-C750/1.0,Embedix/Qtopia/1.3.0) NetFront/3.0 Zaurus C750" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "windowsce",
    "description" => "NetFront Sharp Zuarus Linux based SL-C750"
  ),
  "OPWV-SDK UP.Browser/7.0.2.3.119 (GUI) MMP/2.0 Push/PO" => array(
    "robot" => "other",
    "browser" => "openwave",
    "os" => "windowsxp",
    "description" => "OpenWave V7 simulator under Windows XP SP 2 on x86"
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/Palm-D050; Blazer/4.3) 16;320x320)" => array(
    "robot" => "other",
    "browser" => "blazer",
    "os" => "palm",
    "description" => "Palm Blazer 4.3 on Palm TX PDA"
  ),
  "Mozilla/4.76 [en] (PalmOS; U; WebPro/3.0; Palm-Arz1)" => array(
    "robot" => "other",
    "browser" => "palmsource",
    "os" => "palm",
    "description" => "?"
  ),
  "Mozilla/4.76 (compatible; MSIE 6.0; U; Windows 95; PalmSource; PalmOS; WebPro; Tungsten Proxyless 1.1 320x320x16)" => array(
    "robot" => "other",
    "browser" => "palmsource",
    "os" => "palm",
    "description" => "Palm browser 2.0.1.1 on a Tungsten C"
  ),
  "Mozilla/4.0 (compatible;MSIE 6.0;Windows95;PalmSource) Netfront/3.0;8;320x320" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "palm",
    "description" => "PalmSource Web Browser 2.0 for OS5.1 (with depth and resolution)"
  ),
  "Mozilla/4.0 (compatible;MSIE 6.0;Windows95;PalmSource) Netfront/3.0" => array(
    "robot" => "other",
    "browser" => "netfront",
    "os" => "palm",
    "description" => "PalmSource Web Browser 2.0 for OS5.1"
  ),
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; ca; rv:1.5) Gecko/20031007\nPlucker/Py-1.4\nMozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)" => array(
    "robot" => "other",
    "browser" => "plucker",
    "os" => "windowsxp",
    "description" => "OpenWave V7 simulator under Windows XP SP 2 on x86"
  ),
  "Mozilla/3.0 (compatible; PHPEd Version 3.2 (Build 3217))" => array(
    "robot" => "other",
    "browser" => "phped",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (PSP (PlayStation Portable); 2.00)" => array(
    "robot" => "other",
    "browser" => "psp",
    "os" => "psp",
    "description" => "PSP with firmware 2.00"
  ),
// Not sure if this is correct
//  "Xbox/2.0.2858.0 UPnP/1.0 Xbox/2.0.2858.0" => array(
//    "robot" => "other",
//    "browser" => "xbox",
//    "os" => "xbox",
//    "description" => ""
//  ),
  "Mozilla/5.0 (X11; U; OpenVMS AlphaServer_ES40; en-US; rv:1.4) Gecko/20030826 SWB/V1.4 (HP)" => array(
    "robot" => "other",
    "browser" => "securewebbrowser",
    "os" => "openvms",
    "description" => ""
  ),
  "WinampMPEG/5.0" => array(
    "robot" => "other",
    "browser" => "winamp",
    "os" => "windows",
    "description" => "Winamp 5",
  ),
  "Nullsoft Winamp3 version 3.0d build 488" => array(
    "robot" => "other",
    "browser" => "winamp",
    "os" => "windows",
    "description" => "Winamp 3"
  ),
  "Xiino/1.0.9E [en] (v. 4.1; 153x130; g4)" => array(
    "robot" => "other",
    "browser" => "xiino",
    "os" => "other",
    "description" => "Xiino 1.0.9E under PalmOS 4.1"
  ),
  "xine/1.1.3" => array(
    "robot" => "other",
    "browser" => "xine",
    "os" => "other",
    "description" => "xine-lib 1.1.3"
  ),
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.8) Gecko/20050511" => array(
    "robot" => "other",
    "browser" => "mozilla",
    "os" => "linux",
    "description" => "Mozilla 1.7.9 on Linux (american english)"
  ),
  "Mozilla/5.0 (X11; U; Linux i686; cs-CZ; rv:1.7.12) Gecko/20050929" => array(
    "robot" => "other",
    "browser" => "mozilla",
    "os" => "linux",
    "description" => "Mozilla 1.7.12 on Gentoo Linux"
  ),
  // Site: http://www.pgts.com.au/pgtsj/pgtsj0208h.html
  "abot/0.1 (abot; http://www.abot.com; abot@abot.com)" => array(
    "robot" => "abot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Ace Explorer" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "other",
    "description" => ""
  ),
  "ACS-NF/3.0 NEC-c616/001.00" => array(
    "robot" => "TODO",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ACS-NF/3.0 NEC-e616/001.01 (www.proxomitron.de)" => array(
    "robot" => "TODO",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ActiveBookmark 1.0" => array(
    "robot" => "active",
    "browser" => "other",
    "os" => "other",
    "description" => "LibMaster.com Active Bookmark HTML page creator"
  ),
  "Mozilla/5.0 (compatible; AI)" => array(
    "robot" => "ai",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "AIM" => array(
    "robot" => "aim",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "AIM/30 (Mozilla 1.24b; Windows; I; 32-bit)" => array(
    "robot" => "aim",
    "browser" => "other",
    "os" => "windows",
    "description" => ""
  ),
  "aipbot/1.0 (aipbot; http://www.aipbot.com; aipbot@aipbot.com)" => array(
    "robot" => "aipbot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Alizee iPod 2005 (Beta; Mac OS X)" => array(
    "robot" => "other",
    "browser" => "other",
    "os" => "macosx",
    "description" => ""
  ),
  "Mozilla/5.5 (compatible; alpha/06; AmigaOS 1337)" => array(
    "robot" => "other",
    "browser" => "alpha06",
    "os" => "amiga",
    "description" => ""
  ),
  "ALPHA/06_(Win98)" => array(
    "robot" => "other",
    "browser" => "alpha06",
    "os" => "windows98",
    "description" => ""
  ),
  "alpha 06" => array(
    "robot" => "other",
    "browser" => "alpha06",
    "os" => "other",
    "description" => ""
  ),
  "amaya/8.3 libwww/5.4.0" => array(
    "robot" => "other",
    "browser" => "amaya",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/3.01 (compatible; AmigaVoyager/2.95; AmigaOS/MC680x0)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "AmigaVoyager/2.95 (compatible; MC680x0; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/4.6 (compatible; AmigaVoyager; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; Voyager; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "AmigaVoyager/3.4.4 (MorphOS/PPC native)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "morph",
    "description" => "AmigaVoyager 3.4.4 on PowerPC"
  ),
  "Arexx (AmigaVoyager/2.95; AmigaOS/MC680x0)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "AmigaVoyager/3.3.122 (AmigaOS/PPC)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "AmigaOS AmigaVoyager" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Amiga Voyager" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; AnsearchBot/1.0; +http://www.ansearch.com.au/)" => array(
    "robot" => "ansearch",
    "browser" => "mozilla",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; ANTFresco 2.20; RISC OS 3.11)" => array(
    "robot" => "other",
    "browser" => "ant",
    "os" => "risc",
    "description" => ""
  ),
  "Mozilla/3.04 (compatible; NCBrowser/2.35 (1.45.2.1); ANTFresco/2.17; RISC OS-NC 5.13 Laz1UK1802)" => array(
    "robot" => "other",
    "browser" => "ant",
    "os" => "risc",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; ANTFresco 1.20; RISC OS 3.11)" => array(
    "robot" => "other",
    "browser" => "ant",
    "os" => "risc",
    "description" => ""
  ),
  "Mozilla/3.04 (compatible; ANTFresco/2.13; RISC OS 4.02)" => array(
    "robot" => "other",
    "browser" => "ant",
    "os" => "risc",
    "description" => ""
  ),
  "AOL/8.0 (Lindows 2003)" => array(
    "robot" => "other",
    "browser" => "aol",
    "os" => "other",
    "description" => ""
  ),
  "AOLserver-Tcl/3.5.6" => array(
    "robot" => "aol",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "AOL 8.0 (compatible; AOL 8.0; DOS; .NET CLR 1.1.4322)" => array(
    "robot" => "other",
    "browser" => "aol",
    "os" => "other",
    "description" => ""
  ),
  "aolbrowser/1.1 InterCon-Web-Library/1.2" => array(
    "robot" => "other",
    "browser" => "aol",
    "os" => "other",
    "description" => ""
  ),
  "ArachnetAgent 2.3/4.78 (TuringOS; Turing Machine; 0.0)" => array(
    "robot" => "TODO",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Arexx (compatible; MSIE 6.0; AmigaOS5.0) IBrowse 4.0" => array(
    "robot" => "other",
    "browser" => "ibrowse",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx (compatible; AmigaVoyager/2.95; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx (AmigaVoyager/2.95; AmigaOS/MC680x0; Mod-X by Pe)" => array(
    "robot" => "other",
    "browser" => "voyager",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; ARexx; AmigaOS; Avant Browser [avantbrowser.com]; .NET CLR 1.1.4322; .NET CLR 1.0.3705)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx ( ; ; AmigaOS 3.0)" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx (compatible; MSIE 6.0; AmigaOS5.0)" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "ARexx (compatible; ARexx; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 5.5; arexx)" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx (compatible; AmigaVoyager/2.95; AmigaOS" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "Arexx (compatible; AmigaVoyager/2.95; AmigaOS" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; ARexx; AmigaOS)" => array(
    "robot" => "other",
    "browser" => "TODO",
    "os" => "amiga",
    "description" => ""
  ),
  "ArtfaceBot (compatible; MSIE 6.0; Mozilla/4.0; Windows NT 5.1;)" => array(
    "robot" => "artface",
    "browser" => "explorer",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://sp.ask.com/docs/about/tech_crawling.html)" => array(
    "robot" => "ask",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Astra/1.0 (WinNT; I)" => array(
    "robot" => "astra",
    "browser" => "other",
    "os" => "windowsnt",
    "description" => ""
  ),
  "Astra/2.0 (WinNT; I)" => array(
    "robot" => "astra",
    "browser" => "other",
    "os" => "windowsnt",
    "description" => ""
  ),
  "Atari/2600b (compatible; 2port; Wood Grain)" => array(
    "robot" => "other",
    "browser" => "atari",
    "os" => "atari",
    "description" => ""
  ),
  "Atari/2600 (GalaxianOS; U; en-US) cartridge/$29.95" => array(
    "robot" => "other",
    "browser" => "atari",
    "os" => "atari",
    "description" => ""
  ),
  "Auto-Proxy Downloader" => array(
    "robot" => "TODO",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Avant Browser [avantbrowser.com])" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "windows98",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Avant Browser [avantbrowser.com]; .NET CLR 1.0.3705)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "windows2k",
    "description" => ""
  ),
  "Avant Browser/1.2.789rel1 (http://www.avantbrowser.com)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Avant Browser [avantbrowser.com]; .NET CLR 1.0.3705)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Avant Browser [avantbrowser.com])" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Avant Browser [avantbrowser.com])" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "windows2k",
    "description" => ""
  ),
  "Advanced Browser (http://www.avantbrowser.com)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "other",
    "description" => ""
  ),
  "Avant Browser (http://www.avantbrowser.com)" => array(
    "robot" => "other",
    "browser" => "avantbrowser",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9a1) Gecko/20061204 GranParadiso/3.0a1" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows;) NimbleCrawler 2.0.1 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com" => array(
    "robot" => "nimble",
    "browser" => "mozilla",
    "os" => "windows",
    "description" => ""
  ),
  "TurnitinBot/2.1 (http://www.turnitin.com/robot/crawlerinfo.html)" => array(
    "robot" => "turnitin",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ExaBotTest/3.0" => array(
    "robot" => "exabot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows; U; Win 9x 4.90) Gecko/20020502 CS 2000 7.0/7.0" => array(
    "robot" => "other",
    "browser" => "compuserve",
    "os" => "windows",
    "description" => "CompuServe packaged version of Mozilla browser (indicated by the the CS 2000)"
  ),
  "msnbot-media/1.0 (+http://search.msn.com/msnbot.htm)" => array(
    "robot" => "mslivebot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "IlseBot/1.0" => array(
    "robot" => "ilse",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Verzamelgids/2.2 (http://www.verzamelgids.nl)" => array(
    "robot" => "verzamelgids",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Windows-Media-Player/11.0.5721.5145" => array(
    "robot" => "other",
    "browser" => "mediaplayer",
    "os" => "windows",
    "description" => "Windows Media Player 11"
  ),
  "KDDI-SA31 UP.Browser/6.2.0.7.3.129 (GUI) MMP/2.0" => array(
    "robot" => "other",
    "browser" => "upbrowser",
    "os" => "mobile",
    "description" => ""
  ),
  "SIE-M65/50 UP.Browser/7.0.2.2.d.3(GUI) MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 (Siemens M65 phone)" => array(
    "robot" => "other",
    "browser" => "upbrowser",
    "os" => "mobile",
    "description" => ""
  ),
  "Opera/8.01 (J2ME/MIDP; Opera Mini/1.2.2960; en; U; ssr)" => array(
    "robot" => "other",
    "browser" => "operamini",
    "os" => "java",
    "description" => "Opera Mini (english, U (strong security (USA))"
  ),
  "Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4719; en; U; ssr)" => array(
    "robot" => "other",
    "browser" => "operamini",
    "os" => "java",
    "description" => "2.0.4719 on J2ME on the Sony Ericsson W800i"
  ),
  "Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4509/1316; fi; U; ssr)" => array(
    "robot" => "other",
    "browser" => "operamini",
    "os" => "java",
    "description" => "2.0.4509 on J2ME on the Motorola RAZR V3"
  ),
  "Mozilla/5.0 (compatible; Konqueror/3.2; Linux 2.6.2) (KHTML, like Gecko)" => array(
    "robot" => "other",
    "browser" => "konqueror",
    "os" => "linux",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.04 [de]" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "windowsxp",
    "description" => ""
  ),
  "Mozilla/5.0 (Windows; U; Windows NT 5.0; de-DE; rv:1.6) Gecko/20040206 Firefox/1.0.1" => array(
    "robot" => "other",
    "browser" => "firefox",
    "os" => "windows2k",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)" => array(
    "robot" => "other",
    "browser" => "explorer",
    "os" => "windows2k",
    "description" => ""
  ),
  "Lynx/2.8.4rel.1 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.6c" => array(
    "robot" => "other",
    "browser" => "lynx",
    "os" => "other",
    "description" => ""
  ),
  "LG/KU800/v1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "MOT-E1000/80.28.08I MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "NokiaN73-1/2.0626.0.0.2 S60/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "SHARP-TQ-GX30i/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 UP.Browser/6.2.2.6.c.1.104 (GUI)" => array(
    "robot" => "other",
    "browser" => "upbrowser",
    "os" => "mobile",
    "description" => ""
  ),
  "SIE-SL65/25 UP.Browser/7.0.0.1.c.3 (GUI) MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "upbrowser",
    "os" => "mobile",
    "description" => ""
  ),
  "SonyEricssonT68/R201A" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "SonyEricssonP910i/R2A SEMC-Browser/Symbian/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.0" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "symbian",
    "description" => ""
  ),
  "Thunderbird 1.5.0.7 (X11/20060927)" => array(
    "robot" => "thunderbird",
    "browser" => "other",
    "os" => "other",
    "description" => "A thunderbird running on X11/Linux"
  ),
  "Mutt/1.5.13 (2006-08-11)" => array(
    "robot" => "mutt",
    "browser" => "other",
    "os" => "other",
    "description" => "A mutt mail client"
  ),
  "Microsoft Outlook IMO, Build 9.0.6604 (9.0.2911.0)" => array(
    "robot" => "outlook",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Miva (AlgoFeedback@miva.com)" => array(
    "robot" => "miva",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "DoCoMo/1.0/P502i/c10 (Google CHTML Proxy/1.0)" => array(
    "robot" => "goo",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "DoCoMo/J-PHONE/KDDI/1.0 (CROOZ)" => array(
    "robot" => "goo",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Blogslive (info@blogslive.com)" => array(
    "robot" => "blogslife",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "MyApp/0.1 libwww-perl/5.76" => array(
    "robot" => "libwww",
    "browser" => "other",
    "os" => "other",
    "description" => "MyAPP/0.1 is default for the libwww-perl robot"
  ),
  "Slider/2.0" => array(
    "robot" => "slider",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "rssImagesBot/0.1 (+http://herbert.groot.jebbink.nl/?app=rssImages)" => array(
    "robot" => "rssimages",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "traycebot/0.2-alpha [http://trayce.com/traycebot.html]" => array(
    "robot" => "trayce",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "unido-bot, http://mobicom.cs.uni-dortmund.de/bot.html" => array(
    "robot" => "unido",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "eBay Relevance Ad Crawler powered by contentDetection (www.mindup.de)" => array(
    "robot" => "ebay",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Nokia6820/2.0 (4.83) Profile/MIDP-1.0 Configuration/CLDC-1.0 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)" => array(
    "robot" => "googlemobile",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "R600 1.0 WAP1.2.1 (Google WAP Proxy/1.0)" => array(
    "robot" => "googlewap",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "BlackBerry7290/4.0.2 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "Nokia3200/1.0 () Profile/MIDP-1.0 Configuration/CLDC-1.0 (Google WAP Proxy/1.0)" => array(
    "robot" => "googlewap",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "Nokia6610I/1.0 (3.10) Profile/MIDP-1.0 Configuration/CLDC-1.0 UP.Link/1.1 (Google WAP Proxy/1.0)" => array(
    "robot" => "googlewap",
    "browser" => "wap",
    "os" => "mobile",
    "description" => ""
  ),
  "Nokia6630/1.0 (3.45.113) SymbianOS/8.0 Series60/2.6 Profile/MIDP-2.0 Configuration/CLDC-1.1" => array(
    "robot" => "other",
    "browser" => "wap",
    "os" => "symbian",
    "description" => ""
  ),
  "UP.Browser/6.1.0.1.140 (Google CHTML Proxy/1.0)" => array(
    "robot" => "googlewap",
    "browser" => "upbrowser",
    "os" => "mobile",
    "description" => ""
  ),
  "FindLinks (http://wortschatz.uni-leipzig.de/findlinks/)" => array(
    "robot" => "findlinks",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "DomaindateiSpider/1.0 (http://www.domaindatei.de/spider.html * Deine Domain ist gespidert worden!!!" => array(
    "robot" => "domaindatei",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Microsoft Data Access Internet Publishing Provider Protocol Discovery" => array(
    "robot" => "TODO",
    "browser" => "TODO",
    "os" => "TODO",
    "description" => ""
  ),
  "Forex Trading Network Organization info@netforex.org" => array(
    "robot" => "TODO",
    "browser" => "TODO",
    "os" => "TODO",
    "description" => ""
  ),
  "YodaoBot/1.0 (http://www.yodao.com/help/webmaster/spider/; )" => array(
    "robot" => "yodao",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.0 (compatible; DepSpid/5.07; +http://about.depspid.net)" => array(
    "robot" => "depspid",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/4.7 (compatible; OffByOne; Windows 2000)" => array(
    "robot" => "other",
    "browser" => "offbyone",
    "os" => "windows2k",
    "description" => "Off-by-One browser on Windows XP (even if it reports to be in 2k)"
  ),
  "Mozilla/4.0 (compatible; MSIE is not me; DAUMOA/1.0.0; DAUM Web Robot; Daum Communications Corp., Korea)" => array(
    "robot" => "daum",
    "browser" => "explorer",
    "os" => "other",
    "description" => "Korean bot with better user agent than before"
  ),
  "Pooodle predictor 1.0" => array(
    "robot" => "TODO",
    "browser" => "TODO",
    "os" => "TODO",
    "description" => ""
  ),
  "GoGuidesBot/0.0.1 (GoGuides Indexing Spider; http://www.goguides.org/spider.html)" => array(
    "robot" => "goguides",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Pingdom GIGRIB v1.1 (http://www.pingdom.com)" => array(
    "robot" => "pingdom",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "FacebookFeedParser/1.0 (UniversalFeedParser/4.1;) +http://facebook.com/" => array(
    "robot" => "facebook",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "SMBot/1.1 (www.specificmedia.com)" => array(
    "robot" => "specificmedia",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Opera/9.00 (Nintendo Wii; U; 1309-9; en)" => array(
    "robot" => "other",
    "browser" => "opera",
    "os" => "wii",
    "description" => ""
  ),
  "Mozilla/3.0 (INGRID/3.0 MT; webcrawler@NOSPAMexperimental.net; http://webmaster.ilse.nl/jsp/webmaster.jsp)" => array(
    "robot" => "ilse",
    "browser" => "netscape",
    "os" => "other",
    "description" => ""
  ),
  "Mozilla/5.0 (compatible; Konqueror/3.1-rc4; i686 Linux; 20020321)" => array(
    "robot" => "websense",
    "browser" => "konqueror",
    "os" => "linux",
    "description" => "Websense with faked user agent"
  ),
  "HouxouCrawler/Nutch-0.8.2-dev (houxou.com's nutch-based crawler which serves special interest on-line communities; http://www.houxou.com/crawler; crawler at houxou dot com)" => array(
    "robot" => "houxou",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "SBIder/SBIder-0.8.2-dev (http://www.sitesell.com/sbider.html)" => array(
    "robot" => "sbider",
    "browser" => "other",
    "os" => "other",
    "description" => "Websense with faked user agent"
  ),
  "bot/1.0 (bot; http://; bot@bot.bot)" => array(
    "robot" => "robot",
    "browser" => "other",
    "os" => "other",
    "description" => "From 71.13.115.117 (71-13-115-117.static.mdsn.wi.charter.com)"
  ),
  "Twiceler www.cuill.com/twiceler/robot.html" => array(
    "robot" => "twiceler",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ASPseek/1.2.11pre" => array(
    "robot" => "aspseek",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Jyxobot/1" => array(
    "robot" => "jyxo",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "MQbot http://metaquerier.cs.uiuc.edu/crawler" => array(
    "robot" => "mqbot",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "ping.blogug.ch aggregator 1.0" => array(
    "robot" => "TODO",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "bloxon/0.1" => array(
    "robot" => "bloxon",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "slug.ch crawl 1.10 (+www.slug.ch)" => array(
    "robot" => "slugch",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
  "Feedfetcher-Google; (+http://www.google.com/feedfetcher.html)" => array(
    "robot" => "googlefeeds",
    "browser" => "other",
    "os" => "other",
    "description" => ""
  ),
)
?>