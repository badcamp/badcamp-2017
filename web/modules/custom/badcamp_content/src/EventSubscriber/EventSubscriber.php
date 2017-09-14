<?php

namespace Drupal\badcamp_content\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect 403 to User Login event subscriber.
 */
class EventSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new R4032LoginSubscriber.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * Redirects on 403 Access Denied kernel exceptions.
   */
  public function onKernelResponse(Event $event) {
    $req = $event->getRequest();
    if ($req->attributes->get('_route') == 'view.my_schedule.page_1') {
      $uid = \Drupal::routeMatch()->getParameter('user');
      $user = User::load($uid);
      if($uid != $this->currentUser->id() && $user->get('field_make_my_schedule_public')->getString() != 1) {
        throw new AccessDeniedHttpException('The used authentication method is not allowed on this route.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = array('onKernelResponse', 100);
    return $events;
  }

}
