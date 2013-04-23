<?php
/**
 * Cron job. Finds empty profile records with invalid data and sets defaults
 */
function user_profile_cron() {
  // look for missing folks, up to 500 at a time
  $missing = db_query('SELECT uid FROM {users} where uid not in (select uid from {user_profile}) LIMIT 500');

  $updates = array();
  while ($row = db_result($missing)) {
    $updates[] = $row;
  }

  if (count($updates) > 0) {
    foreach ($updates as $key => $uid) {
      $account = user_load($uid);

      // fix missing values
      $account->birth_date['year'] = $account->birth_date['year'] ? $account->birth_date['year'] : 0000;
      $account->birth_date['month'] = $account->birth_date['month'] ? $account->birth_date['month'] : 00;
      $account->birth_date['day'] = $account->birth_date['day'] ? $account->birth_date['day'] : 00;
      $account->cotyoptin = $account->cotyoptin ? $account->cotyoptin : 0;
      $account->sallyoptin = $account->sallyoptin ? $account->sallyoptin : 0;

      $result = db_query("INSERT INTO {user_profile} (`uid`,`first_name`,`last_name`,`email`,`birth_date`,`address`,`city`, `state`,`country`,`zip`,`cotyoptin`,`sallyoptin`,`privacy`)
        VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
          $account->uid,
          $account->first_name,
          $account->last_name,
          $account->mail,
          $account->birth_date['year'].'-'.
          $account->birth_date['month'].'-'.
          $account->birth_date['day'].' 00:00:00',
          $account->address,
          $account->city,
          $account->state,
          $account->country,
          $account->zip,
          $account->cotyoptin,
          $account->sallyoptin,
          1 // privacy always 1 if existing user
      );
    }
    watchdog('user_profile','updated '.count($updates).' profiles missing entries in user_profile table');
  }
}
