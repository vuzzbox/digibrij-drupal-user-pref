<?php
if ($_GET['q'] == 'admin/user/user/create') {
	print drupal_render($form);
} else {
?>
<section>
	<div id="user-profile-registration-form">
<?php
	print drupal_render($form['account']);
	print drupal_render($form['my_profile']);
	print drupal_render($form['my_beauty_profile']);
	print drupal_render($form['my_settings']);
	print drupal_render($form['save_and_submit']);
	print drupal_render($form['form_id']);
	print drupal_render($form['form_build_id']);
	print drupal_render($form['form_token']);
?>
	</div>
</section>
<?php
}
?>
