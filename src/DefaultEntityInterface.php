<?php

/**
 * @file
 * Contains Drupal\gestiondenuncias\DefaultEntityInterface.
 */

namespace Drupal\gestiondenuncias;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Default entity entities.
 *
 * @ingroup gestiondenuncias
 */
interface DefaultEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

}
