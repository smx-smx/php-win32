<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows;

use FFI;
use Smx\Windows\Registry\RegistryKey;
use Smx\Windows\Registry\RegistryNative;
use Smx\Windows\Utils\NativeUtils;


class Registry {
    private Windows $windows;
    private RegistryNative $reg;

    private NativeUtils $natSvc;

    private static $hiveMap = array(
        'HKEY_CLASSES_ROOT' => Constants::HKEY_CLASSES_ROOT,
        'HKEY_CURRENT_USER' => Constants::HKEY_CURRENT_USER,
        'HKEY_LOCAL_MACHINE' => Constants::HKEY_LOCAL_MACHINE,
        'HKEY_USERS' => Constants::HKEY_USERS,
        'HKEY_PERFORMANCE_DATA' => Constants::HKEY_PERFORMANCE_DATA,
        'HKEY_CURRENT_CONFIG' => Constants::HKEY_CURRENT_CONFIG,
        'HKEY_DYN_DATA' => Constants::HKEY_DYN_DATA,
        'HKEY_CURRENT_USER_LOCAL_SETTINGS' => Constants::HKEY_CURRENT_USER_LOCAL_SETTINGS,
        'HKEY_PERFORMANCE_TEXT' => Constants::HKEY_PERFORMANCE_TEXT,
        'HKEY_PERFORMANCE_NLSTEXT' => Constants::HKEY_PERFORMANCE_NLSTEXT,
    );

    private static $hiveShortLongMap = array(
        'HKCR' => 'HKEY_CLASSES_ROOT',
        'HKCU' => 'HKEY_CURRENT_USER',
        'HKLM' => 'HKEY_LOCAL_MACHINE',
        'HKU' => 'HKEY_USERS',
        'HKCC' => 'HKEY_CURRENT_CONFIG'
    );

    private static function getHiveKey(string $root){
        $len = strlen($root);
        if($len > 4 && $root[4] == '_'){
            return self::$hiveMap[$root] ?? null;
        } else if($len >= 3){
            $longHive = self::$hiveShortLongMap[$root] ?? null;
            if($longHive === null) return null;
            return self::$hiveMap[$longHive] ?? null;
        } else {
            return null;
        }
        
    }

    private function RegOpenKeyEx(int $hKey, string $subKey, int $options, int $samDesired){
        $hkeyHandle = $this->reg->newHKEY($hKey);

        $subKeyW = $this->natSvc->toWSTR($subKey);
        $hkResult = $this->reg->newHKEY();
        
        $result = $this->reg->RegOpenKeyExW($hkeyHandle, $subKeyW, $options, $samDesired, FFI::addr($hkResult));
        if($result !== 0){
            return null;
        }
        return $hkResult;
    }

    private function RegLoadKey(int $hkey, string $subKey, string $hivePath){
        $hkeyHandle = $this->reg->newHKEY($hkey);

        $subKeyW = $this->natSvc->toWSTR($subKey);
        $hivePathW = $this->natSvc->toWSTR($hivePath);

        $result = $this->reg->RegLoadKeyW($hkeyHandle, $subKeyW, $hivePathW);
        if($result !== 0){
            return false;
        }
        return true;
    }

    private function RegUnloadKey(int $hkey, string $subKey){
        $hkeyHandle = $this->reg->newHKEY($hkey);
        $subKeyW = $this->natSvc->toWSTR($subKey);

        $result = $this->reg->RegUnLoadKeyW($hkeyHandle, $subKeyW);
        if($result !== 0){
            return false;
        }
        return true;
    }

    private static function trySplitKeyPath(string $path, &$hkey_out, &$subKey_out){
        $parts = explode("\\", $path, 2);
        if(count($parts) != 2) return false;

        list($root, $subKey) = $parts;
        
        $hkey = self::getHiveKey($root);
        if($hkey === null) return false;

        $hkey_out = $hkey;
        $subKey_out = $subKey;
        return true;
    }

    public function openKey(string $path){
        if(!self::trySplitKeyPath($path, $hkey, $subKey)){
            return null;
        }

        $flags = (0
            | Constants::KEY_ALL_ACCESS
            | Constants::KEY_WOW64_64KEY
        );
        $handle = $this->RegOpenKeyEx($hkey, $subKey, 0, $flags);
        if($handle === null) return null;

        return new RegistryKey($this->windows, $this->reg, $path, $handle);
    }

    public function loadHive(string $regKey, string $filePath){
        if(!self::trySplitKeyPath($regKey, $hkey, $subKey)){
            return false;
        }

        return $this->RegLoadKey($hkey, $subKey, $filePath);
    }

    public function unloadHive(string $regKey){
        if(!self::trySplitKeyPath($regKey, $hkey, $subKey)){
            return false;
        }

        return $this->RegUnloadKey($hkey, $subKey);
    }

    public function __construct(Windows $windows){
        $this->windows = $windows;
        $this->reg = new RegistryNative($windows);
        $this->natSvc = $windows->getNativeUtils();
    }
}
