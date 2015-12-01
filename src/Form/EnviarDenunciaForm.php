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
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;


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

public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
  $httpClient = \Drupal::httpClient();
  $configuration = $this->config('gestiondenuncias.configuration');
  $pwd = $configuration->get('contrasena_verifyemail');
  $usr = $configuration->get('nombre_ususario_verifyemail');
  $email = $form_state->getValues('denunciante')['email'];
  $serverResponse = json_decode($httpClient->request('POST',"http://api.verify-email.org/api.php?usr=$usr&pwd=$pwd&check=$email")->getBody()->getContents());
  $response = new AjaxResponse();
  if($serverResponse->authentication_status != 1){
    \Drupal::logger('gestiondenuncias.verify-email')->error('Los parametros de conexion a verify-email son incorrectos');
  }
  else if($serverResponse->limit_status){
    \Drupal::logger('gestiondenuncias.verify-email')->error('Se llego al limite de consultas de verify-email');
  }
  else{
      if ($serverResponse->verify_status) {
        $css = ['border' => '1px solid green'];
        $message = $this->t('Email ok.');
      }
      else {
        $css = ['border' => '1px solid red'];
        $message = $this->t('Email not valid.');
      }
      $message = $message . $form_state->getValues()['denunciante']['email'];
      $response->addCommand(new CssCommand('#edit-email', $css));
      $response->addCommand(new HtmlCommand('.email-valid-message', $message));
  }
  return $response;
}

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

      /**
        * TODO: agregar funcionalidad del captcha
        **/
    $configuration = $this->config('gestiondenuncias.configuration');
    //kint( $configuration->getRawData() );
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
        $opciones = array();
        $opciones["Anónima"] = "Anónima";
        $opciones["Personal"] = "No Anónima";
        if( $user->hasPermission('enviar denuncias fisicas') )
            $opciones["Física"] = "Física";
        if( $user->hasPermission('enviar denuncias comunales') )
            $opciones['Comunal'] = "Comunal";

        $form['tipo_denuncia_m'] = array(
           '#type' => 'select',
           '#title' => t(''),
           '#options' => $opciones,
           '#default_value' => $category['selected'],
           '#description' => t('Seleccionar el tipo de denuncia'),
           '#required' => TRUE,
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
    $ajax = NULL;
    if ( $configuration->get('validar_email') ){
        $ajax = [
           'callback' => array($this, 'validateEmailAjax'),
           'event' => 'change',
           'progress' => array( 'type' => 'throbber', 'message' => t('Verificando email...'), ),
        ];
    }
    $form['denunciante']['email'] = array(
      '#type' => 'email',
      '#attributes' => array('placeholder' => 'Dirección de Correo Electrónico'),
      '#ajax' => $ajax,
      '#suffix' => '<span class="email-valid-message"></span>'
    );
    $form['imagenes'] = array(
      '#type' => 'managed_file',
      '#title' => 'Imágenes',
      '#multiple' => TRUE,
      //'#theme' => 'file_widget_multiple',
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        'file_validate_size' => array(2*1024*1024),
      )
    );
    $form['error-hidd'] = array(
     '#type' => 'hidden',
    );
    if( $user->isAnonymous() && $configuration->get('verificar_via_recaptcha') )
    {

        if( $configuration->get('entorno_prueba_recaptcha') ){
            $widgetRecaptcha = "<br /><div class='g-recaptcha' data-sitekey='6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI' data-callback='copiardatoscaptcha'></div>";
        }
        else{
            $llavePublica = $configuration->get('public_key_recaptcha');
            $widgetRecaptcha = "<br /><div class='g-recaptcha' data-sitekey='$llavePublica' data-callback='copiardatoscaptcha'></div>";
        }

        $form['#attached']['library'][] = 'gestiondenuncias/gestiondenuncias.recaptcha';
        $form['captcha-info'] = array(
         '#type' => 'hidden',
         '#attributes' => array(
            'id' => 'drupal-captcha-response',
         ),
         '#suffix' => $widgetRecaptcha
        );
    }



    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    //$httpClient = \Drupal::httpClient();
    //kint($httpClient);
    //kint());
    //kint($form['denunciante']['email']);
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $configuration = $this->config('gestiondenuncias.configuration');
    $httpClient = \Drupal::httpClient();
    if( $user->isAnonymous() && $configuration->get('verificar_via_recaptcha') )
    {
        $captchauser = $form_state->getValues()['captcha-info'];
        $key = ( $configuration->get('entorno_prueba_recaptcha') ) ? '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe' : $configuration->get('private_key_recaptcha');
        $captcharesponse = json_decode( $httpClient->request('POST',"https://www.google.com/recaptcha/api/siteverify?secret=$key&response=$captchauser")->getBody()->getContents());
        if(! $captcharesponse->success){
            $form_state->setErrorByName('captcha-info', 'Fallo en el captcha');
        }
    }
    if (count($form_state->getValues()['imagenes']) > 2) {
        $form_state->setErrorByName('error-hidd', 'Solo se permiten dos imagenes');
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
      $user = \Drupal::currentUser();
      $valores = $form_state->getValues();
      if( $user->isAnonymous() ){
          $tipoDenuncia = ( $valores['tipo_denuncia_bool'] ) ? 'Anónima' : 'Personal' ;
      }
      else {
         $tipoDenuncia = $valores['tipo_denuncia_m'];
      }
      $denunciante = $form_state->getValues('denunciante');
      $imagenes = array_map(function ($n){ return array('target_id'=>$n); }, $valores['imagenes']);

      $terminos = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('tipos_denuncia',0,NULL,true);
      $terminosAsociados = array();
      foreach ($terminos as $termino)
      {
          $terminosAsociados[$termino->label()] = $termino->id();
      }
      // si no es anonimo
      $CodigoTipoDeDenuncia = $terminosAsociados[$tipoDenuncia];
      $num = $this->obtenerSiguiente($tipoDenuncia);
      $titulo = 'Denuncia-' . $tipoDenuncia . '-'. $num;
    $node = Node::create([
      'type'  => 'denuncia',
      'title' => $titulo,
      'field_image' => $imagenes,
      'field_tipo_denuncia' => $CodigoTipoDeDenuncia,
      'field_descripcion' => $valores['descripcion'],
      'field_denunciante_nombre' => $denunciante['nombre'],
      'field_denunciante_apellidos' => $denunciante['apellidos'],
      'field_denunciante_email' => $denunciante['email'],
    ]);
    $node->save();
    drupal_set_message("Denuncia recibida. Muchas gracias!");

  }

  private function obtenerSiguiente($tipo){
      switch( $tipo ){
          case 'Anónima':
          case 'Personal':
          case 'Física':
          case 'Comunal':
                  try {
                    $result = db_query('SELECT * FROM {consecutivos_denuncias} WHERE tipoDenuncia= :tipo', array(':tipo' => $tipo))->fetchAllAssoc('tipoDenuncia');
                    db_query('UPDATE {consecutivos_denuncias} SET numeroActual = numeroActual + 1 WHERE tipoDenuncia= :tipo', array(':tipo' => $tipo));
                    return $result[$tipo]->numeroActual;
                  }
                  catch ( Exception $exception ) {
                    $t->rollBack();
                    throw $exception;
                  }
              break;
          default:
              \Drupal::logger('gestiondenuncias.obtenerConsecutivos')->error('Problemas al obtener el siguiente del tipo @tipo', array( '@tipo' => $tipo ));
              return FALSE;
      }
  }

}
?>
