<?

/* @var $model Foomo\SimpleData\Frontend\Model */
/* @var $view \Foomo\MVC\View */

?><h1>Scan</h1>
<h2>Configuration</h2>
<p>
	Scanning in data folder:<br>
	<code><?= $view->escape($model->config->rootFolder) ?></code>
</p>
<p>Validators:</p>
<ul>
	<? foreach($model->config->validators as $validatorSesstings): ?>
		<li>
			<pre>class : <?= $view->escape($validatorSesstings['class']) ?></pre>
			<pre>regex : <?= $view->escape(implode(', ', $validatorSesstings['regex'])) ?></pre>
		</li>
	<? endforeach; ?>
</ul>
<h2>Validation</h2>
<ul>
	<? foreach($model->crawlerResult->validationReports as $validationReport): ?>
		<? // var_dump($validationReport); ?>
		<? /* @var $validationReport Foomo\SimpleData\Validation\Report */ ?>
		<li>
			<h3><?= $view->escape($validationReport->className) ?></h3>
			<p>
				Says: <i><?= $view->escape($validationReport->report) ?></i>
				in path: . <?= $view->escape($validationReport->path) ?>
			</p>
			<?
				switch($validationReport->sourceType) {
					case Foomo\SimpleData\Validation\Report::SOURCE_TYPE_JSON:
						$lang = 'javascript';
						break;
					case Foomo\SimpleData\Validation\Report::SOURCE_TYPE_YAML:
						$lang = 'rails';
						break;
					default:
						$lang = null;
				}
			?>
			<? if(!is_null($lang)): ?>
				<p>Source <?= $validationReport->sourceType ?></p>
				<?= $view->partial('geshi', array('lang' => $lang, 'source' => $validationReport->sourceData)) ?>
				<p>Interpretation</p>
				<?= $view->partial('data', array('data' => $validationReport->parsedData)) ?>
			<? endif; ?>
		</li>
	<? endforeach; ?>
</ul>
<h2>Scan result</h2>
<?= $view->partial('data', array('data' => $model->crawlerResult->data)) ?>