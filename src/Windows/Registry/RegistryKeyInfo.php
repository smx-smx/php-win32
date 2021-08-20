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

class RegistryKeyInfo {
    public int $numberOfSubKeys;
    public int $numberOfValues;

    /**
     * Maximum key name length, in characters (including NULL)
     */
    public int $maxKeyNameLength;
    /**
     * Maximum class name length, in characters (including NULL)
     */
    public int $maxClassNameLength;

    public int $maxValueNameLength;

    public function __construct(RegistryNative $reg, CData $handle){
        $cchName = FFI::new('uint32_t');
        $cchClass = FFI::new('uint32_t');
        $cSubKeys = FFI::new('uint32_t');
        $cValues = FFI::new('uint32_t');
        $cchValue = FFI::new('uint32_t');

        $reg->RegQueryInfoKeyW($handle,
            null, null, null,
            FFI::addr($cSubKeys),
            FFI::addr($cchName), FFI::addr($cchClass),
            FFI::addr($cValues), FFI::addr($cchValue),
            null, null, null
        );

        $this->numberOfSubKeys = $cSubKeys->cdata;
        $this->numberOfValues = $cValues->cdata;
        
        // +1 to add NULL terminator character
        $this->maxKeyNameLength = $cchName->cdata + 1;
        $this->maxClassNameLength = $cchClass->cdata + 1;
        $this->maxValueNameLength = $cchValue->cdata + 1;
    }

    public function newKeyNameBuffer(){
        if($this->maxKeyNameLength == 0) return null;

        $bufT = FFI::arrayType(FFI::type('uint8_t'), [$this->maxKeyNameLength * 2]);
        return FFI::new($bufT);
    }

    public function newValueNameBuffer(){
        if($this->maxValueNameLength == 0) return null;

        $bufT = FFI::arrayType(FFI::type('uint8_t'), [$this->maxValueNameLength * 2]);
        return FFI::new($bufT);
    }

    public function newClassNameBuffer(){
        if($this->maxClassNameLength == 0) return null;

        $bufT = FFI::arrayType(FFI::type('uint8_t'), [$this->maxClassNameLength * 2]);
        return FFI::new($bufT);
    }
}
