<?php

namespace Foomo\SimpleData\Validation;

abstract class AbstractValidator {
	private $pathExpressions = array();
	
	public function __construct(array $pathExpressions)
	{
		$this->pathExpressions = $pathExpressions;
	}
	public function validatePath($path, $data)
	{
		foreach($this->pathExpressions as $pathRule) {
			if($this->pathMatches($pathRule, $path)) {
				$report = $this->validate($data);
				$report->path = $path;
				return $report;
			}
		}
	}
	protected function pathMatches($rule, $path)
	{
		if(preg_match($rule, $path)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * validate data and return a report
	 * 
	 * @return Report
	 */
	abstract public function validate($data);
}