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
use Smx\Windows\Native\Handle;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Native\OwnedHandle;
use Smx\Windows\Utils\NativeUtils;
use Smx\Windows\Windows;

class Process {
    private Windows $windows;
    private ProcessNative $proc;
    private Kernel32 $kernel32;
    private NativeUtils $natSvc;
    private CData $handle;

    public function __construct(Windows $windows, ProcessNative $proc, CData $handle){
        $this->windows = $windows;
        $this->handle = $handle;
        $this->proc = $proc;
        $this->natSvc = $windows->getNativeUtils();
        $this->kernel32 = $windows->getKernel32();
    }

    public function __destruct(){
        $this->proc->CloseHandle($this->handle);
    }

    public function newTokenHandle(int $flags): ?Handle {
        $hToken = $this->proc->new('HANDLE');
        $ret = $this->proc->OpenProcessToken($this->handle, $flags, FFI::addr($hToken));
        if($ret === 0){
            return null;
        }
        return new OwnedHandle($this->windows, $hToken);
    }
}
