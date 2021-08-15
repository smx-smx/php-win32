<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows\Native\Libraries;

use FFI;
use Smx\Windows\Native\NativeLibrary;

class Kernel32 extends NativeLibrary {
    public function __construct(){
        parent::__construct("
            typedef void* HANDLE, *PHANDLE;
            typedef void* LPCWSTR;
            typedef uint32_t DWORD, *PDWORD;
            typedef uint32_t BOOL;
            typedef uintptr_t LONG;

            typedef struct _LUID {
                DWORD LowPart;
                LONG  HighPart;
            } LUID, *PLUID;

            typedef struct _LUID_AND_ATTRIBUTES {
                LUID  Luid;
                DWORD Attributes;
            } LUID_AND_ATTRIBUTES, *PLUID_AND_ATTRIBUTES;

            typedef struct _TOKEN_PRIVILEGES {
                DWORD               PrivilegeCount;
                LUID_AND_ATTRIBUTES Privileges[1];
            } TOKEN_PRIVILEGES, *PTOKEN_PRIVILEGES;

            DWORD GetCurrentProcessId();
            HANDLE OpenProcess(
                DWORD dwDesiredAccess,
                BOOL  bInheritHandle,
                DWORD dwProcessId
            );
            HANDLE GetCurrentProcess();
            BOOL OpenProcessToken(
                HANDLE  ProcessHandle,
                DWORD   DesiredAccess,
                PHANDLE TokenHandle
            );

            BOOL CloseHandle(
                HANDLE hObject
            );
            DWORD GetLastError();
        ","Kernel32.dll");
    }
}
