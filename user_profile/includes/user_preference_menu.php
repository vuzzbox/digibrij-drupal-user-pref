<?php
/**
 * @file Defies hooks and callbacks for custom menus and menu access for Preference Center
 */

module_load_include('php', 'user_profile', 'user_preference_functions');

/**
 * Implements hook_menu()
 *
 * Provides path and access settings for User Profile pages
 */
function user_profile_menu() {
  $items = array();

  // Administrative configuraton form page
  $items['admin/user/user_profile_admin'] = array(
    'title' => 'User Preference Center Admin',
    'description' => 'Admin form introduced by the User Profile module',
    'type' => MENU_NORMAL_ITEM,
    'page callback' => 'drupal_get_form',
    'page arguments' => array('user_profile_form_admin'),
    'access arguments' => array('administer users'),
  );

  // Preference Center forms - Page callbacks
  $items['user/preference_center/beauty_profile/%user_uid_optional'] = array(
    'title callback' => '_preference_center_title_callback',
    'title arguments' => array(2),
    'page callback' => 'drupal_get_form',
    'page arguments' => array('user_profile_form', 3, 'account', 2),
    'access callback' => 'preference_center_path_access',
    'access arguments' => array(3),
    'type' => MENU_NORMAL_ITEM,
    'menu_name' => 'menu-user-profile',
  );

  // Preference Center forms - Page callbacks
  $items['user/preference_center/settings/%user_uid_optional'] = array(
    'title callback' => '_preference_center_title_callback',
    'title arguments' => array(2),
    'page callback' => 'drupal_get_form',
    'page arguments' => array('user_profile_form', 3, 'account', 2),
    'access callback' => 'preference_center_path_access',
    'access arguments' => array(3),
    'type' => MENU_NORMAL_ITEM,
    'menu_name' => 'menu-user-profile',
  );

  // Preference Center forms - Page callbacks
  $items['user/preference_center/profile/%user_uid_optional'] = array(
    'title callback' => '_preference_center_title_callback',
    'title arguments' => array(2),
    'page callback' => 'drupal_get_form',
    'page arguments' => array('user_profile_form', 3, 'account', 2),
    'access callback' => 'user_profile_page_path_access',
    'access arguments' => array(3),
    'type' => MENU_NORMAL_ITEM,
    'load arguments' => array('%map', '%index'),
    'menu_name' => 'menu-user-profile',
  );

  $items['user_profile_reminder/%ctools_js'] = array(
    'title' => 'Just a reminder',
    'page callback' => 'user_profile_update_reminder_callback',
    'page arguments' => array(1),
    'type' => MENU_CALL_BACK,
    'access arguments' => array('access content'),
  );


  return $items;
}

/**
 * Implements hook_menu_alter()
 */
function user_profile_menu_alter(&$items) {
  $items['user/%user_uid_optional']['access callback'] = 'user_profile_noedit_path_access';
  $items['user/%user_uid_optional']['page callback'] = 'user_edit';
  $items['user/%user_category/edit']['access callback'] = 'user_profile_edit_path_access';

  // modify menus for default dialog_user modal form
  $items['user/login/%ctools_js']['title'] = 'CLOSE';
  $items['user/password/%ctools_js']['title'] = 'CLOSE';
}


/**
 * Access callback for user account editing when path does not include /edit at the end.
 */
function user_profile_noedit_path_access($account) {
  return (($GLOBALS['user']->uid == $account->uid) || user_access('administer users')) && $account->uid > 0;
}

/**
 * Access callback for user account editing when path does include /edit at the end.
 */
function user_profile_edit_path_access($account) {
  return user_access('administer users') && $account->uid > 0;
}

/**
 * Access callback for User Preference Center access
 */
function preference_center_path_access($user) {
  // Check to see if the current visitor is from a location where the preference center is in use
  // and check to see if the visitor is accessing his or her own record.
  $access = (((_get_profile_type() == 'full' && $user->uid > 0) && ($GLOBALS['user']->uid == $user->uid)) || user_access('administer users'));

  return $access;
}

/**
 * Access callback for User Profile access
 */
function user_profile_page_path_access($user) {
  // Check to see if the current visitor is from a location where the preference center is in use
  // and check to see if the visitor is accessing his or her own record.
  $access = ((($user->uid > 0) && ($GLOBALS['user']->uid == $user->uid)) || user_access('administer users'));

  return $access;
}


function user_profile_update_reminder_callback($js) {
  ctools_include('ajax');
  $output[] = dialog_command_display('CLOSE', theme('user_profile_update_reminder_page'));
  ctools_ajax_render($output);
}
