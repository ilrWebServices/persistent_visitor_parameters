<?php

namespace Drupal\persistent_visitor_parameters;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CookieManager.
 */
class CookieManager {

  /**
   * @inheritDoc
   */
  const COOKIE_NAME = 'pvp_stored_variables';

  /**
   * @inheritDoc
   */
  protected $requestStack;

  /**
   * @inheritDoc
   */
  protected $configFactory;

  /**
   * @inheritDoc
   */
  protected $time;

  /**
   * @inheritDoc
   */
  public function __construct(RequestStack $request_stack, ConfigFactoryInterface $config_factory, TimeInterface $time) {
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
    $this->time = $time;
  }

  /**
   * @inheritDoc
   */
  public function setCookie($response) {
    $cookieContent = array_merge($this->neededGetParams(), $this->neededServerParams());

    if ($cookieContent) {
      $expire = $this->cookieExpiration();
      $cookie = new Cookie(self::COOKIE_NAME, serialize($cookieContent), $expire, '/');
      $response->headers->setCookie($cookie);
    }

  }

  /**
   * @inheritDoc
   */
  public function getCookie() {
    $cookie = $this->requestStack->getCurrentRequest()->cookies->get(self::COOKIE_NAME);
    return unserialize($cookie);
  }

  /**
   * @inheritDoc
   */
  public function neededGetParams() {
    $config = $this->configFactory->get('persistent_visitor_parameters.settings');
    $getParamsConfig = array_filter(explode('|', $config->get('get_parameters')));
    $queryParams = $this->requestStack->getCurrentRequest()->query->all();
    return array_intersect_key($queryParams, array_flip($getParamsConfig));
  }

  /**
   * @inheritDoc
   */
  public function neededServerParams() {
    $config = $this->configFactory->get('persistent_visitor_parameters.settings');
    $serverParamsConfig = array_filter(explode('|', $config->get('server_parameters')));
    $serverParams = $this->requestStack->getCurrentRequest()->server->all();
    return array_intersect_key($serverParams, array_flip($serverParamsConfig));
  }

  /**
   * @inheritDoc
   */
  public function cookieExpiration() {
    $config = $this->configFactory->get('persistent_visitor_parameters.settings');

    switch ($config->get('cookie_expire')) {
      case '1':
        // maximum future time value
        $value = '2147483647';
        break;

      case '2':
        $value = $this->time->getRequestTime() + $config->get('custom_expire');
        break;

      default:
        $value = 0;
        break;
    }

    return $value;
  }

  /**
   * @inheritDoc
   */
  public function dontRespectDnt() {
    $config = $this->configFactory->get('persistent_visitor_parameters.settings');
    $dontRespectConfig = $config->get('dont_respect_dnt');
    return empty($dontRespectConfig) ? FALSE : TRUE;
  }

}