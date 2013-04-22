<?php

$this->widget('bootstrap.widgets.TbAlert', array(
	'block'=>true, // display a larger alert block?
	'fade'=>true, // use transitions?
	'closeText'=>'&times;'
));

Yii::import('bootstrap.widgets.TbForm');

$form = TbForm::createForm($model->formConstructor(),$model,
	array(
		'htmlOptions'=>array('class'=>'well'),
		'type'=>'horizontal',
	)
);

echo $form->render();