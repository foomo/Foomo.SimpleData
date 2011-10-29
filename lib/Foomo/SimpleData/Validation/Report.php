<?php

namespace Foomo\SimpleData\Validation;
/**
 * a validation report on a path
 */
class Report {
	/**
	 * relative from the simple data root
	 * 
	 * @var string
	 */
	public $path;
	/**
	 * are the data valid
	 * 
	 * @var boolean
	 */
	public $valid;
	/**
	 * human readable feedback
	 * 
	 * @var string
	 */
	public $report;
	const SOURCE_TYPE_YAML = 'YAML';
	const SOURCE_TYPE_JSON = 'JSON';
	const SOURCE_TYPE_FILE = 'FILE';
	/**
	 *
	 * @var string
	 */
	public $sourceType;
	/**
	 * JSON or YAML
	 * 
	 * @var string
	 */
	public $sourceData;
	/**
	 * parsed data
	 * 
	 * @var array
	 */
	public $parsedData;
	/**
	 * validator class name
	 * 
	 * @var string
	 */
	public $className;
	public static function validationReport($report, $valid)
	{
		$ret = new self;
		$ret->valid = $valid;
		$ret->report = $report;
		return $ret;
	}
}