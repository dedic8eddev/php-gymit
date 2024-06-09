/** @todo Move it somewhere else ? */
var GYM = GYM || (function () {
  var self;
  return {
    /**
     * Send a POST ajax request with a file or files included, catch res with .done()
     * @param {string} URL
     * @param {object} data FormData object of the form
     */
    _upload: function(url, formData){
      return $.ajax({
        type: "POST",
        url: url,
        processData: false,
        contentType: false,
        data: formData,
        dataType: "json",
        success: function (res) {}
      });
    },
    /** 
     * Validate and submit form
     */
    _submitForm: function (data) {
      $(data.form).addClass('loading');

      var formData = new FormData(data.form);

      var inputs = $(data.form).find('input:required, select:required, textarea:required');

      $('.js-media-input-target-id').each(function (i, input) { // photos
        if ($(this).prop('required')) inputs.push($(this));
      });

      $.each(inputs, function (i, input) {
        if ($(input).val() && $(input).val() != '') { // && $(input).val()!='' -> because of multiple select2 items
          $(input).removeClass("is-invalid");
          if ($(input).hasClass('select2')) $(input).parent().find('.select2.select2-container').removeClass('is-invalid');
          if ($(input).hasClass('js-trumbowyg-editor')) $(input).parent().removeClass('is-invalid');
          if ($(input).hasClass('js-media-input-target-id')) $(input).prev('.image-preview').removeClass('is-invalid');
        } else {
          if ($(input).attr("type") != "checkbox") $(input).addClass("is-invalid");
          if ($(input).hasClass('select2')) $(input).parent().find('.select2.select2-container').addClass('is-invalid');
          if ($(input).hasClass('js-trumbowyg-editor')) $(input).closest('.trumbowyg-box').addClass('is-invalid');
          if ($(input).hasClass('js-media-input-target-id')) $(input).prev('.image-preview').addClass('is-invalid');
        }
      });

      if ($(data.form).find(".is-invalid").length <= 0) {
        GYM._upload(data.url, formData).done(function (res) {
          if (!res.error) {
            N.show('success', data.succes_text);
            if (typeof data.success_function !== 'undefined') data.success_function();
          } else N.show('error', res.message);
        });
      } else {
        N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');
      }
      $(data.form).removeClass('loading');
    }
  }
}());

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

      GYM._submitForm({
        url: $(this).data('ajax'),
        form: $($form)[0],
        succes_text: 'Uživatelský účet byl úspěšně upraven!',
        error_text: 'Nepodařilo se vytvořit účet, zkontrolujte údaje nebo to zkuste znovu!',
        success_function: function () {
          $form.removeClass('loading');
          $parent.removeClass('js-editEnable');
          $parent.find("input").prop("disabled", true);
          $this.hide();
          $btnEdit.show();
        }
      });
    });

    }
});
