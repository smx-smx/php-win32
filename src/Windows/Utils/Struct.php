<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

namespace Smx\Windows\Utils;

class Struct {
	public static function makeDef(array $fields){
		$arr = array();
		foreach($fields as $name => $type){
			$arr[] = "{$type}{$name}";
		}
		$def = implode('/', $arr);
		return $def;
	}

	private string $def;
	private int $size;

	public function __construct(array $fields, int $size){
		$this->def = self::makeDef($fields);
		$this->size = $size;
	}

	public function fromStream($fh){
		$data = fread($fh, $this->size);
		return unpack($this->def, $data);
	}

	public function fromData(string $data){
		return unpack($this->def, $data);
	}
}
