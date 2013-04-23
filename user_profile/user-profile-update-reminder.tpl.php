<?php global $user; ?>

<section>
  <div id="newsletter-subscribe-form-container">
    <div id="newsletter-subscribe-form-container-inner-left">
      <h2>sally hansen</h2>
    </div>
    <div id="newsletter-subscribe-form-container-inner-right">
      <div id="newsletter-subscribe-thankyou-text">
        <p><strong><?php print t('Have you updated your Sally Hansen profile lately?'); ?></strong></p>
        <p><?php print t('Please take a moment to update your user profile.'); ?> </p>
        <div class="button flag-wrapper">
          <?php if ($user->uid > 0) { ?>
            <?php print l(t('UPDATE YOUR PROFILE'), 'user/preference_center/profile/' . $user->uid); ?>
          <?php } else { ?>
            <?php print l(t('SIGN IN'), 'user/login/nojs', array('attributes' => array('class' => 'ctools-use-dialog'))); ?>
          <?php } ?>
        </div>
      </div>
    </div>
</section>
