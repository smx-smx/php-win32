<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

namespace Smx\Windows;

class WindowsInstance {
    private Registry $reg;

    public function __construct(Windows $windows){
        $this->reg = $windows->getRegistry();
    }

    public function getSystemRoot(){
        $key = $this->reg->openKey('HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows NT\CurrentVersion');
        return $key->read('SystemRoot');
    }

    public function getCurrentControlSet(){
        $key = $this->reg->openKey('HKEY_LOCAL_MACHINE\SYSTEM\Select');
        return $key->read('Current');
    }
}
