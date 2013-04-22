<?
$title = Yii::app()->name;
$showNavBar = true;
$showFooter = true;
$containerClass = 'container-fluid';

if(isset($this->layoutData['title']))$title = $this->layoutData['title'];
if(isset($this->layoutData['showNavBar']))$showNavBar = $this->layoutData['showNavBar'];
if(isset($this->layoutData['showFooter']))$showFooter = $this->layoutData['showFooter'];
if(isset($this->layoutData['containerClass']))$containerClass = $this->layoutData['containerClass'];
if(isset($this->layoutData['operations']))$operations = $this->layoutData['operations'];
if(isset($this->layoutData['breadcrumb']))$breadcrumb = $this->layoutData['breadcrumb'];

?><!DOCTYPE html>
<html lang="<?=Yii::app()->language?>">
<head>
<meta charset="utf-8">
<title><?=$title;?></title>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<?
if(!empty($operations) || !empty($breadcrumb)){?>
<header class="navbar-fixed-top" style="background-color: #FFFFFF; border-top:60px solid #FFFFFF;">
	<?if(!empty($operations)){?>
    <div class="span2">
		<?
		$this->widget('bootstrap.widgets.TbButtonGroup', array(
			'type'=>'primary',
			'size'=>'large',
			'htmlOptions' => array(
				'class'=> '',
			),
			'buttons'=>array(
				array(
					'label'=>Yii::t('eyii.menu','Operations'),
					'icon'=>'icon-list icon-white',
					'items'=>$operations
				),
			),
		));
		?>

    </div>
    <?}?>
    <?if(!empty($breadcrumb)){?>
    <div class="offset2">
		<?
			$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				'homeLink' => CHtml::link('<i class="icon-home"></i>',Yii::app()->homeUrl),
				'links'=>$breadcrumb,
				/*'icon' => 'icon-home icon-white',*/
				'htmlOptions' => array(
					'style'=> 'margin-top: 4px;',
				),
			));
		?>
    </div>
    <?}?>
</header>
<br><br><br>
<?}?>
<?
if($showNavBar){
$assetsPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('eyii.assets'));

Yii::app()->clientScript->registerCssFile($assetsPath."/navbar.css");

	if($this->beginCache('navbar|'.Yii::app()->user->rightsString.time())){
		if(Yii::app()->user->isGuest || !Yii::app()->isInstall){
			$navBarItems = array(
				array(
					'class' => 'bootstrap.widgets.TbMenu',
					'items' => array(
						array('label'=>Yii::t('eyii.menu','Site'), 'icon' => 'icon-circle-arrow-left icon-white',  'url'=>Yii::app()->originBaseUrl."/"),
					)
				),
			);		}else{
			$navBarItems = array(
				array(
					'class' => 'bootstrap.widgets.TbMenu',
					'items' => array(
						array('label'=>Yii::t('eyii.menu','Site'), 'icon' => 'icon-circle-arrow-left icon-white',  'url'=>Yii::app()->originBaseUrl."/"),
						array('label'=>Yii::t('eyii.menu','Desktop'), 'url'=>Yii::app()->baseUrl."/"),
						array('label'=>Yii::t('eyii.menu','Menu'), 'items'=> Yii::app()->layoutMenu->get()
							/*array(
								array('label'=>'Item1', 'url'=>'#'),
								array('label'=>'Item1', 'url'=>'#'),
								array('label'=>'Item1', 'url'=>'#'),
							)*/
						),
						/*array('label'=>'Version', 'url'=>'#'),
						array('label'=>'Help', 'url'=>'#'),*/
						array('label'=> Yii::t('eyii.menu','Logout'), 'url'=>Yii::app()->createUrl('login/out')),
					)
				),
				/*'<form class="navbar-search pull-right" action="#">
						<input type="text" class="search-query span2" placeholder="Поиск">
					</form>'*/
			);
		}

		$this->widget('bootstrap.widgets.TbNavbar', array(
			'brand' => Yii::app()->name,
			'fixed' => 'top',
			'type'  => 'inverse',
			'fluid' => true,
			'items' => $navBarItems
		));

		$this->endCache();
	}
}
?>

<div class="<?=$containerClass;?>">

<?php echo $content; ?>

<?if($showFooter){?>
<hr>
<footer>
	<p><?=EYii::powered();?></p>
	<p><?=EYii::copyright();?></p>
</footer>
<?}?>
	</div> <!-- /container -->
</body>
</html>