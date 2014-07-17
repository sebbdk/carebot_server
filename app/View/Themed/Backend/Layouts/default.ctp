<?php
	App::uses('AuthComponent', 'Controller/Component');

	//sort menu items
	$menuItems = array();
	if(Configure::read('adminMenu')) {
		$menuItems = Configure::read('adminMenu');

		usort($menuItems, function($a, $b) {
			if(!isset($a['sort'])) {
				return 0;
			}

			if(!isset($b['sort']) || $a['sort'] > $b['sort']) {
				return 1;
			} else {
				return 0;
			}
		});
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>Backend</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap');
		echo $this->Html->css('justified-nav');
		echo $this->Html->css('backend');

		echo $this->fetch('meta');
		echo $this->fetch('css');

		echo $this->Html->script([
			'//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
			'dropdown',
			'backend'
		]);

		echo $this->fetch('script');
	?>

	<style type="text/css" media="screen">
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
			vertical-align: middle;
		}
	</style>	
	
</head>
<body>
	<div class="container load">	

		<?php if(AuthComponent::user()) : ?>
			<div class="masthead">
				<h3 class="text-muted">Admin section</h3>
				<ul class="nav nav-justified">
					<?php foreach($menuItems as $menuItem) : ?>
						<?php
							$isActive = $this->params['controller'] == $menuItem['url']['controller'] && 
										$this->action == $menuItem['url']['action'];
						?>
						<li <?= $isActive ? 'class="active"':''; ?>>
							<?= $this->Html->link($menuItem['name'], $menuItem['url']); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<br />		
		<?php endif; ?>

		<?= $this->Session->flash(); ?>

		<?= $this->fetch('content'); ?>

	    <div class="jumbotron">
			<?php //echo $this->element('sql_dump'); ?>
		</div>			
	</div>
</body>
</html>