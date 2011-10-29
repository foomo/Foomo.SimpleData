<?php

namespace Foomo\SimpleData;

class DomainConfig extends \Foomo\Config\AbstractConfig {
	const NAME = 'Foomo.SimpleData.validator';
	/**
	 * relative path from the perspective of you module
	 * 
	 * @var string
	 */
	public $rootFolder;
	/**
	 * array(array('class' => 'ClassName\Of\Validator', 'expr' => array('regex', 'regex', ...)))
	 * 
	 * @var array
	 */
	public $validators = array();
	/**
	 *
	 * @return Validation\AbstractValidator[]
	 */
	public function getValidators()
	{
		$ret = array();
		foreach($this->validators as $validatorData) {
			$className = $validatorData['class'];
			if(class_exists($className)) {
				$ret[] = new $className($validatorData['regex']);
			} else {
				trigger_error('skipping invalid validator ' . $className, E_USER_WARNING);
			}
		}
		return $ret;
	}
}