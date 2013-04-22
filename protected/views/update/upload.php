<div class="row">
	<div class="span10 offset2">
<?
$this->widget('bootstrap.widgets.TbWizard', array(
	'type' => 'pills', // 'tabs' or 'pills'
	'tabs' => array(
		array('label' => 'Local', 'content' => $this->renderPartForm($modelLocal, true), 'active' => true),
		array('label' => 'Link', 'content' => $this->renderPartForm($modelLink, true)),
		array('label' => 'File', 'content' => $this->renderPartForm($modelFile, true)),
	),
));

?>
	</div>
</div>