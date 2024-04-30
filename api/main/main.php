<?php
namespace api\main;
use core\engine\std_module;
class main extends std_module {
	public $active = true;
	public $forauth = false;
	protected $routes = [
		'/' => [
			'do' => 'main',
		],
	];
	public function main(){
		//echo 'main api';
	}
}
?>