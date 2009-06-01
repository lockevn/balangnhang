<?php
/* This file is part of BBClone (The PHP web counter on steroids)
 *
 * $Header: /cvs/bbclone/lib/browser.php,v 1.90 2006/12/27 17:01:44 christoph Exp $
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

$browser = array(
  "1X" => array(
    "icon" => "question",
    "title" => "1X",
    "rule" => array(
      "^Science Traveller International 1X[ /]([0-9.]{1,10})" => "\\1",
    ),
    "uri" => "http://jansfreeware.com/jfinternet.htm"
  ),
  "abrowse" => array(
    "icon" => "abrowse",
    "title" => "ABrowse",
    "rule" => array(
      "abrowse[ /\-]([0-9.]{1,10})" => "\\1",
      "^abrowse" => ""
    ),
    "uri" => "http://abrowse.sourceforge.net/"
  ),
  "acorn" => array(
    "icon" => "question",
    "title" => "Acorn Browser",
    "rule" => array(
      "Acorn (Browse|Phoenix)[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.vigay.com/inet/acorn/browse-html.html"
  ),
/* FIXME: unusable user agent
  "act10" => array(
    "icon" => "question",
    "title" => "Act 10",
    "rule" => array(
      "Mozilla/3.0 (compatible)" => ""
    ),
    "uri" => "http://jansfreeware.com/jfinternet.htm",
    Mozilla/3.0 (compatible)" => "Act 10 on Windows"
  ),*/
  "amaya" => array(
    "icon" => "amaya",
    "title" => "Amaya",
    "rule" => array(
      "amaya/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.w3c.org/Amaya/"
  ),
  "ant" => array(
    "icon" => "ant",
    "title" => "ANTFresco",
    "rule" => array(
      "ANTFresco[ /]([0-9.]{1,10})" => "\\1",
    ),
    "uri" => ""
  ),
  "aol" => array(
    "icon" => "aol",
    "title" => "AOL",
    "rule" => array(
      "aol[ /\-]([0-9.]{1,10})" => "\\1",
      "America Online Browser[ /]([0-9.]{1,10}).*rev([0-9.]{1,10})" => "\\1",
      "aol[ /\-]?browser" => ""
    ),
    "uri" => ""
  ),
  "avantbrowser" => array(
    "icon" => "avantbrowser",
    "title" => "Avant Browser",
    "rule" => array(
      "Avant[ ]?Browser" => ""
    ),
    "uri" => "http://www.avantbrowser.com/"
  ),
  // mobile one
  "avantgo" => array(
    "icon" => "avantgo",
    "title" => "AvantGo",
    "rule" => array(
      "AvantGo[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.avantgo.com/frontdoor/"
  ),
  "aweb" => array(
    "icon" => "aweb",
    "title" => "Aweb",
    "rule" => array(
      "Amiga\-Aweb[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://aweb.sunsite.dk/"
  ),
  "beonex" => array(
    "icon" => "beonex",
    "title" => "Beonex",
    "rule" => array(
      "beonex/([0-9.]{1,10})" => "\\1"
    )
  ),
  "blazer" => array(
    "icon" => "blazer",
    "title" => "Blazer",
    "rule" => array(
      "Blazer[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bluefish" => array(
    "icon" => "bluefish",
    "title" => "BlueFish",
    "rule" => array(
      "bluefish[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://bluefish.openoffice.nl/"
  ),
  "browsex" => array(
    "icon" => "question",
    "title" => "BrowseX",
    "rule" => array(
      "BrowseX.*\(([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://browsex.com/"
  ),
  "camino" => array(
    "icon" => "camino",
    "title" => "Camino",
    "rule" => array(
      "camino/([0-9.+]{1,10})" => "\\1"
    ),
    "uri" => "http://www.mozilla.org/projects/camino/"
  ),
  "checkandget" => array(
    "icon" => "checkandget",
    "title" => "Check&Get",
    "rule" => array(
      "Check\&Get[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://activeurls.com/"
  ),
  "chimera" => array(
    "icon" => "chimera",
    "title" => "Chimera",
    "rule" => array(
      "chimera/([0-9.+]{1,10})" => "\\1"
    ),
    "uri" => "http://www.chimera.org/"
  ),
  "contiki" => array(
    "icon" => "question",
    "title" => "Contiki",
    "rule" => array(
      "^Contiki[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.sics.se/~adam/contiki/apps/webbrowser.html"
  ),
  "columbus" => array(
    "icon" => "columbus",
    "title" => "Columbus",
    "rule" => array(
      "columbus[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "crazybrowser" => array(
    "icon" => "crazybrowser",
    "title" => "Crazy Browser",
    "rule" => array(
      "Crazy Browser[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "curl" => array(
    "icon" => "curl",
    "title" => "Curl",
    "rule" => array(
      "curl[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://curl.haxx.se/"
  ),
  "cuteftp" => array(
    "icon" => "question",
    "title" => "Cute FTP",
    "rule" => array(
      "Cute FTP .*[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "deepnet" => array(
    "icon" => "deepnet",
    "title" => "Deepnet Explorer",
    "rule" => array(
      "Deepnet Explorer[/ ]([0-9.]{1,10})" => "\\1",
      " Deepnet Explorer[\);]" => ""
    )
  ),
  "democracy" => array(
    "icon" => "question",
    "title" => "Democracy",
    "rule" => array(
      "Democracy[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.getdemocracy.com/"
  ),
  "dillo" => array(
    "icon" => "dillo",
    "title" => "Dillo",
    "rule" => array(
      "dillo/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.dillo.org/"
  ),
  "divx" => array(
    "icon" => "dillo",
    "title" => "DivX Player",
    "rule" => array(
      "DivX Player[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "doczilla" => array(
    "icon" => "doczilla",
    "title" => "DocZilla",
    "rule" => array(
      "DocZilla/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.doczilla.com/"
  ),
  "donut" => array(
    "icon" => "donut",
    "title" => "Donut RAPT",
    "rule" => array(
      "Donut RAPT[/ ]#?([0-9.]{1,10})" => "\\1"
    )
  ),
  "doris" => array(
    "icon" => "doris",
    "title" => "Doris",
    "rule" => array(
      "Doris/([0-9.]{1,10})" => "\\1"
    )
  ),
  "edbrowse" => array(
    "icon" => "question",
    "title" => "edbrowse",
    "rule" => array(
      "edbrowse/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.eklhad.net/linux/app/"
  ),
  "elinks" => array(
    "icon" => "links",
    "title" => "ELinks",
    "rule" => array(
      "ELinks[ /][\(]*([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://elinks.or.cz/"
  ),
  "emacs" => array(
    "icon" => "question",
    "title" => "Emacs/w3s",
    "rule" => array(
      "Emacs-W3/([0-9.(pre)]{1,10})" => "\\1"
    ),
    "uri" => "http://www.gnu.org/software/w3/",
  ),
  "epiphany"  => array(
    "icon"  => "epiphany",
    "title" => "Epiphany",
    "rule"  => array(
      "Epiphany/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.gnome.org/projects/epiphany/"
  ),
  // mobile one
  "eudoraweb" => array(
    "icon" => "mobile",
    "title" => "EudoraWeb",
    "rule" => array(
      "EudoraWeb[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.eudora.com/internetsuite/eudoraweb.html"
  ),
  "firebird"  => array(
    "icon"  => "firebird",
    "title" => "Firebird",
    "rule"  => array(
      "Firebird/([0-9.+]{1,10})" => "\\1"
    )
  ),
  "flock" => array(
    "icon" => "flock",
    "title" => "Flock",
    "rule" => array(
      "Flock/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.flock.com/"
  ),
  // mobile one
/*  "fxtbrowser" => array(
    "icon" => "mobile",
    "title" => "ftxBrowser",
    "rule" => array(
      "" => ""
    ),
    "uri" => "http://www.access-us-inc.com/",
    "known" => array(
      "Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320)" => "ftxBrowser under Windows CE 2003 on Pocket PC",
      "Mozilla/2.0 (compatible; MSIE 3.02; Windows CE; PPC; 240x320)" => "ftxBrowser under Windows CE 2002 on Pocket PC"
    )
  ),*/
  "galeon" => array(
    "icon" => "galeon",
    "title" => "Galeon",
    "rule" => array(
      "galeon/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://galeon.sourceforge.net/"
  ),
  "ibrowse" => array(
    "icon" => "ibrowse",
    "title" => "IBrowse",
    "rule" => array(
      "ibrowse[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.ibrowse-dev.net/"
  ),
  "icab" => array(
    "icon" => "icab",
    "title" => "iCab",
    "rule" => array(
      "icab[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.icab.de/"
  ),
  "ice" => array(
    "icon" => "ice",
    "title" => "ICEbrowser",
    "rule" => array(
      "ICE[ ]?Browser/v?([0-9._]{1,10})" => "\\1"
    ),
    "uri" => "http://www.borland.com/jbuilder/"
  ),
  // mobile one
  "iexplorepocket" => array(
    "icon" => "mobile",
    "title" => "Internet Explorer Pocket",
    "rule" => array(
      "Microsoft Pocket Internet Explorer[ /]([0-9.]{1,10})" => "\\1",
      "MSPIE[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "irider" => array(
    "icon" => "irider",
    "title" => "iRider",
    "rule" => array(
      "iRider[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "isilox" => array(
    "icon" => "isilox",
    "title" => "iSiloX",
    "rule" => array(
      "iSilox/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "lotus" => array(
    "icon" => "lotus",
    "title" => "Lotus Notes",
    "rule" => array(
      "Lotus[ \-]?Notes[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "kazehakase" => array(
    "icon" => "kazehakase",
    "title" => "Kazehakase",
    "rule" => array(
      "Kazehakase[ /]([0-9a-z.]{1,10})" => "\\1"
    ),
    "uri" => "http://kazehakase.sourceforge.jp/20031201.html"
  ),
  "kkman" => array(
    "icon" => "kkman",
    "title" => "KKman",
    "rule" => array(
      "KKman[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.kkman.com.tw/"
  ),
  "k-meleon" => array(
    "icon" => "k-meleon",
    "title" => "K-Meleon",
    "rule" => array(
      "K-Meleon[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://kmeleon.sourceforge.net/"
  ),
  "k-ninja" => array(
    "icon" => "k-ninja",
    "title" => "K-Ninja",
    "rule" => array(
      "K-Ninja[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.geocities.com/grenleef/"
  ),
  "konqueror" => array(
    "icon" => "konqueror",
    "title" => "Konqueror",
    "rule" => array(
      "konqueror/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.konqueror.org/"
  ),
  "links" => array(
    "icon" => "links",
    "title" => "Links",
    "rule" => array(
      "Links[ /]\(([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://artax.karlin.mff.cuni.cz/~mikulas/links"
  ),
  "lunascape" => array(
    "icon" => "lunascape",
    "title" => "Lunascape",
    "rule" => array(
      "Lunascape[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "lynx" => array(
    "icon" => "lynx",
    "title" => "Lynx",
    "rule" => array(
      "lynx/([0-9a-z.]{1,10})" => "\\1"
    ),
    "uri" => "http://lynx.browser.org/"
  ),
   "maxthon" => array(
    "icon" => "maxthon",
    "title" => "Maxthon",
    "rule" => array(
      " Maxthon[\);]" => ""
    )
  ),
  "mbrowser" => array(
    "icon" => "mbrowser",
    "title" => "mBrowser",
    "rule" => array(
      "mBrowser[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mediaplayer" => array(
    "icon" => "wmp10",
    "title" => "Media Player",
    "rule" => array(
      "NSPlayer[ /]([0-9.]{1,10})" => "\\1",
      "WMFSDK[ /]([0-9.]{1,10})" => "\\1",
      "Windows-Media-Player[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  // mobile one
  "minimo" => array(
    "icon" => "mobile",
    "title" => "Minimo",
    "rule" => array(
      "Minimo[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.mozilla.org/projects/minimo/"
  ),
  "mosaic" => array(
    "icon" => "mosaic",
    "title" => "Mosaic",
    "rule" => array(
      "mosaic[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mpc" => array(
    "icon" => "mpc",
    "title" => "Media Player Classic",
    "rule" => array(
      "Media Player Classic" => ""
    ),
    "uri" => "http://sourceforge.net/projects/guliverkli/"
  ),
  "mplayer" => array(
    "icon" => "mplayer",
    "title" => "MPlayer",
    "rule" => array(
      "^MPlayer[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.mplayerhq.hu"
  ),
  "msn" => array(
    "icon" => "msn",
    "title" => "MSN Explorer",
    "rule" => array(
      "MSN[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.mplayerhq.hu"
  ),
  "multibrowser" => array(
    "icon" => "multibrowser",
    "title" => "Multi-Browser",
    "rule" => array(
      "Multi-Browser[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://archive.ncsa.uiuc.edu/SDG/Software/XMosaic/"
  ),
  "myie2" => array(
    "icon" => "myie2",
    "title" => "MyIE2",
    "rule" => array(
      " MyIE2[\);]" => ""
    )
  ),
  "netsurf" => array(
    "icon" => "netsurf",
    "title" => "NetSurf",
    "rule" => array(
      "Netsurf" => ""
    ),
    "uri" => ""
  ),
  "nautilus" => array(
    "icon" => "nautilus",
    "title" => "Nautilus",
    "rule" => array(
      "(gnome[ \-]?vfs|nautilus)/([0-9.]{1,10})" => "\\2"
    ),
    "uri" => ""
  ),
  "netcaptor" => array(
    "icon" => "netcaptor",
    "title" => "Netcaptor",
    "rule" => array(
      "netcaptor[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  // mobile one
  "netfront" => array(
    "icon" => "netfront",
    "title" => "Netfront",
    "rule" => array(
      "NetFront[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.access-us-inc.com/"
  ),
  "netpositive" => array(
    "icon" => "netpositive",
    "title" => "NetPositive",
    "rule" => array(
      "netpositive[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://browsers.evolt.org/?netpositive/"
  ),
  "nexus" => array(
    "icon" => "question",
    "title" => "Nexus",
    "rule" => array(
      "^Nexus" => ""
    ),
    "uri" => "http://browsers.evolt.org/"
  ),
  "offbyone" => array(
    "icon" => "offbyone",
    "title" => "OffByOne",
    "rule" => array(
      "OffByOne" => ""
    ),
    "uri" => "http://www.offbyone.com/"
  ),
  "omniweb" => array(
    "icon" => "omniweb",
    "title" => "OmniWeb",
    "rule" => array(
      "omniweb/[ a-z]?([0-9.]{1,10})$" => "\\1"
    )
  ),
  // mobile one
  "openwave" => array(
    "icon" => "mobile",
    "title" => "OpenWave",
    "rule" => array(
      "OPWV-SDK UP\.Browser[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.openwave.com/us/products/mobile/device_products/mobile_browser/index.htm"
  ),
  "operamini" => array(
    "icon" => "opera",
    "title" => "Opera Mini",
    "rule" => array(
      "opera mini[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "opera" => array(
    "icon" => "opera",
    "title" => "Opera",
    "rule" => array(
      "opera[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "oregano" => array(
    "icon" => "oregano",
    "title" => "Oregano",
    "rule" => array(
      "Oregano[0-9]?[ /]([0-9.]{1,10})$" => "\\1"
    ),
    "uri" => ""
  ),
/*  "oxygen" => array(
    "icon" => "oxygen",
    "title" => "Oxygen",
    "rule" => array(
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:0.9.6) Gecko/20011128" => ""
    ),
    "uri" => "http://www.netdive.com/oxygen/"
  ),*/
  // mobile one
  "palmsource" => array(
    "icon" => "mobile",
    "title" => "PalmSource Web Browser",
    "rule" => array(
      "PalmSource" => "",
      "Palm-Arz1" => ""
    ),
    "uri" => "http://www.palmos.com/dev/tech/palmos5/webbrowser.html"
  ),
  "paparazzi" => array(
    "icon" => "question",
    "title" => "Paparazzi",
    "rule" => array(
      "Paparazzi\!/([0-9.]{1,10})" => "\\1"
    )
  ),
  "phaseout" => array(
    "icon" => "phaseout",
    "title" => "PhaseOut",
    "rule" => array(
      "www\.phaseout\.net" => ""
    )
  ),
  "plink" => array(
    "icon" => "plink",
    "title" => "PLink",
    "rule" => array(
      "PLink[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  // mobile one
  "plucker" => array(
    "icon" => "mobile",
    "title" => "Plucker",
    "rule" => array(
      "Plucker[ /](Py-)?([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.openwave.com/us/products/mobile/device_products/mobile_browser/index.htm"
  ),
  "phoenix" => array(
    "icon" => "phoenix",
    "title" => "Phoenix",
    "rule" => array(
      "Phoenix/([0-9.+]{1,10})" => "\\1"
    )
  ),
  "phped" => array(
    "icon" => "question",
    "title" => "PHPEd",
    "rule" => array(
      "PHPEd Version[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "printsmart" => array(
    "icon" => "question",
    "title" => "HP Web PrintSmart",
    "rule" => array(
      "HP Web PrintSmart ([0-9.a-z]{1,10})" => "\\1"
    )
  ),
  "proxomitron" => array(
    "icon" => "proxomitron",
    "title" => "Proxomitron",
    "rule" => array(
      "[(Space)| ]?BisoN/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.proxomitron.info/"
  ),
  "psp" => array(
    "icon" => "question",
    "title" => "PlayStation Portable",
    "rule" => array(
      "PSP \(PlayStation Portable\); ([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "quicktime" => array(
    "icon" => "quicktime",
    "title" => "QuickTime",
    "rule" => array(
      "QuickTime..qtver.([0-9.]{1,10})" => "\\1",
      "qtver.([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "realplayer" => array(
    "icon" => "realplayer",
    "title" => "Real Player",
    "rule" => array(
      "RealPlayer/([0-9.+]{1,10})" => "\\1",
      "^Mozilla/([0-9.+]{1,10}).*\(R1 1.5\)\)" => "",
      "RMA/([0-9.+]{1,10})" => ""
    )
  ),
  "retawq" => array(
    "icon" => "question",
    "title" => "retawq",
    "rule" => array(
      "retawq/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://retawq.sourceforge.net/"
  ),
  // FIXME: The Safari-rule misdetects Shiira as Safari, so it has to be BEFORE Safari
  "shiira" => array(
    "icon" => "shiira",
    "title" => "Shiira",
    "rule" => array(
      "Shiira/([0-9.]{1,10})" => "\\1"
    )
  ),
  "safari" => array(
    "icon" => "safari",
    "title" => "Safari",
    "rule" => array(
      "safari/([0-9.]{1,10})" => "\\1"
    )
  ),
  "seamonkey" => array(
    "icon" => "seamonkey",
    "title" => "Seamonkey",
    "rule" => array(
      "Seamonkey/([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "securewebbrowser" => array(
    "icon" => "question",
    "title" => "HP Secure Web Browser",
    "rule" => array(
      "SWB[ /]V?([0-9.]{1,10}) \(HP\)" => "\\1"
    ),
    "uri" => "http://h71000.www7.hp.com/openvms/products/ips/cswb/cswb.html"
  ),
  "sleipnir" => array(
    "icon" => "sleipnir",
    "title" => "Sleipnir",
    "rule" => array(
      "Sleipnir( Version)?[ /]([0-9a-z.]{1,10})" => "\\2"
    )
  ),
  "slimbrowser" => array(
    "icon" => "slimbrowser",
    "title" => "SlimBrowser",
    "rule" => array(
      "Slimbrowser" => ""
    )
  ),
  "songbird" => array(
    "icon" => "question",
    "title" => "Songbird",
    "rule" => array(
      "Songbird[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.songbirdnest.com/"
  ),
  "spectruminternetsuite" => array(
    "icon" => "question",
    "title" => "Spectrum Internet Suite",
    "rule" => array(
      " SIS ([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://sis.gwlink.net/"
  ),
  "squid" => array(
    "icon" => "question",
    "title" => "Squid Proxy",
    "rule" => array(
      "^Cafi[ /]([0-9.]{1,10})" => "\\1",
      "SquidClamAV_Redirector[ /]([0-9.]{1,10})" => ""
    ),
    "uri" => ""
  ),
  "staroffice" => array(
    "icon" => "staroffice",
    "title" => "StarOffice",
    "rule" => array(
      "staroffice[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sunrise" => array(
    "icon" => "sunrise",
    "title" => "Sunrise",
    "rule" => array(
      "SunriseBrowser[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sylera" => array(
    "icon" => "question",
    "title" => "Sylera",
    "rule" => array(
      "Sylera[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.zawameki.net/izmi/prog/sylera_en.html"
  ),
  "tonline" => array(
    "icon" => "tonline",
    "title" => "T-Online",
    "rule" => array(
      "^T-Online Browser" => "\\1"
    )
  ),
  // This one is mobile browser
  "upbrowser" => array(
    "icon" => "browser",
    "title" => "UP.Browser",
    "rule" => array(
      "UP\.Browser[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "voyager" => array(
    "icon" => "voyager",
    "title" => "Voyager",
    "rule" => array(
      "voyager[ /]([0-9.]{1,10})" => "\\1",
      "AmigaVoyager" => "",
      " Voyager" => ""
    ),
    "uri" => "http://v3.vapor.com/"
  ),
  "w3clinemode" => array(
    "icon" => "question",
    "title" => "W3C Line Mode",
    "rule" => array(
      "W3CLineMode/([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.w3.org/LineMode"
  ),
  "w3m" => array(
    "icon" => "w3m",
    "title" => "w3m",
    "rule" => array(
      "w3m/([0-9.]{1,10})" => "\\1"
    )
  ),
  "warrior" => array(
    "icon" => "warrior",
    "title" => "Warrior",
    "rule" => array(
      "^Warrior" => "\\1",
    )
  ),
  "webcapture" => array(
    "icon" => "question",
    "title" => "WebCapture (Adobe)",
    "rule" => array(
      "WebCapture[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webtv" => array(
    "icon" => "webtv",
    "title" => "Webtv",
    "rule" => array(
      "webtv[ /]([0-9.]{1,10})" => "\\1",
      "webtv" => ""
    )
  ),
  "winamp" => array(
    "icon" => "winamp",
    "title" => "Winamp",
    "rule" => array(
      "^WinampMPEG[ /]([0-9.]{1,10})" => "\\1",
      "^Nullsoft Winamp3 version[ /]([0-9.a-z]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  // mobile one
  "xiino" => array(
    "icon" => "xiino",
    "title" => "Xiino",
    "rule" => array(
      "^Xiino[ /]([0-9a-z.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.access-us-inc.com/"
  ),
  "xine" => array(
    "icon" => "xine",
    "title" => "xine",
    "rule" => array(
      "^xine[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://xine.sourceforge.net/"
  ),
// Catch up for the originals. they got to stay in that order.
  "explorer" => array(
    "icon" => "explorer",
    "title" => "Explorer",
    "rule" => array(
      "\(compatible; MSIE[ /]([0-9a-z.]{1,10})" => "\\1",
      "Internet Explorer[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "netscape" => array(
    "icon" => "netscape",
    "title" => "Netscape",
    "rule" => array(
      "netscape[0-9]?/([0-9.]{1,10})" => "\\1",
      "^mozilla/([0-4]\.[0-9.]{1,10})" => "\\1"
    )
  ),
  "firefox"  => array(
    "icon"  => "firefox",
    "title" => "Firefox",
    "rule"  => array(
      "Firefox/([0-9.+]{1,10})" => "\\1",
      "BonEcho/([0-9.+]{1,10})" => "\\1", // Firefox 2.0 beta
      "Iceweasel/([0-9.+]{1,10})" => "\\1", // Unbranded Firefox 2.0, GNU compatible
      "GranParadiso/([0-9.+]{1,10})" => "\\1", // Firefox 3.0 alpha
      "Firefox" => ""
    ),
    "uri" => "",
    "known" => array(
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1) Gecko/20061019 Firefox" => "Firefox nightly on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; nl-NL; rv:1.7.5) Gecko/20041202 Firefox/1.0" => "Firefox 1.0 on Windows XP (dutch)",
      "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.7.6) Gecko/20050512 Firefox" => "Firefox 1.0.4 on Ubuntu Linux (AMD64)",
      "Mozilla/5.0 (X11; U; FreeBSD i386; en-US; rv:1.7.8) Gecko/20050609 Firefox/1.0.4" => "Firefox 1.0.4 on FreeBSD (i386)",
      "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.9) Gecko/20050711 Firefox/1.0.5" => "Firefox 1.0.5 on Slackware",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6" => "Firefox 1.0.6 on Windows XP",
      "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-GB; rv:1.7.10) Gecko/20050717 Firefox/1.0.6" => "Firefox 1.0.6 on Mac OS X 10.4 PPC",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7" => "Firefox 1.0.7 on Windows XP",
      "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7" => "Firefox 1.0.7 on Mac OS X 10.3 PPC",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8b4) Gecko/20050908 Firefox/1.4" => "Firefox 1.5 beta 1 on Windows XP",
      "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.8b4) Gecko/20050908 Firefox/1.4" => "Firefox 1.5 beta 1 on Mac OS X 10.3 PPC",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5" => "Firefox 1.5 on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1" => "Firefox 1.5.0.1 on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1" => "Firefox 1.5.0.1 on Windows Vista",
      "Mozilla/5.0 (BeOS; U; BeOS BePC; en-US; rv:1.9a1) Gecko/20051002 Firefox/1.6a1" => "1.6 alpha 1 on BeOS R5",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8) Gecko/20060321 Firefox/2.0a1" => "2.0 alpha 1 on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1b1) Gecko/20060710 Firefox/2.0b1" => "2.0 beta 1 on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1b2) Gecko/20060710 Firefox/2.0b2" => "2.0 beta 2 on Windows XP",
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1) Gecko/20060918 Firefox/2.0" => "2.0 on Windows XP"
    )
  ),
  "mozilla" => array(
    "icon" => "mozilla",
    "title" => "Mozilla",
    "rule" => array(
      "^mozilla/[5-9]\.[0-9.]{1,10}.+rv:([0-9a-z.+]{1,10})" => "\\1",
      "^mozilla/([5-9]\.[0-9a-z.]{1,10})" => "\\1",
      "GNUzilla/([0-9.+]{1,10})" => "\\1" // Unbranded Mozilla, GNU compatible
    ),
    "uri" => "",
    "known" => array(
      "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.8) Gecko/20050511" => "Mozilla 1.7.9 on Linux (american english)",
      "Mozilla/5.0 (X11; U; Linux i686; cs-CZ; rv:1.7.12) Gecko/20050929" => "Mozilla 1.7.12 on Gentoo Linux"
    )
  ),
// WAP catchup
  "wap" => array(
    "icon" => "question",
    "title" => "WAP",
    "rule" => array(
      "Profile[ /]MIDP-([0-9.+]{1,10})" => "",
      "Configuration[ /]CLDC-([0-9.+]{1,10})" => ""
    )
  ),
// Things we don't know by now
  "other" => array(
    "icon" => "question",
    "title" => "other",
    "rule" => array(
      ".*" => ""
    )
  )
);
?>
