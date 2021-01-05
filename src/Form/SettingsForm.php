<?php

namespace Drupal\persistent_visitor_parameters\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return [
      'persistent_visitor_parameters.settings',
    ];
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'persistent_visitor_parameters_settings_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('persistent_visitor_parameters.settings');

    $form['get_parameters'] = [
      '#type' => 'textfield',
      '#title' => t('List of GET parameters'),
      '#description' => t('List GET parameters for tracking, separated by "|"'),
      '#default_value' => $config->get('get_parameters'),
    ];

    $form['server_parameters'] = [
      '#type' => 'textfield',
      '#title' => t('List of SERVER parameters'),
      '#description' => t('List of SERVER parameters for tracking, separated by "|"'),
      '#default_value' => $config->get('server_parameters'),
    ];

    $form['cookie_expire'] = [
      '#type' => 'radios',
      '#title' => t('Cookie expiration'),
      '#options' => [
        0 => t('Session'),
        1 => t('Forever'),
        2 => t('Custom'),
      ],
      '#default_value' => $config->get('cookie_expire') ? $config->get('cookie_expire') : 0,
    ];

    $form['custom_expire'] = [
      '#type' => 'number',
      '#title' => t('Custom duration'),
      '#description' => t('The time the cookie expires. This is the number of seconds from the current time.'),
      '#default_value' => $config->get('custom_expire') ? $config->get('custom_expire') : 2592000,
      '#states' => [
        'visible' => [
          ':input[name="cookie_expire"]' => ['value' => 2],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('persistent_visitor_parameters.settings')
      ->set('get_parameters', $form_state->getValue('get_parameters'))
      ->set('server_parameters', $form_state->getValue('server_parameters'))
      ->set('cookie_expire', $form_state->getValue('cookie_expire'))
      ->set('custom_expire', $form_state->getValue('custom_expire'))
      ->save();
  }

}