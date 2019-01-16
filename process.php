<?php

require __DIR__ . '/vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;

/**
 * A class representing the assets_loader tool.
 */
class AssetsLoader {

  /**
   * The URL to be used for load the assets.
   *
   * @var string
   */
  protected $url;

  /**
   * The base site URL.
   *
   * @var string
   */
  protected $base_url;

  /**
   * The remote assets docroot.
   *
   * @var string
   */
  protected $remote_docroot;

  /**
   * The local assets docroot.
   *
   * @var string
   */
  protected $local_docroot;

  /**
   * Constructs a AssetsLoader object.
   *
   * @param string $url
   *   The URL to be used for load the assets.
   * @param string $remote_docroot
   *   The remote assets docroot.
   * @param string $local_docroot
   *   The local assets docroot.
   */
  function __construct($url, $remote_docroot, $local_docroot) {
    $this->url = $url;
    $this->remote_docroot = $remote_docroot;
    $this->local_docroot = $local_docroot;

    // Extract the base site URL.
    $url_components = parse_url($this->url);
    $this->base_url = $url_components['scheme'] . '://' . $url_components['host'];
  }

  /**
   * Returns a list of all image assets were found.
   *
   * @return array
   *   A list of assets.
   */
  public function assetsGetList() {
    $assets = array();

    // add a check
    $dom = HtmlDomParser::file_get_html($this->url, FALSE, NULL, 0);

    foreach($dom->find('img') as $element) {
      // @todo: remote_docroot can be /
      if (strpos($element->src, $this->remote_docroot) === FALSE) {
        continue;
      }

      $src = str_replace($this->base_url, '', $element->src);
      $assets[] = $this->base_url . $src;
    }

    return $assets;
  }

  /**
   * Helps to download the image assets to your local directory.
   */
  public function process() {
    if (!$assetsList = $this->assetsGetList()) {
      // todo
      return;
    }

    foreach ($assetsList as $item) {
      // add a check
      $content = file_get_contents($item);
      $test = $this->local_docroot . str_replace($this->base_url .'/'. $this->remote_docroot, '', $item);

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

$url = $_SERVER["argv"][1];
$remote_docroot = trim($_SERVER["argv"][2], '/');
$local_docroot = rtrim($_SERVER["argv"][3], '/');

// URL valid?
if (!filter_var($url, FILTER_VALIDATE_URL)) {
  echo "\033[33m [WARNING] {$url} is a valid URL\033[0m" . PHP_EOL;
}

// Local docroot exist and writable?
if (!is_dir($local_docroot) || !is_writable($local_docroot)){
  echo "\033[31m [ERROR] $local_docroot is not exist it is not writable.\033[0m" . PHP_EOL;
}

// Lets download the assets!
$assetsLoader = new AssetsLoader($url, $remote_docroot, $local_docroot);
$assetsLoader->process();
