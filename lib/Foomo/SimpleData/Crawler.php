<?php

namespace Foomo\SimpleData;

class Crawler {
	private $root;
	private $validators = array();
	public $validationReports = array();
	public $invalidFiles = array();
	/**
	 * parsed data
	 * 
	 * @var array
	 */
	public $result;
	public function __construct($root, array $validators = array())
	{
		$this->root = $root;
		$this->validators = $validators;
		$this->validationReports = array();
	}
	private function validate(\SplFileInfo $fileInfo, $content, $sourceType)
	{
		foreach($this->validators as $validator) {
			$report = $validator->validatePath(
				$this->getRelativePath(
					$this->root,
					$fileInfo->getPathname()
				), 
				$content
			);
			if($report) {
				$report->className = get_class($validator);
				$report->sourceType = $sourceType;
				$report->sourceData = file_get_contents($fileInfo->getPathname());
				$report->parsedData = $content;
				$this->validationReports[] = $report;
			}
		}
	}
	public function crawl($folder = null, array &$data = null)
	{
		if(is_null($folder)) {
			$folder = $this->root;
		}
		if(is_null($data)) {
			$this->result = array();
			$data = &$this->result;
		}
		if(file_exists($folder) && is_dir($folder)) {
			$iterator = new \DirectoryIterator($folder);

			$infos = array();

			// stupid sorting

			/* @var $fileInfo \SplFileInfo */
			foreach($iterator as $fileInfo) {
				if(substr($fileInfo->getFilename(),0,1) != '.') {
					$infos[$fileInfo->getFilename()] = $fileInfo->getPathname();
				}
			}

			$keys = array_keys($infos);
			sort($keys);

			foreach($keys as $fileInfoKey) {
				$fileInfo = new \SplFileInfo($infos[$fileInfoKey]);
				if($fileInfo->isDir() && is_string($fileInfo->getPathname())) {
					$newData = array();
					$this->crawl($fileInfo->getPathname(), $newData);
					$this->validate($fileInfo, $newData, Validation\Report::SOURCE_TYPE_DIR);
					if(substr($fileInfo->getFilename(), 0, 1) == '-') {
						$data[] = $newData;
					} else {
						$data[$fileInfo->getFilename()] = $newData;
					}

				} else if($fileInfo->isFile()) {
					$suffix = self::getSuffix($fileInfo->getFilename());
					if(!is_null($suffix)) {
						$key = substr($fileInfo->getFilename(), 0, - (strlen($suffix) +  1));
						$content = null;
						switch ($suffix) {
							case 'yml':
							case 'yaml':
								$sourceType = Validation\Report::SOURCE_TYPE_YAML;
								$content = \Foomo\Yaml::parse(file_get_contents($fileInfo->getPathname()));
								break;
							case 'json':
								$sourceType = Validation\Report::SOURCE_TYPE_JSON;
								$content = json_decode(file_get_contents($fileInfo->getPathname()), true);
								if(is_null($content)) {
									$this->invalidFiles[] = $fileInfo->getPathname();
									// throw new \Foomo\Services\Types\Exception('i guess either parsing went wrong or this file should not be here ' . substr($fileInfo->getPathname(), strlen($this->root)) , E_USER_ERROR);
								}
								break;
							default:
								// that is a file
								$sourceType = Validation\Report::SOURCE_TYPE_FILE;
								$content = $fileInfo->getPathname();
						}
						$this->validate($fileInfo, $content, $sourceType);
						if(!is_null($key)) {
							// run validation
							/* @var $validator Validation\AbstractValidator */
							if(substr($key, 0, 1) == '-') {
								$data[] = $content;
							} else {
								$data[$key] = $content;
							}
						}
					}
				}
			}
		}
	}
	private static function getRelativePath($root, $path)
	{
		return substr($path, strlen($root));
	}
	private static function getSuffix($filename)
	{
		$pos = strpos(strrev($filename), '.');
		if($pos !== false && $pos > 0) {
			return substr($filename, -$pos);
		}
	}
}