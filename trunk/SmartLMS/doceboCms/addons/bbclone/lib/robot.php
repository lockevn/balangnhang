<?php
/*This file is part of BBClone (The PHP web counter on steroids)
 *
 * $Header: /cvs/bbclone/lib/robot.php,v 1.170 2006/12/27 17:01:44 christoph Exp $
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

// Missing:
// 128.255.xx.xx - - [08/Feb/2004:16:38:00 +0100] "GET /robots.txt HTTP/1.0" 200 280 "-" "Generic"
// 128.107.xx.xx - - [21/Feb/2004:06:56:11 +0100] "GET /robots.txt HTTP/1.0" 200 280 "-" "CE-Preload"

$robot = array(
  "1noon" => array(
    "icon" => "1noon",
    "title" => "1noon",
    "rule" => array(
      "1Noonbot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "2dehands" => array(
    "icon" => "2dehands",
    "title" => "2deHands",
    "rule" => array(
      "2dehands\.nl" => ""
    )
  ),
  "a2b" => array(
    "icon" => "a2b",
    "title" => "A2B",
    "rule" => array(
      "www\.a2b\.cc" => ""
    )
  ),
  "abacho" => array(
    "icon" => "robot",
    "title" => "Abacho",
    "rule" => array(
      "^ABACHOBot" => ""
    )
  ),
  "abot" => array(
    "icon" => "robot",
    "title" => "aBot",
    "rule" => array(
      "^abot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "about" => array(
    "icon" => "about",
    "title" => "About",
    "rule" => array(
      "Libby[_/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "acoon" => array(
    "icon" => "acoon",
    "title" => "Acoon",
    "rule" => array(
      "Acoon[ \-]?Robot" => ""
    )
  ),
  "accoona" => array(
    "icon" => "accoona",
    "title" => "Accoona",
    "rule" => array(
      "Accoona-AI-Agent[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "active" => array(
    "icon" => "robot",
    "title" => "ActiveBookmark",
    "rule" => array(
      "ActiveBookmark[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "aibot" => array(
    "icon" => "robot",
    "title" => "Aibot",
    "rule" => array(
      "AIBOT[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "aipbot" => array(
    "icon" => "robot",
    "title" => "Aipbot",
    "rule" => array(
      "aipbot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "aleksika" => array(
    "icon" => "aleksika",
    "title" => "Aleksika",
    "rule" => array(
      "Aleksika Spider[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "alertsite" => array(
    "icon" => "robot",
    "title" => "AlertSite",
    "rule" => array(
      "ipd[ /]([0-9.]{1,10}).*Alertsite\.com" => "\\1"
    ),
    "uri" => "http://www.alertsite.com/index.html"
  ),
  "alexa" => array(
    "icon" => "alexa",
    "title" => "Alexa",
    "rule" => array(
      "^ia_archive" => ""
    )
  ),
  "almaden" => array(
    "icon" => "almaden",
    "title" => "IBM Crawler",
    "rule" => array(
      "www\.almaden\.ibm\.com/cs/crawler" => ""
    )
  ),
  "altavista" => array(
    "icon" => "altavista",
    "title" => "Altavista",
    "rule" => array(
      "Scooter[ /\-]*[a-z]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "amidalla" => array(
    "icon" => "amidalla",
    "title" => "Amidalla",
    "rule" => array(
      "^amibot" => ""
    )
  ),
  "amfibi" => array(
    "icon" => "amfibi",
    "title" => "Amfibi",
    "rule" => array(
      "Amfibibot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "amphetadesk" => array(
    "icon" => "robot",
    "title" => "AmphetaDesk",
    "rule" => array(
      "AmphetaDesk[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "amphetameme" => array(
    "icon" => "robot",
    "title" => "Amphetameme",
    "rule" => array(
      "amphetameme[ \-]?crawler" => ""
    )
  ),
  "ansearch" => array(
    "icon" => "robot",
    "title" => "Ansearch",
    "rule" => array(
      "AnsearchBot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "aonde" => array(
    "icon" => "aonde",
    "title" => "Aonde",
    "rule" => array(
      "^AONDE-Spider" => ""
    )
  ),
  "aol" => array(
    "icon" => "aol",
    "title" => "AOLserver",
    "rule" => array(
      "^AOLserver-Tcl[/ ]([0-9.]{1,10})" => "\\1",
      "^AOLserver" => ""
    )
  ),
  "apachebench" => array(
    "icon" => "robot",
    "title" => "ApacheBench",
    "rule" => array(
      "ApacheBench[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "aport" => array(
    "icon" => "aport",
    "title" => "Aport",
    "rule" => array(
      "^Aport" => ""
    )
  ),
  "appie" => array(
    "icon" => "robot",
    "title" => "Walhello",
    "rule" => array(
      "appie[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "arachmo" => array(
    "icon" => "arachmo",
    "title" => "Arachmo",
    "rule" => array(
      "compatible; Arachmo" => ""
    )
  ),
  "artface" => array(
    "icon" => "robot",
    "title" => "Artface",
    "rule" => array(
      "^ArtfaceBot" => ""
    )
  ),
  "ask" => array(
    "icon" => "askjeeves",
    "title" => "Ask Jeeves",
    "rule" => array(
      "Ask[ \-]?Jeeves" => "",
      "teomaagent" => ""
    )
  ),
  "aspseek" => array(
    "icon" => "robot",
    "title" => "ASPseek",
    "rule" => array(
      "^ASPseek[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "atlocal" => array(
    "icon" => "robot",
    "title" => "At Local",
    "rule" => array(
      "AtlocalBot[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.atlocal.com/"
  ),
  "atomz" => array(
    "icon" => "atomz",
    "title" => "Atomz",
    "rule" => array(
      "Atomz[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "axel" => array(
    "icon" => "robot",
    "title" => "Axel",
    "rule" => array(
      "^axel" => ""
    )
  ),
  "axmo" => array(
    "icon" => "axmo",
    "title" => "Axmo",
    "rule" => array(
      "AxmoRobot" => ""
    )
  ),
  "answerbus" => array(
    "icon" => "answerbus",
    "title" => "AnswerBus",
    "rule" => array(
      "answerbus" => ""
    )
  ),
  "automapit" => array(
    "icon" => "robot",
    "title" => "AutoMapIt",
    "rule" => array(
      "AutoMapIt[ /](Bot)?" => ""
    ),
    "uri" => "http://www.automapit.com/bot.html"
  ),
  "augurnfind" => array(
    "icon" => "robot",
    "title" => "Augurnfind",
    "rule" => array(
      "augurnfind[/ ][v\-]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "awasu" => array(
    "icon" => "awasu",
    "title" => "Awasu",
    "rule" => array(
      "Awasu[/ ]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "baidu" => array(
    "icon" => "baidu",
    "title" => "Baidu",
    "rule" => array(
      "Baiduspider" => ""
    )
  ),
  "bananatree" => array(
    "icon" => "robot",
    "title" => "BananaTree",
    "rule" => array(
      "www\.thebananatree\.org" => ""
    )
  ),
  "become" => array(
    "icon" => "become",
    "title" => "Become",
    "rule" => array(
      "BecomeBot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bigsearch" => array(
    "icon" => "robot",
    "title" => "Bigsearch",
    "rule" => array(
      "Bigsearch.ca[/ ]Nutch[- ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "bitacle" => array(
    "icon" => "bitacle",
    "title" => "Bitacle",
    "rule" => array(
      "Bitacle bot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bitbeamer" => array(
    "icon" => "bitbeamer",
    "title" => "BitBeamer",
    "rule" => array(
      "BitBeamer/([0-9.]{1,10})" => "\\1"
    )
  ),
  "biz360" => array(
    "icon" => "robot",
    "title" => "Biz360",
    "rule" => array(
      "^Biz360 spider" => "\\1"
    )
  ),
  "blaizbee" => array(
    "icon" => "robot",
    "title" => "Blaiz-Bee",
    "rule" => array(
      "Blaiz-Bee[ /]([0-9.]{1,10})" => ""
    )
  ),
  "blogbot" => array(
    "icon" => "blogbot",
    "title" => "BlogBot",
    "rule" => array(
      "Blog[ \-]?Bot" => ""
    )
  ),
  "blogdex" => array(
    "icon" => "robot",
    "title" => "Blogdex",
    "rule" => array(
      "Blogdex[ /]([0-9.]{1,10})" => "\\1",
    )
  ),
  "blogg" => array(
    "icon" => "blogg",
    "title" => "Blogg",
    "rule" => array(
      "^blogg\.de" => ""
    )
  ),
  "blogland" => array(
    "icon" => "robot",
    "title" => "BlogLand",
    "rule" => array(
      "BlogLand[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bloglines" => array(
    "icon" => "bloglines",
    "title" => "Bloglines",
    "rule" => array(
      "Bloglines[ /]([0-9.]{1,10})" => "\\1",
      "Bloglines" => ""
    )
  ),
  "blogmap" => array(
    "icon" => "robot",
    "title" => "Blogmap",
    "rule" => array(
      "blogmap" => ""
    )
  ),
  "blogosphere" => array(
    "icon" => "robot",
    "title" => "Blogosphere",
    "rule" => array(
      "Blogosphere" => ""
    )
  ),
  "blogpeople" => array(
    "icon" => "robot",
    "title" => "BlogPeople",
    "rule" => array(
      "BlogPeople" => ""
    )
  ),
  "blogpulse" => array(
    "icon" => "blogpulse",
    "title" => "Blogpulse",
    "rule" => array(
      "Blogpulse" => ""
    )
  ),
  "blogranking" => array(
    "icon" => "blogranking",
    "title" => "BlogRanking",
    "rule" => array(
      "^BlogRanking(/RSS checker)?" => ""

    )
  ),
  "blogs" => array(
    "icon" => "robot",
    "title" => "Blo.gs",
    "rule" => array(
      "blo\.gs[ /]([0-9.]{1,10})" => "\\1",
      "blo\.gs" => ""

    )
  ),
  "blogsearch" => array(
    "icon" => "blogsearch",
    "title" => "Icerocket",
    "rule" => array(
      "BlogSearch[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "blogshares" => array(
    "icon" => "blogshares",
    "title" => "BlogShares",
    "rule" => array(
      "BlogShares[ /]V?([0-9.]{1,10})" => "\\1",
      "(^| |\()Blogshares(\.com| |\))" => ""
    ),
    "uri" => ""
  ),
  "blogslife" => array(
    "icon" => "robot",
    "title" => "BlogsLife",
    "rule" => array(
      "Blogslive" => ""
    )
  ),
  "blogsnow" => array(
    "icon" => "blogsnow",
    "title" => "BlogsNow",
    "rule" => array(
      "blogsnowbot" => "",
      "BlogsNow" => ""
    )
  ),
  "blogstreet" => array(
    "icon" => "blogstreet",
    "title" => "BlogStreet",
    "rule" => array(
      "^BlogStreetBot" => ""
    )
  ),
  "blogsurf" => array(
    "icon" => "robot",
    "title" => "BlogSurf",
    "rule" => array(
      "nomadscafe_ra[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "blogtick" => array(
    "icon" => "robot",
    "title" => "BlogTick",
    "rule" => array(
      "BlogTickServer" => ""
    )
  ),
  "blogwatcher" => array(
    "icon" => "blogwatcher",
    "title" => "Blogwatcher",
    "rule" => array(
      "blogWatcher_Spider[/ ]([0-9.]{1,10})" => "\\1",
    )
  ),
  "blogwise" => array(
    "icon" => "robot",
    "title" => "Blogwise",
    "rule" => array(
      "Blogwise\.com(-MetaChecker)?[/ ]([0-9.]{1,10})" => "\\2",
    )
  ),
  "bobby" => array(
    "icon" => "bobby",
    "title" => "Bobby",
    "rule" => array(
      "bobby[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "boitho" => array(
    "icon" => "robot",
    "title" => "Boitho",
    "rule" => array(
      "Boitho\.com[ \-](dc|robot)?[/ ]([0-9.]{1,10})" => "\\2"
    )
  ),
  "booch" => array(
    "icon" => "robot",
    "title" => "Booch",
    "rule" => array(
      "^booch[_ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "book" => array(
    "icon" => "book",
    "title" => "Bookmark",
    "rule" => array(
      "http://www\.bookmark\.ne\.jp" => ""
    )
  ),
  "bookdog" => array(
    "icon" => "robot",
    "title" => "Bookdog",
    "rule" => array(
      "^Bookdog[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bordermanager" => array(
    "icon" => "bordermanager",
    "title" => "Border Manager",
    "rule" => array(
      "BorderManager[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "bottomfeeder" => array(
    "icon" => "bottomfeeder",
    "title" => "BottomFeeder",
    "rule" => array(
      "BottomFeeder[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "browserspy" => array(
    "icon" => "robot",
    "title" => "BrowserSpy",
    "rule" => array(
      "BrowserSpy" => ""
    )
  ),
  "bruinbot" => array(
    "icon" => "robot",
    "title" => "BruinBot",
    "rule" => array(
      "BruinBot" => ""
    )
  ),
  "bruno" => array(
    "icon" => "robot",
    "title" => "Bruno",
    "rule" => array(
      "^Bruno" => ""
    )
  ),
  "btbot" => array(
    "icon" => "robot",
    "title" => "BitTorrent",
    "rule" => array(
      "BTbot/([0-9.]{1,10})" => "\\1"
    )
  ),
  "bulkfeeds" => array(
    "icon" => "robot",
    "title" => "Bulkfeeds",
    "rule" => array(
      "Bulkfeeds[/ ]([a-z0-9.]{1,10})" => "\\1"
    )
  ),
  "butch" => array(
    "icon" => "robot",
    "title" => "Butch",
    "rule" => array(
      "Butch(__| )?([a-z0-9.]{1,10})" => "\\2"
    ),
    "uri" => ""
  ),
  "camdiscover" => array(
    "icon" => "robot",
    "title" => "Camdiscover",
    "rule" => array(
      "^Camcrawler" => ""
    )
  ),
  "cazoodle" => array(
    "icon" => "robot",
    "title" => "Cazoodle",
    "rule" => array(
      "^CazoodleBot/Nutch[/ \-]([0-9.]{1,10})" => "\\1"
    )
  ),
  "centrum" => array(
    "icon" => "centrum",
    "title" => "Centrum",
    "rule" => array(
      "holmes[/ ]([0-9.]{1,10})" => "\\1",
      "^Centrum-checker" => ""
    )
  ),
  "cerberian" => array(
    "icon" => "robot",
    "title" => "Cerberian Drtrs",
    "rule" => array(
      "^Cerberian Drtrs" => ""
    )
  ),
  "cfnetwork" => array(
    "icon" => "robot",
    "title" => "CFNetwork",
    "rule" => array(
      "^CFNetwork[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.cfnetwork.be/"
  ),
  "charlotte" => array(
    "icon" => "robot",
    "title" => "Charlotte",
    "rule" => array(
      "Charlotte[/ ]([0-9a-z.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "cirilizator" => array(
    "icon" => "cirilizator",
    "title" => "Cirilizator",
    "rule" => array(
      "Cirilizator[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "claria" => array(
    "icon" => "robot",
    "title" => "Claria",
    "rule" => array(
      "(Claria|Diamond)(Bot)?[ /]([0-9.]{1,10})" => "\\3",
      "(Claria|Diamond)(Bot)" => "",

    )
  ),
  "claymont" => array(
    "icon" => "claymont",
    "title" => "Claymont",
    "rule" => array(
      "claymont\.com" => ""
    )
  ),
  "clush" => array(
    "icon" => "clush",
    "title" => "Clush",
    "rule" => array(
      "Clus(tered-Search-|h)Bot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "coldfusion" => array(
    "icon" => "coldfusion",
    "title" => "ColdFusion",
    "rule" => array(
      "^coldfusion" => ""
    )
  ),
  "combine" => array(
    "icon" => "robot",
    "title" => "Combine",
    "rule" => array(
      "Combine[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "combot" => array(
    "icon" => "robot",
    "title" => "comBot",
    "rule" => array(
      "comBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "comet" => array(
    "icon" => "comet",
    "title" => "Comet",
    "rule" => array(
      "cometsearch@cometsystems" => ""
    )
  ),
  "commerobo" => array(
    "icon" => "robot",
    "title" => "Commerobo",
    "rule" => array(
      "Commerobo[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "convera" => array(
    "icon" => "convera",
    "title" => "Convera",
    "rule" => array(
      "ConveraCrawler[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "coolbot" => array(
    "icon" => "robot",
    "title" => "CoolBot",
    "rule" => array(
      "^CoolBot" => ""
    )
  ),
  "cosmos" => array(
    "icon" => "robot",
    "title" => "Cosmos",
    "rule" => array(
      "^cosmos" => ""
    )
  ),
  "creativecommons" => array(
    "icon" => "creativecommons",
    "title" => "Creative Commons",
    "rule" => array(
    "CreativeCommons[/ ]([0-9.]{1,6}(-dev)?)" => "\\1"
    )
  ),
  "csscheck" => array(
    "icon" => "css",
    "title" => "CSSCheck",
    "rule" => array(
      "CSS(Check|_Validator)" => ""
    )
  ),
  "custo" => array(
    "icon" => "robot",
    "title" => "Custo",
    "rule" => array(
      "Custo[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "cybernavi" => array(
    "icon" => "robot",
    "title" => "CyberNavi",
    "rule" => array(
      "CyberNavi_WebGet[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "cyberz" => array(
    "icon" => "cyberz",
    "title" => "Cyberz",
    "rule" => array(
      "Cyberz Communication Agent" => ""
    )
  ),
  "cydral" => array(
    "icon" => "robot",
    "title" => "Cydral",
    "rule" => array(
      "CydralSpider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "cynthia" => array(
    "icon" => "cynthia",
    "title" => "Cynthia Says",
    "rule" => array(
      "Cynthia[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "d4x" => array(
    "icon" => "d4x",
    "title" => "Downloader for X",
    "rule" => array(
      "Downloader for X[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "da" => array(
    "icon" => "da",
    "title" => "DA",
    "rule" => array(
      "^DA[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "daum" => array(
    "icon" => "robot",
    "title" => "DAUM",
    "rule" => array(
      "DAUMOA[ /]([0-9.]{1,10})" => "\\1",
      "DAUM Web Robot" => "",
      "Daum Communications Corp" => "",
      "EDI[ /]([0-9.]{1,10})" => "\\1",
      "Edacious.*Intelligent Web Robot" => "",
      "RaBot[/ ]([0-9.]{1,10}) Agent" => "\\1"
    ),
    "uri" => ""
  ),
  "daypop" => array(
    "icon" => "robot",
    "title" => "Daypop",
    "rule" => array(
      "daypopbot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "delfi" => array(
    "icon" => "delfi",
    "title" => "Delfi",
    "rule" => array(
      "crawl at delfi dot lt" => ""
    )
  ),
  "depspid" => array(
    "icon" => "depspid",
    "title" => "DepSpid",
    "rule" => array(
      "DepSpid[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "devonagent" => array(
    "icon" => "devonagent",
    "title" => "DEVONagent",
    "rule" => array(
      "DEVONtech" => ""
    )
  ),
  "discopump" => array(
    "icon" => "robot",
    "title" => "DISCo Pump",
    "rule" => array(
      "DISCo Pump[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "doctorhtml" => array(
    "icon" => "robot",
    "title" => "DoctorHTML",
    "rule" => array(
      "Doctor[ \-]?HTML" => ""
    )
  ),
  "domaindatei" => array(
    "icon" => "robot",
    "title" => "Domaindatei",
    "rule" => array(
      "DomaindateiSpider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "downloadninja" => array(
    "icon" => "robot",
    "title" => "Download Ninja",
    "rule" => array(
      "Download Ninja[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "drupal" => array(
    "icon" => "drupal",
    "title" => "Drupal",
    "rule" => array(
      "^Drupal" => ""
    )
  ),
  "dsns" => array(
    "icon" => "robot",
    "title" => "DSNS Scanner",
    "rule" => array(
      "^DSNS" => ""
    )
  ),
  "dtsagent" => array(
    "icon" => "robot",
    "title" => "DTS Agent",
    "rule" => array(
      "DTS Agent" => ""
    )
  ),
  "earthcom" => array(
    "icon" => "earthcom",
    "title" => "Earthcom",
    "rule" => array(
      "EARTHCOM\.info[/ ]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "ebay" => array(
    "icon" => "robot",
    "title" => "eBay",
    "rule" => array(
      "eBay Relevance Ad Crawler" => ""
    )
  ),
  "eknip" => array(
    "icon" => "robot",
    "title" => "E-Knip",
    "rule" => array(
      "eknip[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "eliyon" => array(
    "icon" => "robot",
    "title" => "Eliyon",
    "rule" => array(
      "NextGenSearchBot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "emailsiphon" => array(
    "icon" => "robot",
    "title" => "Email Siphon",
    "rule" => array(
      "Email[ \-]?Siphon" => ""
    )
  ),
  "emeraldshield" => array(
    "icon" => "robot",
    "title" => "EmeraldShield",
    "rule" => array(
      "^EmeraldShield" => ""
    )
  ),
  "empas" => array(
    "icon" => "empas",
    "title" => "Empas",
    "rule" => array(
      "DigExt; empas\)$" => "",
      "^EMPAS[_\-]ROBOT" => ""
    )
  ),
  "entireweb" => array(
    "icon" => "entireweb",
    "title" => "Entireweb",
    "rule" => array(
      "Speedy[ ]?Spider" => ""
    )
  ),
  "envolk" => array(
    "icon" => "envolk",
    "title" => "Envolk",
    "rule" => array(
      "envolk\[ITS\]spider[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "esnet" => array(
    "icon" => "robot",
    "title" => "ES.NET",
    "rule" => array(
      "ES.NET Crawler[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "estyle" => array(
    "icon" => "robot",
    "title" => "eStyle Search",
    "rule" => array(
      "eStyleSearch[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "eurip" => array(
    "icon" => "robot",
    "title" => "Eurip",
    "rule" => array(
      "EuripBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "euroseek" => array(
    "icon" => "euroseek",
    "title" => "EuroSeek",
    "rule" => array(
      "Arachnoidea" => ""
    )
  ),
  "everbee" => array(
    "icon" => "everbee",
    "title" => "Everbee",
    "rule" => array(
      "EverbeeCrawler" => ""
    )
  ),
  "everest" => array(
    "icon" => "robot",
    "title" => "Everest",
    "rule" => array(
      "Everest-Vulcan Inc.[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "exabot" => array(
    "icon" => "exabot",
    "title" => "ExaBot",
    "rule" => array(
      "^NG[ /]([0-9.]{1,10})" => "\\1",
      "Exabot/([0-9.]{1,10})" => "\\1",
      "ExaBotTest/([0-9.]{1,10})" => "\\1"
    )
  ),
  "exactseek" => array(
    "icon" => "robot",
    "title" => "ExactSeek",
    "rule" => array(
      "ExactSeek[ \-]?Crawler" => ""
    )
  ),
  "exava" => array(
    "icon" => "robot",
    "title" => "Exava",
    "rule" => array(
      "Exabot@exava\.com\)$" => ""
    )
  ),
  "excite" => array(
    "icon" => "excite",
    "title" => "Excite",
    "rule" => array(
      "Architext[ \-]?Spider" => ""
    )
  ),
  "expertmonitor" => array(
    "icon" => "robot",
    "title" => "ExpertMonitor",
    "rule" => array(
      "^NetMonitor[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "facebook" => array(
    "icon" => "facebook",
    "title" => "Facebook",
    "rule" => array(
      "FacebookFeedParser[/ ]([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "fast" => array(
    "icon" => "fast",
    "title" => "Fast",
    "rule" => array(
      "^FAST( Enterprise |-Web| MetaWeb )Crawler[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "fastbuzz" => array(
    "icon" => "fastbuzz",
    "title" => "Fastbuzz",
    "rule" => array(
      "^fastbuzz\.com" => ""
    )
  ),
  "favorg" => array(
    "icon" => "robot",
    "title" => "FavOrg",
    "rule" => array(
      "^FavOrg" => ""
    )
  ),
  "faxo" => array(
    "icon" => "robot",
    "title" => "Faxo",
    "rule" => array(
      "^Faxobot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "fdse" => array(
    "icon" => "robot",
    "title" => "FDSE Robot",
    "rule" => array(
      "FDSE[ \-]?robot" => ""
    )
  ),
  "feedback" => array(
    "icon" => "robot",
    "title" => "FeedBack",
    "rule" => array(
      "FeedBack[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedburner" => array(
    "icon" => "feedburner",
    "title" => "FeedBurner",
    "rule" => array(
      "^FeedBurner[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feeddemon" => array(
    "icon" => "feeddemon",
    "title" => "FeedDemon",
    "rule" => array(
      "FeedDemon[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedfind" => array(
    "icon" => "robot",
    "title" => "FeedFind",
    "rule" => array(
      "Feed::Find[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedonfeeds" => array(
    "icon" => "robot",
    "title" => "Feed On Feeds",
    "rule" => array(
      "FeedOnFeeds[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedparser" => array(
    "icon" => "robot",
    "title" => "Feedparser",
    "rule" => array(
      "UniversalFeedParser[/ ]([0-9a-z.\-]{1,10})" => "\\1",
      "FeedParser" => ""
    )
  ),
  "feedreader" => array(
    "icon" => "feedreader",
    "title" => "Feedreader",
    "rule" => array(
      "^Feedreader" => ""
    )
  ),
  "feedserver" => array(
    "icon" => "robot",
    "title" => "FeedServer",
    "rule" => array(
      "FeedServer[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedster" => array(
    "icon" => "feedster",
    "title" => "Feedster",
    "rule" => array(
      "Feedster Crawler[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "feedvalidator" => array(
    "icon" => "feedvalidator",
    "title" => "Feed Validator",
    "rule" => array(
      "^FeedValidator[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "fdm" => array(
    "icon" => "other",
    "title" => "Free Download Manager",
    "rule" => array(
      "^FDM[/ ]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "findengines" => array(
    "icon" => "findengines",
    "title" => "FindEngines",
    "rule" => array(
      "FindEngines! Bot" => ""
    )
  ),
  "findexa" => array(
    "icon" => "findexa",
    "title" => "Findexa",
    "rule" => array(
      "Findexa Crawler" => ""
    )
  ),
  "findlinks" => array(
    "icon" => "findlinks",
    "title" => "FindLinks",
    "rule" => array(
      "findlinks[ /]([0-9.]{1,10})" => "\\1",
      "^FindLinks" => ""
    )
  ),
  "findoor" => array(
    "icon" => "robot",
    "title" => "findoor",
    "rule" => array(
      "^findoor(-Bot)?" => "\\1"
    )
  ),
  "firefly" => array(
    "icon" => "firefly",
    "title" => "Firefly",
    "rule" => array(
      "Firefly" => ""
    )
  ),
  "flashget" => array(
    "icon" => "flashget",
    "title" => "FlashGet",
    "rule" => array(
      "^FlashGet" => ""
    )
  ),
  "flickbot" => array(
    "icon" => "robot",
    "title" => "FlickBot",
    "rule" => array(
      "FlickBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "freshmeat" => array(
    "icon" => "robot",
    "title" => "freshmeat",
    "rule" => array(
      "fmII URL validator[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.freshmeat.net/"
  ),
  "friend" => array(
    "icon" => "friend",
    "title" => "Friend",
    "rule" => array(
      "www\.friend\.fr" => ""
    )
  ),
  "frontier" => array(
    "icon" => "frontier",
    "title" => "Frontier",
    "rule" => array(
      "Frontier[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "gaisbot" => array(
    "icon" => "robot",
    "title" => "Gaisbot",
    "rule" => array(
      "Gaisbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "galaxy" => array(
    "icon" => "galaxy",
    "title" => "Galaxy",
    "rule" => array(
      "GalaxyBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "gamespy" => array(
    "icon" => "robot",
    "title" => "GameSpy",
    "rule" => array(
      "GameSpyHTTP[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "gdesktop" => array(
    "icon" => "gdesktop",
    "title" => "Google Desktop",
    "rule" => array(
      "compatible; Google Desktop" => ""
    )
  ),
  "genome" => array(
    "icon" => "robot",
    "title" => "Genome Machine",
    "rule" => array(
      "Genome[ \-]?Machine" => ""
    )
  ),
  "geona" => array(
    "icon" => "robot",
    "title" => "Geona",
    "rule" => array(
      "GeonaBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "geoblog" => array(
    "icon" => "robot",
    "title" => "The World as a Blog",
    "rule" => array(
      "The World as a Blog" => ""
    )
  ),
  "geourl" => array(
    "icon" => "geourl",
    "title" => "GeoUrl",
    "rule" => array(
      "geourl[ /]([0-9.]{1,10})" => "\\1",
      "^GeoURLBot[ /]([0-9.]{1,10})" => "\\1",
    )
  ),
  "getright" => array(
    "icon" => "getright",
    "title" => "GetRight",
    "rule" => array(
      "GetRight[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "getsmart" => array(
    "icon" => "getsmart",
    "title" => "GetSmart",
    "rule" => array(
      "GetSmart[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "gigabot" => array(
    "icon" => "gigablast",
    "title" => "Gigablast",
    "rule" => array(
      "(Gigabot|Sitesearch)[/ ]([0-9.]{1,10})" => "\\2",
      "GigabotSiteSearch[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "girafabot" => array(
    "icon" => "girafa",
    "title" => "Girafa",
    "rule" => array(
      "Girafabot" => ""
    )
  ),
  "globalspec" => array(
    "icon" => "robot",
    "title" => "GlobalSpec",
    "rule" => array(
      "Ocelli[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "glucose" => array(
    "icon" => "glucose",
    "title" => "Glucose",
    "rule" => array(
      "glucose[ /]([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "goforit" => array(
    "icon" => "goforit",
    "title" => "GoForIt",
    "rule" => array(
      "^GoForIt\.com" => "",
      "^GOFORITBOT" => ""
    )
  ),
  "goguides" => array(
    "icon" => "robot",
    "title" => "GoGuides",
    "rule" => array(
      "^GoGuidesBot[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.goguides.org/"
  ),
  "goo" => array(
    "icon" => "goo",
    "title" => "Goo",
    "rule" => array (
      "(gazz|ichiro|mog(et|imogi))[ /]([0-9.]{1,10})" => "\\3",
      "DoCoMo[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "google" => array(
    "icon" => "google",
    "title" => "Google",
    "rule" => array (
      "Googl(e|ebot)(-Image)?/([0-9.]{1,10})" => "\\3",
      "Googl(e|ebot)(-Image)?/" => ""
    )
  ),
//TODO: Merge with google?
  "googlesitemaps" => array(
    "icon" => "google",
    "title" => "Google-Sitemaps",
    "rule" => array (
      "Googl(e|ebot)(-Sitemaps)?/([0-9.]{1,10})" => "\\3",
      "Googl(e|ebot)(-Sitemaps)?/" => ""
    )
  ),
//TODO: Merge with google?
  "googlemobile" => array(
    "icon" => "google",
    "title" => "Google-Mobile",
    "rule" => array (
      "Googl(e|ebot)(-Mobile)?/([0-9.]{1,10})" => "\\3",
      "Googl(e|ebot)(-Mobile)?/" => ""
    )
  ),
  "googleads" => array(
    "icon" => "google",
    "title" => "Google-AdsBot",
    "rule" => array (
      "^AdsBot-Google" => "",
    )
  ),
  "googlefeeds" => array(
    "icon" => "google",
    "title" => "Google-Feedfetcher",
    "rule" => array (
      "^Feedfetcher-Google" => "",
    )
  ),
  "gpost" => array(
    "icon" => "gpost",
    "title" => "GPost",
    "rule" => array(
      "^GPostbot" => ""
    )
  ),
  "gregarius" => array(
    "icon" => "robot",
    "title" => "Gregarius",
    "rule" => array(
      "^Gregarius[/ ]([0-9.]{1,10})" => ""
    )
  ),
  "grub" => array(
    "icon" => "grub",
    "title" => "Grub",
    "rule" => array(
      "grub[ \-]?client[ /\-]{1,5}([0-9.]{1,10})" => "\\1",
      "grub crawler" => ""
    ),
    "uri" => ""
  ),
  "gulliver" => array(
    "icon" => "robot",
    "title" => "Gulliver",
    "rule" => array(
      "Gulliver" => ""
    )
  ),
  "guruji" => array(
    "icon" => "robot",
    "title" => "Guruji",
    "rule" => array(
      "^GurujiBot[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.guruji.com/"
  ),
  "gush" => array(
    "icon" => "robot",
    "title" => "Gush",
    "rule" => array(
      "^Gush[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "gziptester" => array(
    "icon" => "robot",
    "title" => "Gzip Tester",
    "rule" => array(
      "g(id)?zip[ \-]?test(er)?" => ""
    )
  ),
  "hanzoweb" => array(
    "icon" => "robot",
    "title" => "Hanzoweb",
    "rule" => array(
      "^Hanzoweb" => ""
    )
  ),
  "harbot" => array(
    "icon" => "harbot",
    "title" => "Harbot",
    "rule" => array(
      "^Harbot GateStation" => ""
    )
  ),
  "hatena" => array(
    "icon" => "hatena",
    "title" => "Hatena",
    "rule" => array(
      "Hatena (Antenna|Bookmark|Pagetitle Agent)[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "heritrix" => array(
    "icon" => "heritrix",
    "title" => "Heritrix",
    "rule" => array(
      "heritrix[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "hiddenmarket" => array(
    "icon" => "robot",
    "title" => "HiddenMarket",
    "rule" => array(
      "HiddenMarket[ /\-]([0-9.]{1,10})" => "\\1"
    )
  ),
  "hoowwwer" => array(
    "icon" => "hoowwwer",
    "title" => "HooWWWer",
    "rule" => array(
      "HooWWWer[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "hotzonu" => array(
    "icon" => "hotzonu",
    "title" => "Hotzonu",
    "rule" => array(
      "Hotzonu[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "houxou" => array(
    "icon" => "robot",
    "title" => "Houxou",
    "rule" => array(
      "HouxouCrawler[ /]Nutch.([0-9.]{1,10})" => "\\1",
      "HouxouCrawler" => ""
    )
  ),
  "htdig" => array(
    "icon" => "htdig",
    "title" => "ht://Dig",
    "rule" => array(
      "htdig[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "html2jpg" => array(
    "icon" => "html2jpg",
    "title" => "HTML2JPG",
    "rule" => array(
      "^HTML2JPG" => ""
    )
  ),
  "httperf" => array(
    "icon" => "robot",
    "title" => "HTTPerf",
    "rule" => array(
      "httperf[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "httpunit" => array(
    "icon" => "httpunit",
    "title" => "HttpUnit",
    "rule" => array(
      "httpunit[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "httrack" => array(
    "icon" => "robot",
    "title" => "HTTrack",
    "rule" => array(
      "HTTrack[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "hungary" => array(
    "icon" => "hungary",
    "title" => "Hungary",
    "rule" => array(
      "HuRob[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "iask" => array(
    "icon" => "iask",
    "title" => "IAsk",
    "rule" => array(
      "iaskspider[ /]([0-9.]{1,10})" => "\\1",
      "^iaskspider" => ""
    ),
    "uri" => "http://iask.com"
  ),
  "icerocket" => array(
    "icon" => "icerocket",
    "title" => "Icerocket",
    "rule" => array(
      "BlogzIce[ /]([0-9.]{1,10})" => "\\1",
      "BlogSearch[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "ics" => array(
    "icon" => "robot",
    "title" => "Novell iChain Cool Solutions caching",
    "rule" => array(
      "^Mozilla[/ ]([0-9.]{1,10})[/ ]\(compatible[ ;]*ICS" => "\\1"
    )
  ),
  "iknow" => array(
    "icon" => "robot",
    "title" => "I know",
    "rule" => array(
      "Comaneci_bot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "ilse" => array(
    "icon" => "ilse",
    "title" => "Ilse",
    "rule" => array(
      "I(NGRID|lseRobot|lseBot)[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://ilse.nl/"
  ),
  "iltrovatore" => array(
    "icon" => "iltrovatore",
    "title" => "IlTrovatore",
    "rule" => array(
      "iltrovatore-setaccio[ /]([0-9.]{1,10})" => "\\1",
      "Iltrovatore-Setaccio" => ""
    )
  ),
  "indylibrary" => array(
    "icon" => "robot",
    "title" => "Indy Library",
    "rule" => array(
      "Indy[ \-]?Library" => ""
    )
  ),
  "ineturl" => array(
    "icon" => "robot",
    "title" => "InetURL",
    "rule" => array(
      "InetURL.?[ /]([0-9.]{1,10})" => ""
    ),
    "uri" => ""
  ),
  "infoart" => array(
    "icon" => "robot",
    "title" => "InfoArt",
    "rule" => array(
      "InfoArt crawler" => ""
    )
  ),
  "infoseek" => array(
    "icon" => "infoseek",
    "title" => "Infoseek",
    "rule" => array(
      "SideWinder[ /]?([0-9a-z.]{1,10})" => "\\1",
      "Infoseek" => ""
    )
  ),
  "inktomi" => array(
    "icon" => "inktomi",
    "title" => "Inktomi",
    "rule" => array(
      "slurp@inktomi\.com" => ""
    )
  ),
  "insitor" => array(
    "icon" => "robot",
    "title" => "Insitor",
    "rule" => array(
      "^Insitor," => ""
    )
  ),
  "internetninja" => array(
    "icon" => "robot",
    "title" => "Internet Ninja",
    "rule" => array(
      "^Internet Ninja[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "internetseer" => array(
    "icon" => "internetseer",
    "title" => "InternetSeer",
    "rule" => array(
      "^InternetSeer\.com" => ""
    )
  ),
  "intravnews" => array(
    "icon" => "intravnews",
    "title" => "IntraVnews",
    "rule" => array(
      "IntraVnews[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "irca" => array(
    "icon" => "robot",
    "title" => "ICRA",
    "rule" => array(
      "^ICRA_(label_generator|Semantic_spider)[ /]([0-9.]{1,10})" => "\\2"
    ),
    "uri" => "http://www.icra.org"
  ),
  "irvine" => array(
    "icon" => "robot",
    "title" => "Irvine",
    "rule" => array(
      "Irvine[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "ivia" => array(
    "icon" => "robot",
    "title" => "iVia",
    "rule" => array(
      "iVia Site Checker.?[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "jeteye" => array(
    "icon" => "jeteye",
    "title" => "Jeteye",
    "rule" => array(
      "Jetbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "jigsaw" => array(
    "icon" => "jigsaw",
    "title" => "Jigsaw",
    "rule" => array(
      "Jigsaw[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "jpluck" => array(
    "icon" => "robot",
    "title" => "Jpluck",
    "rule" => array(
      "JPluck[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "jxta" => array(
    "icon" => "robot",
    "title" => "Jxta",
    "rule" => array(
      "falcon[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "jyte" => array(
    "icon" => "jyte",
    "title" => "Jyte",
    "rule" => array(
      "jyte_fetcher[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "jyxo" => array(
    "icon" => "jyxo",
    "title" => "Jyxo",
    "rule" => array(
      "Jyxobot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "keywen" => array(
    "icon" => "keywen",
    "title" => "Keywen",
    "rule" => array(
      "EasyDL[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "kinja" => array(
    "icon" => "kinja",
    "title" => "Kinja",
    "rule" => array(
      "kinjabot[ /]([0-9.]{1,10})" => "\\1",
      "^kinjabot" => ""
    )
  ),
  "lachesis" => array(
    "icon" => "robot",
    "title" => "Lachesis",
    "rule" => array(
      "lachesis" => ""
    )
  ),
  "lanshan" => array(
    "icon" => "robot",
    "title" => "Lachesis",
    "rule" => array(
      "lanshanbot[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "lapozz" => array(
    "icon" => "lapozz",
    "title" => "Lapozz",
    "rule" => array(
      "LapozzBot[/ ]?([0-9.]{1,10})" => "\\1"
    )
  ),
  "larbin" => array(
    "icon" => "robot",
    "title" => "Larbin",
    "rule" => array(
      "larbin[_/ ]?([0-9.]{1,10})" => "\\1"
    )
  ),
  "leechget" => array(
    "icon" => "leechget",
    "title" => "LeechGet",
    "rule" => array(
      "^LeechGet[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "liferea" => array(
    "icon" => "liferea",
    "title" => "Liferea",
    "rule" => array(
      "Liferea[ /]([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "linkman" => array(
    "icon" => "linkman",
    "title" => "Linkman",
    "rule" => array(
      "\(compatible; Linkman\)" => ""
    )
  ),
  "linkcheck" => array(
    "icon" => "linkcheck",
    "title" => "Linkcheck",
    "rule" => array(
      "checklink[ /]([0-9.]{1,10})" => "\\1",
      "Link[ \-]?(Chec(k|ker)|Val(et|idator))" => ""
    )
  ),
  "linkru" => array(
    "icon" => "robot",
    "title" => "Link.RU",
    "rule" => array(
      "^Link.RU bot" => ""
    )
  ),
  "linkssql" => array(
    "icon" => "robot",
    "title" => "Links SQL",
    "rule" => array(
      "links sql" => ""
    )
  ),
  "linksweeper" => array(
    "icon" => "robot",
    "title" => "Link Sweeper",
    "rule" => array(
      "LinkSweeper[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "linkwalker" => array(
    "icon" => "robot",
    "title" => "Link Walker",
    "rule" => array(
      "^LinkWalker" => ""
    )
  ),
  "livedoor" => array(
    "icon" => "livedoor",
    "title" => "Livedoor",
    "rule" => array(
      "^Livedoor( SF( - California Crawl)?|Checkers)[ /]" => ""
    )
  ),
  "livejournal" => array(
    "icon" => "livejournal",
    "title" => "Live Journal",
    "rule" => array(
      "^LiveJournal\.com" => ""
    )
  ),
  "lmspider" => array(
    "icon" => "robot",
    "title" => "Lmspider",
    "rule" => array(
      "^lmspider" => ""
    )
  ),
  "locators" => array(
    "icon" => "robot",
    "title" => "Locaters",
    "rule" => array(
      "^FiNDoBot[/ ]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "look" => array(
    "icon" => "look",
    "title" => "Look",
    "rule" => array(
      "www\.look\.com" => "",
      "Lookbot" => ""
    )
  ),
  "loop" => array(
    "icon" => "loop",
    "title" => "LOOP",
    "rule" => array(
      "NetResearchServer[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "looksmart" => array(
    "icon" => "looksmart",
    "title" => "Looksmart",
    "rule" => array(
      "looksmart-sv-fw" => ""
    )
  ),
  "lotkyll" => array(
    "icon" => "robot",
    "title" => "Lotkyll",
    "rule" => array(
      "Lotkyll" => ""
    )
  ),
  "lwp" => array(
    "icon" => "robot",
    "title" => "lwp",
    "rule" => array(
      "lwp(-trivial|::simple)[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "lycos" => array(
    "icon" => "lycos",
    "title" => "Lycos",
    "rule" => array(
      "Lycos_Spider_" => ""
    )
  ),
  "magpierss" => array(
    "icon" => "robot",
    "title" => "MagpieRSS",
    "rule" => array(
      "MagpieRSS" => ""
    )
  ),
  "mailsweeper" => array(
    "icon" => "robot",
    "title" => "Mail Sweeper",
    "rule" => array(
      "Mail[ \-]?Sweeper" => ""
    )
  ),
  "marvin" => array(
    "icon" => "robot",
    "title" => "Marvin",
    "rule" => array(
      "^Marvin" => ""
    )
  ),
  "matkurja" => array(
    "icon" => "matkurja",
    "title" => "Mat'Kurja",
    "rule" => array(
      "Mosad[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mavicanet" => array(
    "icon" => "mavicanet",
    "title" => "Mavicanet",
    "rule" => array(
      "Mavicanet robot" => ""
    ),
    "uri" => ""
  ),
  "mediapartners" => array(
    "icon" => "google",
    "title" => "Mediapartners",
    "rule" => array (
      "Mediapartners-Google[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mercator" => array(
    "icon" => "robot",
    "title" => "Mercator",
    "rule" => array(
      "Mercator" => ""
    )
  ),
  "metager" => array(
    "icon" => "metager",
    "title" => "MetaGer",
    "rule" => array(
      "MetaGer" => ""
    )
  ),
  "metamedic" => array(
    "icon" => "metamedic",
    "title" => "MetaMedic",
    "rule" => array(
      "MediBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mirago" => array(
    "icon" => "mirago",
    "title" => "Mirago",
    "rule" => array(
      "Mirago" => ""
    )
  ),
  "missigua" => array(
    "icon" => "robot",
    "title" => "Missigua Locator",
    "rule" => array(
      "Missigua Locator[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "miva" => array(
    "icon" => "miva",
    "title" => "Miva",
    "rule" => array(
      "AlgoFeedback@miva\.com" => ""
    ),
    "uri" => "http://www.miva.com/"
  ),
  "mj12" => array(
    "icon" => "mj12",
    "title" => "Majestic-12",
    "rule" => array(
      "Mj12bot[ /]v?([0-9.]{1,10})" => "\\1"
    )
  ),
  "mnogo" => array(
    "icon" => "robot",
    "title" => "Mnogo",
    "rule" => array(
      "Mnogosearch[ /\-]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mojeekbot" => array(
    "icon" => "robot",
    "title" => "MojeekBot",
    "rule" => array(
      "MojeekBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "momspider" => array(
    "icon" => "robot",
    "title" => "MOM Spider",
    "rule" => array(
      "MOMspider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "moreover" => array(
    "icon" => "moreover",
    "title" => "Moreover",
    "rule" => array(
      "^Moreoverbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "movabletype" => array(
    "icon" => "movabletype",
    "title" => "Movable Type",
    "rule" => array(
      "MovableType[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mozdex" => array(
    "icon" => "mozdex",
    "title" => "MozDex",
    "rule" => array(
      "mozDex[ /]([0-9.]{1,6}(-dev)?)" => "\\1"
    )
  ),
  "mqbot" => array(
    "icon" => "robot",
    "title" => "MQbot",
    "rule" => array(
      "MQbot" => ""
    )
  ),
  "msnbot" => array(
    "icon" => "msn",
    "title" => "MSN",
    "rule" => array(
      "MSN(BOT|PTC)[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "mslivebot" => array(
    "icon" => "livesearch",
    "title" => "MS Live Search",
    "rule" => array(
      "MSNBOT-(MEDIA|PRODUCTS)[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "msproxy" => array(
    "icon" => "robot",
    "title" => "MSProxy",
    "rule" => array(
      "MSProxy[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "mswebdav" => array(
    "icon" => "robot",
    "title" => "MS-WebDAV",
    "rule" => array(
      "Microsoft[ \-]?WebDAV[ \-]?MiniRedir" => ""
    )
  ),
  "mticon" => array(
    "icon" => "robot",
    "title" => "MTIcon",
    "rule" => array(
      "MTIcon[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "myrss" => array(
    "icon" => "robot",
    "title" => "MyRSS",
    "rule" => array(
      "MyRSS.jp[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "multimap" => array(
    "icon" => "robot",
    "title" => "Multimap",
    "rule" => array(
      "Multimap Geotag Blog Parser[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "nameprotect" => array(
    "icon" => "nameprotect",
    "title" => "Name Protect",
    "rule" => array(
      "NPBot" => ""
    )
  ),
  "nationaldirectory" => array(
    "icon" => "robot",
    "title" => "National Directory",
    "rule" => array(
      "NationalDirectory-WebSpider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "natsumican" => array(
    "icon" => "robot",
    "title" => "Natsu Mican",
    "rule" => array(
      "NATSU[ \-]MICAN[/ ]([0-9a-z.]{1,10})" => "\\1",
    )
  ),
  "naverbot" => array(
    "icon" => "naverbot",
    "title" => "Naver",
    "rule" => array(
      "NaverBot([_\-]dloader)?[/ \-]([0-9.]{1,10})" => "\\2",
      "Naver(Bot)?" => ""
    )
  ),
  "neomo" => array(
    "icon" => "robot",
    "title" => "Neomo",
    "rule" => array(
      "Francis[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "nessus" => array(
    "icon" => "nessus",
    "title" => "Nessus",
    "rule" => array(
      "Nessus\)$" => ""
    )
  ),
  "netants" => array(
    "icon" => "netants",
    "title" => "NetAnts",
    "rule" => array(
      "NetAnts[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "netcraft" => array(
    "icon" => "netcraft",
    "title" => "Netcraft",
    "rule" => array(
      "netcraft" => ""
    )
  ),
  "netmechanic" => array(
    "icon" => "netmechanic",
    "title" => "NetMechanic",
    "rule" => array(
      "NetMechanic[ /V]{1,5}([0-9.]{1,10})" => "\\1"
    )
  ),
  "netnewswire" => array(
    "icon" => "netnewswire",
    "title" => "NetNewsWire",
    "rule" => array(
      "NetNewsWire[/ ]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "netnose" => array(
    "icon" => "netnose",
    "title" => "NetNose",
    "rule" => array(
      "NetNose[ \-]Crawler[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "netoskop" => array(
    "icon" => "robot",
    "title" => "Netoskop",
    "rule" => array(
      "netoskop" => ""
    )
  ),
  "netscapeproxy" => array(
    "icon" => "netscape",
    "title" => "Netscape Proxy",
    "rule" => array(
      "Netscape\-Proxy[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "netvibes" => array(
    "icon" => "netvibes",
    "title" => " Netvibes",
    "rule" => array(
      "^Netvibes" => "\\1"
    )
  ),
  "newsfire" => array(
    "icon" => "newsfire",
    "title" => "NewsFire",
    "rule" => array(
      "NewsFire[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "newsgator" => array(
    "icon" => "newsgator",
    "title" => "NewsGator",
    "rule" => array(
      "NewsGato(r|rOnline)[/ ]([0-9.]{1,10})" => "\\2"
    )
  ),
  "newzcrawler" => array(
    "icon" => "newzcrawler",
    "title" => "NewzCrawler",
    "rule" => array(
      "NewzCrawler[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "nextopia" => array(
    "icon" => "robot",
    "title" => "Nextopia",
    "rule" => array(
      "^NextopiaBOT.*[v ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => ""
  ),
  "ngsearch" => array(
    "icon" => "ngsearch",
    "title" => "NG Search",
    "rule" => array(
      "NG-Search[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "nimble" => array(
    "icon" => "other",
    "title" => "Nimble",
    "rule" => array(
      "NimbleCrawler[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "noxtrum" => array(
    "icon" => "noxtrum",
    "title" => "noXtrum",
    "rule" => array(
      "noxtrumbot[/ ]?([0-9.]{1,10})" => "\\1"
    )
  ),
  "noviforum" => array(
    "icon" => "noviforum",
    "title" => "Noviforum",
    "rule" => array(
      "TridentSpider[/ ]?([0-9.]{1,10})" => "\\1"
    )
  ),
  "obidosbot" => array(
    "icon" => "robot",
    "title" => "Bookwatch",
    "rule" => array(
      "obidos[ \-]?bot" => ""
    )
  ),
  "objectssearch" => array(
    "icon" => "robot",
    "title" => "Objects Search",
    "rule" => array(
      "ObjectsSearch[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "omea" => array(
    "icon" => "omea",
    "title" => "Omea Reader",
    "rule" => array(
      "Omea Reader[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "omni" => array(
    "icon" => "robot",
    "title" => "Omni Explorer",
    "rule" => array(
      "OmniExplorer_Bot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "onet" => array(
    "icon" => "onet",
    "title" => "Onet",
    "rule" => array(
      "OnetSzukaj[ /]([0-9.]{1,10})" => "\\1",
      "^Onet\.pl" => ""
    )
  ),
  "openfind" => array(
    "icon" => "openfind",
    "title" => "Openfind",
    "rule" => array(
      "openbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "organica" => array(
    "icon" => "robot",
    "title" => "Organica",
    "rule" => array(
      "crawler@organica\.us" => ""
    )
  ),
  "outfox" => array(
    "icon" => "robor",
    "title" => "Outfox Melon",
    "rule" => array(
      "OutfoxMelonBot[ /]([0-9.]{1,10})" => "\\1",
      "OutfoxBot[ /]([0-9.]{1,10})" => "\\1",
    ),
    "uri" => ""
  ),
  "overture" => array(
    "icon" => "overture",
    "title" => "Overture",
    "rule" => array(
      "Overture[ \-]?WebCrawler" => ""
    )
  ),
  "pagebytes" => array(
    "icon" => "robot",
    "title" => "PageBites",
    "rule" => array(
      "^PageBitesHyperBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "panopeabot" => array(
    "icon" => "robot",
    "title" => "PanopeaBot",
    "rule" => array(
      "PanopeaBot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "peerbot" => array(
    "icon" => "peerbot",
    "title" => "Peerbot",
    "rule" => array(
      "^PEERbot" => ""
    )
  ),
  "pingdom" => array(
    "icon" => "pingdom",
    "title" => "Pingdom",
    "rule" => array(
      "^Pingdom GIGRIB v([0-9.]{1,10})" => "\\2"
    ),
    "uri" => "www.pingdom.com"
  ),
  "php" => array(
    "icon" => "php",
    "title" => "PHP",
    "rule" => array(
      "^PHP[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "phpdig" => array(
    "icon" => "robot",
    "title" => "PhpDig",
    "rule" => array(
      "^PhpDig[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "pictureofinternet" => array(
    "icon" => "robot",
    "title" => "PictureOfInternet",
    "rule" => array(
      "^PictureOfInternet[ /]([0-9.]{1,10})" => ""
    )
  ),
  "pinseri" => array(
    "icon" => "pinseri",
    "title" => "Pinseri",
    "rule" => array(
      "www\.pinseri\.com/bloglist" => ""
    )
  ),
  "planet" => array(
    "icon" => "planet",
    "title" => "Planet",
    "rule" => array(
      "Planet[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "plantynet" => array(
    "icon" => "robot",
    "title" => "PlantyNet",
    "rule" => array(
      "PlantyNet_WebRobot[_ /]V?([0-9.]{1,10})" => "\\1"
    )
  ),
  "pluck" => array(
    "icon" => "pluck",
    "title" => "Pluck",
    "rule" => array(
      "PluckFeedCrawler[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "plsearch" => array(
    "icon" => "plsearch",
    "title" => "PlanetSearch",
    "rule" => array(
      "fido[ /]([0-9.]{1,10}) Harvest" => "\\1"
    )
  ),
  "poe" => array(
    "icon" => "robot",
    "title" => "POE-Component",
    "rule" => array(
      "^POE-Component-Client-HTTP[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "pogodak" => array(
    "icon" => "pogodak",
    "title" => "Pogodak",
    "rule" => array(
      "Pogodak\.hr[/ ]?([0-9.]{1,10})" => "\\1"
    )
  ),
  "poodle" => array(
    "icon" => "robot",
    "title" => "Poodle predictor",
    "rule" => array(
      "Poodle[ \-]?predictor" => ""
    )
  ),
  "pompos" => array(
    "icon" => "pompos",
    "title" => "Pompos",
    "rule" => array(
      "Pompos[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "popdex" => array(
    "icon" => "robot",
    "title" => "Popdexter",
    "rule" => array(
      "Popdexter" => ""
    )
  ),
  "powermarks" => array(
    "icon" => "robot",
    "title" => "Powermarks",
    "rule" => array(
      "Powermarks[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "proxycache" => array(
    "icon" => "robot",
    "title" => "Proxy Cache",
    "rule" => array(
      "^Mozilla/[0-9.]{1,10} \(compatible\;\)$" => ""
    )
  ),
  "proxyhunter" => array(
    "icon" => "robot",
    "title" => "ProxyHunter",
    "rule" => array(
      "ProxyHunter" => ""
    )
  ),
  "psbot" => array(
    "icon" => "picsearch",
    "title" => "PicSearch",
    "rule" => array(
      "^psbot" => ""
    )
  ),
  "pubsub" => array(
    "icon" => "pubsub",
    "title" => "PubSub",
    "rule" => array(
      "^PubSub-RSS-Reader[ /]([0-9.]{1,10})" => "\\1",
      "^PubSub\.com" => ""
      )
  ),
  "pukiwiki" => array(
    "icon" => "pukiwiki",
    "title" => "PukiWiki",
    "rule" => array(
      "PukiWiki[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "pxys" => array(
    "icon" => "robot",
    "title" => "PXYS",
    "rule" => array(
      "^pxys" => ""
    )
  ),
  "quepasa" => array(
    "icon" => "quepasa",
    "title" => "Quepasa",
    "rule" => array(
      "Quepasa[ \-]?Creep" => ""
    )
  ),
  "questfinder" => array(
    "icon" => "robot",
    "title" => "QuestFinder",
    "rule" => array(
      "www\.questfinder\.com" => ""
    )
  ),
  "rambler" => array(
    "icon" => "rambler",
    "title" => "Rambler",
    "rule" => array(
      "StackRambler[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "ramiba" => array(
    "icon" => "robot",
    "title" => "ramiba",
    "rule" => array(
      "^ramiba(-bot)?" => "\\1"
    )
  ),
  "repia" => array(
    "icon" => "robot",
    "title" => "Repia",
    "rule" => array(
      "webmaster@repia\.com" => ""
    )
  ),
  "robozilla" => array(
    "icon" => "robot",
    "title" => "Robozilla",
    "rule" => array(
      "Robozilla" => ""
    )
  ),
  "rojo" => array(
    "icon" => "rojo",
    "title" => "Rojo",
    "rule" => array(
      "Rojo[ /]([0-9.]{1,10})" => "\\1",
    )
  ),
  "rssbot" => array(
    "icon" => "robot",
    "title" => "rss-bot",
    "rule" => array(
      "rss-bot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "rssbandit" => array(
    "icon" => "rssbandit",
    "title" => "RssBandit",
    "rule" => array(
      "RssBandit[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "rssimages" => array(
    "icon" => "robot",
    "title" => "rssImages",
    "rule" => array(
      "rssImagesBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "rssowl" => array(
    "icon" => "rssowl",
    "title" => "RSSOwl",
    "rule" => array(
      "RSSOwl[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "rssreader" => array(
    "icon" => "robot",
    "title" => "RssReader",
    "rule" => array(
      "RssReader[ /]([0-9.]{1,10})" => ""
    )
  ),
  "rufusbot" => array(
    "icon" => "robot",
    "title" => "RufusBot",
    "rule" => array(
      "RufusBot" => ""
    )
  ),
  "sage" => array(
    "icon" => "robot",
    "title" => "Sage",
    "rule" => array(
      "\(Sage\)" => ""
    )
  ),
  "sanszbot" => array(
    "icon" => "robot",
    "title" => "Sansz",
    "rule" => array(
      "SanszBot" => ""
    ),
    "uri" => ""
  ),
  "saucereader" => array(
    "icon" => "saucereader",
    "title" => "Sauce Reader",
    "rule" => array(
      "Sauce[ ]?Reader[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sbider" => array(
    "icon" => "sbider",
    "title" => "SBIder",
    "rule" => array(
    "SBIder[/ ]([0-9.]{1,10})" => "\\1",
    "SBIder[/ ]SBIder.([0-9.]{1,10})" => "\\1"
    )
  ),
  "scirus" => array(
    "icon" => "robot",
    "title" => "Scirus",
    "rule" => array(
    "FAST-WebCrawler/[0-9a-z.]{1,10}/Scirus" => ""
    )
  ),
  "scrubby" => array(
    "icon" => "scrubby",
    "title" => "Scrubby",
    "rule" => array(
      "Scrubby[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sdm" => array(
    "icon" => "sdm",
    "title" => "SUN Download Manager",
    "rule" => array(
      "Sun Download Manager[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sealinks" => array(
    "icon" => "robot",
    "title" => "Sea Links",
    "rule" => array(
    "SEA-Links( HTML-Scanner Pingoo\!)?[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "searchch" => array(
    "icon" => "robot",
    "title" => "Search.ch",
    "rule" => array(
      "search\.ch[ /]?V?([0-9.]{1,10})" => "\\1"
    )
  ),
  "searchthruus" => array(
    "icon" => "robot",
    "title" => "SearchThruUs",
    "rule" => array(
      "www\.unitek-systems\.co\.uk[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "securecomputing" => array(
    "icon" => "robot",
    "title" => "Secure Computing",
    "rule" => array(
      "securecomputing" => ""
    )
  ),
  "seekport" => array(
    "icon" => "seekport",
    "title" => "Seekport",
    "rule" => array(
      "Seekbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "semanticdiscovery" => array(
    "icon" => "robot",
    "title" => "Semantic Discovery",
    "rule" => array(
      "semanticdiscovery[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "seznam" => array(
    "icon" => "seznam",
    "title" => "Seznam",
    "rule" => array(
      "SeznamBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "shareware" => array(
    "icon" => "robot",
    "title" => "Shareware",
    "rule" => array(
      "Program[ \-]?Shareware[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sharpreader" => array(
    "icon" => "sharpreader",
    "title" => "SharpReader",
    "rule" => array(
      "SharpReader[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sherlockspider" => array(
    "icon" => "robot",
    "title" => "Sherlock Spider",
    "rule" => array(
      "sherlock_spider" => ""
    )
  ),
  "shim" => array(
    "icon" => "robot",
    "title" => "Shim Crawler",
    "rule" => array(
      "shim[ \-]crawler" => ""
    )
  ),
  "shopwiki" => array(
    "icon" => "shopwiki",
    "title" => "ShopWiki",
    "rule" => array(
      "^ShopWiki[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "siege" => array(
    "icon" => "robot",
    "title" => "Siege",
    "rule" => array(
      "Siege[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "siets" => array(
    "icon" => "robot",
    "title" => "Siets",
    "rule" => array(
      "SietsCrawler[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "simpy" => array(
    "icon" => "simpy",
    "title" => "Simpy",
    "rule" => array(
      "^(argus|simpy)[ /]([0-9.]{1,10})" => "\\2",
    )
  ),
  "singingfish" => array(
    "icon" => "singingfish",
    "title" => "SingingFish",
    "rule" => array(
      "asterias[ /]([0-9.]{1,10})" => "\\1",
      "asterias" => ""
    )
  ),
  "sirobot" => array(
    "icon" => "robot",
    "title" => "SiroBot",
    "rule" => array(
      "sirobot" => ""
    )
  ),
  "sitebar" => array(
    "icon" => "sitebar",
    "title" => "SiteBar",
    "rule" => array(
      "SiteBar[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "sitesell" => array(
    "icon" => "sitesell",
    "title" => "SiteSell",
    "rule" => array(
      "SBIder[/ ]([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "sitespider" => array(
    "icon" => "robot",
    "title" => "SiteSpider",
    "rule" => array(
      "^SiteSpider" => ""
    )
  ),
  "slugch" => array(
    "icon" => "robot",
    "title" => "slugch",
    "rule" => array(
      "^slug\.ch crawl ([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "snoopy" => array(
    "icon" => "robot",
    "title" => "Snoopy",
    "rule" => array(
      "^Snoopy[ &/v]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "slider" => array(
    "icon" => "robot",
    "title" => "Slider",
    "rule" => array(
      "^Slider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "soegning" => array(
    "icon" => "soegning",
    "title" => "S&oslash;gning",
    "rule" => array(
      "soegning\.dk[/ ]spider[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "soft411" => array(
    "icon" => "soft411",
    "title" => "Soft411",
    "rule" => array(
      "SOFT411 Directory" => ""
    )
  ),
  "sohu" => array(
    "icon" => "robot",
    "title" => "Sohu",
    "rule" => array(
      "sohu[ \-](agent|search)" => ""
    )
  ),
  "souppot" => array(
    "icon" => "robot",
    "title" => "SoupPot",
    "rule" => array(
      "SoupPotBot" => ""
    )
  ),
  "specificmedia" => array(
    "icon" => "robot",
    "title" => "Specific Media",
    "rule" => array(
      "^SMBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "spherescout" => array(
    "icon" => "robot",
    "title" => "Sphere Scout",
    "rule" => array(
      "^Sphere Scout[ &/v]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "spurlbot" => array(
    "icon" => "robot",
    "title" => "SpurlBot",
    "rule" => array(
      "SpurlBot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "stardownloader" => array(
    "icon" => "stardownloader",
    "title" => "Star Downloader",
    "rule" => array(
      "^Star Downloader( Pro)?" => ""
    )
  ),
  "steeler" => array(
    "icon" => "robot",
    "title" => "Steeler",
    "rule" => array(
      "Steeler[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "suchende" => array(
    "icon" => "robot",
    "title" => "suchen.de",
    "rule" => array(
      "^gonzo([0-9]{1,2}).*www.suchen.de" => "\\1"
    )
  ),
  "sunrise" => array(
    "icon" => "sunrise",
    "title" => "Sunrise",
    "rule" => array(
      "Sunrise[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "superbot" => array(
    "icon" => "superbot",
    "title" => "SuperBot",
    "rule" => array(
      "SuperBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "surfcontrol" => array(
    "icon" => "robot",
    "title" => "SurfControl",
    "rule" => array(
      "SurfControl" => ""
    )
  ),
  "surfnet" => array(
    "icon" => "robot",
    "title" => "SURFnet",
    "rule" => array(
      "AVSearch[ \-]([0-9.]{1,10})" => "\\1"
    )
  ),
  "surveybot" => array(
    "icon" => "robot",
    "title" => "Whois Survey",
    "rule" => array(
      "SurveyBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "syndic8" => array(
    "icon" => "syndic8",
    "title" => "Syndic8",
    "rule" => array(
      "Syndic8[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "www.syndic8.com"
  ),
  "syndicatie" => array(
    "icon" => "robot",
    "title" => "Syndicatie.nl",
    "rule" => array(
      "Syndicatie\.nl robot v ([0-9.]{1,10})" => "\\1",
      "Syndicatie\.nl robot;" => ""
    )
  ),
  "synoo" => array(
    "icon" => "robot",
    "title" => "SynooBot",
    "rule" => array(
      "SynooBot[ /]([0-9.]{1,10})" => "\\1",
    )
  ),
  "szukacz" => array(
    "icon" => "szukacz",
    "title" => "Szukacz",
    "rule" => array(
      "Szukacz[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "tamu" => array(
    "icon" => "robot",
    "title" => "Tamu Crawler",
    "rule" => array(
      "IRLbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "technorati" => array(
    "icon" => "technorati",
    "title" => "Technorati",
    "rule" => array(
      "Technoratibot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "teleport" => array(
    "icon" => "teleport",
    "title" => "Teleport",
    "rule" => array(
      "Teleport[ \-]?Pro" => ""
    )
  ),
  "terrar" => array(
    "icon" => "robot",
    "title" => "Terrar",
    "rule" => array(
      "^Fresh Search :: Terrar" => ""
    )
  ),
  "thumbnailscz" => array(
    "icon" => "robot",
    "title" => "thumbnails.cz",
    "rule" => array(
      "^thumbnail\.cz robot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "thumbshots" => array(
    "icon" => "robot",
    "title" => "thumbshots",
    "rule" => array(
      "^thumbshots.*(Version: |v)([0-9.]{2,10})e" => "\\1",
      "^thumbshots-de" => ""
    ),
    "uri" => "http://www.thumbshots.de"
  ),
  "thunderbird" => array(
    "icon" => "thunderbird",
    "title" => "Thunderbird",
    "rule" => array(
      "Thunderbird[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "thunderstone" => array(
    "icon" => "thunderstone",
    "title" => "Thunderstone",
    "rule" => array(
      "T-H-U-N-D-E-R-S-T-O-N-E" => ""
    )
  ),
  "timbobot" => array(
    "icon" => "robot",
    "title" => "timboBot",
    "rule" => array(
      "timboBot" => ""
    )
  ),
  "trayce" => array(
    "icon" => "robot",
    "title" => "trayce",
    "rule" => array(
      "traycebot[ /]([0-9a-z.\-]{1,10})" => "\\1"
    )
  ),
  "tricus" => array(
    "icon" => "robot",
    "title" => "Tricus",
    "rule" => array(
      "B_l_i_t_z_B_O_T_@_t_r_i_c_u_s_\._c_o_m" => ""
    )
  ),
  "topicblogs" => array(
    "icon" => "robot",
    "title" => "Topicblogs",
    "rule" => array(
      "topicblogs[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "turnitin" => array(
    "icon" => "turnitin",
    "title" => "Turnitin",
    "rule" => array(
      "TurnitinBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "tutorgig" => array(
    "icon" => "robot",
    "title" => "TutorGig",
    "rule" => array(
      "TutorGig(Bot)?[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "twiceler" => array(
    "icon" => "robot",
    "title" => "twiceler",
    "rule" => array(
      "Twiceler" => ""
    )
  ),
  "typepad" => array(
    "icon" => "typepad",
    "title" => "TypePad",
    "rule" => array(
      "TypePad/([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "udmsearch" => array(
    "icon" => "robot",
    "title" => "UdmSearch",
    "rule" => array(
      "UdmSearch[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "ultraseek" => array(
    "icon" => "robot",
    "title" => "Ultraseek",
    "rule" => array(
      "Ultraseek" => ""
    )
  ),
  "ultraspider" => array(
    "icon" => "robot",
    "title" => "UltraSpider",
    "rule" => array(
      "UltraSpider3000[/ ]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.search.ch"
  ),
  "unchaos" => array(
    "icon" => "robot",
    "title" => "Unchaos",
    "rule" => array(
      "unchaos_crawler[_ /]([0-9.]{1,10})" => "\\1",
      "unchaos bot" => ""
    )
  ),
  "unido" => array(
    "icon" => "robot",
    "title" => "unido",
    "rule" => array(
      "^unido-bot" => "\\1"
    ),
    "uri" => "http://mobicom.cs.uni-dortmund.de/bot.html"
  ),
  "updated" => array(
    "icon" => "robot",
    "title" => "Updated",
    "rule" => array(
      "updated[ /]([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "urlcontr" => array(
    "icon" => "robot",
    "title" => "MS URL Control",
    "rule" => array(
      "Microsoft URL[ \-]?Control" => "\\1"
    )
  ),
  "urlscope" => array(
    "icon" => "robot",
    "title" => "UrlScope",
    "rule" => array(
      "UrlScope" => ""
    )
  ),
  "urltrends" => array(
    "icon" => "urltrends",
    "title" => "urltrends",
    "rule" => array(
      "Snappy/([0-9.]{1,10})" => "\\1",
    )
  ),
  "vagabondo" => array(
    "icon" => "wiseguys",
    "title" => "WiseGuys",
    "rule" => array(
      "Vagabondo[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "validator" => array(
    "icon" => "validator",
    "title" => "W3C Validator",
    "rule" => array(
      "W3C_Validator[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "verzamelgids" => array(
    "icon" => "validator",
    "title" => "Verzamelgids",
    "rule" => array(
      "Verzamelgids[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.verzamelgids.nl/"
  ),
  "vindex" => array(
    "icon" => "vindex",
    "title" => "Vindex",
    "rule" => array(
      "Vindex[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "voila" => array(
    "icon" => "voila",
    "title" => "Voila",
    "rule" => array(
      "VoilaBot[ /]?[a-z ]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "wagger" => array(
    "icon" => "robot",
    "title" => "Wagger",
    "rule" => array(
      "^Waggr" => ""
    ),
    "uri" => "http://www.waggr.com/"
  ),
  "watson" => array(
    "icon" => "addy",
    "title" => "Dr.Watson",
    "rule" => array(
      "Watson[ /]([0-9.]{1,10})" => "\\1",
      "watson\.addy\.com" => ""
    )
  ),
  "wavefire" => array(
    "icon" => "robot",
    "title" => "Wavefire",
    "rule" => array(
      "^Wavefire[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "waypath" => array(
    "icon" => "waypath",
    "title" => "Waypath",
    "rule" => array(
      "Waypath[ \-]?Scout" => "",
      "Waypath (development )?crawler" => ""
    )
  ),
  "webcapture" => array(
    "icon" => "robot",
    "title" => "WebCapture",
    "rule" => array(
      "WebCapture[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webcollage" => array(
    "icon" => "robot",
    "title" => "Webcollage",
    "rule" => array(
      "webcollage" => ""
    )
  ),
  "webcopier" => array(
    "icon" => "webcopier",
    "title" => "WebCopier",
    "rule" => array(
      "WebCopier[/ ]v?([0-9.]{1,10})" => "\\1"
    )
  ),
  "webcrawl" => array(
    "icon" => "robot",
    "title" => "WebCrawl",
    "rule" => array(
      "webcrawl\.net" => ""
    )
  ),
  "webmin" => array(
    "icon" => "webmin",
    "title" => "Webmin",
    "rule" => array(
      "^webmin" => ""
    )
  ),
  "webmon" => array(
    "icon" => "webmon",
    "title" => "Webmon",
    "rule" => array(
      "WebMon[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webpix" => array(
    "icon" => "webpix",
    "title" => "WebPix",
    "rule" => array(
      "WebPix[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webresult" => array(
    "icon" => "robot",
    "title" => "Webresult",
    "rule" => array(
      "Der webresult\.de Robot" => ""
    )
  ),
  "webring" => array(
    "icon" => "robot",
    "title" => "Webring Checker",
    "rule" => array(
      "WebRingChecker[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webox" => array(
    "icon" => "robot",
    "title" => " WeBoX",
    "rule" => array(
      "WeBoX[/ ]([0-9.]{1,10})" => ""
    ),
    "uri" => ""
  ),
  "websense" => array(
    "icon" => "websense",
    "title" => "Websense",
    "rule" => array(
      "(Sqworm|websense|Konqueror/3\.(0|1)(\-rc[1-6])?; i686 Linux; 2002[0-9]{4})" => ""
    ),
    "uri" => ""
  ),
  "websquash" => array(
    "icon" => "websquash",
    "title" => "Websquash",
    "rule" => array(
      "webs(quash\.com|ite[ \-]?Monitor)" => ""
    )
  ),
  "webstripper" => array(
    "icon" => "robot",
    "title" => "WebStripper",
    "rule" => array(
      "WebStripper[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "webzip" => array(
    "icon" => "webzip",
    "title" => "WebZIP",
    "rule" => array(
      "Web[ \-]?ZIP[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wep" => array(
    "icon" => "robot",
    "title" => "WEP Search",
    "rule" => array(
      "WEP Search[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "westwind" => array(
    "icon" => "robot",
    "title" => "West Wind Internet Protocols",
    "rule" => array(
      "^West Wind Internet Protocols[ /]([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://www.west-wind.com/wwipstuff.asp"
  ),
  "wget" => array(
    "icon" => "wget",
    "title" => "Wget",
    "rule" => array(
      "Wget[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wmp" => array(
    "icon" => "robot",
    "title" => "WMP",
    "rule" => array(
      "^WMP" => ""
    )
  ),
  "wordpress" => array(
    "icon" => "wordpress",
    "title" => "WordPress",
    "rule" => array(
      "WordPress[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "worldlight" => array(
    "icon" => "worldlight",
    "title" => "WorldLight",
    "rule" => array(
      "^WorldLight" => ""
    )
  ),
  "worqmada" => array(
    "icon" => "robot",
    "title" => "WorQmada",
    "rule" => array(
        "WorQmada[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wotbox" => array(
    "icon" => "wotbox",
    "title" => "Wotbox",
    "rule" => array(
      "Wotbox[ /]?[a-z]*([0-9.]{1,10})" => "\\1"
    )
  ),
  "wp" => array(
    "icon" => "wp",
    "title" => "Wirtualna Polska",
    "rule" => array(
      "NetSprint[ /\-]{1,4}([0-9.]{1,10})" => "\\1"
    ),
    "uri" => "http://wp.pl"
  ),
  "wwgrapevine" => array(
    "icon" => "wwgrapevine",
    "title" => "WWgrapevine",
    "rule" => array(
      "wwgrapevine[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wwwc" => array(
    "icon" => "wwwc",
    "title" => "WWWC",
    "rule" => array(
      "^WWWC[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wwwd" => array(
    "icon" => "robot",
    "title" => "WWWD",
    "rule" => array(
      "^WWWD[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wwweasel" => array(
    "icon" => "wwweasel",
    "title" => "WWWeasel",
    "rule" => array(
      "WWWeasel( Robot)?[/ ]v?([0-9.]{1,10})" => "\\2"
    )
  ),
  "wwwster" => array(
    "icon" => "robot",
    "title" => "WWWster",
    "rule" => array(
      "^wwwster[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "wysigot" => array(
    "icon" => "wysigot",
    "title" => "Wysigot",
    "rule" => array(
      "Wysigot[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "xenu" => array(
    "icon" => "robot",
    "title" => "Xenu Link Sleuth",
    "rule" => array(
      "Xenu(&#039;s)? Link Sleuth[/ ]([0-9a-z.]{1,10})" => "\\1",
      "Xenu_Link_Sleuth_([0-9a-z.]{1,10})" => "\\1"
    )
  ),
  "xmlrpc" => array(
    "icon" => "robot",
    "title" => "Trackback",
    "rule" => array(
      "XMLRPC" => ""
    )
  ),
  "yacy" => array(
    "icon" => "yacy",
    "title" => "Yacy",
    "rule" => array(
      "yacy\.net" => ""
    )
  ),
  "yahoo" => array(
    "icon" => "yahoo",
    "title" => "Yahoo",
    "rule" => array(
      "Yahoo(! ([a-z]{1,3} )?Slurp|-|FeedSeeker)" => "",
      "Yahoo-MMCrawler[/ ]([0-9a-z.]{1,10})" => "\\1",
      "Yahoo-VerticalCrawler-FormerWebCrawler[/ ]([0-9a-z.]{1,10})" => "\\1",
    )
  ),
  "yandex" => array(
    "icon" => "yandex",
    "title" => "Yandex",
    "rule" => array(
      "Yandex[/ ]([0-9.]{1,10})" => "\\1"
    )
  ),
  "yell" => array(
    "icon" => "yell",
    "title" => "Yell",
    "rule" => array(
      "YellCrawl[ /]V?([0-9.]{1,10})" => "\\1",
      "Yellbot[ /]Nutch-([0-9.]{1,10})" => "\\1",
    )
  ),
  "yodao" => array(
    "icon" => "robot",
    "title" => "Yodao",
    "rule" => array(
      "YodaoBot/([0-9.]{1,10})" => "\\1"
    )
  ),
  "yoono" => array(
    "icon" => "robot",
    "title" => "Yoono",
    "rule" => array(
      "Yoono" => ""
    )
  ),
  "zao" => array(
    "icon" => "robot",
    "title" => "Zao",
    "rule" => array(
      "Zao[ /]([0-9.]{1,10})" => "\\1",
      "Zao-crawler" => ""
    )
  ),
  "zealbot" => array(
    "icon" => "zeal",
    "title" => "ZealBot",
    "rule" => array(
      "Zealbot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "zeus" => array(
    "icon" => "robot",
    "title" => "Zeus",
    "rule" => array(
      "Zeus" => ""
    )
  ),
  "zippp" => array(
    "icon" => "robot",
    "title" => "Zippp",
    "rule" => array(
      "ZipppBot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "zoeky" => array(
    "icon" => "robot",
    "title" => "Zoeky",
    "rule" => array(
      "Zoekybot[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "zyborg" => array(
    "icon" => "zyborg",
    "title" => "WiseNutBot",
    "rule" => array(
      "(WISE|Zy)bo(rg|t)[ /]([0-9.]{1,10})" => "\\3"
    )
  ),
// generic stuff
  "httpclient" => array(
    "icon" => "robot",
    "title" => "HTTPClient",
    "rule" => array(
      "HTTP[ \-]?Client[ /]([0-9.]{1,10})" => "\\1",
      "HTTP[ \-]?Client" => ""
    ),
    "uri" => "http://www.innovation.ch/java/HTTPClient/"
  ),
  "java" => array(
    "icon" => "java",
    "title" => "Java",
    "rule" => array(
      "^java[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
  "libfetch" => array(
    "icon" => "robot",
    "title" => "Libfetch",
    "rule" => array(
      "^(fetch )?libfetch[ /]([0-9.]{1,10})" => "\\2"
    ),
    "uri" => "http://www.freebsd.org/"
  ),
  "libwww" => array(
    "icon" => "libwww",
    "title" => "libWWW",
    "rule" => array(
      "^libww(w|w-perl|w-FM)[ /]([0-9.]{1,10})" => "\\2",
      "^libww(w|w-perl|w-FM)" => "",
      "MyApp.*libww(w|w-perl|w-FM)" => ""
    )
  ),
  "nutchorg" => array(
    "icon" => "nutchorg",
    "title" => "Nutch",
    "rule" => array(
      "Nutc(hOrg|hCVS|h)?[ /]([0-9.]{1,10})" => "\\2"
    )
  ),
  "pythonurl" => array(
    "icon" => "robot",
    "title" => "Python-url",
    "rule" => array(
      "Python[ \-]?urllib" => ""
    )
  ),
// Proxy
  "googlewap" => array(
    "icon" => "google",
    "title" => "Google-Feedfetcher",
    "rule" => array (
      "Google CHTML Proxy[ /]([0-9.]{1,10})" => "\\1",
      "Google WAP Proxy[ /]([0-9.]{1,10})" => "\\1"
    )
  ),
// Spam Haverster
  "SPAM" => array(
    "icon" => "nameprotect",
    "title" => "Name Protect",
    "rule" => array(
      "NASA Search[/ ]([0-9.]{1,10})" => "\\1",
      "^PHOTO CHECK" => "",
      "^FOTOCHECKER" => "",
      "^IPTC CHECK" => "",
      "^DataCha0s" => ""
    )
  ),
// Catch up for things we don't know by now
  "robot" => array(
    "icon" => "robot",
    "title" => "Robot",
    "rule" => array(
      "(robot|crawler|spider|harvest|bot)" => ""
    )
  )
);
