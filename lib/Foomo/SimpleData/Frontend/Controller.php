<?php

namespace Foomo\SimpleData\Frontend;

class Controller {
	/**
	 * @var Model
	 */
	public $model;
	public function actionDefault()
	{
		$confs = array();
		foreach(\Foomo\Config\Utils::getConfigs() as $module => $subDomainConfigs) {
			foreach($subDomainConfigs as $subDomain => $configs) {
				foreach($configs as $configName => $configFilename) {
					if($configName == \Foomo\SimpleData\DomainConfig::NAME) {
						$confs[] = array('module' => $module, 'subDomain' => $subDomain);
					}
				}
			}
		}
		$this->model->allConfs = $confs;
	}
	public function actionScan($configModule, $configSubDomain)
	{
		$this->model->config = \Foomo\Config::getConf($configModule, \Foomo\SimpleData\DomainConfig::NAME, $configSubDomain);
		$this->model->crawlerResult = \Foomo\SimpleData::read(
			$this->model->config->rootFolder,
			$this->model->config->getValidators()
		);
	}
}