$(document).ready(function () {
    var NavY = $('.nav').offset().top;
  
    var stickyNav = function () {
      var ScrollY = $(window).scrollTop();
  
      if ($(window).width() > 768) { 
        if (ScrollY > NavY) {
          $('.nav').addClass('sticky');
          $('body').css('margin-top', '59px');
        } else {
          $('.nav').removeClass('sticky');
          $('body').css('margin-top', '0');
        }
      } else {
        $('.nav').removeClass('sticky'); 
        $('body').css('margin-top', '0'); 
      }
    };
  
    stickyNav();
  
    $(window).on('scroll resize', function () { 
      stickyNav();
    });
  });
  
