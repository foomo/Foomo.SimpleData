<?php

namespace Foomo\SimpleData\Mock\Vo;
/**
 * mock person
 */
class Person {
	public static $addToAddressCounter = 0;
	public static $setSexCounter = 0;
	/**
	 * @var Address[]
	 */
	public $addresses = array();
	/**
	 * @var string
	 */
	public $sex;
	/**
	 * @var string
	 */
	public $firstName;
	/**
	 * aka birthday as timestamp
	 * 
	 * @var integer
	 */
	public $timestamp = 0;
	/**
	 * @var string
	 */
	public $lastName;
	public function addToAddresses(Address $address)
	{
		self::$addToAddressCounter ++;
		$this->addresses[] = $address;
	}
	public function setSex($sex)
	{
		self::$setSexCounter ++;
		if(in_array($sex, array('male', 'female'))) {
			$this->sex = $sex;
		}
	}
	
}