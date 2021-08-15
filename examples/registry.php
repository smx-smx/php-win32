<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

use Smx\Windows\Constants;
use Smx\Windows\PrivilegeManager;
use Smx\Windows\Process;
use Smx\Windows\ProcessManager;
use Smx\Windows\Registry;
use Smx\Windows\Windows;

require_once __DIR__ . '/../vendor/autoload.php';

$windows = new Windows;

$reg = new Registry($windows);
$procMan = new ProcessManager($windows);
$privMgr = new PrivilegeManager($windows);

$proc = $procMan->openProcess();
$tokenHandle = $proc->newTokenHandle(Constants::TOKEN_ADJUST_PRIVILEGES | Constants::TOKEN_QUERY);
$res = $privMgr->enablePrivilege($tokenHandle, "SeRestorePrivilege");
assert($res === true);

$res = $privMgr->enablePrivilege($tokenHandle, "SeBackupPrivilege");
assert($res === true);
$tokenHandle = null;

$key = $reg->openKey('HKEY_LOCAL_MACHINE\SYSTEM\Select');
$data = $key->read('Current');
print("Current Control Set: {$data}\n");

$su = new \Smx\Windows\Sam\Users($reg);
foreach($su->users() as $user){
    var_dump($user->read(''));
}
