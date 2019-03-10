<?php

namespace Olexyy\QueryTools\Proxy\Provider;

/**
 * Interface ProviderInterface.
 *
 * @package Olexyy\QueryTools\Proxy
 */
interface ProviderInterface {

  /**
   * @return mixed
   */
  public function getUrl();

  /**
   * @param $content
   *
   * @return mixed
   */
  public function parse($content);

  /**
   * @param $row
   *
   * @return \Olexyy\QueryTools\Proxy\Proxy
   *   Proxy instance if any.
   * @throws \Exception
   */
  public function parseRow($row);

}
