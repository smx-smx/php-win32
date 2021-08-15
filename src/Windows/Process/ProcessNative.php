<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Process;

use FFI;
use FFI\CData;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Windows;

/**
 * @method CData OpenProcess($dwDesiredAccess, $bInheritHandle, $dwProcessId)
 * @method int GetCurrentProcessId()
 * @method int CloseHandle($handle)
 */
class ProcessNative {
    private Kernel32 $ffi;

    public function __call($name, $arguments = []){
        return $this->ffi->{$name}(...$arguments);
    }

    public function new($type, $owned = TRUE, $persistent = FALSE): CData {
        return $this->ffi->new($type, $owned, $persistent);
    }

    public function type($type){
        return $this->ffi->type($type);
    }

    public function __construct(Windows $windows){
        $this->ffi = $windows->getKernel32();
    }
}
