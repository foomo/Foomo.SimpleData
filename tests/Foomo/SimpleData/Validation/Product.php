<?php

namespace Foomo\SimpleData\Validation;

class Product extends AbstractValidator {
	public function validate($data)
	{
		$report = '';
		$valid = true;
		if(!isset($data['hannes'])) {
			$report = 'no hannes ...' . PHP_EOL;
		}
		if($data['price'] < 1) {
			$report .= 'that is too cheap ' . $data['price'];
			$valid = false;
		}
		return Report::validationReport($report, $valid);
	}
}