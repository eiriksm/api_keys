<?php

/**
 * @file
 * Contains \Drupal\api_keys\Authentication\Provider\ApiKeysAuthenticationProvider.
 */

namespace Drupal\api_keys\Authentication\Provider;

use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ApiKeysAuthenticationProvider.
 *
 * @package Drupal\api_keys\Authentication\Provider
 */
class ApiKeysAuthenticationProvider implements AuthenticationProviderInterface {
  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;
  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;
  /**
   * Database.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
  /**
   * Constructs the provider.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger interface.
   * @param \Drupal\Core\Database\Connection $database
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityManagerInterface $entity_manager, LoggerInterface $logger, Connection $database) {
    $this->configFactory = $config_factory;
    $this->entityManager = $entity_manager;
    $this->logger = $logger;
    $this->database = $database;
  }
  /**
   * Checks whether suitable authentication credentials are on the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return bool
   *   TRUE if authentication credentials suitable for this provider are on the
   *   request, FALSE otherwise.
   */
  public function applies(Request $request) {
    // Only applies if the api keys header is set.
    // @todo. Make this a property on the class, or probably configurable?
    $header = $request->headers->get('x-drupal-api-key');
    if (!$header) {
      return FALSE;
    }
    return TRUE;
  }
  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {
    // See if we can find the API key.
    $q = $this->database->select('api_keys', 'a')
      ->fields('a');

    // @todo. Hardcoded header ahead.
    $q->condition('a.user_key', $request->headers->get('x-drupal-api-key'));

    $res = $q->execute()->fetchObject();
    if (!$res) {
      return [];
    }
    else {
      return $this->entityManager->getStorage('user')->load($res->uid);
    }
  }
  /**
   * {@inheritdoc}
   */
  public function cleanup(Request $request) {}
  /**
   * {@inheritdoc}
   */
  public function handleException(GetResponseForExceptionEvent $event) {
    $exception = $event->getException();
    if ($exception instanceof AccessDeniedHttpException) {
      $event->setException(new UnauthorizedHttpException('Invalid consumer origin.', $exception));
      return TRUE;
    }
    return FALSE;
  }

}
