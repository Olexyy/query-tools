<?php

namespace Olexyy\QueryTools\UserAgent;

use Olexyy\QueryTools\GeneratorInterface;

/**
 * Class Generator.
 *
 * @package Olexyy\QueryTools\UserAgent
 */
class Generator implements GeneratorInterface {

  const CHROME = 'chrome';
  const EXPLORER = 'explorer';
  const FIREFOX = 'firefox';
  const OPERA = 'opera';
  const SAFARI = 'safari';

  /**
   * One of types above.
   *
   * @var null|string
   */
  protected $type;

  /**
   * Inline constructor.
   *
   * @param null|string $type
   *   Browser type.
   *
   * @return $this
   *   Instance.
   */
  public static function create($type = NULL) {

    return new static($type);
  }

  /**
   * Generator constructor.
   *
   * @param null|string $type
   *   Browser type.
   */
  public function __construct($type = NULL) {

    $this->type = $type;
  }

  /**
   * Getter.
   *
   * @return array|string[]
   *   Browsers list.
   */
  public function getBrowsers() {

    return [
      static::CHROME, static::EXPLORER, static::FIREFOX, static::OPERA, static::SAFARI,
    ];
  }

  /**
   * Generator.
   *
   * @return string|null
   *   User agent if any.
   */
  public function generate() {

    $browser = $this->type;
    if (!$browser || !in_array($browser, $this->getBrowsers())) {
      $browser = $this->getBrowsers()[mt_rand(0, count($this->getBrowsers()) - 1)];
    }
    $path = dirname(__FILE__) . "/Data/{$browser}.txt";
    if ($list = file_get_contents($path)) {
      if ($list = array_filter(explode(PHP_EOL, $list))) {

        return trim($list[mt_rand(0, count($list) - 1)]);
      }
    }

    return NULL;
  }

}
