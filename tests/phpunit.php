<?php

use Garden\Container\Container;
use Vanilla\Addon;
use Vanilla\InjectableInterface;

// Define some constants to help with testing.
define('APPLICATION', 'Vanilla Tests');
define('PATH_ROOT', realpath(__DIR__.'/..'));

// Copy the cgi-bin files.
$dir = PATH_ROOT.'/cgi-bin';
if (!file_exists($dir)) {
    mkdir($dir);
}

$files = glob(__DIR__."/travis/templates/vanilla/cgi-bin/*.php");
foreach ($files as $file) {
    $dest = $dir.'/'.basename($file);
    $r = copy($file, $dest);
}

// ===========================================================================
// Adding the minimum dependencies to support unit testing for core libraries
// ===========================================================================
require PATH_ROOT.'/environment.php';

// Set up the dependency injection container.
$bootstrap = new \VanillaTests\Bootstrap();
$bootstrap->run(new Container());

// Clear the test cache.
\Gdn_FileSystem::removeFolder(PATH_ROOT.'/tests/cache');

require_once PATH_LIBRARY_CORE.'/functions.validation.php';

require_once PATH_LIBRARY_CORE.'/functions.render.php';
