<?php

/**
 *  Submit function for intitial user registration.
 */
function user_profile_user_register_submit(&$form, &$form_state) {
  global $fragrance_types_vid;
  global $fragrance_families_vid;
  global $fragrance_personas_vid;
  global $email_preferences_vid;

  $account = $form_state['user'];
  $gender = ($account->gender == 0 ) ? 'female' : 'male';
  $profileType = $form['profile_type']['#value'];

  $results = db_query("INSERT INTO {user_profile} (uid, first_name, last_name, email, birth_date, gender, address, city,
      state, country, zip, cotyoptin, sallyoptin, privacy
    )
    VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
      $account->uid,
      $form_state['values']['first_name'],
      $form_state['values']['last_name'],
      $form_state['values']['mail'],
      $form_state['values']['birth_date']['year']. '-' .
      $form_state['values']['birth_date']['month']. '-' .
      $form_state['values']['birth_date']['day']. ' 00:00:00',
      $gender,
      $form_state['values']['address'],
      $form_state['values']['city'],
      $form_state['values']['state'],
      $form_state['values']['country'],
      $form_state['values']['zip'],
      $form_state['values']['cotyoptin'],
      $form_state['values']['sallyoptin'],
      $form_state['values']['privacy']
  );

  if ($results  && ($profileType == 'full')) {
    _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_types'],
       $account->uid, $fragrance_types_vid);
    _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_families'],
       $account->uid, $fragrance_families_vid);
    _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_personas'],
       $account->uid, $fragrance_personas_vid);
    _user_profile_preferences_insert($form_state['values']['user_preference_email_preferences'],
       $account->uid, $email_preferences_vid);

  } else {
    drupal_set_message(t('We had a problem creating your user profile. The problem has been reported. Please try again later.'), 'error', FALSE);
    watchdog('user_profile', 'Error inserting user profile.', null, WATCHDOG_ERROR);
  }

  // submit user's subscriptions and preferences (if any) to Cheetah Mail
  $list_action[SALLY_HANSEN_LISTID] = ($form_state['values']['sallyoptin'] == 1) ? 'opt_in' : 'opt_out';
  $list_action[COTY_LISTID] = ($form_state['values']['cotyoptin'] == 1) ? 'opt_in' : 'opt_out';
  user_profile_cheetah_mail_subscribe($form_state, $form, $list_action, CHEETAH_MAIL_EVENT_ID_PROFILE_WELCOME);

  //Set cookie with date of last profile update
  setcookie('user_profile_last_user_profile_update', time(),  time() + (86400 * 3650), '/');
}

/**
 *  Submit function for user edit.
 */
function user_profile_user_edit_submit(&$form, &$form_state) {
  global $fragrance_types_vid;
  global $fragrance_families_vid;
  global $fragrance_personas_vid;
  global $email_preferences_vid;

  $uid = $form['#uid'];
  $profileType = $form['profile_type']['#value'];

  $gender = ($form_state['values']['gender'] == 0 ) ? 'female' : 'male';

  // On User Profile Edit, check to see if User record exists in User Profile table.
  // If it does not exist, create it.
  // kpr($form_state);

  $result = db_result(db_query('SELECT uid FROM {user_profile} WHERE uid = %d',$uid));
  if (!$result) {
    $results = db_query("INSERT INTO {user_profile} (uid, first_name, last_name, email, birth_date, gender, address, city,
      state, country, zip, cotyoptin, sallyoptin, privacy
    )
      VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
        $uid,
        $form_state['values']['first_name'],
        $form_state['values']['last_name'],
        $form_state['values']['mail'],
        $form_state['values']['birth_date']['year'].'-'.
        $form_state['values']['birth_date']['month'].'-'.
        $form_state['values']['birth_date']['day'].' 00:00:00',
        $gender,
        $form_state['values']['address'],
        $form_state['values']['city'],
        $form_state['values']['state'],
        $form_state['values']['country'],
        $form_state['values']['zip'],
        $form_state['values']['cotyoptin'],
        $form_state['values']['sallyoptin'],
        '1' // privacy always 1 if existing user
    );
  } else {
    $result = db_query("UPDATE {user_profile} "
      . "set first_name = '%s', "
      . "last_name = '%s', "
      . "email = '%s', "
      . "birth_date = '%s', "
      . "gender = '%s', "
      . "address = '%s', "
      . "city = '%s', "
      . "state = '%s', "
      . "country = '%s', "
      . "zip = '%s' ,"
      . "cotyoptin = '%s' ,"
      . "sallyoptin = '%s' "
      . "where uid = %d"
       ,
      $form_state['values']['first_name'],
      $form_state['values']['last_name'],
      $form_state['values']['mail'],
      $form_state['values']['birth_date']['year'].'-'.
      $form_state['values']['birth_date']['month'].'-'.
      $form_state['values']['birth_date']['day'].' 00:00:00',
      $gender,
      $form_state['values']['address'],
      $form_state['values']['city'],
      $form_state['values']['state'],
      $form_state['values']['country'],
      $form_state['values']['zip'],
      $form_state['values']['cotyoptin'],
      $form_state['values']['sallyoptin'],
      $uid
    );

    if ($result && ($profileType == 'full')) {
      // delete all existing preferences for given user...
      _user_profile_preferences_delete($uid, $fragrance_types_vid);
      _user_profile_preferences_delete($uid, $fragrance_families_vid);
      _user_profile_preferences_delete($uid, $fragrance_personas_vid);
      _user_profile_preferences_delete($uid, $email_preferences_vid);

      // ...and insert new ones from form
      _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_types'], $uid, $fragrance_types_vid);
      _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_families'], $uid, $fragrance_families_vid);
      _user_profile_preferences_insert($form_state['values']['user_preference_fragrance_personas'], $uid, $fragrance_personas_vid);
      _user_profile_preferences_insert($form_state['values']['user_preference_email_preferences'], $uid, $email_preferences_vid);
    }
  }

  // Check for change in subscriber status for Sally Hansen or Coty lists in Cheetah Mail
  // if initial form value is not equal to submitted form value
  $list_action = array();
  if ($form['_account']['#value']->sallyoptin != $form_state['values']['sallyoptin']) {
    $list_action[SALLY_HANSEN_LISTID] = ($form_state['values']['sallyoptin'] == 1) ? 'opt_in' : 'opt_out';
  }

  if ($form['_account']['#value']->cotyoptin != $form_state['values']['cotyoptin']) {
    $list_action[COTY_LISTID] = ($form_state['values']['cotyoptin'] == 1) ? 'opt_in' : 'opt_out';
  }

  // submit user's updated preferences and subscriptions (if any) to Cheetah Mail
  user_profile_cheetah_mail_subscribe($form_state, $form, $list_action, CHEETAH_MAIL_EVENT_ID_PROFILE_UPDATE);

  //Set cookie with date of last profile update
  setcookie('user_profile_last_user_profile_update', time(), time() + (86400 * 3650), '/');
}
