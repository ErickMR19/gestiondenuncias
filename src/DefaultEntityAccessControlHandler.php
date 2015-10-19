<?php

/**
 * @file
 * Contains Drupal\gestiondenuncias\DefaultEntityAccessControlHandler.
 */

namespace Drupal\gestiondenuncias;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Default entity entity.
 *
 * @see \Drupal\gestiondenuncias\Entity\DefaultEntity.
 */
class DefaultEntityAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view default entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit default entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete default entity entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add default entity entities');
  }

}
