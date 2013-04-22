<?php
$constructor = $model->widgetConstructor();

if(!empty($constructor['title'])){?>
	<h3><?=$constructor['title'];?></h3>
	<hr>
<?}
if(!empty($constructor['description'])){?>
	<p><?=$constructor['description'];?></p>
<?php }

$this->widget($constructor['class'],$constructor['options']);