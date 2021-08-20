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

/**
 * @method int RegOpenKeyExW($hKey, $subKey, $options, $samDesired, $phkResult)
 * @method int RegCloseKey($handle)
 * @method int RegEnumKeyExW($hKey, int $dwIndex, $lpName, $lpcchName, $lpReserved, $lpClass, $lpcchClass, $lpftLastWriteTime)
 * @method int RegQueryInfoKeyW($hKey, $lpClass, $lpcchClass, $lpReserved, $lpcSubKeys, $lpcbMaxSubKeyLen, $lpcbMaxClassLen, $lpcValues, $lpcbMaxValueNameLen, $lpcbMaxValueLen, $lpcbSecurityDescriptor, $lpftLastWriteTime)
 * @method int RegGetValueW($hkey, $lpSubKey, $lpValue, $dwFlags, $pdwType, $pvData, $pcbData)
 * @method int RegLoadKeyW($hKey, $lpSubKey, $lpFile)
 */
class Advapi32 extends NativeLibrary {
    public function __construct(){
        parent::__construct("
            typedef void* HANDLE;
            typedef void* HKEY, *PHKEY;
            typedef void* LPCWSTR;
            typedef void* LPVOID;
            typedef void* LPBYTE;
            typedef const void* LPCVOID;
            typedef uint32_t DWORD, *PDWORD, *LPDWORD;
            typedef uint32_t REGSAM;
            typedef uint32_t BOOL;
            typedef void* LPWSTR;
            typedef void* PVOID;
            typedef uint32_t LONG;
            typedef uint32_t LSTATUS;

            typedef struct _FILETIME {
                DWORD dwLowDateTime;
                DWORD dwHighDateTime;
            } FILETIME, *PFILETIME, *LPFILETIME;

            typedef struct _SECURITY_ATTRIBUTES {
                DWORD nLength;
                LPVOID lpSecurityDescriptor;
                BOOL bInheritHandle;
            } SECURITY_ATTRIBUTES, *PSECURITY_ATTRIBUTES, *LPSECURITY_ATTRIBUTES;

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

            LSTATUS RegLoadKeyW(
                HKEY    hKey,
                LPCWSTR lpSubKey,
                LPCWSTR lpFile
            );
            LSTATUS RegUnLoadKeyW(
                HKEY    hKey,
                LPCWSTR lpSubKey
            );

            LSTATUS RegOpenKeyExW(
                HKEY hKey,
                LPCWSTR lpSubKey,
                DWORD ulOptions,
                REGSAM samDesired,
                PHKEY phkResult
            );
            LSTATUS RegQueryInfoKeyW(
                HKEY      hKey,
                LPWSTR    lpClass,
                LPDWORD   lpcchClass,
                LPDWORD   lpReserved,
                LPDWORD   lpcSubKeys,
                LPDWORD   lpcbMaxSubKeyLen,
                LPDWORD   lpcbMaxClassLen,
                LPDWORD   lpcValues,
                LPDWORD   lpcbMaxValueNameLen,
                LPDWORD   lpcbMaxValueLen,
                LPDWORD   lpcbSecurityDescriptor,
                PFILETIME lpftLastWriteTime
            );
            LSTATUS RegEnumKeyExW(
                HKEY      hKey,
                DWORD     dwIndex,
                LPWSTR    lpName,
                LPDWORD   lpcchName,
                LPDWORD   lpReserved,
                LPWSTR    lpClass,
                LPDWORD   lpcchClass,
                PFILETIME lpftLastWriteTime
            );
            LSTATUS RegEnumValueW(
                HKEY    hKey,
                DWORD   dwIndex,
                LPWSTR  lpValueName,
                LPDWORD lpcchValueName,
                LPDWORD lpReserved,
                LPDWORD lpType,
                LPBYTE  lpData,
                LPDWORD lpcbData
            );
            LSTATUS RegRenameKey(
                HKEY    hKey,
                LPCWSTR lpSubKeyName,
                LPCWSTR lpNewKeyName
            );
            LSTATUS RegGetValueW(
                HKEY    hkey,
                LPCWSTR lpSubKey,
                LPCWSTR lpValue,
                DWORD   dwFlags,
                LPDWORD pdwType,
                PVOID   pvData,
                LPDWORD pcbData
            );
            LSTATUS RegSetKeyValueW(
                HKEY    hKey,
                LPCWSTR lpSubKey,
                LPCWSTR lpValueName,
                DWORD   dwType,
                LPCVOID lpData,
                DWORD   cbData
            );
            LSTATUS RegCreateKeyExW(
                HKEY                        hKey,
                LPCWSTR                     lpSubKey,
                DWORD                       Reserved,
                LPWSTR                      lpClass,
                DWORD                       dwOptions,
                REGSAM                      samDesired,
                const LPSECURITY_ATTRIBUTES lpSecurityAttributes,
                PHKEY                       phkResult,
                LPDWORD                     lpdwDisposition
            );
            LSTATUS RegCloseKey(
                HKEY hKey
            );


            BOOL LookupPrivilegeValueW(
                LPCWSTR lpSystemName,
                LPCWSTR lpName,
                PLUID   lpLuid
            );
            BOOL AdjustTokenPrivileges(
                HANDLE            TokenHandle,
                BOOL              DisableAllPrivileges,
                PTOKEN_PRIVILEGES NewState,
                DWORD             BufferLength,
                PTOKEN_PRIVILEGES PreviousState,
                PDWORD            ReturnLength
            );

        ", "Advapi32.dll");
    }
 }
