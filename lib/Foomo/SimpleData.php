<?php

namespace Foomo;

class SimpleData {
	public static function read($rootFolder, array $validators = array(), $maxDepth = 0)
	{
		$crawler = new SimpleData\Crawler($rootFolder, $validators, $maxDepth);
		$crawler->crawl();
		$result = new SimpleData\CrawlerResult;
		$result->data = $crawler->result;
		$result->validationReports = $crawler->validationReports;
		$result->invalidFiles = $crawler->invalidFiles;
		return $result;
	}
}