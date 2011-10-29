<?php

namespace Foomo\SimpleData\Serialization;

class JSON extends AbstractSerializer {
	public function serialize($data) {
		return json_encode($data);
	}
	public function unserialize($serializedData) {
		return json_decode($serializedData, true);
	}
}