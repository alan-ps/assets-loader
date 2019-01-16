<?php

namespace AssetsLoader;

use Sunra\PhpSimple\HtmlDomParser;
use AssetsLoader\AssetsLoaderLogs;

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
      echo AssetsLoaderLogs::status($test);
    }
  }
}
