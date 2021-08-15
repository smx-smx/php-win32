<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Registry;

use Exception;
use FFI;
use FFI\CData;
use Smx\Windows\Constants;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Utils\NativeUtils;
use Smx\Windows\Windows;

class RegistryKey {
    private RegistryNative $reg;
    private CData $handle;
    private NativeUtils $natSvc;
    
    private Kernel32 $kernel32;

    private string $keyPath;

    public function getPath(){
        return $this->keyPath;
    }

    /**
     * @param mixed $data
     */
    public function write(string $valueName, int $type, $data, bool $typed = true){
        $valueNameW = $this->natSvc->toWSTR($valueName);

        if($typed){
            switch($type){
                case Constants::REG_DWORD:
                    $buf = pack('V', $data);
                    break;
                case Constants::REG_QWORD:
                    $buf = pack('P', $data);
                    break;
                case Constants::REG_SZ:
                case Constants::REG_EXPAND_SZ:
                    $buf = $this->natSvc->toWSTR($data);
                    break;
                case Constants::REG_MULTI_SZ:
                    $buf = '';
                    foreach($data as $str){
                        $buf.= $this->natSvc->toWSTR($str);
                    }
                    $buf.= "\x00\x00";
                    break;
                case Constants::REG_NONE:
                case Constants::REG_BINARY:
                default:
                    $buf = $data;
                    break;
            }
        } else {
            $buf = $data;
        }

        $result = $this->reg->RegSetKeyValueW($this->handle, null,
            $valueNameW, $type, $buf, strlen($buf)
        );
        if($result !== 0){
            return false;
        }
        return true;
    }

    public function read(string $valueName, int $flags = Constants::RRF_RT_ANY){
        $dwType = FFI::new('uint32_t');
        $cbData = FFI::new('uint32_t');

        $pdwType = FFI::addr($dwType);
        $pcbData = FFI::addr($cbData);

        $valueNameW = $this->natSvc->toWSTR($valueName);

        $result = $this->reg->RegGetValueW($this->handle, null,
            $valueNameW, $flags,
            $pdwType, null, $pcbData
        );
        if($result !== 0) return null;

        if($cbData->cdata === 0){
            /**
             * This normally means there is no data
             * However, in the SAM database, there are custom keys with 0 length data
             * and custom type
             * in other words, data is stored in the type field instead of the value
             */
            return $dwType->cdata;
            //return null;
        }
        
        $buf = $this->natSvc->malloc($cbData->cdata);
        $this->reg->RegGetValueW($this->handle, null,
            $valueNameW, Constants::RRF_RT_ANY,
            $pdwType, $buf, $pcbData
        );

        //var_dump($buf);

        switch($dwType->cdata){
            case Constants::REG_NONE:
            case Constants::REG_BINARY:
                $data = FFI::string($buf, $cbData->cdata);    
                return $data;
            case Constants::REG_DWORD:
                $data = FFI::string($buf, $cbData->cdata);
                return unpack("V", $data)[1];
            case Constants::REG_QWORD:
                $data = FFI::string($buf, $cbData->cdata);
                return unpack("P", $data)[1];
            case Constants::REG_MULTI_SZ:
                $data = FFI::string($buf, $cbData->cdata);
                
                $res = array();
                $last = 0;
                for($i = 0; $i<$cbData->cdata; $i+=2){
                    $hw = $data[$i+1] . $data[$i];
                    // null terminator
                    if($hw === "\x00\x00"){
                        $str = substr($data, $last, $i - $last);
                        $last = $i + 2;

                        if(strlen($str) > 0){
                            $res[] = $this->natSvc->fromWSTR($str);
                        }
                    }                  
                }
                return $res;
            case Constants::REG_SZ:
            case Constants::REG_EXPAND_SZ:
                $data = FFI::string($buf, $cbData->cdata - 2);
                return $this->natSvc->fromWSTR($data);
            default:
                throw new Exception("Not yet implemented: {$dwType->cdata}");
        }
    }

    public function createKey(string $subkeyName){
        $subkeyW = $this->natSvc->toWSTR($subkeyName);

        $flags = (0
            | Constants::KEY_ALL_ACCESS
            | Constants::KEY_WOW64_64KEY
        );

        $hkResult = $this->reg->newHKEY();
        $dwDisposition = $this->reg->new('DWORD');

        $result = $this->reg->RegCreateKeyExW($this->handle,
            $subkeyW, 0, null, 0, $flags, null,
            FFI::addr($hkResult),
            FFI::addr($dwDisposition)
        );

        if($result !== 0){
            return false;
        }
        
        $this->kernel32->CloseHandle($hkResult);
        return true;
    }

    public function enumerate(){
        $info = new RegistryKeyInfo($this->reg, $this->handle);

        $cchName = FFI::new('uint32_t');
        $cchClass = FFI::new('uint32_t');

        $keyName = $info->newKeyNameBuffer();
        $udClass = $info->newClassNameBuffer();

        // list of key names
        $keyNamesOut = array();

        for($i=0; $i<$info->numberOfSubKeys; $i++){  
            $cchName->cdata = $info->maxKeyNameLength;
            $cchClass->cdata = $info->maxClassNameLength;

            $lpcchName = FFI::addr($cchName);
            $lpcchClass = FFI::addr($cchClass);
          
            $ret = $this->reg->RegEnumKeyExW(
                $this->handle, $i,
                $keyName, $lpcchName, null,
                $udClass, $lpcchClass, null
            );
            switch($ret){
                case 0:
                    $keyNameLen = $lpcchName[0] * 2;
                    $keyNameBin = FFI::string($keyName, $keyNameLen);
                    $keyNameStr = $this->natSvc->fromWSTR($keyNameBin);
                    $keyNamesOut[] = $keyNameStr;
                    continue 2;
                case 259: //ERROR_NO_MORE_ITEMS
                    break 2;
                default:
                    break 2;
            }
        }

        natcasesort($keyNamesOut);
        return $keyNamesOut;
    }

    public function __construct(Windows $windows, RegistryNative $reg, string $keyPath, CData $handle){
        $this->reg = $reg;
        $this->handle = $handle;
        $this->keyPath = $keyPath;
        $this->natSvc = $windows->getNativeUtils();
        $this->kernel32 = $windows->getKernel32();
    }

    public function __destruct(){
        $this->reg->RegCloseKey($this->handle);
    }
}
