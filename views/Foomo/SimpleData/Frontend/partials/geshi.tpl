<?php

if(class_exists('GeSHi')) {
	$geshi = new GeSHi($source, $lang); 
	echo $geshi->parse_code();
} else {
	echo $view->escape($source);
}
