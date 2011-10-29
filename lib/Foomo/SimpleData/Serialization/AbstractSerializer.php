<?php

namespace Foomo\SimpleData\Serialization;

abstract class AbstractSerializer {
	abstract public function serialize($data);
	abstract public function unserialize($serializedData);
}