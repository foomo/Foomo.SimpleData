<?php

namespace Foomo\SimpleData;

/**
 * maps arrays to value objects
 */
class VoMapper {
	/**
	 * recursively map data to a value object
	 * 
	 * @param mixed $data
	 * @param mixed $voTarget
	 * 
	 * @return mixed the passed in vo target - for your comfort
	 */
	public static function map($data, $voTarget)
	{
		if(is_object($data)) {
			$data = (array) $data;
		}
		$targetClass = get_class($voTarget);
		// is "this" set?
		if(isset($data['this'])) {
			// the conventions is to elevate the content of "this" to root
			foreach ($data['this'] as $key => $value) {
				$data[$key] = $value;
			}
			// clean it up
			unset($data['this']);
		}
		
		$objectNamespace = self::getObjectNamespace($voTarget);
		// map to vo
		//$refl = new \Foomo\Services\Reflection\ServiceObjectType(get_class($voTarget));
		$refl = \Foomo\Services\Reflection\ServiceObjectType::getCachedType($targetClass);
		// iterate on what is expected not necessarily on what is there
		foreach($refl->props as $name => $propType) {
			// add element
			if(isset($data[$name])) {
				// get the type
				$type = self::getTypeInNamespace($propType->type, $objectNamespace);
				// check if nested vo
				if(!is_null($type)) {
					// ? crawl it
					if($propType->isArrayOf) {
						// typed array
						$expectedKey = 0;
						foreach($data[$name] as $key => $childData) {
							$childVo = new $type;
							if(is_array($childData)) {
								self::map($childData, $childVo);
							} else {
								// not mappable
								$childVo = $childData;
							}
							if($expectedKey === $key) {
								// regular array
								self::addToPropertyArray($voTarget, $name, $childVo);
							} else {
								// that is a fckn hash
								self::addToPropertyHash($voTarget, $name, $key, $childVo);
							}
							$expectedKey ++;
						}
					} else {
						self::map($data[$name], $childVo = new $type);
						self::assignProperty($voTarget, $name, $childVo);
					}
				} else {
					// : assign it
					self::assignProperty($voTarget, $name, $data[$name], $propType->type);
				}
			}
		}
		return $voTarget;
	}
	private static function castScalarType($type, $value)
	{
		// i guess should somehow got to some common code ...
		if(is_scalar($value)) {
			switch($type) {
				case 'int':
				case 'integer':
					$value = (integer) $value;
					break;
				case 'float':
				case 'double':
					$value = (float) $value;
					break;
				case 'bool':
				case 'boolean':
					$value = (bool) $value;
					break;
				case 'string':
					$value = (string) $value;
					break;
			}
		}
		return $value;
	}
	/**
	 * add a to an array property - will check if a addTo<PropName>($value) 
	 * exists and call it or array_push into the array, if the method is not 
	 * callable on $vo
	 * 
	 * @param stdClass $vo value object
	 * @param string $propName
	 * @param mixed $value 
	 */
	private static function addToPropertyArray($vo, $propName, $value)
	{
		$adderFunc = array($vo, 'addTo' . ucfirst($propName));
		if(is_callable($adderFunc)) {
			call_user_func_array($adderFunc, array($value));
		} else {
			array_push($vo->$propName, $value);
		}
		
	}

	/**
	 * @param $vo
	 * @param $propName
	 * @param $key
	 *
	 * @param $value
	 */
	private static function addToPropertyHash($vo, $propName, $key, $value)
	{
		$adderFunc = array($vo, 'addTo' . ucfirst($propName));
		if(is_callable($adderFunc)) {
			call_user_func_array($adderFunc, array($key, $value));
		} else {
			$vo->{$propName}[$key] = $value;
		}
	}
	/**
	 * assign a property - will check if a set<PropName>($value) method exists
	 * and call it or simply assign the value
	 * 
	 * @param stdClass $vo a value object
	 * @param string $propName
	 * @param mixed $value 
	 */
	private static function assignProperty(&$vo, $propName, $value, $type = null)
	{
		$setterFunc = array($vo, 'set' . ucfirst($propName));
		if(is_callable($setterFunc)) {
			call_user_func_array($setterFunc, array($value));
		} else {
			if(!is_null($type)) {
				$value = self::castScalarType($type, $value);
			}
			if(is_object($vo)) {
				$vo->$propName = $value;
			} elseif(is_array($vo)) {
				$vo[$propName] = $value;
			} else {
				trigger_error('now what kinda vo is that supposed to be', E_USER_ERROR);
			}
		}
	}
	/**
	 * helper to get the namespace of an object
	 * 
	 * @param stdClass $obj
	 * 
	 * @return string
	 */
	private static function getObjectNamespace($obj)
	{
		$class = get_class($obj);
		$parts = explode('\\', $class);
		array_pop($parts);
		return implode('\\', $parts);
	}
	/**
	 * given a relative classname, we look our namespace first and then globally
	 * 
	 * @param string $type class name
	 * @param string $namespace
	 * 
	 * @return string
	 */
	private static function getTypeInNamespace($type, $namespace)
	{
		static $cache = array();
		$key = $namespace . '-' . $type;
		if(!isset($cache[$key])) {
			if(class_exists($namespace . '\\' . $type)) {
				// look in the NS first
				$cache[$key] = $namespace . '\\' . $type;
			} else if(class_exists($type)) {
				// global
				$cache[$key] = $type;
			} else {
				// that has to be sth. scalar
				$cache[$key] = false;
			}
		}
		return $cache[$key]===false?null:$cache[$key];
	}
}