<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); ?>

<?php foreach ($this->arrayData as $item): ?>
    <a href = '/profile/<?= $item['u'] ?>' title="<?= $item['u'] ?>"><img src="<?= Config::AVATAR_URL . strtolower($item['u']) ?>.gif" width="<?= Config::AVATARSMALLSIZE ?>" class="icon" align="absbottom" /></a>
<?php endforeach; ?>