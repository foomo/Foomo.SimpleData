<?php

namespace Foomo\SimpleData\Serialization;

class YAML extends AbstractSerializer {
	public function serialize($data) {
		return \Foomo\Yaml::dump($data);
	}
	public function unserialize($serializedData) {
		return \Foomo\Yaml::parse($data);
	}
}