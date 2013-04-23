<?php

/**
 * These variables store the Vocabulary IDs for terms used in the
 * User Preferences. Variables are set through an Admin interface at
 * /admin/user/user_profile_admin
 **/
global $fragrance_types_vid;
global $fragrance_families_vid;
global $fragrance_personas_vid;
global $email_preferences_vid;

$fragrance_types_vid = variable_get(user_profile_vocab1,0);
$fragrance_families_vid = variable_get(user_profile_vocab2,0);
$fragrance_personas_vid = variable_get(user_profile_vocab3,0);
$email_preferences_vid = variable_get(user_profile_vocab4,0);

define("SALLY_HANSEN_LISTID",     "2085991336");
define("COTY_LISTID",             "2085987683");

/**
 * Sets properties and attributes on the standard user account form to make it
 * compatible with the User Preference Center structure
 *
 * @param  array  $form               - Standard Drupal user account form
 * @param  array  $edit               - Standard Drupal User array/object
 * @param  string $preference_page    - Page/tab on which the form will be displayed
  */
function _user_preference_set_account_form(&$form, $edit, $profile_type, $preference_page) {
  // If the form is being built for registration (not editing), then the
  // Account group needs to be created. Create it and set attributes and props.
  if(!$edit) {
    // Create the new group (fieldset)
    $form['account'] = array(
      '#type' => 'fieldset',
      '#title' => 'My Username and Password',
      '#weight' => '-10',
      '#attributes' => array(
        'id' => 'user-profile-fieldset0',
        'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-on',
      ),
     );

    // Copy the basic user properties under this group
    $form['account']['name'] = $form['name'];
    $form['account']['mail'] = $form['mail'];
    $form['account']['pass'] = $form['pass'];
    $form['account']['status'] = $form['status'];
    $form['account']['roles'] = $form['roles'];
    $form['account']['picture'] = $form['picture'];

    // And remove them from the top level so they don't show up twice.
    unset($form['name']);
    unset($form['mail']);
    unset($form['pass']);
    unset($form['status']);
    unset($form['roles']);
    unset($form['picture']);

    // If displaying the form Preference Center for a new user
    // registration, the "Continue" buttons should be present
    if ($profile_type == 'full') {
      $form['account']['#collapsible'] = FALSE;
      $form['account']['next'] = array(
        '#type' => 'button',
        '#executes_submit_callback' => FALSE,
        '#value' => t('NEXT'),
        '#attributes' => array(
          "class" => "next-button",
          "onClick" => "Drupal.SallyHansen_User_Profile.next(0); return false;",
        )
      );

    // Random little style tweaks
    $form['account']['name']['#prefix'] = '<div class="container-inline">';
    $form['account']['name']['#suffix'] = '</div>';
    $form['account']['name']['#attributes'] = array('class' => 'right');
    }
  // Account group modifications for the edit form
  } else {
    $form['account']['#attributes'] = array(
      'id' => 'user-profile-fieldset0',
      'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-' . _get_display_state($profile_type, $edit, $preference_page, 'fieldset0'),
    );

    // When displaying form for edit, hide the username field (for design reasons),
    // change the title and tweak other minor stuff.
    $form['account']['#title'] = t('My Account');
    $form['account']['name']['#type'] = 'hidden';

    // Move picture into the Account group
    $form['account']['picture'] = $form['picture'];
    unset($form['picture']);
  }

  // And the changes from this point forward
  // apply to both registration and edit forms
  $form['account']['name']['#description'] = '';
  $form['account']['mail']['#description'] = '';
  $form['account']['mail']['#title'] = t('Email');
  $form['account']['mail']['#prefix'] = '<div class="container-inline">';
  $form['account']['mail']['#suffix'] = '</div>';
  $form['account']['mail']['#attributes'] = array('class' => 'right');
  $form['account']['pass']['password_confirm']['#prefix'] = '<div class="container-inline">';
  $form['account']['pass']['password_confirm']['#suffix'] = '</div>';
  $form['account']['pass']['password_confirm']['#attributes'] = array('class' => 'right');

  // Get current profile image path, if it exists
  $profile_picture = $form['_account']["#value"]->picture;

  // Show the default picture if user has not uploaded a picture.
  if (empty($profile_picture)) {
    $profile_picture_path = base_path() . path_to_theme() . "/images/default_user.png";
    $form['account']['picture']['picture_upload']['#prefix'] = '<div class="border"><img id="edit-pic-selected" src="' . $profile_picture_path . '" alt="user picture" /></div>';
  }

  // Set other image default attributes and properties
  $form['account']['picture']['#weight'] = -99;
  $form['account']['picture']['#title'] = '';
  $form['account']['picture']['#attributes']['id'] = 'user-profile-picture-fieldset';
  $form['account']['picture']['picture_upload']['#title'] = "Upload your picture";
  $form['account']['picture']['picture_upload']['#description'] = 'Minimum photo dimensions are 57x57. Larger images will be automatically resized to fit.';
  $form['account']['picture']['picture_upload']['#size'] = 60;
  $form['account']['picture']['picture_upload']['#attributes']['onchange'] = '$(\'#edit-pic-selected\').val(1);';
}


/**
 * Inserts user preference values into user_preference_terms table
 *
 * @param  array  $term_array an array key value pairs with TermID and 0 or 1
 * @param  int    $uid        UserID
 * @param  int    $vid        Vocabulary ID
 * @return boolean            Returns true on successful insert
 */
  function _user_profile_preferences_insert($term_array, $uid, $vid) {
    $at_least_one = FALSE;

    $sql = array();
    foreach($term_array as $tid ) {
      if($tid>0){
        $sql[] = '('.$uid.', '.$vid.', '.$tid.')';
        $at_least_one = TRUE;
      }
    }

    if($at_least_one) {
        $result = db_query('INSERT INTO {user_preference_terms} (uid, vid, tid) VALUES '.implode(',', $sql));
    }

    if ($result){
      return TRUE;
    } else {
      return FALSE;
    }
  }

/**
 * Deletes all user preference values from user_preference_terms table for
 * given User ID and Vocabulary ID
 *
 * @param  int    $uid        UserID
 * @param  int    $vid        Vocabulary ID
 * @return boolean            Returns true on successful delete
 */
  function _user_profile_preferences_delete($uid, $vid) {
    $result = db_query('DELETE from {user_preference_terms} WHERE uid = %d AND vid = %d',$uid, $vid);

    if ($result) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

/**
 * Return all selected user preference values as a comma seperated list for
 * given User ID and Vocabulary ID
 *
 * @param  int    $uid        UserID
 * @param  int    $vid        Vocabulary ID
 * @return boolean            Returns comma seperated list of tids or empty strng
 */
function _get_selected_preferences($uid, $vid){
  $result = db_query('SELECT tid from {user_preference_terms} WHERE uid = %d AND vid = %d',$uid, $vid);

  $items = array();
  while ($row = db_fetch_object($result)) {
    $items[] = $row->tid;
  }

  return $items;
}

/**
 * Returns (pulls) all possible user preference taxonomy terms for given
 * Vocabular(y)ies
 *
 * @param  array        $term_array  - contains Vocabulary IDs
 * @return boolean      Returns an array of arrays. Each array contains a set of
 *                      terms for a given Vocabulary.
 */
function _pull_terms($term_array){
  $new_term_array = array();

  for ($x=0; $x<count($term_array); $x++){
    $new_term_array[$term_array[$x]->tid] = t($term_array[$x]->name);
  }

  return $new_term_array;
}


/**
 * Determines whether user should see the Full preference center or
 * just the basic user profile form.
 *
 * The test is based on geographic location and relies on the
 * custom Coty Quova module
 *
 * Right now, only US visitors get the full monty.
 *
 * @return  string      containing: "full"  or "basic"
 */
function _get_profile_type() {
  // Default to basic
  $type = 'basic';

  $allowed_countries = explode(',', variable_get('preference_center_country_access', 'null'));

  if ($allowed_countries[0] != 'null') {
    $country = _get_country();
    foreach ($allowed_countries as $allowed){
      if ($country == $allowed) {
        $type = 'full';
      }
    }
  }

  return $type;
}

function _get_country() {
  $geo = coty_quova_ipinfo_request();
  return $geo->ipinfo->Location->CountryData->country_code;
}

/**
 * Determines whether a given fieldset in the User Preference form should be displayed.
 */
function _get_display_state($profile_type, $edit, $preference_page = 'null', $preference_field_set){
  // All fieldsets 'on' (visible) for Basic profile type
  if($profile_type == 'basic'){
    return 'on';
  }

  if($profile_type == 'full' && $edit) {
    switch ($preference_page) {
      case 'profile':
        switch ($preference_field_set) {
          case 'fieldset0':
            $display_state = 'on';
            break;
          case 'fieldset1':
            $display_state = 'on';
            break;
          case 'fieldset4':
            $display_state = 'on';
            break;
          default:
            $display_state = 'off';
            break;
        }
        break;
      case 'beauty_profile':
        switch ($preference_field_set) {
          case 'fieldset2':
            $display_state = 'on';
            break;
          case 'fieldset4':
            $display_state = 'on';
            break;
          default:
            $display_state = 'off';
            break;
        }
        break;
      case 'settings':
        switch ($preference_field_set) {
          case 'fieldset3':
            $display_state = 'on';
            break;
          case 'fieldset4':
            $display_state = 'on';
            break;
          default:
            $display_state = 'off';
            break;
        }
        break;
    }
    return $display_state;
  }

  if($profile_type == 'full' && !$edit) {
    switch ($preference_field_set) {
      case 'fieldset0':
        $display_state = 'on';
        break;
      default:
        $display_state = 'off-legend';
        break;
    }
    return $display_state;
  }
}

/**
 * Format titles for Preference Center menus selects
 */
function _preference_center_title_callback($preference_page) {
  switch ($preference_page) {
    case 'profile':
      $title = 'My Profile';
      break;
    case 'settings':
      $title = "My Settings";
      break;
    case 'beauty_profile':
      $title = 'My Beauty Profile';
      break;
    default:
      $title = '';
      break;
  }

  return " Preference Center | " . $title;
}

/**
 * Format date selects
 */
function _user_preference_birth_date_callback($form_element, $form_values) {
  $aTmpDay = array('0' => t('Day'));
  $aTmpMonth = array('0' => t('Month'));
  $aTmpYear = array('0' => t('Year'));

  $form_element['day']['#options'] = $aTmpDay + drupal_map_assoc(range(1, 31));
  $form_element['month']['#options'] = $aTmpMonth + drupal_map_assoc(range(1, 12), 'map_month');
  $form_element['year']['#options'] = $aTmpYear + drupal_map_assoc(range(2011, 1900));

  return $form_element;
}


/**
 * Output a list of options in the number of columns specified by the element's
 * #columns value.
 */
function theme_multicolumn_options($element) {
  // Initialize variables.
  $output = '';
  $total_columns = $element['#columns'];
  $total_options = count($element['#options']);
  $options_per_column = ceil($total_options / $total_columns);
  $keys = array_keys($element['#options']);
  $type = $element[$keys[0]]['#type'];

  // Start wrapper div.
  $output .= '<div class="multicolumn-options-wrapper">';
  $current_column = 1;
  $current_option = 0;

  while ($current_column <= $total_columns) {
    // Start column div.
    $output .=  '<div class="multicolumn-options-column" style="width: ' . 100 / $total_columns . '%; float: left">';

    // Keep looping through until the maximum options per column are reached,
    // or you run out of options.
    while ($current_option < $options_per_column * $current_column &&
           $current_option < $total_options) {

      // Output as either check or radio button depending on the element type.
      $output .= theme($type, $element[$keys[$current_option]]);
      $current_option++;
    }

    // End column div.
    $output .= '</div>';
    $current_column++;
  }

  // End wrapper div.
  $output .= '</div>';
  $output .= '<div class="clear-block"></div>';

  return $output;
}

function user_profile_cheetah_mail_subscribe($form_state, $form, $list_actions, $events) {
  module_load_include('php', 'emf_cheetah_mail', 'CheetahMailClass');

  $subs = '';
  $unsubs = '';


  if ($list_actions[SALLY_HANSEN_LISTID] == 'opt_in') {
    $subs = SALLY_HANSEN_LISTID;
  } elseif ($list_actions[SALLY_HANSEN_LISTID] == 'opt_out') {
    $unsubs = SALLY_HANSEN_LISTID;
  }

  if ($list_actions[COTY_LISTID] == 'opt_in') {
    $subs = ($subs == '') ? COTY_LISTID : $subs . '|' . COTY_LISTID;
  } elseif ($list_actions[COTY_LISTID] == 'opt_out') {
    $unsubs = ($unsubs == '') ? COTY_LISTID : $unsubs . '|' . COTY_LISTID;
  }

  $username = $form_state['values']['name'];
  $email = $form_state['values']['mail'];
  $first_name = $form_state['values']['first_name'];
  $last_name = $form_state['values']['last_name'];
  $gender = ($form_state['values']['gender'] == 0) ? 'F' : 'M';
  $address = (!empty($form_state['values']['address'])) ? $form_state['values']['address'] : '';
  $city = (!empty($form_state['values']['city'])) ? $form_state['values']['city'] : '';
  $state_prov = (empty($form_state['values']['state']) || $form_state['values']['state'] == 'null') ? '' : $form_state['values']['state'];
  $zip_postal = (!empty($form_state['values']['zip'])) ? $form_state['values']['zip'] : '';
  $country = (!empty($form_state['values']['country'])) ? $form_state['values']['country'] : '';
  $birth_date = $form_state['values']['birth_date']['day'] . '-'
    . date("M", mktime(0, 0, 0, $form_state['values']['birth_date']['month'], 10)) . '-'
    . $form_state['values']['birth_date']['year'];

  // Calculate bitfield values
  if (isset($form_state['values']['user_preference_email_preferences']))
    $beauty_interests_bitfield = calculate_bitfield_value($form_state['values']['user_preference_email_preferences']);
  if (isset($form_state['values']['user_preference_fragrance_types']))
    $fragrance_types_bitfield = calculate_bitfield_value($form_state['values']['user_preference_fragrance_types']);
  if (isset($form_state['values']['user_preference_fragrance_families']))
    $fragrance_notes_bitfield = calculate_bitfield_value($form_state['values']['user_preference_fragrance_families']);
  if (isset($form_state['values']['user_preference_fragrance_personas']))
    $fragrance_personas_bitfield = calculate_bitfield_value($form_state['values']['user_preference_fragrance_personas']);

  // Create options array to submit to Cheetah Mail API
  $options  = array(
    'email' => $email,
    'FNAME' => $first_name,
    'LNAME' => $last_name,
    'GENDER' => $gender,
    'BIRTH_DATE' => $birth_date,
    'ADDRESS' => $address,
    'CITY' => $city,
    'STATE' => $state_prov,
    'ZIP' => $zip_postal,
    'COUNTRY' => $country,
    'BEAUTY_INTERESTS' => $beauty_interests_bitfield,
    'FRAGRANCE_TYPES' =>  $fragrance_types_bitfield,
    'FRAGRANCE_NOTES' => $fragrance_notes_bitfield,
    'FRAGRANCE_PERSONA' => $fragrance_personas_bitfield,
    'CUSTOM_1' => $username,
    'resub' => 3,
    'unsub' => $unsubs,   // put unsubscribes in here as an action so we can handle all requests, including unsubscribes, in the subscriberAdd method
  );

  // Add event(s) to the options array if they are passed
  if (!empty($events)) {
    $options['e'] = $events;
  }

  $resubscribe = false;  // this value is being set to false so the resub value in options above is not overwritten by the subscriberAdd method below
  $user = variable_get('emf_cheetah_mail_username', '');
  $pwd = variable_get('emf_cheetah_mail_password', '');

  $cm = new CheetahMail($user, $pwd, file_directory_temp() . '/user_profile_cheetah_mail_cookie.txt');
  $results = $cm->subscriberAdd($email, $subs, $options, $resubscribe);

  if (!$results || isset($results['errorCode'])) {
      if(isset($results['errorCode'])) {
        $error = $results['errorCode'];
      } else {
        $error = 'no error message';
      }
      drupal_set_message(t('We experienced a problem subscribing you to Sally Hansen communications. The problem has been reported.  Please try again later.'));
      watchdog('user_profile', 'Error in call to Cheetahmail->subscribeAdd: ' . $error, array('email' => $email, 'first name' =>  $first_name, 'error message' => $error), WATCHDOG_ERROR);
  }

}

function user_profile_cheetah_mail_unsubscribe($form_state, $list_id) {
  module_load_include('php', 'emf_cheetah_mail', 'CheetahMailClass');

  $user = variable_get('emf_cheetah_mail_username', '');
  $pwd = variable_get('emf_cheetah_mail_password', '');

  $email = $form_state['values']['mail'];

  $cm = new CheetahMail($user, $pwd, file_directory_temp() . '/user_profile_cheetah_mail_cookie.txt');
  $results = $cm->unSubscribe($email, $list_id);

  if (!$results || isset($results['errorCode'])) {
      if(isset($results['errorCode'])) {
        $error = $results['errorCode'];
      } else {
        $error = 'no error message';
      }
      drupal_set_message(t('We experienced a problem unsubscribing you from Sally Hansen communications. The problem has been reported.  Please try again later.'));
      watchdog('user_profile', 'Error in call to Cheetahmail->unSubscribe', array('email' => $email, 'error message' => $error), WATCHDOG_ERROR);
  }
}

function calculate_bitfield_value($terms) {
  // module_load_include('php', 'includes/user_preference_term_bitfield_map', 'user_profile');
  global $term_bitfield_map;

  $bitfield_value = 0;

  foreach ($terms as $termid) {
    if ($termid != 0) {
      $bitfield_value += $term_bitfield_map[$termid];
    }
  }

  return ($bitfield_value == 0) ? null : $bitfield_value;
}
