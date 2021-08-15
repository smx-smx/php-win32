<?php
/**
  * Services Container
  * 
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows;

use Smx\Windows\Native\Libraries\Advapi32;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Utils\NativeUtils;

class Windows {
    private static ?NativeUtils $nativeUtils = null;
    private static ?Advapi32 $advapi32 = null;
    private static ?Kernel32 $kernel32 = null;

    private static function getOrMake(string $field, callable $valueFactory){
        if(self::$$field != null){
            return self::$$field;
        }
        self::$$field = $valueFactory();
        return self::$$field;
    }

    private static function _getNativeUtils() : NativeUtils {
        return self::getOrMake('nativeUtils', function(){
            return new NativeUtils;
        });
    }

    private static function _getAdvapi32() : Advapi32 {
        return self::getOrMake('advapi32', function(){
            return new Advapi32;
        });
    }

    private static function _getKernel32() : Kernel32 {
        return self::getOrMake('kernel32', function(){
            return new Kernel32;
        });
    }

    public function getNativeUtils(){
        return self::_getNativeUtils();
    }

    public function getAdvapi32(){
        return self::_getAdvapi32();
    }

    public function getKernel32(){
        return self::_getKernel32();
    }

    public function getRegistry(){
        return new Registry($this);
    }

    public function getWindowsInstance(){
        return new WindowsInstance($this);
    }

    public function getProcessManager(){
        return new ProcessManager($this);
    }

    public function getPrivilegeManager(){
        return new PrivilegeManager($this);
    }
}
