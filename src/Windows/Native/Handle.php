<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Native;

use FFI;
use FFI\CData;

class Handle {
    protected $handle;
    public function __construct(CData $handle){
        $this->handle = $handle;
    }

    public function getValue(){
        return $this->handle;
    }

    public static function fromValue(int $value){
        $handle = FFI::new("void *");
        /** @var int $handle */
        $handle+= $value;
        /** @var CData $handle */
        return new self($handle);
    }
}
