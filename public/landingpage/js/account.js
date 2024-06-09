$(function () {
  /*-------------------------------------
  Open user menu
  --------------------------------------*/

  $(".js-openUserMenu").on("click", function () {
    $(this).toggleClass('active');
    $(".menu").slideToggle();
  });


  /*-------------------------------------
    Custom select box
  --------------------------------------*/
  if ($(".js-containerSettings").length) {
    // enable edit form in settings
    $(".js-enableEditSettings").on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $parent = $this.closest('.js-containerSettings');
      var $btnSave = $parent.find('.js-saveEditSettings');

      $parent.addClass('js-editEnable');
      $parent.find("input").prop("disabled", false);
      $this.hide();
      $btnSave.show();
    });

    //save form in settings
    $(".js-saveEditSettings").on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $parent = $this.closest('.js-containerSettings');
      var $btnEdit = $parent.find('.js-enableEditSettings');
      var $form = $parent.find('form');

      $form.addClass('loading');

      console.log('saving...');

      //temp save anim
      setTimeout(function () {
        $form.removeClass('loading');
        $parent.removeClass('js-editEnable');
        $parent.find("input").prop("disabled", true);
        $this.hide();
        $btnEdit.show();
        console.log('SAVE!');
      }, 3000);

    });
  }
});
