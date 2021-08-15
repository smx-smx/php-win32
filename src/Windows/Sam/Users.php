<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

namespace Smx\Windows\Sam;

use Generator;
use Smx\Windows\Constants;
use Smx\Windows\Registry;
use Smx\Windows\Registry\RegistryKey;
use Smx\Windows\Utils\Struct;

class Users {
    private Registry $reg;
    private Struct $structF;

    public function __construct(Registry $reg){
        $this->reg = $reg;

        $this->structF = new Struct(array(
            "unk0" => "a8",
            "last_login" => "a8",
            "unk1" => "a8",
            "passord_reset_time" => "a8",
            "expiration_time" => "a8",
            "last_failed_login" => "a8",
            "rid" => "V",
            "account_status" => "C",
            "country_code" => "v",
            "unk2" => "v",
            "invalid_login_count" => "v",
            "login_count" => "v"
        ), 0x43);
    }
    

    /**
     * @return Generator|RegistryKey[]
     */
    public function users() {
        $hkSAM = 'HKEY_LOCAL_MACHINE\SAM\SAM';
        $names = $this->reg->openKey("{$hkSAM}\\Domains\\Account\\Users\\Names");
        
        foreach($names->enumerate() as $userName){
            /** resolve the user RID */
            $user = $this->reg->openKey("{$hkSAM}\\Domains\\Account\\Users\\Names\\{$userName}");
            yield $user;
        }
    }

    //// WIP

    private function decodeSamData(int $rid){
        $hkSAM = 'HKEY_LOCAL_MACHINE\SAM\SAM';

        /** lookup the SAM entry */
        $ridHex = sprintf("%08X", $rid);

        $user = $this->reg->openKey("{$hkSAM}\\Domains\\Account\\Users\\{$ridHex}");
        
        $F = $user->read('F');
        $V = $user->read('V');

        $fData = $this->structF->fromData($F);
        var_dump($fData);
    }
}
