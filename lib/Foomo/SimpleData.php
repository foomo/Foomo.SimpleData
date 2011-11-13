<?php

namespace Foomo;

class SimpleData {
	public static function read($rootFolder, array $validators = array())
	{
		$ret = array();
		$crawler = new SimpleData\Crawler($rootFolder, $validators);
		$crawler->crawl();
		$result = new SimpleData\CrawlerResult;
		$result->data = $crawler->result;
		$result->validationReports = $crawler->validationReports;
		$result->invalidFiles = $crawler->invalidFiles;
		return $result;
	}
}