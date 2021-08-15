<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

namespace Smx\Windows\Native;

use FFI\CData;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Windows;

class OwnedHandle extends Handle {
    private Kernel32 $kernel32;

    public function __construct(Windows $windows, CData $handle){
        parent::__construct($handle);
        $this->kernel32 = $windows->getKernel32();
    }

    public function __destruct(){
        $this->kernel32->CloseHandle($this->handle);
    }
}
