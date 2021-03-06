<?php

namespace Foomo\SimpleData;

class VoMapperTest extends \PHPUnit_Framework_TestCase {
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
	public function testConventionAddTo()
	{
		$this->assertEquals($expected = 5, Mock\Vo\Person::$addToAddressCounter);
	}
	public function testConventionAddToHash()
	{
		$this->assertEquals($expected = 2, Mock\Vo\Person::$addToPhonesCounterObj);
		$this->assertEquals($expected = 1, Mock\Vo\Person::$addToPhonesCounterString);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function testArrayMappingToArrayOfWrongType()
	{
		$persons = \Foomo\SimpleData::read(implode( DIRECTORY_SEPARATOR, array( __DIR__, 'mockData', 'personCollection' )))->data;
		foreach($persons as $personArray) {
			$personVo = new Mock\Vo\Person();
			$personVo->addresses = 1;
			VoMapper::map($personArray, $personVo);
		}
	}
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testArrayMappingToArrayOfWrongTypeNoAdderMethod()
	{
		$persons = \Foomo\SimpleData::read(implode( DIRECTORY_SEPARATOR, array( __DIR__, 'mockData', 'personCollection' )))->data;
		foreach($persons as $personArray) {
			$personVo = new Mock\Vo\Person();
			$personVo->emails = 'wrong type';
			VoMapper::map($personArray, $personVo);
		}
	}
	public function testArrayMappingToNullArray()
	{
		$persons = \Foomo\SimpleData::read(implode( DIRECTORY_SEPARATOR, array( __DIR__, 'mockData', 'personCollection' )))->data;
		foreach($persons as $personArray) {
			$personVo = new Mock\Vo\Person();
			$personVo->addresses = null;
			VoMapper::map($personArray, $personVo);
			if(!empty($personArray['addresses'])) {
				$this->assertTrue(is_array($personVo->addresses), 'i expected addresses to be an array but got ' . gettype($personVo->addresses));
			}
		}
	}

}