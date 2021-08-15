<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Registry;

use FFI;
use FFI\CData;
use Smx\Windows\Native\Libraries\Advapi32;
use Smx\Windows\Windows;

class RegistryNative {
    private Advapi32 $ffi;

    public function __call($name, $arguments = []){
        return $this->ffi->{$name}(...$arguments);
    }

    public function new($type, $owned = TRUE, $persistent = FALSE): CData {
        return $this->ffi->new($type, $owned, $persistent);
    }

    public function type($type){
        return $this->ffi->type($type);
    }

    public function newHKEY(int $value = 0) : CData {
        $hkey = $this->new('HKEY');
        /** @var int $hkey */
        $hkey += $value;
        /** @var CData $hkey */
        return $hkey;
    }

    public function __construct(Windows $windows){
        $this->ffi = $windows->getAdvapi32();
    }
}
