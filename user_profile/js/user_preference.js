(function ($) {
  Drupal.Digibrij_User_Profile = Drupal.Digibrij_User_Profile || {};

  $('[class$="user-profile-fieldset-display-off-legend"]').children().not("legend").hide();
  $('[class$="user-profile-fieldset-display-off"]').hide();
  $('[class$="user-profile-fieldset-display-on"]').children().show();

  Drupal.Digibrij_User_Profile.next = function(index) {

    // hide the current fieldset
    $('#user-profile-fieldset'+index).children().not("legend").hide(300);

    // show the next one
    $('#user-profile-fieldset'+(index+1)).children().not("legend").toggle(300);

    // scroll to set focus to the top of the newly opened fieldset (actually, just
    // below the top of just-closed fieldset.)
    $('html, body').animate({
      scrollTop: $('#user-profile-fieldset'+(index)).offset().top + 70
      }, 500);

    return false;
  }

  Drupal.Digibrij_User_Profile.back = function(index) {

    // hide the current fieldset
    $('#user-profile-fieldset'+index).children().not("legend").hide(300);

    // show the previous one
    $('#user-profile-fieldset'+(index-1)).children().not("legend").toggle(300);

    // scroll to set focus to the top of the newly opened fieldset (actually, just
    // below the top of just-closed fieldset.)
    $('html, body').animate({
      scrollTop: $('#user-profile-fieldset'+(index-1)).offset().top - 10
      }, 500);

    return false;
  }

  Drupal.Digibrij_User_Profile.legendClick = function(index) {

    // hide the current fieldset
    $('#user-profile-fieldset'+index).children().not("legend").hide(300);

    // show the next one
    $('#user-profile-fieldset'+(index+1)).children().not("legend").toggle(300);

    // scroll to set focus to the top of the newly opened fieldset (actually, just
    // below the top of just-closed fieldset.)
    $('html, body').animate({
      scrollTop: $('#user-profile-fieldset'+(index)).offset().top + 70
      }, 500);

    return false;
  }

  Drupal.behaviors.user_profile = function(context) {

    //jQuery event stuff here.
    //
    //

  }


})(jQuery);
