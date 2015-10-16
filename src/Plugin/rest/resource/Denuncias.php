<?php

/**
 * @file
 * Contains Drupal\serviciosweb\Plugin\rest\resource\serviciosweb.
 */

namespace Drupal\gestiondenuncias\Plugin\rest\resource;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "denuncias",
 *   label = @Translation("Denuncias"),
 *   uri_paths = {
 *     "canonical" = "/denuncias"
 *   }
 * )
 */
class Denuncias extends ResourceBase {
  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityManagerInterface $entity_manager,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityManager = $entity_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity.manager'),
      $container->get('current_user')
    );
  }
  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
   public function get() {
    return new ResourceResponse(array('id'=>'1','numero'=>'2'));
    /*  if ($entity) {
        $permission = 'Administer content types';
        /*if(!$this->currentUser->hasPermission($permission)) {
          throw new AccessDeniedHttpException();
        }*/
        //$bundles_entities = \Drupal::entityManager()->getStorage($entity .'_type')->loadMultiple();
    //    $bundles = array('id'=>'1','id'=>'2');
        /*foreach ($bundles_entities as $entity) {
          $bundles[$entity->id()] = $entity->label();
        }*/
      /*  if (!empty($bundles)) {
          return new ResourceResponse($bundles);
        }
        throw new NotFoundHttpException(t('Bundles for entity @entity were not found', array('@entity' => $entity)));
      }

      throw new HttpException(t('Entity wasn\'t provided'));
    }*/



}
}
