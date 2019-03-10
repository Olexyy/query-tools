<?php

namespace Olexyy\QueryTools\Proxy\Provider;

use Olexyy\QueryTools\Proxy\Proxy;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ProxyNova.
 *
 * @package Olexyy\QueryTools\Proxy\Provider
 */
class ProxyNova implements ProviderInterface {

  /**
   * @return string
   */
  public function getUrl() {
    return 'https://www.proxynova.com/proxy-server-list/country-ua/';
  }

  /**
   * @param string $content
   *
   * @return array
   */
  public function parse($content) {

    $crawler = new Crawler($content);
    $rows = [];

    if ($crawler->filter('table#tbl_proxy_list tbody tr')->count()) {
      foreach ($crawler->filter('table#tbl_proxy_list tbody tr')->getIterator() as $i => $node) {
        if ($row = $this->parseRow($node->textContent)) {
          $rows[] = $row;
        }
      }
    }

    return $rows;
  }

  /**
   * @param $row
   *
   * @return \Olexyy\QueryTools\Proxy\Proxy
   *   Proxy instance if any.
   */
  public function parseRow($row) {

    $elements = preg_split('~\R~', $row);
    foreach ($elements as &$element) {
      $element = trim($element);
    }
    $elements = array_values(array_filter($elements));
    $matches = [];
    if (count($elements) > 7) {
      if ($parsed = preg_match("@document\.write\(\'(.*)\'\.substr\(8\) \+ \'(.*)\'\);@", $elements[0], $matches)) {
        $ip = substr($matches[1], 8, strlen($matches[1])) . $matches[2];
        $port = $elements[1];
        $speed = (int) $elements[2];
        $uptime = (int) $elements[3];
        $proxy = new Proxy('http', $ip, $port);
        $proxy->upTime = $uptime;
        $proxy->speed = $speed;

        return $proxy;
      }
    }

    return NULL;
  }

}
