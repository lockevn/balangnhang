<?php

header('Content-Type: text/css'); 
header('Expires: ' . date('r', time() + 86400));

?>

.main_title_<?php echo $_GET['image']; ?> {
	background-image: url('../images/area_title/<?php echo $_GET['image']; ?>.gif');
}