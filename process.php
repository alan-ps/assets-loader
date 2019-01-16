<?php

require __DIR__ . '/vendor/autoload.php';

use AssetsLoader\AssetsLoader;
use AssetsLoader\AssetsLoaderLogs;

// The number of arguments should be 4 (the first one is a script filename):
// domain, remote docroor and local docroot.
if ($_SERVER['argc'] <> 4) {
  echo AssetsLoaderLogs::warning('Invalid number of arguments. Please check README file for more details.');
  return;
}

$url = $_SERVER["argv"][1];
$remote_docroot = trim($_SERVER["argv"][2], '/');
$local_docroot = rtrim($_SERVER["argv"][3], '/');

// URL valid?
if (!filter_var($url, FILTER_VALIDATE_URL)) {
  echo AssetsLoaderLogs::warning("{$url} is not a valid URL.");
  return;
}

// Local docroot exist and writable?
if (!is_dir($local_docroot) || !is_writable($local_docroot)){
  echo AssetsLoaderLogs::error("{$local_docroot} is not exist it is not writable.");
  return;
}

// Lets download the assets!
$assetsLoader = new AssetsLoader($url, $remote_docroot, $local_docroot);
$assetsLoader->process();
