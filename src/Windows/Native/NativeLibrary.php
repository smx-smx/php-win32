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

abstract class NativeLibrary {
    protected FFI $ffi;

    public function __call($name, $arguments = []){
        return $this->ffi->{$name}(...$arguments);
    }

    public function new($type, $owned = TRUE, $persistent = FALSE): CData {
        return $this->ffi->new($type, $owned, $persistent);
    }

    public function type($type){
        return $this->ffi->type($type);
    }

    public function __construct(string $code, string $libraryName){
        $this->ffi = FFI::cdef($code, $libraryName);
    }
}
