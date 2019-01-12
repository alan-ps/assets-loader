<?php

/**
 * @file
 */


require __DIR__ . '/vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;

class AssetsLoader {

  /**
   *
   */
  protected $domain;

  /**
   *
   */
  protected $remote_docroot;

  /**
   *
   */
  protected $local_docroot;

  /**
   *
   */
  protected $extensions;


  /**
   * Constructor
   */
  function __construct($domain, $remote_docroot, $local_docroot) {
    $this->domain = $domain;
    $this->remote_docroot = $remote_docroot;
    $this->local_docroot = $local_docroot;

    //
    $this->extensions = array('png', 'jpg', 'jpeg');
  }

  /**
   *
   */
  public function assetsGetExtensions() {
    return $this->extensions;
  }

  /**
   *
   */
  public function assetsSetExtensions(array $extensions) {
    $this->extensions = $extensions;
  }

  /**
   *
   * @return array
   *   A list of assets.
   */
  public function assetsGetList() {
    $assets = array();

    // add a check
    $dom = HtmlDomParser::file_get_html($this->domain, FALSE, NULL, 0);

    foreach($dom->find('img') as $element) {
      $src = str_replace($this->domain, '', $element->src);
      $assets[] = $this->domain . $src;
    }

    return $assets;
  }

  /**
   *
   */
  public function process() {
    if (!$assetsList = $this->assetsGetList()) {
      // todo
      return;
    }

    foreach ($assetsList as $item) {
      // add a check
      $content = file_get_contents($item);

      $test = $this->local_docroot . str_replace($this->domain .'/'. $this->remote_docroot, '', $item);

      // dir doesn't exist, make it
      if (!is_dir(dirname($test))) {
        mkdir(dirname($test), 0755, true);
      }

      file_put_contents($test, $content);
      print_r($test . "\n");
    }
  }
}

// The number of arguments should be 4 (the first one is a script filename):
// domain, remote docroor and local docroot.
if ($_SERVER['argc'] <> 4) {
  echo "\033[33m [WARNING] Invalid number of arguments. Please check README file for more details.\033[0m" . PHP_EOL;
  return;
}

$url = trim($_SERVER["argv"][1], '/');
$remote_docroot = trim($_SERVER["argv"][2], '/');
$local_docroot = rtrim($_SERVER["argv"][3], '/');

//
if (!filter_var($url, FILTER_VALIDATE_URL)) {
  echo "\033[33m [WARNING] $url is a valid URL\033[0m" . PHP_EOL;
}

//
if (!is_dir($local_docroot) || !is_writable($local_docroot)){
  echo "\033[31m [ERROR] $local_docroot is not exist it is not writable.\033[0m" . PHP_EOL;
}

//
$assetsLoader = new AssetsLoader($url, $remote_docroot, $local_docroot);
$assetsLoader->process();