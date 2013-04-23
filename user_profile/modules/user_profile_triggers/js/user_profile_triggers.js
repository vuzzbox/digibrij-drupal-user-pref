(function ($) {
  // Make sure our object and dependent objects are defined.
  Drupal.CTools = Drupal.CTools || {};
  Drupal.Dialog = Drupal.Dialog || {};
  Drupal.Digibrij_User_Profile_triggers = Drupal.Digibrij_User_Profile_triggers || {};


  Drupal.Digibrij_User_Profile_triggers.action = function(action,variable) {
    switch (action) {
      case 'popup':
        var url = variable;
        switch (url) {
          case '/sh_newsletter/form/nojs':
            Drupal.Digibrij_User_Profile_triggers.auto_open(url);
            Drupal.settings.Digibrij_User_Profile_triggers.user_profile_return_visitor = 1;
             $.cookie('user_profile_return_visitor', '1', { expires: 3650, path: '/' });
            break;
          case '/user_profile_reminder/nojs':
            Drupal.Digibrij_User_Profile_triggers.auto_open(url);
            // reset reminder date for 7 seven days
            new_profile_date = Math.round(new Date().getTime() / 1000);
            $.cookie('user_profile_last_user_profile_update', new_profile_date, { expires: 3650, path: '/' });
            break;
        }
        break;
    }
  }

  Drupal.Digibrij_User_Profile_triggers.auto_open = function(url) {
      Drupal.Dialog.show();
      Drupal.Dialog.dialog.addClass('ctools-ajaxing');

      // this comes right out of Ctools ajax-responder.js
      try {
        url = Drupal.CTools.AJAX.urlReplaceNojs(url);
        $.ajax({
          type: "POST",
          url: url,
          data: { 'js': 1, 'ctools_ajax': 1},
          global: true,
          success: Drupal.CTools.AJAX.respond,
          error: function(xhr) {
            Drupal.CTools.AJAX.handleErrors(xhr, url);
          },
          complete: function() {
            $('.ctools-ajaxing').removeClass('ctools-ajaxing');
          },
          dataType: 'json'
        });
      }
      catch (err) {
        alert("An error occurred while attempting to process " + url);
        $('.ctools-ajaxing').removeClass('ctools-ajaxing');
        return false;
      }
  }

  Drupal.behaviors.user_profile_triggers = function(context) {
    // Check this only once on document load
    if (context =='[object HTMLDocument]' && Drupal.settings.user_profile_triggers.newsletter_signup_flag == 1)  {
      Drupal.Digibrij_User_Profile_triggers.action('popup','/sh_newsletter/form/nojs');
    }

    if (context =='[object HTMLDocument]' && Drupal.settings.user_profile_triggers.user_profile_update_flag == 1)  {
      Drupal.Digibrij_User_Profile_triggers.action('popup','/user_profile_reminder/nojs');
    }

  }
})(jQuery);
