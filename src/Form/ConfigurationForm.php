<?php

/**
 * @file
 * Contains \Drupal\gestiondenuncias\Form\ConfigurationForm.
 */

namespace Drupal\gestiondenuncias\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigurationForm.
 *
 * @package Drupal\gestiondenuncias\Form
 */
class ConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gestiondenuncias.configuration'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('gestiondenuncias.configuration');

    $form['activar_validar_email'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Validar correos electronicos'),
      '#description' => $this->t('Selecciona si desea utilizar el servicio Verify-Email.org'),
      '#default_value' => $config->get('validar_email'),
    );
    $form['verify-email'] = array(
      '#type' => 'fieldset',
      '#title' => t('Datos de Verify Email'),
      '#states' => array(
          // Hide the settings when the cancel notify checkbox is disabled.
          'invisible' => array(
              ':input[name="activar_validar_email"]' => array('checked' => FALSE),
          )
      )
    );
    $form['verify-email']['nombre_ususario_verifyemail'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de usuario'),
      '#description' => $this->t('Nombre de usuario del servicio Verify-Email.org'),
      '#maxlength' => 50,
      '#size' => 50,
      '#default_value' => $config->get('nombre_ususario_verifyemail'),
    );
    $form['verify-email']['contrasena_verifyemail'] = array(
      '#type' => 'password',
      '#title' => $this->t('Contraseña'),
      '#description' => $this->t('Contraseña del servicio Verfiy-Email.org (no se guarda encriptada)'),
      '#maxlength' => 50,
      '#size' => 50,
      '#default_value' => $config->get('contrasena_verifyemail'),
      '#attributes' => array('placeholder' => 'No cambiar el valor guardado'),
    );
    $form['verificar_via_recaptcha'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Verificacion de recaptcha'),
      '#description' => $this->t('Activar verificacion utilizando el servici recaptcha para usuarios no autenticados'),
      '#default_value' => $config->get('verificar_via_recaptcha'),
    );
    $form['datos-recaptcha'] = array(
      '#type' => 'fieldset',
      '#title' => t('Datos de ReCaptcha'),
      '#states' => array(
          // Hide the settings when the cancel notify checkbox is disabled.
          'invisible' => array(
              ':input[name="verificar_via_recaptcha"]' => array('checked' => FALSE),
          ),
      )
    );
    $form['datos-recaptcha']['private_key_recaptcha'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Llave privada'),
      '#description' => $this->t('Llave privada del servicio recaptcha'),
      '#maxlength' => 250,
      '#size' => 250,
      '#default_value' => $config->get('private_key_recaptcha'),
      '#states' => array(
          // Hide the settings when the cancel notify checkbox is disabled.
          'disabled' => array(
              ':input[name="entorno_prueba_recaptcha"]' => array('checked' => TRUE),
          ),
      )
    );
    $form['datos-recaptcha']['public_key_recaptcha'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Llave publica'),
      '#description' => $this->t('Llave publica del servicio recaptcha'),
      '#maxlength' => 250,
      '#size' => 250,
      '#default_value' => $config->get('public_key_recaptcha'),
      '#states' => array(
          // Hide the settings when the cancel notify checkbox is disabled.
          'disabled' => array(
              ':input[name="entorno_prueba_recaptcha"]' => array('checked' => TRUE),
          ),
      )
    );
    $form['datos-recaptcha']['entorno_prueba_recaptcha'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Entorno de pruebas'),
      '#description' => $this->t('Utiliza las llaves utilizadas para pruebas, por ello no se realiza una comproabcion del captcha, pero si se muestraa'),
      '#default_value' => $config->get('entorno_prueba_recaptcha'),
    );

    $form['enviar-email-supervisores'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enviar correos al recibir denunicas'),
      '#description' => $this->t('Activar si desea notificar a los supervisores'),
      '#default_value' => $config->get('enviar_email_supervisores'),
    );

    $form['usar-fecha-prueba-cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Usar fecha de pruebas para CRON'),
      '#default_value' => $config->get('usar_fecha_prueba_cron'),
    );

    $form['fecha-prueba-cron'] = array(
      '#type' => 'date',
      '#title' => $this->t('Fecha de pruebas'),
      '#description' => $this->t('Se tomara esta fecha como actual para compararla con la fecha esperada'),
      '#default_value' => $config->get('fecha_prueba_cron'),
      '#states' => array(
          // Hide the settings when the cancel notify checkbox is disabled.
          'disabled' => array(
              ':input[name="usar-fecha-prueba-cron"]' => array('checked' => FALSE),
          ),
      )
    );


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if( $form_state->getValue('usar-fecha-prueba-cron') && ! $form_state->getValue('fecha-prueba-cron') )
        $form_state->setErrorByName('fecha-prueba-cron', "Se requiere una fecha si se activa la opcion de prueba para CRON");
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    if( $form_state->getValues('verify-email')['contrasena_verifyemail'] != '' ){
        $this->config('gestiondenuncias.configuration')->set('contrasena_verifyemail', $form_state->getValues('verify-email')['contrasena_verifyemail']);
    }
    $this->config('gestiondenuncias.configuration')
      ->set('validar_email', $form_state->getValue('activar_validar_email'))
      ->set('nombre_ususario_verifyemail', $form_state->getValues('verify-email')['nombre_ususario_verifyemail'])
      ->set('verificar_via_recaptcha', $form_state->getValue('verificar_via_recaptcha'))
      ->set('private_key_recaptcha', $form_state->getValues('datos-recaptcha')['private_key_recaptcha'])
      ->set('public_key_recaptcha', $form_state->getValues('datos-recaptcha')['public_key_recaptcha'])
      ->set('entorno_prueba_recaptcha', $form_state->getValues('datos-recaptcha')['entorno_prueba_recaptcha'])
      ->set('enviar_email_supervisores', $form_state->getValue('enviar-email-supervisores'))
      ->set('fecha_prueba_cron', $form_state->getValue('fecha-prueba-cron'))
      ->set('usar_fecha_prueba_cron', $form_state->getValue('usar-fecha-prueba-cron'))
      ->save();

      drupal_set_message("Es recomendable Vaciar todas las cachés");
  }

}
