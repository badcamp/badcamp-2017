var swiper;
(function(Drupal, $) {

  Drupal.behaviors.featuredAttendees = {
    attach:function(context) {
      swiper = new Swiper('.swiper-container', {
        nextButton: '.swiper-fa-button-next',
        prevButton: '.swiper-fa-button-prev',
        slidesPerView: 'auto',
        paginationClickable: true,
        loop: true,
        freeMode: true,
        uniqueNavElements: false,
        centeredSlides: true,
        spaceBetween: 30,
        breakpoints: {
          0: {
            slidesPerView: 1
          }
        }
      });
    }
  }
}(Drupal, jQuery));