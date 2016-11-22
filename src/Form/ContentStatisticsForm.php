<?php

/**
 * @file
 * Contains \Drupal\content_statistics\ContentStatisticsForm.
 */ 

namespace Drupal\content_statistics\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

class ContentStatisticsForm extends ConfigFormBase {

  public function getFormId() {
    return 'content_statistics_get_content_types';
  }
  
  public function getEditableConfigNames() {
    return ['content_statistics.settings'];
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('content_statistics.settings');
    $case = $config->get('content_statistics.content_types');
    $form['content_types'] = array(
      '#type' => 'checkboxes',
      '#options' => node_type_get_names(),
      '#title' => $this->t('Select content types'),
	  '#default_value' => !empty($case) ? $case : array(),
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    );
    return $form;
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('content_statistics.settings')
      ->set('content_statistics.content_types', $form_state->getValue('content_types'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
