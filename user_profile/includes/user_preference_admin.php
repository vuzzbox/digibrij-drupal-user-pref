<?php
/**
 * User Preference Center Admin form
 *
 * For managing Profile Preference Vocabulary IDs and other module settings.
 */
function user_profile_form_admin() {
    $form = array();

    $form['preference_center_country_access'] = array(
      '#title' => 'Full Preference Center Access - Country Codes',
      '#description' => 'A comma seperated list of ISO-3166 two character country codes (no spaces, lower case). Visitors from these countries will be given access to the Full Preference Center',
      '#type' => 'textfield',
      '#default_value' => variable_get('preference_center_country_access',0),
    );

    $form['user_profile_vocab1'] = array(
      '#title' => 'Vocabulary ID 1 - Fragrance Types',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab1',0),
    );

    $form['user_profile_vocab2'] = array(
      '#title' => 'Vocabulary ID 2 - Fragrance Families',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab2',0),
    );

    $form['user_profile_vocab3'] = array(
      '#title' => 'Vocabulary ID 3 - Fragrance Personas',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab3',0),
    );

    $form['user_profile_vocab4'] = array(
      '#title' => 'Vocabulary ID 4 - Beauty Preferences',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab4',0),
    );

    $form['user_profile_vocab5'] = array(
      '#title' => 'Vocabulary ID 5 - Undefined',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab5',0),
    );

    $form['user_profile_vocab6'] = array(
      '#title' => 'Vocabulary ID 6 - Undefined',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab6',0),
    );

    $form['user_profile_vocab7'] = array(
      '#title' => 'Vocabulary ID 7 - Undefined',
      '#description' => 'ID of a taxonomy vocabulary used for user preferences',
      '#type' => 'textfield',
      '#default_value' => variable_get('user_profile_vocab7',0),
    );

    return system_settings_form($form);
}

/**
 * User Profile Admin form submission handling
 */
function user_profile_form_admin_submit(&$form, &$form_state) {
  foreach ($form['profile_categories'] as $vocab) {
    set_variable($vocab->key, $vocab->value);
  }

  drupal_set_message( 'User Profile values saved.');
}

/**
 * TODO: User Profile Admin form validation
 */
function user_profile_form_admin_validation(&$form, &$form_state) {
  // Form validation here
}

function user_profile_form_user_admin_settings_alter(&$form, &$form_state) {
  // allow admin to choose if a notification mail is sent to user (who register without approval)
  $form['email']['no_approval_required']['user_mail_register_no_approval_required_notify'] = array(
    '#type' => 'checkbox',
    '#title' => t('Send'),
    '#default_value' => variable_get('user_mail_register_no_approval_required_notify', true),
  );
}