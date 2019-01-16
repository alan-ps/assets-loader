<?php

namespace AssetsLoader;

/**
 * Static class represents the message wrappers for assets loader.
 */
class AssetsLoaderLogs {

  /**
   * Warning message wrapper.
   */
  public static function warning($msg) {
    return "\033[33m [WARNING] {$msg}\033[0m" . PHP_EOL;
  }

  /**
   * Error message wrapper.
   */
  public static function error($msg) {
    return "\033[31m [WARNING] {$msg}\033[0m" . PHP_EOL;
  }

  /**
   * Status message wrapper.
   */
  public static function status($msg) {
    return "\033[32m [OK] {$msg}\033[0m" . PHP_EOL;
  }
}
