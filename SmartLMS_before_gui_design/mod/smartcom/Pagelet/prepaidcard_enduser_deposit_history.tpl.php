<?php if(!is_array($this->histories) || empty($this->histories)): ?>
	<div class="info">This user has not used any prepaidcard</div> 
<?php else:  ?>
<table border="1">
<tr>
<th>Tài khoản nạp thẻ</th>
<th>Số tiền được cộng</th>
<th>Số ngày sử dụng được cộng</th>
<th>Ngày nạp</th>
<th>Seri thẻ</th>
<th>Mệnh giá</th>
</tr>
<? foreach((array)$this->histories as $element): ?>
<tr>
<td><?= $element->depositforusername ?></td>
<td><?= $element->coinvalue ?></td>
<td><?= $element->periodvalue ?></td>
<td><?= $element->useddatetime ?></td>
<td><?= $element->serialno?></td>
<td><?= $element->facevalue ?></td>
</tr>
<? endforeach; ?>
</table>
<?php endif;  ?>