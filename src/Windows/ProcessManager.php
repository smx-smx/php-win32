<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows;

use Smx\Windows\Native\Libraries\Advapi32;
use Smx\Windows\Process\Process;
use Smx\Windows\Process\ProcessNative;
use Smx\Windows\Utils\NativeUtils;

class ProcessManager {
    private Windows $windows;
    private ProcessNative $proc;
    private NativeUtils $natSvc;

    public function __construct(Windows $windows){
        $this->windows = $windows;
        $this->proc = new ProcessNative($windows);
        $this->natSvc = $windows->getNativeUtils();
    }

    public function openProcess(?int $pid = null) : ?Process {
        if($pid !== null){
            $pid = $this->proc->GetCurrentProcessId();
            $flags = (0
                | Constants::STANDARD_RIGHTS_REQUIRED
                | Constants::SYNCHRONIZE
                | 0xFFFF
            );
            $hProc = $this->proc->OpenProcess($flags, 0, $pid);
            if($this->natSvc->ptrval($hProc) === -1){
                return null;
            }
        } else {
            $hProc = $this->proc->GetCurrentProcess();
        }
        return new Process($this->windows, $this->proc, $hProc);
    }

}
