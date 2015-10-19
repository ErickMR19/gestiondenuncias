<?php

/**
 * @file
 * Contains Drupal\gestiondenuncias\Plugin\rest\resource\gestiondenuncias.
 */

namespace Drupal\gestiondenuncias\Plugin\rest\resource;

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
 *   id = "denuncias_resource",
 *   label = @Translation("Recurso REST para las denuncias"),
 *   serialization_class = "Drupal\gestiondenuncias\Entity\DefaultEntity",
 *   uri_paths = {
 *     "canonical" = "/denuncias_resource/{denuncia}",
 *     "https://www.drupal.org/link-relations/create" = "/crear/denuncias_resource"
 *   }
 * )
 */
class DenunciasResource extends ResourceBase {
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
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

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
      $container->get('current_user')
    );
  }
  public function get($denuncia) {
    $node_storage = \Drupal::entityManager()->getStorage('node');

    if($denuncia == "todas")
    {

        $query = \Drupal::entityQuery('node')
            ->condition('type','denuncia');

            $nids = $query->execute();
            $resultado = array();
            foreach ($node_storage->loadMultiple($nids) as $key => $content)
            {
                $resultado[$key] = array( "titulo" =>  $content->get("title")->value,"cuerpo" => $content->get('body')->value,);
            }
            return new ResourceResponse( $resultado );
    }
    return new ResourceResponse( $node_storage->load($denuncia) );



    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.

    /*
    if(!$this->currentUser->hasPermission($permission)) {
        throw new AccessDeniedHttpException();
    }
    */

    // Throw an exception if it is required.
    // throw new HttpException(t('Throw an exception if it is required.'));
    //return new ResourceResponse("Implement REST State GET!");
  }

  public function delete($denuncia) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.

    /*
    if(!$this->currentUser->hasPermission($permission)) {
        throw new AccessDeniedHttpException();
    }
    */

    // Throw an exception if it is required.
    // throw new HttpException(t('Throw an exception if it is required.'));
    return new ResourceResponse("Implement REST State DELETE!");
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($ejemplo) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    $salida = $ejemplo->get('title')->value;
    $salida = $ejemplo->set('title','titulo');
    $salida = $ejemplo->set('body','cuerpo denuncia');
    $ejemplo->save();

    /*
    if(!$this->currentUser->hasPermission($permission)) {
        throw new AccessDeniedHttpException();
    }
    */

    // Throw an exception if it is required.
    // throw new HttpException(t('Throw an exception if it is required.'));
    return new ResourceResponse( "Implement REST State POST! ", 201 );
  }
  /**
   * Responds to PATCH requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function patch() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.

    /*
    if(!$this->currentUser->hasPermission($permission)) {
        throw new AccessDeniedHttpException();
    }
    */

    // Throw an exception if it is required.
    // throw new HttpException(t('Throw an exception if it is required.'));
    return new ResourceResponse("Implement REST State PATCH!");
  }

}
