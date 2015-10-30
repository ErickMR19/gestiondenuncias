<?php
/**
 * @file
 * Contains \Drupal\gestiondenuncias\Form\EnviarDenunciaForm.
 */

namespace Drupal\gestiondenuncias\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;

/**
 * EnviarDenunciasForm form.
 */
class EnviarDenunciaForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
      return 'gestiondenuncias_enviardenuncia_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

      /**
        * TODO: agregar funcionalidad del captcha
        **/
    //$form['#attached']['library'][] = 'gestiondenuncias/gestiondenuncias.recaptcha';

    //optiene el usuario actual
    $user = \Drupal::currentUser();
    if($user->isAnonymous()){
        $form['tipo_denuncia_bool'] = array(
          '#type' => 'checkbox',
          '#title' => 'Denuncia Anónima',
        );
        $ocultarDenunciante = array(
            ':input[name="tipo_denuncia_bool"]' => array('checked' => TRUE),
        );
    }
    else{
        /**
          * TODO: agregar elementos al array dependiendo del permiso
          **/
        $opciones = array();
        $opciones["Anónima"] = "Anónima";
        $opciones["Personal"] = "No Anónima";
        $opciones["Física"] = "Física";
        $opciones['Comunal'] = "Comunal";

        $form['tipo_denuncia_m'] = array(
           '#type' => 'select',
           '#title' => t(''),
           '#options' => $opciones,
           '#default_value' => $category['selected'],
           '#description' => t('Seleccionar el tipo de denuncia'),
         );
         $ocultarDenunciante = array(
             ':input[name="tipo_denuncia_m"]' => array('value' => 'Anónima'),
         );
    }

    $form['descripcion'] = array(
      '#type' => 'textarea',
      '#title' => t('Descripcion'),
      '#required' => TRUE,
    );

    $form['denunciante'] = array(
      '#type' => 'fieldset',
      '#title' => t('Datos del Denunciante'),
      '#states' => array(

          // Hide the settings when the cancel notify checkbox is disabled.

          'invisible' => $ocultarDenunciante,
      )
    );
    $form['denunciante']['nombre'] = array(
      '#type' => 'textfield',
      '#attributes' => array('placeholder' => 'Nombre'),
    );
    $form['denunciante']['apellidos'] = array(
      '#type' => 'textfield',
      '#attributes' => array('placeholder' => 'Apellidos'),
    );
    $form['denunciante']['email'] = array(
      '#type' => 'email',
      '#attributes' => array('placeholder' => 'Dirección de Correo Electrónico'),
    );

    $form['imagenes'] = array(
      '#type' => 'managed_file',
      '#title' => 'Imágenes',
      '#multiple' => TRUE,
      '#theme' => 'file_widget_multiple',
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        'file_validate_size' => array(2*1024*1024),
      )
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
      if (count($form_state->getValues()['imagenes']) > 2) {
            $form_state->setErrorByName('imagenes', 'Solo se permiten dos imagenes');
      }
      //unset($form_state->getValues()['imagenes']);

          /**
            * TODO: verficiar permisos y el tipo de denuncia
            **/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $valores = $form_state->getValues();
      $denunciante = $form_state->getValues('denunciante');
      $imagenes = array_map(function ($n){ return array('target_id'=>$n); }, $valores['imagenes']);

      $terminos = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('tipos_denuncia',0,NULL,true);
      $terminosAsociados = array();
      foreach ($terminos as $termino)
      {
          $terminosAsociados[$termino->label()] = $termino->id();
      }
      // si no es anonimo
      $tipoDeDenuncia = $terminosAsociados[$valores['tipo_denuncia_m']];
      $titulo = 'Denuncia' . $valores['tipo_denuncia_m'] . '19';
    $node = Node::create([
      'type'  => 'denuncia',
      'title' => $titulo,
      'field_image' => $imagenes,
      'field_tipo_denuncia' => $tipoDeDenuncia,
      'field_descripcion' => $valores['descripcion'],
      'field_denunciante_nombre' => $denunciante['nombre'],
      'field_denunciante_apellidos' => $denunciante['apellidos'],
      'field_denunciante_email' => $denunciante['email'],
    ]);
    $node->save();
    drupal_set_message("Denuncia recibida. Muchas gracias!");

  }
}
?>
