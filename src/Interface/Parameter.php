<?php
namespace KrishnaAPI\Interface;
interface Parameter {
	// const Consumes = 1;
	// const Name = '';
	
	public static function verify($value) : \KrishnaAPI\Returner;
}