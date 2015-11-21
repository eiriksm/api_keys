<?php

/**
 * @file
 * Contains \Drupal\api_keys\Controller\UserApiKeysController.
 */

namespace Drupal\api_keys\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\FlattenException;


/**
 * Class UserApiKeysController.
 *
 * @package Drupal\api_keys\Controller
 */
class UserApiKeysController extends ControllerBase {

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }
  /**
   * Construct the userTempController.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   A database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Index.
   * @param \Drupal\user\UserInterface $user
   * @return array
   * @throws \Exception
   * @internal param string $uid
   */
  public function index(UserInterface $user) {
    // See if the user already has an API key.
    $q = $this->database->select('api_keys', 'a')
      ->fields('a');
    $q->condition('a.uid', $user->id());
    $user_key_object = $q->execute()->fetchObject();

    if (!$user_key_object) {
      // The user does not have a key. Generate one for them.
      $user_key = sha1(uniqid());
      // Insert it to the database.
      $this->database
        ->insert('api_keys')
        ->fields(array(
          'uid' => $user->id(),
          'user_key' => $user_key,
        ))
        ->execute();
    }
    else {
      $user_key = $user_key_object->user_key;
    }
    // Generate the URL which we should use in the CURL explaination.
    $post_url = Url::fromRoute('api_keys.user_api_keys_controller_index', [
      'user' => $user->id(),
    ], [
      'absolute' => TRUE,
    ])->toString();
    return [
      '#theme' => 'api-keys-user-keys',
      '#api_key' => $user_key,
      // @todo. Fix when route for entity is ready.
      '#post_url' => 'example.com/entity/log',
      '#markup' => $this->t('URL : !url and key: !key', [
        '!url' => $post_url,
        '!key' => $user_key,
      ]),
    ];
  }

}
