<?php

namespace Drupal\persistent_visitor_parameters\EventSubscriber;

use Drupal\Core\Render\HtmlResponse;
use Drupal\Core\Session\AccountInterface;
use Drupal\persistent_visitor_parameters\CookieManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ResponseSubscriber.
 */
class ResponseSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  protected $cookieManager;

  /**
   * @inheritDoc
   */
  protected $currentUser;

  /**
   * @inheritDoc
   */
  public function __construct(CookieManager $cookie_manager, AccountInterface $current_user) {
    $this->cookieManager = $cookie_manager;
    $this->currentUser = $current_user;
  }

  /**
   * @inheritDoc
   */
  public function onRespond(FilterResponseEvent $event) {
    if (!$event->isMasterRequest()) {
      return;
    }

    // only process Html Responses.
    $response = $event->getResponse();
    if (!$response instanceof HtmlResponse) {
      return;
    }

    $request = $event->getRequest();

    // respect Do Not Track if not otherwise configured
    if (!$this->cookieManager->dontRespectDnt()) {
      if ($request->server->get('HTTP_DNT')) {
        return;
      }
    }

    // only non-logged users
    if (!$this->currentUser->isAnonymous()) {
      return;
    }

    // create cookie only on the very first visit
    if (!empty($this->cookieManager->getCookie())) {
      return;
    }

    $this->cookieManager->setCookie($response);
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onRespond'];
    return $events;
  }

}