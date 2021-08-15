<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Utils;

use FFI;
use FFI\CData;
use FFI\CType;

class NativeUtils {
    public function strdup(string $str){
        $sz = strlen($str);
        $mem = $this->malloc($sz);
        FFI::memcpy($mem, $str, $sz);
        return $mem;
    }

    public function ptrval(CData $ptr){
        $pptr = FFI::addr($ptr);
        $uptr = FFI::cast("uintptr_t *", $pptr);
        return $uptr[0];
    }

    public function fromWSTR(string $str){
        return rtrim(iconv('WCHAR_T', 'UTF-8', $str), "\x00");
    }
    public function toWSTR(string $str){
        return iconv('UTF-8', 'WCHAR_T', $str) . "\x00\x00";
    }

    private CType $uint8_t;

    public function malloc(int $sz){
        $bufT = FFI::arrayType($this->uint8_t, [$sz]);
        return FFI::new($bufT);
    }

    public function __construct(){
        $this->uint8_t = FFI::type('uint8_t');
    }
}
