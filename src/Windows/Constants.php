<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */
namespace Smx\Windows;

abstract class Constants {
    const KEY_ALL_ACCESS = 0xF003F;
    const KEY_CREATE_LINK = 0x0020;
    const KEY_CREATE_SUB_KEY = 0x0004;
    const KEY_ENUMERATE_SUB_KEYS = 0x0008;
    const KEY_EXECUTE = 0x20019;
    const KEY_NOTIFY = 0x0010;
    const KEY_QUERY_VALUE = 0x0001;
    const KEY_READ = 0x20019;
    const KEY_SET_VALUE = 0x0002;
    const KEY_WOW64_32KEY = 0x0200;
    const KEY_WOW64_64KEY = 0x0100;
    const KEY_WRITE = 0x20006;

    const HKEY_CLASSES_ROOT = 0x80000000;
    const HKEY_CURRENT_USER = 0x80000001;
    const HKEY_LOCAL_MACHINE = 0x80000002;
    const HKEY_USERS = 0x80000003;
    const HKEY_PERFORMANCE_DATA = 0x80000004;
    const HKEY_CURRENT_CONFIG = 0x80000005;
    const HKEY_DYN_DATA = 0x80000006;
    const HKEY_CURRENT_USER_LOCAL_SETTINGS = 0x80000007;
    const HKEY_PERFORMANCE_TEXT = 0x80000050;
    const HKEY_PERFORMANCE_NLSTEXT = 0x80000060;

    const RRF_RT_ANY = 0x0000ffff;
    const RRF_RT_DWORD = 0x00000018;
    const RRF_RT_QWORD = 0x00000048;
    const RRF_RT_REG_BINARY = 0x00000008;
    const RRF_RT_REG_DWORD = 0x00000010;
    const RRF_RT_REG_EXPAND_SZ = 0x00000004;
    const RRF_RT_REG_MULTI_SZ = 0x00000020;
    const RRF_RT_REG_NONE = 0x00000001;
    const RRF_RT_REG_QWORD = 0x00000040;
    const RRF_RT_REG_SZ = 0x00000002;

    const RRF_NOEXPAND = 0x10000000;
    const RRF_ZEROONFAILURE = 0x20000000;
    const RRF_SUBKEY_WOW6464KEY = 0x00010000;
    const RRF_SUBKEY_WOW6432KEY = 0x00020000;

    const REG_NONE = 0;
    const REG_SZ = 1;
    const REG_EXPAND_SZ = 2;
    const REG_BINARY = 3;
    const REG_DWORD = 4;
    const REG_LINK = 6;
    const REG_MULTI_SZ = 7;
    const REG_RESOURCE_LIST = 8;
    const REG_QWORD = 11;

    const DELETE = 0x00010000;
    const READ_CONTROL = 0x00020000;
    const SYNCHRONIZE = 0x00100000;
    const WRITE_DAC = 0x00040000;
    const WRITE_OWNER = 0x00080000;

    const SE_PRIVILEGE_ENABLED_BY_DEFAULT = 0x00000001;
    const SE_PRIVILEGE_ENABLED = 0x00000002;
    const SE_PRIVILEGE_REMOVED = 0x00000004;

    const TOKEN_QUERY = 0x0008;
    const TOKEN_ADJUST_PRIVILEGES = 0x0020;

    const STANDARD_RIGHTS_REQUIRED = 0x000F0000;
}
