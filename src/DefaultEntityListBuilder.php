<?php

/**
 * @file
 * Contains Drupal\gestiondenuncias\DefaultEntityListBuilder.
 */

namespace Drupal\gestiondenuncias;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Default entity entities.
 *
 * @ingroup gestiondenuncias
 */
class DefaultEntityListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Default entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\gestiondenuncias\Entity\DefaultEntity */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $this->getLabel($entity),
      new Url(
        'entity.default_entity.edit_form', array(
          'default_entity' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
