<?php

namespace Foomo;

class SimpleDataTest extends \PHPUnit_Framework_TestCase {
	
	private function getMockDir($relativePath)
	{
		return __DIR__ . DIRECTORY_SEPARATOR . 'SimpleData' . DIRECTORY_SEPARATOR . 'mockdata' . DIRECTORY_SEPARATOR . $relativePath;
	}
	
	public function testReadCollectionYAML()
	{
		$this->_testReadCollection($this->getMockDir('simpleCollections' . DIRECTORY_SEPARATOR . 'YAML'));
	}
	public function testReadCollectionJSON()
	{
		$this->_testReadCollection($this->getMockDir('simpleCollections' . DIRECTORY_SEPARATOR . 'JSON'));
	}
	public function testReadCollectionMixed()
	{
		$this->_testReadCollection($this->getMockDir('simpleCollections' . DIRECTORY_SEPARATOR . 'mixed'));
	}
	public function testReadComplexCollection()
	{
		$expected = array(
			0 => array(
				'name' => array(
					'first' => 'John',
					'last' => 'Doe'
				),
				'city' => array(
					'name' => 'Monaco',
					'zip' => '80000',
					'country' => 'GER'
				)
			),
			1 => array(
				'name' => array(
					'first' => 'Uta',
					'last' => 'Schmidt'
				),
				'city' => array(
					'name' => 'Berlin',
					'zip' => '10000',
					'country' => 'GER'
				)
			)
		);
		$actual = SimpleData::read($this->getMockDir('complexCollection'));
		$this->assertEquals($expected, $actual->data);
	}
	public function testNestedHeterogeneousCollection()
	{
		
		$expected = array(
			0 => array(
				'cart' => array(
					0 => array(
						'type' => 'jacket',
						'name' => 'foo',
						'price' => 1.10
					),
					1 => array(
						'type' => 'pants',
						'name' => 'tightIsRight',
						'price' => 0.99
					),
					2 => array(
						'type' => 'towel',
						'name' => 'superTowel',
						'price' => 9.99
					)
					
				),
				'sepp' => $this->getMockDir('nestedHeterogeneousCollection' . DIRECTORY_SEPARATOR . '- a' . DIRECTORY_SEPARATOR . 'sepp.txt'),
				'user' => array(
					'name' => 'jan'
				),
				
			),
			1 => array(
				'user' => array('name' => 'john', 'sex' => 'male'),
				'foo' => 'bar'
			)
		);
		$actual = SimpleData::read($this->getMockDir('nestedHeterogeneousCollection'));
		$this->assertEquals($expected, $actual->data);
	}
	public function testProductValidation()
	{
		$actual = SimpleData::read(
			$this->getMockDir('nestedHeterogeneousCollection'),
			array(new SimpleData\Validation\Product(array('/- a\\/cart\\/- .*\.yml/')))
		);
		
		$this->assertTrue($actual->validationReports[0]->valid);
		$this->assertFalse($actual->validationReports[1]->valid);
		
		$this->assertEquals($actual->validationReports[0]->parsedData, $actual->data[0]['cart'][0]);
		$this->assertEquals($actual->validationReports[1]->parsedData, $actual->data[0]['cart'][1]);
	}
	private function _testReadCollection($rootFolder)
	{
		$this->assertEquals(
			$expected = array('foo' => array('name' => 'foo'), 'bar' => array('name' => 'bar')),
			$actual = SimpleData::read($rootFolder)->data
		);
	}
}