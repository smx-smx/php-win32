<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

namespace Smx\Windows;

use FFI;
use FFI\CData;
use Smx\Windows\Native\Libraries\Kernel32;
use Smx\Windows\Native\OwnedHandle;
use Smx\Windows\Utils\NativeUtils;

class File {
    private Windows $windows;
    private NativeUtils $natSvc;
    private Kernel32 $kernel32;
    private string $filePath;

    public function __construct(Windows $windows, string $filePath){
        $this->windows = $windows;
        $this->natSvc = $windows->getNativeUtils();
        $this->kernel32 = $windows->getKernel32();
        $this->filePath = $filePath;
    }

    public function getDriveLetter(){
        list($letter, $_) = explode(':', $this->filePath, 2);
        return $letter;
    }

    public function hardlinks(){
        $filePathW = $this->natSvc->toWSTR($this->filePath);
        $cchName = $this->kernel32->new('DWORD');

        /** get the initial buffer size */
        $this->kernel32->FindFirstFileNameW(
            $filePathW,
            0, FFI::addr($cchName), null
        );
        if($this->kernel32->GetLastError() !== 234){ // expect ERROR_MORE_DATA
            return;
        }
        
        /** allocate initial buffer */
        $buf = $this->natSvc->malloc($cchName->cdata * 2);
        
        /** get initial file */
        $hFile = $this->kernel32->FindFirstFileNameW(
            $filePathW,
            0, FFI::addr($cchName), $buf
        );
        if($this->natSvc->ptrval($hFile) === -1){
            return;
        }

        /** claim handle (close on return) **/
        $ownedHandle = new OwnedHandle($this->windows, $hFile);

        $driveLetter = $this->getDriveLetter();

        while(true){
            $currentFileNameW = FFI::string($buf, $cchName->cdata * 2);
            $currentFileName = $this->natSvc->fromWSTR($currentFileNameW);
            yield "{$driveLetter}:{$currentFileName}";

            /** fetch next file, handle buffer growing */
            while(true){
                $ret = $this->kernel32->FindNextFileNameW(
                    $ownedHandle->getValue(),
                    FFI::addr($cchName), $buf
                );
                if($ret === 0 && $this->kernel32->GetLastError() === 234){
                    $buf = $this->natSvc->malloc($cchName->cdata * 2);
                    continue; // try again
                }
                break;
            }
            if($ret === 0){
                $err = $this->kernel32->GetLastError();
                switch($err){
                    case 234: // ERROR_MORE_DATA
                        // there are more names, continue
                        break;
                    case 38: // ERROR_HANDLE_EOF
                    case 18: // ERROR_NO_MORE_FILES
                    default:
                        // no more items or error, stop
                        break 2;
                }
            }
            // continue loop (and handle current result)
        }
    }
}
