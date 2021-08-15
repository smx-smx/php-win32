<?php
/**
  * @package php-win32
  * @author Stefano Moioli
  * @copyright 2021 Stefano Moioli
  * @license https://opensource.org/licenses/Zlib
  */

use Smx\Windows\File;
use Smx\Windows\Windows;

require_once __DIR__ . '/../vendor/autoload.php';

$windows = new Windows;

$explorer = new File($windows, 'C:\Windows\explorer.exe');
foreach($explorer->hardlinks() as $node){
    var_dump($node);
}
