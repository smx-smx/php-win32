<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows;

use FFI;
use Smx\Windows\Native\Handle;
use Smx\Windows\Native\Libraries\Advapi32;
use Smx\Windows\Utils\NativeUtils;

class PrivilegeManager {
    private Advapi32 $advapi32;
    private NativeUtils $natSvc;

    public function __construct(Windows $windows){
        $this->advapi32 = $windows->getAdvapi32();
        $this->natSvc = $windows->getNativeUtils();
    }

    private function lookupPrivilege(string $privName){
        $luid = $this->advapi32->new('LUID');

        $lpNameW = $this->natSvc->toWSTR($privName);
       
        $ret = $this->advapi32->LookupPrivilegeValueW(null, $lpNameW, FFI::addr($luid));
        if($ret === 0){
            return null;
        }

        return $luid;
    }

    public function enablePrivilege(Handle $token, string $privName){
        $luid = $this->lookupPrivilege($privName);
        if($luid === null) return false;
        
        $tp = $this->advapi32->new('TOKEN_PRIVILEGES');
        $tp->PrivilegeCount = 1;
        $tp->Privileges[0]->Attributes = Constants::SE_PRIVILEGE_ENABLED;
        $tp->Privileges[0]->Luid = $luid;

        $ret = $this->advapi32->AdjustTokenPrivileges(
            $token->getValue(), 0,
            FFI::addr($tp), FFI::sizeof($tp),
            null, null
        );
        if($ret === 0){
            return false;
        }
        return true;
    }
}
