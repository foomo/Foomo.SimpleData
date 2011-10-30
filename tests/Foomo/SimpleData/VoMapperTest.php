<?php

namespace Foomo\SimpleData;

class VoMapperTest extends \PHPUnit_Framework_TestCase {
	private $data;
	private $voArray;
	public function setUp()
	{
		static $data;
		static $voArray;
		if(is_null($data)) {
			Mock\Vo\Person::$addToAddressCounter = 0;
			Mock\Vo\Person::$setSexCounter = 0;
			$data = \Foomo\SimpleData::read(implode( DIRECTORY_SEPARATOR, array( __DIR__, 'mockData', 'personCollection' )))->data;
			$voArray = array();
			foreach($data as $dataArray) {
				$voArray[] = $personVo = new Mock\Vo\Person;
				VoMapper::map($dataArray, $personVo);
			}
		}
		$this->data = $data;
		$this->voArray = $voArray;
	}
	public function testBaseMapping()
	{
		$actualFirstNames = array();
		$actualSexes = array();
		foreach($this->voArray as $personVo) {
			$this->assertInstanceOf(__NAMESPACE__ . '\\Mock\\Vo\\Person', $personVo);
			$actualFirstNames[] = $personVo->firstName;
			$actualSexes[] = $personVo->sex;
		}
		$this->assertEquals(array('Hannah', 'Peter', 'Uschi'), $actualFirstNames);
		$this->assertEquals(array(null, 'male', 'female'), $actualSexes);
		
	}
	public function testScalarMapping()
	{
		foreach($this->voArray as $personVo) {
			$this->assertInternalType('int', $personVo->timestamp);
		}
	}
	public function testNestedMapping()
	{
		foreach($this->voArray as $personVo) {
			$this->assertInternalType('array', $personVo->addresses);
			foreach($personVo->addresses as $address) {
				$this->assertInstanceOf(__NAMESPACE__ . '\\Mock\\Vo\\Address', $address);
			}
		}		
	}
	public function testConventionSetter()
	{
		$this->assertEquals($expected = 3, Mock\Vo\Person::$setSexCounter);
	}
	public function testConectionAddTo()
	{
		$this->assertEquals($expected = 5, Mock\Vo\Person::$addToAddressCounter);
	}
}