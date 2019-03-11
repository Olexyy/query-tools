<?php

namespace Olexyy\QueryTools\Proxy;

/**
 * Class Proxy.
 *
 * @package Olexyy\QueryTools\Proxy
 */
class Proxy {

  /**
   * Type (http|https)
   *
   * @var string
   */
  public $type;

  /**
   * Ip.
   *
   * @var int
   */
  public $ip;

  /**
   * Port.
   *
   * @var string
   */
  public $port;

  /**
   * Speed (mbs)
   *
   * @var int
   */
  public $speed;

  /**
   * Up time in percents.
   *
   * @var int
   */
  public $upTime;

  /**
   * Proxy constructor.
   *
   * @param string $type
   *   Type.
   * @param string $ip
   *   Ip.
   * @param string $port
   *   Port.
   */
  public function __construct($type, $ip, $port) {

    $this->type = $type;
    $this->ip = $ip;
    $this->port = $port;
  }

  /**
   * {@inheritdoc}
   *
   * @return string
   *   String.
   */
  public function __toString() {

    return $this->toString();
  }

  /**
   * Casts object to string.
   *
   * @return string
   *   String.
   */
  public function toString() {

    return "{$this->type}://{$this->ip}:{$this->port}";
  }

  /**
   * Serializer.
   *
   * @return string
   *   Data.
   */
  public function sleep() {

    return serialize($this);
  }

  /**
   * @param string $data
   *   Data.
   *
   * @return $this
   *   This object.
   */
  public static function wakeUp($data) {

    return unserialize($data);
  }

}
