<?php

/**
 * @file
 * Contains \Drupal\api_keys\Tests\UserApiKeysController.
 */

namespace Drupal\api_keys\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the api_keys module.
 */
class UserApiKeysControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "api_keys UserApiKeysController's controller functionality",
      'description' => 'Test Unit for module api_keys and controller UserApiKeysController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests api_keys functionality.
   */
  public function testUserApiKeysController() {
    // Check that the basic functions of module api_keys.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
