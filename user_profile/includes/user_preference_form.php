<?php

module_load_include('php', 'user_profile', 'user_preference_functions');

/**
 * User Preference form  for both registration and profile edit forms.
 *
 * @param  array    $edit             - $user object
 * @param  string   $profile_type     - full or basic. determines which fields to display
 * @param  string   $preference_page  - Page/tab on which the form will be displayed
 * @return array    $form             - Returns a structured Drupal form
 *
 */
function _user_profile_form_fields($edit, $profile_type = 'basic', $preference_page = 'profile') {
  global $fragrance_types_vid;
  global $fragrance_families_vid;
  global $fragrance_personas_vid;
  global $email_preferences_vid;

  $show_full_preference_center = ($profile_type == 'full') ? TRUE : FALSE;
  $country = _get_country();
  $us_or_canada = ($country == 'us' || $country == 'ca') ? TRUE : FALSE;

  if ($edit) {
    $uid = $edit->uid;
    $submitButtonText = "SAVE MY PROFILE";
  } else {
    $submitButtonText = "CREATE ACCOUNT";
  }

  $form = array();

  // Store Profile type in form for use in validation, edits and updates.
  $form['profile_type'] = array('#value' => $profile_type);

  $form['my_profile'] = array(
    '#type' => 'fieldset',
    '#title' => t('My Profile'),
    '#weight' => 2,
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#attributes' => array(
      'id'=>'user-profile-fieldset1',
      'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-' . _get_display_state($profile_type, $edit, $preference_page, 'fieldset1'),
    ),
  );

  // Preferences Field Set definition - Full Preferrence display only
  if ($show_full_preference_center) {
    $form['my_beauty_profile'] = array(
      '#type' => 'fieldset',
      '#title' => t('My Beauty Profile'),
      '#description' => 'Help us get to know you better by sharing your interests and preferences.',
      '#weight' => 4,
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#attributes' => array(
        'id'=>'user-profile-fieldset2',
        'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-' . _get_display_state($profile_type, $edit, $preference_page, 'fieldset2'),
      ),
    );
  }

  // Preferences Field Set definition  - Full and basic display
  $form['my_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Privacy and Communications'),
    '#weight' => 6,
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#attributes' => array(
      'id'=>'user-profile-fieldset3',
      'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-' . _get_display_state($profile_type, $edit, $preference_page, 'fieldset3'),
    ),
  );

  $form['save_and_submit'] = array(
    '#type' => 'fieldset',
    '#title' => t('Save and Submit'),
    '#weight' => 8,
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#attributes' => array(
      'id'=>'user-profile-fieldset4',
      'class' => 'user-profile-type-' . $profile_type . ' user-profile-fieldset-display-' . _get_display_state($profile_type, $edit, $preference_page, 'fieldset4'),
    ),
  );

  $form['my_profile']['user_profile']['first_name'] = array(
    '#type' => 'textfield',
    '#title' => t('First Name'),
    '#default_value' => $edit->first_name,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
      'class' => 'right',
    ),
  );

  $form['my_profile']['user_profile']['last_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Last Name'),
    '#default_value' => $edit->last_name,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
      'class' => 'right',
    ),
  );

  // Full birth date, with year, required for Full Preference Center
  // Month and day are only required for Basic.
  if ($show_full_preference_center) {
    $form['my_profile']['user_profile']['birth_date'] = array(
      '#type' => 'date',
      '#title' => t('Birth Date'),
      '#default_value' => ($edit) ? $edit->birth_date : array('month' => 'mm', 'day' =>'dd', 'year' => 'yyyy'),
      '#after_build' => array('_user_preference_birth_date_callback'),
      '#required' => TRUE,
    );
  } else {
    $form['my_profile']['user_profile']['birth_date'] = array(
      '#type' => 'date',
      '#title' => t('Birth Date'),
      '#default_value' => ($edit) ? $edit->birth_date : array('month' => 'mm', 'day' =>'dd', 'year' => 'yyyy'),
      '#after_build' => array('_user_preference_birth_date_callback'),
      '#required' => TRUE,
      '#element_validate' => array(), //This is necessary to disable the default call to date_validate() to avoid validating year
      '#description' => t('Please note that only month and day are required.'),
    );
  }

  // Gender required for Full Preference Center only
  if ($show_full_preference_center) {
    $form['my_profile']['user_profile']['gender'] = array(
      '#type' => 'radios',
      '#title' => t('Gender'),
      '#default_value' => ($edit) ? $edit->gender : NULL,
      '#options' => array( 0 => t('Female'), 1 => t('Male')),
      '#required' => TRUE,
      '#prefix' => '<div class="container-inline">',
      '#suffix' => '</div>',
    );
  }

  $form['my_profile']['user_profile']['address'] = array(
    '#type' => 'textfield',
    '#title' => t('Address'),
    '#default_value' => $edit->address,
    '#maxlength' => 255,
    '#required' => FALSE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
       'class' => 'right',
    ),
  );

  $form['my_profile']['user_profile']['city'] = array(
    '#type' => 'textfield',
    '#title' => t('City'),
    '#default_value' => $edit->city,
    '#maxlength' => 255,
    '#required' => FALSE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
      'class' => 'right',
    ),
  );

  if ($us_or_canada) {
    $form['my_profile']['user_profile']['state'] = array(
      '#type' => 'select',
      '#title' => t('State or Province'),
      '#default_value' => $edit->state,
      '#options' =>  $options = array(
        'null'=>'Select One',
        'U.S. States' => array(
        'AK' => 'Alaska',
        'AL' => 'Alabama',
        'AR' => 'Arkansas',
        'AZ' => 'Arizona',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DC' => 'District of Columbia',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'IA' => 'Iowa',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'MA' => 'Massachusetts',
        'MD' => 'Maryland',
        'ME' => 'Maine',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MO' => 'Missouri',
        'MS' => 'Mississippi',
        'MT' => 'Montana',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'NE' => 'Nebraska',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NV' => 'Nevada',
        'NY' => 'New York',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'PR' => 'Puerto Rico',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VA' => 'Virginia',
        'VT' => 'Vermont',
        'WA' => 'Washington',
        'WI' => 'Wisconsin',
        'WV' => 'West Virginia',
        'WY' => 'Wyoming'),
        'Canadian Provinces' => array(
        'AB' => 'Alberta',
        'BC' => 'British Columbia',
        'MB' => 'Manitoba',
        'NB' => 'New Brunswick',
        'NF' => 'Newfoundland',
        'NT' => 'Northwest Territories',
        'NS' => 'Nova Scotia',
        'NU' => 'Nunavut',
        'ON' => 'Ontario',
        'PE' => 'Prince Edward Island',
        'QC' => 'Quebec',
        'SK' => 'Saskatchewan',
        'YT' => 'Yukon Territory'),
        ),
      '#required' => FALSE,
      '#prefix' => '<div class="container-inline">',
      '#suffix' => '</div>',
      '#attributes' => array(
        'class' => 'right country_select',
      ),
    );
  } else {
    $form['my_profile']['user_profile']['state'] = array(
      '#type' => 'textfield',
      '#title' => t('State or Province'),
      '#default_value' => $edit->state,
      '#maxlength' => 25,
      '#required' => FALSE,
      '#prefix' => '<div class="container-inline">',
      '#suffix' => '</div>',
      '#attributes' => array(
        'class' => 'right',
      ),
    );
  }

  $form['my_profile']['user_profile']['country'] = array(
    '#type' => 'select',
    '#title' => t('Country'),
    '#options' =>  array(' ' => t(' '),'AF' => t('AFGHANISTAN'),'AX' => t('ALAND ISLANDS'),'AL' => t('ALBANIA'),'DZ' => t('ALGERIA'),'AS' => t('AMERICAN SAMOA'),'AD' => t('ANDORRA'),'AO' => t('ANGOLA'),'AI' => t('ANGUILLA'),'AQ' => t('ANTARCTICA'),'AG' => t('ANTIGUA AND BARBUDA'),'AR' => t('ARGENTINA'),'AM' => t('ARMENIA'),'AW' => t('ARUBA'),'AU' => t('AUSTRALIA'),'AT' => t('AUSTRIA'),'AZ' => t('AZERBAIJAN'),'BS' => t('BAHAMAS'),'BH' => t('BAHRAIN'),'BD' => t('BANGLADESH'),'BB' => t('BARBADOS'),'BY' => t('BELARUS'),'BE' => t('BELGIUM'),'BZ' => t('BELIZE'),'BJ' => t('BENIN'),'BM' => t('BERMUDA'),'BT' => t('BHUTAN'),'BO' => t('BOLIVIA'),'BA' => t('BOSNIA AND HERZEGOVINA'),'BW' => t('BOTSWANA'),'BV' => t('BOUVET ISLAND'),'BR' => t('BRAZIL'),'IO' => t('BRITISH INDIAN OCEAN TERRITORY'),'BN' => t('BRUNEI DARUSSALAM'),'BG' => t('BULGARIA'),'BF' => t('BURKINA FASO'),'BI' => t('BURUNDI'),'KH' => t('CAMBODIA'),'CM' => t('CAMEROON'),'CA' => t('CANADA'),'CV' => t('CAPE VERDE'),'KY' => t('CAYMAN ISLANDS'),'CF' => t('CENTRAL AFRICAN REPUBLIC'),'TD' => t('CHAD'),'CL' => t('CHILE'),'CN' => t('CHINA'),'CX' => t('CHRISTMAS ISLAND'),'CC' => t('COCOS (KEELING) ISLANDS'),'CO' => t('COLOMBIA'),'KM' => t('COMOROS'),'CG' => t('CONGO'),'CD' => t('CONGO, THE DEMOCRATIC REPUBLIC OF THE'),'CK' => t('COOK ISLANDS'),'CR' => t('COSTA RICA'),'CI' => t('COTE D`IVOIRE'),'HR' => t('CROATIA'),'CU' => t('CUBA'),'CY' => t('CYPRUS'),'CZ' => t('CZECH REPUBLIC'),'DK' => t('DENMARK'),'DJ' => t('DJIBOUTI'),'DM' => t('DOMINICA'),'DO' => t('DOMINICAN REPUBLIC'),'EC' => t('ECUADOR'),'EG' => t('EGYPT'),'SV' => t('EL SALVADOR'),'GQ' => t('EQUATORIAL GUINEA'),'ER' => t('ERITREA'),'EE' => t('ESTONIA'),'ET' => t('ETHIOPIA'),'FK' => t('FALKLAND ISLANDS (MALVINAS)'),'FO' => t('FAROE ISLANDS'),'FJ' => t('FIJI'),'FI' => t('FINLAND'),'FR' => t('FRANCE'),'GF' => t('FRENCH GUIANA'),'PF' => t('FRENCH POLYNESIA'),'TF' => t('FRENCH SOUTHERN TERRITORIES'),'GA' => t('GABON'),'GM' => t('GAMBIA'),'GE' => t('GEORGIA'),'DE' => t('GERMANY'),'GH' => t('GHANA'),'GI' => t('GIBRALTAR'),'GR' => t('GREECE'),'GL' => t('GREENLAND'),'GD' => t('GRENADA'),'GP' => t('GUADELOUPE'),'GU' => t('GUAM'),'GT' => t('GUATEMALA'),'GG' => t('GUERNSEY'),'GN' => t('GUINEA'),'GW' => t('GUINEA-BISSAU'),'GY' => t('GUYANA'),'HT' => t('HAITI'),'HM' => t('HEARD ISLAND AND MCDONALD ISLANDS'),'VA' => t('HOLY SEE (VATICAN CITY STATE)'),'HN' => t('HONDURAS'),'HK' => t('HONG KONG'),'HU' => t('HUNGARY'),'IS' => t('ICELAND'),'IN' => t('INDIA'),'ID' => t('INDONESIA'),'IR' => t('IRAN; ISLAMIC REPUBLIC OF'),'IQ' => t('IRAQ'),'IE' => t('IRELAND'),'IM' => t('ISLE OF MAN'),'IL' => t('ISRAEL'),'IT' => t('ITALY'),'JM' => t('JAMAICA'),'JP' => t('JAPAN'),'JE' => t('JERSEY'),'JO' => t('JORDAN'),'KZ' => t('KAZAKHSTAN'),'KE' => t('KENYA'),'KI' => t('KIRIBATI'),'KP' => t('KOREA,DEMOCRATIC PEOPLE`S REPUBLIC OF'),'KR' => t('KOREA, REPUBLIC OF'),'KW' => t('KUWAIT'),'KG' => t('KYRGYZSTAN'),'LA' => t('LAO PEOPLE`S DEMOCRATIC REPUBLIC'),'LV' => t('LATVIA'),'LB' => t('LEBANON'),'LS' => t('LESOTHO'),'LR' => t('LIBERIA'),'LY' => t('LIBYAN ARAB JAMAHIRIYA'),'LI' => t('LIECHTENSTEIN'),'LT' => t('LITHUANIA'),'LU' => t('LUXEMBOURG'),'MO' => t('MACAO'),'MK' => t('MACEDONIA'),'MG' => t('MADAGASCAR'),'MW' => t('MALAWI'),'MY' => t('MALAYSIA'),'MV' => t('MALDIVES'),'ML' => t('MALI'),'MT' => t('MALTA'),'MH' => t('MARSHALL ISLANDS'),'MQ' => t('MARTINIQUE'),'MR' => t('MAURITANIA'),'MU' => t('MAURITIUS'),'YT' => t('MAYOTTE'),'MX' => t('MEXICO'),'FM' => t('MICRONESIA, FEDERATED STATES OF'),'MD' => t('MOLDOVA, REPUBLIC OF'),'MC' => t('MONACO'),'MN' => t('MONGOLIA'),'ME' => t('MONTENEGRO'),'MS' => t('MONTSERRAT'),'MA' => t('MOROCCO'),'MZ' => t('MOZAMBIQUE'),'MM' => t('MYANMAR'),'NA' => t('NAMIBIA'),'NR' => t('NAURU'),'NP' => t('NEPAL'),'NL' => t('NETHERLANDS'),'AN' => t('NETHERLANDS ANTILLES'),'NC' => t('NEW CALEDONIA'),'NZ' => t('NEW ZEALAND'),'NI' => t('NICARAGUA'),'NE' => t('NIGER'),'NG' => t('NIGERIA'),'NU' => t('NIUE'),'NF' => t('NORFOLK ISLAND'),'MP' => t('NORTHERN MARIANA ISLANDS'),'NO' => t('NORWAY'),'OM' => t('OMAN'),'PK' => t('PAKISTAN'),'PW' => t('PALAU'),'PS' => t('PALESTINIAN TERRITORY, OCCUPIED'),'PA' => t('PANAMA'),'PG' => t('PAPUA NEW GUINEA'),'PY' => t('PARAGUAY'),'PE' => t('PERU'),'PH' => t('PHILIPPINES'),'PN' => t('PITCAIRN'),'PL' => t('POLAND'),'PT' => t('PORTUGAL'),'PR' => t('PUERTO RICO'),'QA' => t('QATAR'),'RE' => t('REUNION'),'RO' => t('ROMANIA'),'RU' => t('RUSSIAN FEDERATION'),'RW' => t('RWANDA'),'SH' => t('SAINT HELENA'),'KN' => t('SAINT KITTS AND NEVIS'),'LC' => t('SAINT LUCIA'),'PM' => t('SAINT PIERRE AND MIQUELON'),'VC' => t('SAINT VINCENT AND THE GRENADINES'),'WS' => t('SAMOA'),'SM' => t('SAN MARINO'),'ST' => t('SAO TOME AND PRINCIPE'),'SA' => t('SAUDI ARABIA'),'SN' => t('SENEGAL'),'RS' => t('SERBIA'),'SC' => t('SEYCHELLES'),'SL' => t('SIERRA LEONE'),'SG' => t('SINGAPORE'),'SK' => t('SLOVAKIA'),'SI' => t('SLOVENIA'),'SB' => t('SOLOMON ISLANDS'),'SO' => t('SOMALIA'),'ZA' => t('SOUTH AFRICA'),'ES' => t('SPAIN'),'LK' => t('SRI LANKA'),'SD' => t('SUDAN'),'SR' => t('SURINAME'),'SJ' => t('SVALBARD AND JAN MAYEN'),'SZ' => t('SWAZILAND'),'SE' => t('SWEDEN'),'CH' => t('SWITZERLAND'),'SY' => t('SYRIAN ARAB REPUBLIC'),'TW' => t('TAIWAN, PROVINCE OF CHINA'),'TJ' => t('TAJIKISTAN'),'TZ' => t('TANZANIA, UNITED REPUBLIC OF'),'TH' => t('THAILAND'),'TL' => t('TIMOR-LESTE'),'TG' => t('TOGO'),'TK' => t('TOKELAU'),'TO' => t('TONGA'),'TT' => t('TRINIDAD AND TOBAGO'),'TN' => t('TUNISIA'),'TR' => t('TURKEY'),'TM' => t('TURKMENISTAN'),'TC' => t('TURKS AND CAICOS ISLANDS'),'TV' => t('TUVALU'),'UG' => t('UGANDA'),'UA' => t('UKRAINE'),'AE' => t('UNITED ARAB EMIRATES'),'GB' => t('UNITED KINGDOM'),'US' => t('UNITED STATES'),'UM' => t('UNITED STATES MINOR OUTLYING ISLANDS'),'UY' => t('URUGUAY'),'UZ' => t('UZBEKISTAN'),'VU' => t('VANUATU'),'VE' => t('VENEZUELA'),'VN' => t('VIET NAM'),'VG' => t('VIRGIN ISLANDS,BRITISH'),'VI' => t('VIRGIN ISLANDS, U.S.'),'WF' => t('WALLIS AND FUTUNA'),'EH' => t('WESTERN SAHARA'),'YE' => t('YEMEN'),'ZM' => t('ZAMBIA'),'ZW' => t('ZIMBABWE')),
    '#default_value' => (empty($edit->country) || $edit->country == '') ? strtoupper($country) : $edit->country,
    '#required' => FALSE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
      'class' => 'right country_select',
    ),
  );

  $form['my_profile']['user_profile']['zip'] = array(
    '#type' => 'textfield',
    '#title' => t('Zip or Postal Code'),
    '#default_value' => $edit->zip,
    '#maxlength' => 25,
    '#required' => FALSE,
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
    '#attributes' => array(
      'class' => 'right',
    ),
  );

  if ($show_full_preference_center && !$edit) {
    $form['my_profile']['back'] = array(
      '#type' => 'button',
      '#executes_submit_callback' => FALSE,
      '#value' => t('BACK'),
      '#attributes' => array(
        'class'=>'back-button',
        "onClick" => "Drupal.Digibrij_User_Profile.back(1); return false;",
      ),
    );

    $form['my_profile']['next'] = array(
      '#type' => 'button',
      '#executes_submit_callback' => FALSE,
      '#value' => t('NEXT'),
      '#attributes' => array(
        'class'=>'next-button',
        "onClick" => "Drupal.Digibrij_User_Profile.next(1); return false;",
      ),
    );
  }

  /**
   * Beauty Preference checkboxes for Full Preference Center only
   */
  if ($show_full_preference_center) {
    /** Populate User Preferences from taxonomy terms **/
    $fragrance_types = _pull_terms(taxonomy_get_tree($fragrance_types_vid, 0, -1, 1));
    $fragrance_families = _pull_terms(taxonomy_get_tree($fragrance_families_vid, 0, -1, 1));
    $fragrance_personas = _pull_terms(taxonomy_get_tree($fragrance_personas_vid, 0, -1, 1));
    $email_preferences = _pull_terms(taxonomy_get_tree($email_preferences_vid, 0, -1, 1));


    $form['my_beauty_profile']['user_profile']['fragrance-preferences'] = array(
      '#type' => 'fieldset',
      '#title' => 'Fragrances',
      '#weight' => 10,
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#attributes' => array(
        'id'=>'user-profile-preference-group1',
        'class' => 'user-profile-preference-group'
      ),
    );

    $form['my_beauty_profile']['user_profile']['beauty-preferences'] = array(
      '#type' => 'fieldset',
      '#title' => 'Beauty',
      '#weight' => 10,
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#attributes' => array(
        'id'=>'user-profile-preference-group2',
        'class' => 'user-profile-preference-group'
      ),
    );

    $options_checked = _get_selected_preferences($uid, $email_preferences_vid);
    $form['my_beauty_profile']['user_profile']['beauty-preferences']['user_preference_email_preferences'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Select the beauty areas for which you would like to receive updates about new products, latest trends, beauty tips and exclusive offers.'),
      '#required' => FALSE,
      '#options' => $email_preferences,
      '#default_value' => $options_checked,
      '#theme' => array('multicolumn_options'),
      '#columns' => 2,
    );

    $options_checked = _get_selected_preferences($uid, $fragrance_types_vid);
    $form['my_beauty_profile']['user_profile']['fragrance-preferences']['user_preference_fragrance_types'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Which types of fragrance do you use? (select all that apply)'),
      '#required' => FALSE,
      '#options' => $fragrance_types,
      '#default_value' => $options_checked,
      '#theme' => array('multicolumn_options'),
      '#columns' => 2,
    );

    $options_checked = _get_selected_preferences($uid, $fragrance_families_vid);
    $form['my_beauty_profile']['user_profile']['fragrance-preferences']['user_preference_fragrance_families'] = array(
      '#type' => 'checkboxes',
      '#title' => t('What are your preferred fragrance families/notes?'),
      '#required' => FALSE,
      '#options' => $fragrance_families,
      '#default_value' => $options_checked,
      '#theme' => array('multicolumn_options'),
      '#columns' => 2,
    );

    $options_checked = _get_selected_preferences($uid, $fragrance_personas_vid);
    $form['my_beauty_profile']['user_profile']['fragrance-preferences']['user_preference_fragrance_personas'] = array(
      '#type' => 'checkboxes',
      '#title' => t('What is your fragrance persona?'),
      '#required' => FALSE,
      '#options' => $fragrance_personas,
      '#default_value' => $options_checked,
      '#theme' => array('multicolumn_options'),
      '#columns' => 2,
    );

    if(!$edit) {
      $form['my_beauty_profile']['back'] = array(
        '#type' => 'button',
        '#executes_submit_callback' => FALSE,
        '#value' => t('BACK'),
        '#attributes' => array(
          'class'=>'back-button',
          "onClick" => "Drupal.Digibrij_User_Profile.back(2); return false;",
        ),
      );
      $form['my_beauty_profile']['next'] = array(
        '#type' => 'button',
        '#executes_submit_callback' => FALSE,
        '#value' => t('NEXT'),
        '#attributes' => array(
          'class'=>'next-button',
          "onClick" => "Drupal.Digibrij_User_Profile.next(2); return false;",
        ),
      );
    }
  }

  $form['my_settings']['digibrij'] = array(
    '#type' => 'checkbox',
    '#title' => t('I would like to receive communications from us.'),
    '#default_value' => $edit->digibrij,
  );

  $form['my_settings']['parentcooptin'] = array(
    '#type' => 'checkbox',
    '#title' => t('I would like to receive communications from our parent company.'),
    '#default_value' => $edit->parentcooptin,
  );

  /**
   * Only show Privacy check box on Account/Profile creation
   **/
  if (!$edit) {
    $form['my_settings']['privacy'] = array(
    '#type' => 'checkbox',
    '#title' => t('I agree with the ') . l(t('Privacy Policy'),'http://www.digibrij.com/privacy_policy.html',array('attributes'=>array('target'=>'_blank'))),
    '#required' => TRUE
    );
  }

  if ($show_full_preference_center && !$edit) {
    $form['my_settings']['back'] = array(
      '#type' => 'button',
      '#executes_submit_callback' => FALSE,
      '#value' => t('BACK'),
      '#attributes' => array(
        'class'=>'back-button',
        "onClick" => "Drupal.Digibrij_User_Profile.back(3); return false;",
      ),
    );

    $form['my_settings']['next'] = array(
      '#type' => 'button',
      '#executes_submit_callback' => FALSE,
      '#value' => t('NEXT'),
      '#attributes' => array(
        'class'=>'next-button',
        "onClick" => "Drupal.Digibrij_User_Profile.next(3); return false;",
      ),
    );
  }

  // Use captcha on register only, not edit
  if (!$edit) {
    $form['save_and_submit']['my_captcha'] = array(
      '#type' => 'captcha',
      '#attributes'=> array(
        'id'=>'user-profile-captcha'
      ),
    );
  }

 if ($show_full_preference_center && !$edit) {
    $form['save_and_submit']['back'] = array(
      '#type' => 'button',
      '#executes_submit_callback' => FALSE,
      '#value' => t('BACK'),
      '#attributes' => array(
        'class'=>'back-button',
        "onClick" => "Drupal.Digibrij_User_Profile.back(4); return false;",
      ),
    );
  }

  $form['save_and_submit']['submit'] = array(
    '#type' => 'submit',
    '#value' => $submitButtonText,
    '#attributes' => array(
      'class' => 'create-button',
    ),
  );

  return $form;
}
