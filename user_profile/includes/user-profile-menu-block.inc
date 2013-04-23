<?php
global $user;
$uid = $user->uid;
// Call out custom function to retrieve the Profile type of the is user ('full' or 'basic')
// 'Full' - sees all links
// 'Basic' - sees just the profile form link
$preference_center_access_type =  _get_profile_type();

$preference_center_tabs = array(
  'user_profile_tab' => array(
    'path' => '/user/preference_center/profile/' . $uid,
    'path_match' => '#user/preference_center/profile#',
    'active_trail' => false,
  ),
  'beauty_profile_tab' => array(
    'path' => '/user/preference_center/beauty_profile/' . $uid,
    'path_match' => '#user/preference_center/beauty_profile#',
    'active_trail' => false,
  ),
  'communications_settings_tab' => array(
    'path' => '/user/preference_center/settings/' . $uid,
    'path_match' => '#user/preference_center/settings#',
    'active_trail' => false,
  ),
  'favorites_tab' => array(
    'path' => '/user/' . $uid . '/favorites',
    'path_match' => '#user/[0-9]+/favorites#',
    'active_trail' => false,
  ),
  'ratings_reviews_tab' => array(
    'path' => '/user/preference_center/ratings_reviews/' . $uid,
    'path_match' => '#user/preference_center/ratings_reviews#',
    'active_trail' => false,
  ),
);

$current_path = $_GET['q'];
foreach($preference_center_tabs as &$tab){
  if (preg_match($tab['path_match'], $current_path)) {
    $tab['active_trail'] = true;
  }
}
?>
<div id="block-menu-menu-manage-your-account" class="block block-menu tabs">
    <div class="content">
        <ul class="menu">
            <li class="leaf first odd <?php if($preference_center_tabs['user_profile_tab']['active_trail']) { print 'active-trail'; } ?>"><a href="<?php print $preference_center_tabs['user_profile_tab']['path'];?>" title="" class="active active"><?php print t('My Profile'); ?></a></li>
          <?php if ($preference_center_access_type == 'full'): ?>
            <li class="leaf even <?php if($preference_center_tabs['beauty_profile_tab']['active_trail']) { print 'active-trail'; } ?>"><a href="<?php print $preference_center_tabs['beauty_profile_tab']['path'];?>" title="" class="active active"><?php print t('My Beauty Profile'); ?></a></li>
            <li class="leaf odd <?php if($preference_center_tabs['communications_settings_tab']['active_trail']) { print 'active-trail'; }?>"><a href="<?php print $preference_center_tabs['communications_settings_tab']['path'];?>" title="" class="active active"><?php print t('My Settings'); ?></a></li>
            <li class="leaf even <?php if($preference_center_tabs['favorites_tab']['active_trail']) { print 'active-trail'; } ?>"><a href="<?php print $preference_center_tabs['favorites_tab']['path'];?>" title="" class="active active"><?php print t('My Favorites'); ?></a></li>
            <li class="leaf last odd <?php if($preference_center_tabs['ratings_reviews_tab']['active_trail']) { print 'active-trail'; } ?>"><a href="<?php print $preference_center_tabs['ratings_reviews_tab']['path'];?>" title="" class="active active"><?php print t('My Ratings and Reviews'); ?></a></li>
          <?php endif; ?>
        </ul>
    </div>
    <div class="clear"></div>
</div>