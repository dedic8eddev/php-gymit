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
  accordion
--------------------------------------*/
$(document).on("click", ".accordionTitle", function () {
  var $this = $(this);
  if ($this.parent().hasClass('open')) {
    $this.parent().removeClass('open');
    $this.next().slideUp(250);
  } else {
    $this.parent().addClass('open');
    $this.next().slideDown(250);
  }
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
  Lecture trainers slider
--------------------------------------*/
if ($(".lectureTrainers").length) {
  $slick = $('.lectureTrainers .lectureTrainersSlider');
  $slick.on('init', function () {
    var dataID = $(".slick-current").attr('data-trainer-id');
    showTrainer(dataID);

    if (!$slick.find('.slick-slide').hasClass('slick-cloned')) {
      $slick.addClass('no-clones');
    }

  });
  $slick.slick({
    arrows: true,
    dots: false,
    pauseOnHover: false,
    draggable: true,
    slidesToShow: 6,
    slidesToScroll: 1,
    infinite: true,
    speed: 600,
    adaptiveHeight: false,
    autoPlay: true,
    centerMode: true,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
        }
      },
      {
        breakpoint: 800,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 550,
        settings: {
          slidesToShow: 1,
        }
      }
    ]

  }).slick('slickNext');

  $slick.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
    var dataID = $(".slick-current").attr('data-trainer-id');
    showTrainer(dataID);
  });

  $('.lectureTrainers .lectureTrainersSlider .slick-slide').on('click', function () {
    var clicked = $(this);
    var trainerID = $(this).attr('data-trainer-id');
    var slickID = $(this).attr('data-slick-index');


    if (!$slick.hasClass('no-clones')) {
      $slick.slick('slickGoTo', slickID);
      showTrainer(trainerID);
    } else {
      $(".lectureTrainers .lectureTrainersSlider .slick-slide").removeClass('slick-current');
      clicked.addClass('slick-current');
      console.log(trainerID);
      showTrainer(trainerID);
    }
  });

  function showTrainer(trainerID) {
    $(".lectureTrainersDetail .trainer").each(function () {
      var $this = $(this);
      if ($this.attr("data-trainer-id") === trainerID) {
        $(".lectureTrainersDetail .trainer").removeClass('active');
        $this.addClass('active');
      }
    });
  }
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
      alt: $this.children('option').eq(i).attr("data-start-date")
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
    console.log($(this).attr('data-start-date'));
    $list.slideUp(300);
    //console.log($this.val());
  });

  $(document).click(function () {
    $styledSelect.removeClass('active');
    $list.slideUp(300);
  });

});

/*-------------------------------------
  Full width gallery slider
--------------------------------------*/
if ($(".fullWidthGallerySlider").length) {
  var fullWidthGallerySlider = $(".fullWidthGallerySlider");

  fullWidthGallerySlider.slick({
    arrows: true,
    dots: false,
    pauseOnHover: false,
    draggable: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    infinite: false,
    speed: 600,
    adaptiveHeight: false,
    autoPlay: true,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
        }
      },
      {
        breakpoint: 800,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 550,
        settings: {
          slidesToShow: 1,
        }
      }
    ]

  });
}


/*-------------------------------------
  Full width gallery slider
--------------------------------------*/
if ($(".coachReviewsSlider").length) {
  var coachReviewsSlider = $(".coachReviewsSlider");

  coachReviewsSlider.slick({
    arrows: false,
    dots: false,
    pauseOnHover: false,
    draggable: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: true,
    speed: 600,
    adaptiveHeight: false,
    autoPlay: true,
    prevArrow: '.slickNext'
  });

  $(".slickNext").on("click", function () {
    coachReviewsSlider.slick("slickNext");
  });
  $(".slickPrev").on("click", function () {
    coachReviewsSlider.slick("slickPrev");
  });
}

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
    submitHandler: function (form) { },

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


  $("form.contactForm").submit(function (e) {
    if ($('form.contactForm').valid()) {
      e.preventDefault();
      var formData = new FormData($(this)[0]);

      $.ajax({
        type: "POST",
        url: "./assets/php/mailer.php",
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

    }
  });
}

$(document).ready(function () {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'cs',
    plugins: ['timeGrid', 'dayGrid'],
    defaultView: 'timeGridWeek',
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay,listWeek'
    },
    firstDay: 1,
    minTime: '06:00:00',
    maxTime: '20:00:00',
    slotDuration: '00:15:00',
    slotLabelInterval: 15,
    slotMinutes: 15,
    navLinks: false,
    allDaySlot: false,
    slotLabelFormat: {
      hour: '2-digit', //2-digit, numeric
      minute: '2-digit', //2-digit, numeric
      meridiem: false, //lowercase, short, narrow, false (display of AM/PM)
      hour12: false //true, false
    },
    snapDuration: '00:30:00',
    columnHeaderFormat: {
      weekday: 'long',
      day: 'numeric',
      month: 'numeric',
    },
    editable: false,
    eventLimit: true, // allow "more" link when too many events
    dateClick: false,
    eventLimit: 2, // adjust to 6 only for timeGridWeek/timeGridDay
    events: [

      {
        title: 'Birthday Party',
        start: '2019-10-01T07:00:00',
        end: '2019-10-01T10:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-01T07:00:00',
        end: '2019-10-01T10:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },

      {
        title: 'Birthday Party',
        start: '2019-10-02T07:00:00',
        end: '2019-10-02T10:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-02T07:00:00',
        end: '2019-10-02T10:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-02T07:30:00',
        end: '2019-10-02T10:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-02T07:00:00',
        end: '2019-10-02T09:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-02T12:00:00',
        end: '2019-10-02T14:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },
      {
        title: 'Birthday Party',
        start: '2019-10-02T13:30:00',
        end: '2019-10-02T15:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },


      {
        title: 'Birthday Party',
        start: '2019-10-03T07:30:00',
        end: '2019-10-03T15:00:00',
        description: 'Lorem ipsum dolor sit amet',
        className: 'customBox'
      },

    ],
    //tooltip popper on event
    eventRender: function (info) {

      var dataHoje = new Date();
      if (info.event.start < dataHoje && info.event.end > dataHoje) {
        $(info.el).addClass('past');
      } else if (info.event.start < dataHoje && info.event.end < dataHoje) {
        $(info.el).addClass('past');
      } else if (info.event.start > dataHoje && info.event.end > dataHoje) {
        $(info.el).addClass('future');

        var tooltip = new Tooltip(info.el, {
          title: info.event.extendedProps.description,
          placement: 'top',
          container: 'body',
          trigger: "click", //click
        });
      }

      var content = $(info.el).find('.fc-content');
      var html = '';
      html += '<div class="eventTitle">' + info.event.title + '</div><div class="eventDate">' + moment(info.event.start).format('HH:mm') + ' - ' + moment(info.event.end).format('HH:mm') + '</div><div class="eventCoach">Janek</div>';
      content.html(html);
    },


  });

  calendar.render();
  /*-------------------------------------
    change calendar date
  --------------------------------------*/

  $(".rangeSwitcher .select-options li").on("click", function () {
    var date = $(this).attr("alt");
    calendar.gotoDate(date);
  });

  $(".btnDayView").on("click", function () {
    $(".tabSwitcher li").removeClass('active');
    calendar.changeView('timeGridDay');
    $(this).addClass('active');
  });
  $(".btnWeekView").on("click", function () {
    $(".tabSwitcher li").removeClass('active');
    calendar.changeView('timeGridWeek');
    $(this).addClass('active');
  });

});


// custom calendar
$(document).on("click", ".calendarBody .hour .row", function (e) {
  var parent = $(this);
  var parentRow = parent.closest('.hour');
  var parentDataHour = parent.attr("data-hour");

  if (parentRow.hasClass('open')) {
    if ($(e.target).closest(".box").length) {
      console.log('open-popup');
    } else {
      console.log('close-hour');
      // each added rows
      parentRow.find('.added').each(function () {
        var thisAddedRow = $(this);

        thisAddedRow.find(".box").each(function (el, index) {
          var child = $(this);
          var childDataDay = child.closest('.col').attr('data-day');
          parentRow.find('.row[data-min="0"] .col[data-day="' + childDataDay + '"]').append(child);
        });

        thisAddedRow.remove();
      });
      parentRow.removeClass('open');
    }


  } else {

    //create 15
    parentRow.append('<div class="row added" data-min="15"><div class="col label">' + parentDataHour + ':15</div><div class="col day" data-day="1"></div><div class="col day" data-day="2"></div><div class="col day" data-day="3"></div><div class="col day" data-day="4"></div><div class="col day" data-day="5"></div><div class="col day" data-day="6"></div><div class="col day" data-day="0"></div></div>');
    //create 30
    parentRow.append('<div class="row added" data-min="30"><div class="col label">' + parentDataHour + ':30</div><div class="col day" data-day="1"></div><div class="col day" data-day="2"></div><div class="col day" data-day="3"></div><div class="col day" data-day="4"></div><div class="col day" data-day="5"></div><div class="col day" data-day="6"></div><div class="col day" data-day="0"></div></div>');
    //create 45
    parentRow.append('<div class="row added" data-min="45"><div class="col label">' + parentDataHour + ':45</div><div class="col day" data-day="1"></div><div class="col day" data-day="2"></div><div class="col day" data-day="3"></div><div class="col day" data-day="4"></div><div class="col day" data-day="5"></div><div class="col day" data-day="6"></div><div class="col day" data-day="0"></div></div>');

    parent.find(".box").each(function (el, index) {
      var child = $(this);
      var childDataMin = child.attr("data-min");
      var childDataDay = child.closest('.col').attr('data-day');

      //separate minutes to cols
      if (childDataMin >= 0 && childDataMin < 15) {
        //console.log('00');
      } else if (childDataMin >= 15 && childDataMin < 30) {
        //console.log('15');
        parentRow.find('.row[data-min="15"] .col[data-day="' + childDataDay + '"]').append(child);
      } else if (childDataMin >= 30 && childDataMin < 45) {
        //console.log('30');
        parentRow.find('.row[data-min="30"] .col[data-day="' + childDataDay + '"]').append(child);
      } else {
        //console.log('45');
        parentRow.find('.row[data-min="45"] .col[data-day="' + childDataDay + '"]').append(child);
      }
    });

    parentRow.addClass('open');
  }


});

