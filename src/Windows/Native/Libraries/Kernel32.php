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
            typedef void* LPVOID;
            typedef void* LPCWSTR;
            typedef uint32_t DWORD, *PDWORD, *LPDWORD;
            typedef uint16_t WORD;
            typedef uint32_t BOOL;
            typedef uintptr_t LONG;
            typedef void * HMODULE;
			typedef char* LPCSTR;
			typedef void (*FARPROC)();
            typedef uint16_t WCHAR;
            typedef void* PWSTR;

            typedef struct _FILETIME {
                DWORD dwLowDateTime;
                DWORD dwHighDateTime;
            } FILETIME, *PFILETIME, *LPFILETIME;

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

            HMODULE LoadLibraryW(LPCWSTR lpLibFileName);
			FARPROC GetProcAddress(HMODULE hModule, LPCSTR lpProcName);
			BOOL FreeLibrary(HMODULE hLibModule);

            typedef enum _FINDEX_INFO_LEVELS {
                FindExInfoStandard,
                FindExInfoBasic,
                FindExInfoMaxInfoLevel
            } FINDEX_INFO_LEVELS;

            typedef enum _FINDEX_SEARCH_OPS {
                FindExSearchNameMatch,
                FindExSearchLimitToDirectories,
                FindExSearchLimitToDevices,
                FindExSearchMaxSearchOp
            } FINDEX_SEARCH_OPS;

            typedef struct _WIN32_FIND_DATAW {
                DWORD    dwFileAttributes;
                FILETIME ftCreationTime;
                FILETIME ftLastAccessTime;
                FILETIME ftLastWriteTime;
                DWORD    nFileSizeHigh;
                DWORD    nFileSizeLow;
                DWORD    dwReserved0;
                DWORD    dwReserved1;
                WCHAR    cFileName[260];
                WCHAR    cAlternateFileName[14];
                DWORD    dwFileType;
                DWORD    dwCreatorType;
                WORD     wFinderFlags;
            } WIN32_FIND_DATAW, *PWIN32_FIND_DATAW, *LPWIN32_FIND_DATAW;

            HANDLE FindFirstFileExW(
                LPCWSTR            lpFileName,
                FINDEX_INFO_LEVELS fInfoLevelId,
                LPVOID             lpFindFileData,
                FINDEX_SEARCH_OPS  fSearchOp,
                LPVOID             lpSearchFilter,
                DWORD              dwAdditionalFlags
            );
            BOOL FindNextFileW(
                HANDLE             hFindFile,
                LPWIN32_FIND_DATAW lpFindFileData
            );

            HANDLE FindFirstFileNameW(
                LPCWSTR lpFileName,
                DWORD   dwFlags,
                LPDWORD StringLength,
                PWSTR   LinkName
            );
            BOOL FindNextFileNameW(
                HANDLE  hFindStream,
                LPDWORD StringLength,
                PWSTR   LinkName
            );
        ","Kernel32.dll");
    }
}
