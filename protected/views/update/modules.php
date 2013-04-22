<div class="row">
	<div class="span10 offset2">
	<h3><?=Yii::t('eyii.menu','Site Update');?></h3>
	<hr>
<?
if($this->beginCache('some')){


	$gridDataProvider = new CArrayDataProvider($list, array('keyField' => 'id'));
	$gridColumns = array(
		array('name'=>'id', 'header'=>Yii::t('eyii.defaults','Module'), 'htmlOptions'=>array('style'=>'width: 60px')),
		array('name'=>'version', 'header'=>Yii::t('eyii.defaults','Version')),
		array('name'=>'lastModif', 'header'=>Yii::t('eyii.defaults','Date')),
		array('name'=>'info', 'header'=>Yii::t('eyii.defaults','Info')),
		array('name'=>'av', 'header'=>Yii::t('eyii.defaults','Updating'), 'value'=>'($data[\'version\'] != $data[\'installedVersion\'])?\''.Yii::t('eyii.defaults','Available').'\':\''.Yii::t('eyii.defaults','Not Available').'\''),
		array(
			'htmlOptions' => array('nowrap'=>'nowrap'),
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '&nbsp;&nbsp;&nbsp;&nbsp;{delete}',
			'deleteButtonUrl'=>'Yii::app()->controller->createUrl("delete",array("id"=>$data[\'id\']))'
		)
	);

	$this->widget('bootstrap.widgets.TbGridView', array(
		'type'=>'bordered',
		'dataProvider'=>$gridDataProvider,
		'template'=>"{items}",
		'columns'=>$gridColumns,
	));

$this->endCache();
}
?>
	</div>
</div>