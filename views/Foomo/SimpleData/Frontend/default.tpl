<?php

/* @var $model Foomo\SimpleData\Frontend\Model */
/* @var $view \Foomo\MVC\View */

?><h1>Simpledata</h1>
<ul>
	<? foreach($model->allConfs as $conf): ?>
		<li><?= $view->link( $conf['module'] . (!empty($conf['subDomain'])?'/'.$conf['subDomain']:''), 'scan', array($conf['module'], $conf['subDomain'])) ?></li>
	<? endforeach; ?>
</ul>
