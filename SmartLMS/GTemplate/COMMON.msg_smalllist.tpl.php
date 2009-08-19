<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

?>

<?php if(!is_array($this->arrayData)): ?>
    <div class="box">Không có tin nào</div>
<?php else:  ?>
    <?php foreach ($this->arrayData as $item): ?>
    <div class="">
        <a href="/profile/<?php echo $item['fromu']; ?>"><img src="<?= Config::AVATAR_URL.strtolower($item['fromu']); ?>.gif" width="30px" /></a>
        <a href="/profile/<?php echo $item['fromu']; ?>" class=""><?= $item['fromu']; ?></a>&nbsp;<?= $item['c']; ?>
        <?php if($item['img']): ?>
                <a href="<?=Config::IMG_URL . $item['img']; ?>" rel="prettyPhoto"><img src="<?= Config::IMG_URL .'thumb-'. $item['img']; ?>" width="<?= Config::POST_IMG_WIDTH ?>" /></a>
        <?php endif; ?>

        <?php if($item['numofrelate'] > 0): ?>
        <a href="/index.php?mod=view_msg_tree&pid=<?=$item['fromid']; ?>&mguid=<?php echo $item['guid']; ?>"><img src="/images/icons/icon.talk.png" alt="Câu chuyện" /></a>
        <?php endif; ?>
        <?= $item['dt']; ?> gửi từ <a href="/device.php?d=<?=$item['devicename']; ?>" target="_blank"><?=$item['devicename']; ?></a>
    </div>
<?php endforeach; ?>
<?php endif;  ?>