/*-------------------------------------
 window...
 --------------------------------------*/

var window_w = $(window).width(); // Window Width
var window_h = $(window).height(); // Window Height
var window_s = $(window).scrollTop(); // Window Scroll Top


var $html = $("html"); // HTML
var $body = $("body"); // Body

/*-------------------------------------
Header fix
--------------------------------------*/

if (window_s > 30) {
  $("#mainMenu").addClass("sticky");
}
$(window).scroll(function () {
  if ($(this).scrollTop() > 30) {
    $("#mainMenu").addClass("sticky");
  } else {
    $("#mainMenu").removeClass("sticky");
  }
});

/*-------------------------------------
Open mobile menu
--------------------------------------*/

$(".open-menu").on("click", function () {
  $(this).toggleClass("open");
  $(".menuContent").toggleClass("open");
});


$(".menuContent a").on("click", function () {
  $(".open-menu").toggleClass("open");
  $(".menuContent").toggleClass("open");
});



$(document).click(function (event) {
  $target = $(event.target);

  if (!$target.closest('.pageMenuContent').length && !$target.closest('.menuOpen').length && !$target.closest('.pageHeaderMenu').length) {
    // console.log('close menu');
    if ($(".pageMenu").hasClass('open')) {
      $(".pageMenu").removeClass("open");
      $("menuOpen").removeClass("open");
    }
    if ($(".pageHeaderMenu").hasClass('open')) {
      $(".pageHeaderMenu").removeClass("open");
      $(".menuOpen").removeClass("open");
    }
  } else {
    // console.log('stay in menu');
  }
});


/*-------------------------------------
Submenu
--------------------------------------*/
$(".pageMenuContent ul li.parent").on("click", function (e) {
  var $this = $(this);

  if ($this.hasClass('opened')) {
    $this.removeClass('opened');
    $this.find("ul").slideUp(150);
    //setTimeout(function () {
    $this.closest(".pageMenuContent > ul").find("ul").removeClass('opened');
    //}, 200);
  } else {
    $this.addClass('opened');
    $this.closest(".pageMenuContent > ul").find("ul").addClass('opened');
    //setTimeout(function () {
    $this.find("ul").slideDown(150);
    //}, 200);
  }
});



/*-------------------------------------
 Smooth Scroll
 --------------------------------------*/

$('a[href*="#"]:not([href="#"])').click(function () {
  if (
    location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") &&
    location.hostname == this.hostname
  ) {
    var target = $(this.hash);
    target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
    if (target.length) {
      $("html, body").animate({
        scrollTop: target.offset().top - 0
      },
        500
      );
      return false;
    }
  }
});

/*-------------------------------------
  Go TO Top
  --------------------------------------*/
if ($(".js-gotop").length) {
  $(".js-gotop").on("click", function (event) {
    event.preventDefault();
    $("html, body").animate({
      scrollTop: $("html").offset().top
    }, 500);
    return false;
  });

  $(window).scroll(function () {
    var $win = $(window);
    if ($win.scrollTop() > 200) {
      $(".js-gotop").addClass("active");
    } else {
      $(".js-gotop").removeClass("active");
    }
  });
}


/*-------------------------------------
  PopUp
--------------------------------------*/
$(document).on("click", ".js-popUp-close", function (e) {
  e.preventDefault();
  $(this).closest('.popUp').addClass('closed');
  $(this).closest('.popUpOverlay').addClass('closed');
  console.log('popup close');
});


/*-------------------------------------
  HP Slider
--------------------------------------*/
if ($(".servicesList").length) {
  $(document).ready(function () {
    var time = 1.3;
    var $bar,
      $slick,
      isPause,
      tick,
      percentTime;

    $slick = $('.servicesListSlider .sliderContainer');
    $slick.slick({
      arrows: false,
      dots: false,
      pauseOnHover: false,
      accessibility: false,
      draggable: false,
      speed: 600,
      adaptiveHeight: true,
      autoPlay: false
    });

    $slick.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
      $(".servicesListText .block").removeClass('active');
      $(".servicesListText .block:nth-child(" + (nextSlide + 1) + ")").addClass('active');
    });

    $bar = $('.servicesListSlider .sliderProgress .progress');

    function startProgressbar() {
      resetProgressbar();
      percentTime = 0;
      isPause = false;
      tick = setInterval(interval, 30);
    }

    function interval() {
      if (isPause === false) {
        percentTime += 1 / (time + 0.1);
        $bar.css({
          width: percentTime + "%"
        });
        if (percentTime >= 100) {
          $slick.slick('slickNext');
          startProgressbar();
        }
      }
    }

    function resetProgressbar() {
      $bar.css({
        width: 0 + '%'
      });
      clearTimeout(tick);
    }

    startProgressbar();

  });
}


/*-------------------------------------
  Custom select box
--------------------------------------*/
$('.customSelect').each(function () {
  var $this = $(this), numberOfOptions = $(this).children('option').length;

  $this.addClass('select-hidden');
  $this.wrap('<div class="customSelectContainer"></div>');
  $this.after('<div class="select-styled"></div>');

  var $styledSelect = $this.next('div.select-styled');
  $styledSelect.text($this.children('option').eq(0).text());

  var $list = $('<ul />', {
    'class': 'select-options'
  }).insertAfter($styledSelect);

  for (var i = 0; i < numberOfOptions; i++) {
    $('<li />', {
      text: $this.children('option').eq(i).text(),
      rel: $this.children('option').eq(i).val(),
      "data-price": $this.children('option').eq(i).data("price")
    }).appendTo($list);
  }

  var $listItems = $list.children('li');

  $styledSelect.click(function (e) {
    e.stopPropagation();
    $('div.select-styled.active').not(this).each(function () {
      $(this).removeClass('active').next('ul.select-options').slideUp(300);
    });
    $(this).toggleClass('active').next('ul.select-options').slideToggle(300);
  });

  $listItems.click(function (e) {
    e.stopPropagation();
    $styledSelect.text($(this).text()).removeClass('active');
    $this.val($(this).attr('rel'));
    $list.slideUp(300);
  });

  $(document).click(function () {
    $styledSelect.removeClass('active');
    $list.slideUp(300);
  });

});



/*-------------------------------------
  Slider with progress bar
--------------------------------------*/
if ($(".progressBarSlider").length) {
  $(document).ready(function () {
    var time = 1.3;
    var $bar,
      $slick,
      isPause,
      tick,
      percentTime;

    $slick = $('.progressBarSlider .sliderContainer');
    $slick.slick({
      arrows: false,
      dots: false,
      pauseOnHover: false,
      accessibility: false,
      draggable: false,
      speed: 600,
      adaptiveHeight: true,
      autoPlay: false
    });

    $bar = $('.progressBarSlider .sliderProgress .progress');

    function startProgressbar() {
      resetProgressbar();
      percentTime = 0;
      isPause = false;
      tick = setInterval(interval, 30);
    }

    function interval() {
      if (isPause === false) {
        percentTime += 1 / (time + 0.1);
        $bar.css({
          width: percentTime + "%"
        });
        if (percentTime >= 100) {
          $slick.slick('slickNext');
          startProgressbar();
        }
      }
    }

    function resetProgressbar() {
      $bar.css({
        width: 0 + '%'
      });
      clearTimeout(tick);
    }

    startProgressbar();

  });
}



/*-------------------------------------
  Slider with progress bar
--------------------------------------*/
if ($(".group_lessons_body").length) {
  // init Isotope
  var $grid = $('.flexRow').isotope({
    itemSelector: '.col'
  });

  // store filter for each group
  var filters = {},
    $checkboxes = $(".headerBottomOverlay input");

  $checkboxes.change(function () {
    var filters = [];
    // get checked checkboxes values
    $checkboxes.filter(':checked').each(function () {
      filters.push(this.value);
    });
    // ['.red', '.blue'] -> '.red, .blue'
    filters = filters.join(', ');
    $grid.isotope({ filter: filters });
  });

  // flatten object by concatting values
  function concatValues(obj) {
    var value = '';
    for (var prop in obj) {
      value += obj[prop];
    }
    return value;
  }

}


$(document).on("click", ".js-tabTitle", function () {
  var $this = $(this);
  var dataTab = $this.data("tab");
  $this.parent().find('.js-tabTitle').removeClass('active');
  $this.addClass('active');
  $(".js-tabContent").hide();
  $(".js-tabContent[data-tab=" + dataTab + "]").show();
});



/*-------------------------------------
  Contact form
--------------------------------------*/
$.extend($.validator.messages, {
  required: "Tento údaj je povinný.",
  remote: "Prosím, opravte tento údaj.",
  email: "Prosím, zadejte platný e-mail.",
  url: "Prosím, zadejte platné URL.",
  date: "Prosím, zadejte platné datum.",
  dateISO: "Prosím, zadejte platné datum (ISO).",
  number: "Prosím, platné telefonní číslo ve formátu 420111222333.",
  digits: "Prosím, zadávejte pouze číslice.",
  creditcard: "Prosím, zadejte číslo kreditní karty.",
  equalTo: "Prosím, zadejte znovu stejnou hodnotu.",
  extension: "Prosím, zadejte soubor se správnou příponou.",
  maxlength: $.validator.format("Prosím, zadejte nejvíce {0} znaků."),
  minlength: $.validator.format("Prosím, zadejte nejméně {0} znaků."),
  rangelength: $.validator.format("Prosím, zadejte od {0} do {1} znaků."),
  range: $.validator.format("Prosím, zadejte hodnotu od {0} do {1}."),
  max: $.validator.format("Prosím, zadejte hodnotu menší nebo rovnu {0}."),
  min: $.validator.format("Prosím, zadejte hodnotu větší nebo rovnu {0}."),
  step: $.validator.format("Musí být násobkem čísla {0}.")
});


if ($(".contactForm").length) {
  $(".contactForm .input input, .contactForm .input textarea").on("change paste keyup", function () {
    var parent = $(this).closest('.input');
    if ($(this).val()) {
      if (!parent.hasClass('focus')) {
        parent.addClass('focus');
      }
    } else {
      parent.removeClass('focus');
    }
  });


  /* forms */
  $("form.contactForm").validate({
    errorElement: "span",
    submitHandler: function (form) { form.submit(); },

    highlight: function (element) {
      $(element).parent().addClass("error");
    },
    unhighlight: function (element) {
      $(element).parent().removeClass("error");
    },

    errorPlacement: function (error, element) {
      if (element.is(':checkbox')) {
        var placement = element.closest('.customCheckbox');
        console.log(error);
        error.insertAfter(placement);
      }
      else {
        error.insertBefore(element);
      }
    }
  });

  /* membership price */
  $('#membershipSelect').change(function(){
    let price = $(this).find('option:selected').data('price');
    $('#totalPrice').text(""+price+"Kč");
  });
  // set firt price
  let price = $("#membershipSelect").find('option:selected').data('price');
  $('#totalPrice').text(""+price+"Kč");

  /* disable form select*/
  $(document).on("click", ".js-tabTitle", function(){
    if ($(this).hasClass('js-basic')) {

      if ($(this).hasClass('js-student')) {
        $('#membershipSelect option[value=7]').prop('selected', 'selected').change();
        //disable options
        $('#membershipSelect option[value=2]').prop('disabled', 'disabled').change();
        $('#membershipSelect option[value=7]').prop('disabled', false).change();
        console.log('student');
      } else {
        $('#membershipSelect option[value=2]').prop('selected', 'selected').change();
        //disable options
        $('#membershipSelect option[value=7]').prop('disabled', 'disabled').change();
        $('#membershipSelect option[value=2]').prop('disabled', false).change();
        console.log('unlimited');
      }
    } else if ($(this).hasClass('js-platinum')) {
      if ($(this).hasClass('js-student')) {
        $('#membershipSelect option[value=14]').prop('selected', 'selected').change();
        //disable options
        $('#membershipSelect option[value=9]').prop('disabled', 'disabled').change();
        $('#membershipSelect option[value=14]').prop('disabled', false).change();
        console.log('student');
      } else {
        $('#membershipSelect option[value=9]').prop('selected', 'selected').change();
        //disable options
        $('#membershipSelect option[value=14]').prop('disabled', 'disabled').change();
        $('#membershipSelect option[value=9]').prop('disabled', false).change();
        console.log('unlimited');
      }
    }
  });

  $("form.contactForm").submit(function (e) {
    /*if ($('form.contactForm').valid()) {
      e.preventDefault();
      var formData = new FormData($(this)[0]),
          url = $(this).data('url');

      $.ajax({
        type: "POST",
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
          if (!data.error) {
            $('.message-alert-ok').fadeIn().html(data);
            $('form.contactForm')[0].reset();
            setTimeout(function () {
              $('.message-alert-ok').fadeOut("slow");
            }, 5000);
          } else {
            console.log(data);
          }
        }
      });

    }*/
  });
}
