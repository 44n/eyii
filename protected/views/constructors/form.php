<div class="row">
<?if(!empty($this->layoutData['pageMenu'])){?>
<div class="span2 well affix" style="padding: 8px 0;">
<?$this->widget('bootstrap.widgets.TbMenu', array('type'=>'list','items' => $this->layoutData['pageMenu']));?>
</div>
<?}?>
	<div class="span10 offset2">
<?php
$this->renderPartForm($model);
?>
	</div>
</div>