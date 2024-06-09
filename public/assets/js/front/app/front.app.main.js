(async () => {
  const user = await MAIN._user();
  fireEvents(user);
})();

function fireEvents(user){
  /*-------------------------------------
  vars
  --------------------------------------*/
  var d, n;


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
    var $this = $(this),
      numberOfOptions = $(this).children('option').length;
  
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
      $this.val($(this).attr('rel')).trigger('change');
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
      $grid.isotope({
        filter: filters
      });
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
        } else {
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
  
  
  
  /*-------------------------------------
    Custom calendar
  --------------------------------------*/
  
  // switch week date
  $('#weekSelect, #lessonSelect, #coachSelect').change(function(){
    loadCalendarData();
  });

  function loadCalendarData(){
    let from = $('#weekSelect').val(),
        lesson = $('#lessonSelect').val(),
        coach = $('#coachSelect').val();

    $.ajax({
      type: "POST",
      url: "/calendar/get_calendar_data_ajax",
      data: {from:from,lesson:lesson,coach:coach},
      cache: false,
      dataType: 'json',
      success: function (data) {
        if (!data.error) {
          $('.calendarSection').fadeOut(300, function(){
            $(this).html(data.data);
            $(this).fadeIn(300);
          });
        } else {
          console.log(data);
        }
      }
    });
  }

  // Reserve Lesson
  $(document).on("click", ".reserveLesson", function(e){
    e.preventDefault();
    let data={},
        $this = $(this);
    data['lesson_id'] = $this.data('id');
    if(user) data['user_id'] = user.id;
    else {
      data['email'] = $this.closest('form').find('[name="email"]').val();
      data['password'] = $this.closest('form').find('[name="password"]').val();
    }
    MAIN._post('/calendar/reserve-lesson-ajax', data).done(function(res){
      if(res.type == 'success'){
        $this.toggleClass('cancelReservation reserveLesson');
        $this.find('span').text('Zrušit rezervaci');
        let openSlots = res.openSlots == 1 ? '1 volné místo' : (res.openSlots > 1 && res.openSlots < 5) ? `${res.openSlots} volná místa` : `${res.openSlots} volných míst`;        
        $this.closest('.content').find('.open').text(openSlots);        
      } else {
        res.type = 'error';
      }
      N.show(res.type,res.msg);
      console.log(res);
    });
  });

  // Cancel lesson reservation
  $(document).on("click", ".cancelReservation", function(e){
    e.preventDefault();
    let $this = $(this);
        id = $this.data('id'),
        start = moment($this.data('start')),
        now = moment(new Date()); //todays date
        duration = moment.duration(start.diff(now));
        hours = duration.asHours();
    if(hours < 24 && !confirm('Zrušením rezervace méně než 24 hodin před začátkem lekce Vám nebude vrácen rezervační poplatek. Chcete pokračovat?')){
      return false;
    }

    MAIN._post('/calendar/cancel_reservation_ajax', {lesson_id:id}).done(function(res){
      if(res.type != 'success'){
        res.type = 'error';
      }
      $this.toggleClass('cancelReservation reserveLesson');
      $this.find('span').text('Rezervovat');
      let openSlots = res.openSlots == 1 ? '1 volné místo' : (res.openSlots > 1 && res.openSlots < 5) ? `${res.openSlots} volná místa` : `${res.openSlots} volných míst`;        
      $this.closest('.content').find('.open').text(openSlots);
      N.show(res.type,res.msg);
      console.log(res);
    });        

  });
  
  // switch day/week
  $(".btnDayView").on("click", function () {
    $(".tabSwitcher li").removeClass('active');
    $(".calendarSection").addClass('oneDay');
    $(this).addClass('active');
  });
  $(".btnWeekView").on("click", function () {
    $(".tabSwitcher li").removeClass('active');
    $(".calendarSection").removeClass('oneDay');
    $(this).addClass('active');
  });
  
  // add today class function
  function addClassToday(parentRow) {
    var d = new Date();
    var n = d.getDay();
  
    parentRow.find('.col').each(function (i, e) {
      if (i === n) {
        $(this).addClass('today');
      }
    });
  }


  //hide box filter
  var statusFilter = $(".statusFilter");
  if (statusFilter.length) {
    var statusFilterInput = $(".statusFilter input");
    statusFilterInput.change(function () {

      if ($(this).hasClass('available')) {
        if (!$(this).is(':checked')) {
          $(".calendarBody").addClass('hideAvailable');
        } else {
          $(".calendarBody").removeClass('hideAvailable');
        }
      }
      else if ($(this).hasClass('full')) {
        if (!$(this).is(':checked')) {
          $(".calendarBody").addClass('hideFull');
        }
        else {
          $(".calendarBody").removeClass('hideFull');
        }
      }
      else if ($(this).hasClass('finished')) {
        if (!$(this).is(':checked')) {
          $(".calendarBody").addClass('hideFinished');
        }
        else {
          $(".calendarBody").removeClass('hideFinished');
        }
      }
    });
  }

  
  // add today class
  $(".calendarSection .row").each(function () {
    addClassToday($(this));
  });
  
  
  // add rows on click
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
  
  
      // add today class for added rows
      $(".calendarSection .row.added").each(function () {
        addClassToday($(this));
      });
  
  
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
  
  $(document).ready(function () {
    if ($(".calendarSection").length) {
      $(".hour").each(function () {
        $(this).find('.col').each(function () {
          var length = $(this).find('.box').length;
          //console.log(length);
  
          if (length >= 3) {
            var count = length - 2;
            $(this).addClass('overflow');
            $(this).append('<div class="box more">+ ' + count + ' další lekce</div>');
          }
        });
      });
    }
  });

  /* tippy hover */
  tippy('.calendarSection', {
    target: '.box',
    content: 'Loading...',
    flipOnUpdate: true,
    arrow: true,
    theme: 'white',
    animation: 'perspective',
    trigger: 'click',
    interactive: true,
    maxWidth: 515,
    lazy: true,
    onShow(instance) {
      let data = instance.reference.dataset;
          data.openSlots = (data.limit - data.clients <= 0) ? 0 : data.limit - data.clients;
      let name = $(instance.reference).find('.boxTitle').text(),
          loginAndReserveForm = `<input name="email" type='text' placeholder='E-mail' /><input name="password" type='password' placeholder='Heslo' /><button data-id='${data.id}' class='reserveLesson customBtn'><span>Přihlásit se a Rezervovat</span></button>`,
          reserveForm = `<button data-start='${data.start}' data-id='${data.id}' class='reserveLesson customBtn'><span>Rezervovat</span></button>`,
          refundForm = `<button data-start='${data.start}' data-id='${data.id}' class='cancelReservation customBtn'><span>Zrušit rezervaci</span></button>`,
          form = data.registered == 1 ? refundForm : data.openSlots > 0 ? `${user ? reserveForm : loginAndReserveForm}` : '</p>Tato lekce je již plně obsazená</p>',
          openSlots = data.openSlots == 1 ? '1 volné místo' : (data.openSlots > 1 && data.openSlots < 5) ? `${data.openSlots} volná místa` : `${data.openSlots} volných míst`;        

      let start = moment(data.start),
          now = moment(new Date()); //todays date
          duration = moment.duration(start.diff(now));
      if(duration.asMinutes() < 0) form = '</p>Rezervace pro tuto lekci již nejsou možné</p>';
      
      instance.setContent(`<div class='customContent'><div class='img' style='display:inline-block;'>${data.img}</div><div class='content'><h4>${name}</h4><p>Termín: ${moment(data.start).format('D.M.YYYY, H:mm')+' - '+moment(data.end).format('H:mm')}</p><p>Trenér: <a href='#'>${data.coaches}</a></p><div class='open'>${openSlots}</div><form><h5>Rezervace</h5>${form}</form></div></div>`);
    },
  });
  
  
  tippy('.defaultTooltip', {
    flipOnUpdate: true,
    arrow: true,
    animation: 'perspective',
    maxWidth: 400,
    lazy: true,
  });
  
  
  
  $(document).on("click", ".js-tabTitle", function () {
    var $this = $(this);
    var dataTab = $this.data("tab");
    $this.parent().find('.js-tabTitle').removeClass('active');
    $this.addClass('active');
    $(".js-tabContent").hide();
    $(".js-tabContent[data-tab=" + dataTab + "]").show();
  });
  
  /* tabs */
  function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
  }
  
  
  
  /* MAP */
  var locations = [
    ["Test , 150 00 Prague 5", 50.667836, 14.040217, 1]
  ];
  
  var snazzyMapStyle = [
    {
      "featureType": "administrative",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#fefefe"
        },
        {
          "lightness": "20"
        },
        {
          "gamma": "1.00"
        }
      ]
    },
    {
      "featureType": "administrative",
      "elementType": "geometry.stroke",
      "stylers": [
        {
          "color": "#fefefe"
        },
        {
          "lightness": "17"
        },
        {
          "gamma": "1"
        },
        {
          "weight": "1.2"
        }
      ]
    },
    {
      "featureType": "administrative",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "administrative",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "administrative.country",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "gamma": "1.74"
        }
      ]
    },
    {
      "featureType": "landscape",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#f5f5f5"
        },
        {
          "lightness": "20"
        },
        {
          "gamma": "1.00"
        }
      ]
    },
    {
      "featureType": "landscape",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#f5f5f5"
        },
        {
          "lightness": "21"
        },
        {
          "gamma": "1.00"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "poi.attraction",
      "elementType": "labels.text.stroke",
      "stylers": [
        {
          "hue": "#ff0000"
        }
      ]
    },
    {
      "featureType": "poi.business",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "lightness": "21"
        },
        {
          "gamma": "1.00"
        },
        {
          "hue": "#ff0000"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "poi.school",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "hue": "#ff0000"
        }
      ]
    },
    {
      "featureType": "poi.school",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#ffffff"
        },
        {
          "lightness": "29"
        },
        {
          "gamma": "1.00"
        },
        {
          "weight": "0.2"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "geometry.stroke",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "labels.text.stroke",
      "stylers": [
        {
          "hue": "#ff0000"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "road.arterial",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#ffffff"
        },
        {
          "lightness": "18"
        },
        {
          "gamma": "1.00"
        }
      ]
    },
    {
      "featureType": "road.arterial",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "road.local",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#ffffff"
        },
        {
          "lightness": "16"
        },
        {
          "gamma": "1.00"
        }
      ]
    },
    {
      "featureType": "road.local",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "road.local",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "transit",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#f2f2f2"
        }
      ]
    },
    {
      "featureType": "transit",
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "geometry.fill",
      "stylers": [
        {
          "color": "#e9e9e9"
        },
        {
          "lightness": "17"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "geometry.stroke",
      "stylers": [
        {
          "color": "#ff0000"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.stroke",
      "stylers": [
        {
          "color": "#b3a17b"
        }
      ]
    }
  ]
  
  var infowindow = new google.maps.InfoWindow();
  
  var marker, i;
  
  var svg = '<svg version="1.1" id="Vrstva_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 24.9" style="enable-background:new 0 0 16 24.9;" xml:space="preserve"><g><defs><rect id="SVGID_1_" y="0" width="16" height="24.9"/></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_"  style="overflow:visible;"/></clipPath><path style="clip-path:url(#SVGID_2_);fill:#604E2F;" d="M8,0C3.6,0,0,3.6,0,8c0,6.4,7.7,16.7,8,16.9c0,0,8-10.5,8-16.9C16,3.6,12.4,0,8,0"/></g><path style="fill:#FFFFFF;" d="M8.8,6C7.2,6,5.9,7,5.9,8.9c0,1.6,1,2,2,2c0.5,0,1-0.1,1.5-0.5l0.2-0.8H7.6L7.9,8H12l-0.5,3.2 C10.8,12.4,9.1,13,7.5,13c-2.5,0-4.3-1.6-4-4.4c0.3-3,2.6-4.7,5.7-4.7c1.3,0,2.3,0.3,3.1,1.3L12,7.3h-2l0.1-1C9.7,6,9.2,6,8.8,6z"/></svg>';
  
  
  if ($("#mainMap").length) {
    var map = new google.maps.Map(document.getElementById("mainMap"), {
      zoom: 15,
      center: new google.maps.LatLng(50.667836, 14.040217),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      styles: snazzyMapStyle,
      streetViewControl: false,
      mapTypeControl: false
    });
  
    for (i = 0; i < locations.length; i++) {
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        icon: {
          url: "data:image/svg+xml;charset=UTF-8;base64," + btoa(svg),
          scaledSize: new google.maps.Size(60, 60),
        },
        map: map,
      });
    }
  
  }

}
