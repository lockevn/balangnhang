<link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/css/print.css" type="text/css" media="print">
<!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

<div id="pnlOnlinelist" class="notice">
<ul>
<? foreach((array)$this->onlineUsers as $element): ?>
<li><?= $element->username ?></li>
<? endforeach; ?> 
</ul>
</div>

<div id="pnlRealtimeactivity" class="box">Real time activity</div>

<div id="pnlChat" class="box">Chat panel</div>