<?php

namespace Olexyy\QueryTools\Proxy;

use GuzzleHttp\Client;
use Olexyy\QueryTools\GeneratorInterface;
use Olexyy\QueryTools\Proxy\Provider\ProviderInterface;
use Olexyy\QueryTools\Proxy\Provider\ProxyNova;
use Olexyy\QueryTools\UserAgent\Generator as UserAgentGenerator;

/**
 * Class Generator.
 *
 * @package Olexyy\QueryTools\Proxy
 */
class Generator implements GeneratorInterface {

  /**
   * Http client.
   *
   * @var \GuzzleHttp\Client|null
   */
  protected $client;

  /**
   * Connect timeout.
   *
   * @var int
   */
  protected $connectTimeout;

  /**
   * Options array.
   *
   * @var array|string[]
   */
  protected $options;

  /**
   * Provider.
   *
   * @var ProviderInterface
   */
  protected $provider;

  /**
   * Provider proxies.
   *
   * @var Proxy[]
   */
  protected $proxies;

  /**
   * Active proxy.
   *
   * @var Proxy
   */
  protected $proxy;

  /**
   * Factory method.
   *
   * @param array|string[] $options
   *   Options array.
   * @param \GuzzleHttp\Client|null $client
   *   Http client.
   *
   * @return $this
   *   Instance.
   */
  public static function create(array $options, Client $client = NULL) {

    return new static($options, $client);
  }

  /**
   * Generator constructor.
   *
   * @param array|string[] $options
   *   Options array.
   * @param \GuzzleHttp\Client|null $client
   *   Http client.
   */
  public function __construct(array $options, Client $client = NULL) {

    $this->client = $client ? $client : new Client();
    $this->connectTimeout = !empty($options['connectTimeout']) ? $options['connectTimeout'] : 3;
    $this->options = $options;
    $this->provider = new ProxyNova();
    $this->proxies = !empty($options['proxies']) ? $options['proxies'] : [];
  }

  /**
   * Getter for fresh structure.
   *
   * @param bool $findLive
   *   Find live proxy and select.
   *
   * @return $this
   *   Chaining.
   */
  public function generate($findLive = TRUE) {

    $response = $this->client->get($this->provider->getUrl(), [
      'User-Agent' => UserAgentGenerator::create()->generate(),
      'connect_timeout' => $this->connectTimeout,
    ]);
    $content = $response->getBody()->getContents();
    // This will return null if none match or parse error.
    $this->proxies = $this->provider->parse($content);
    if ($findLive) {
      $this->proxy = $this->findLive();
    }

    return $this;
  }

  /**
   * Getter.
   *
   * @return Proxy[]
   *   Proxy list.
   */
  public function getProxies() {

    return $this->proxies;
  }

  /**
   * Proxies setter.
   *
   * @param Proxy[]|array $proxies
   *   Proxies.
   */
  public function setProxies($proxies) {

    $this->proxies = $proxies;
  }

  /**
   * Getter.
   *
   * @return Proxy
   *   Selected proxy.
   */
  public function getProxy() {

    return $this->proxy;
  }

  /**
   * @param Proxy $proxy
   *
   * @return bool
   */
  public function ping(Proxy $proxy) {

    try {
      return (bool) $this->client->get('https://www.google.com/', [
        'connect_timeout' => 5,
        'proxy' => $proxy->toString(),
      ]);
    }
    catch (\Exception $exception) {
      return FALSE;
    }
  }

  /**
   * Getter for live proxy.
   *
   * @return \Olexyy\QueryTools\Proxy\Proxy|mixed|null
   *   Proxy if any.
   */
  public function findLive() {

    foreach ($this->proxies as $index => $proxy) {
      if ($proxy->upTime >= 80) {
        if ($this->ping($proxy)) {
          return $proxy;
        }
        else {
          unset($this->proxies[$index]);
        }
      }
    }
    foreach ($this->proxies as $index => $proxy) {
      if ($proxy->upTime >= 50 && $proxy->upTime < 80) {
        if ($this->ping($proxy)) {
          return $proxy;
        }
        else {
          unset($this->proxies[$index]);
        }
      }
    }
    foreach ($this->proxies as $index => $proxy) {
      if ($proxy->upTime < 50) {
        if ($this->ping($proxy)) {
          return $proxy;
        }
        else {
          unset($this->proxies[$index]);
        }
      }
    }

    return NULL;
  }

}
