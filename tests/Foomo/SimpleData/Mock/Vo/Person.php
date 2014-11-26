<?php

namespace Foomo\SimpleData\Mock\Vo;
/**
 * mock person
 */
class Person {
	public static $addToAddressCounter = 0;
	public static $setSexCounter = 0;
	public static $addToPhonesCounterObj = 0;
	public static $addToPhonesCounterString = 0;
	/**
	 * @var Address[]
	 */
	public $addresses = array();
	/**
	 * @var Phone[]
	 */
	public $phones = array();
	/**
	 * no adder method for this property to test VoMapper's addToPropertyArray method a different way
	 * @var Email[]
	 */
	public $emails = array();
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
	public function addToPhones($key, $value)
	{
		if(is_object($value)) {
			self::$addToPhonesCounterObj ++;
		} else if(is_string($value)) {
			self::$addToPhonesCounterString ++;
		}
	}
	
}