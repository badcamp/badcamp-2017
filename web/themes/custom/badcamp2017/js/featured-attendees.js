var swiper;
var owl;
(function(Drupal, $) {

  Drupal.behaviors.featuredAttendees = {
    attach: function(context) {

      owl = $('.owl-carousel');
      owl.owlCarousel({
        loop: true,
        margin: 45,
        nav: false,
        navText : ["",""],
        stagePadding: 10,
        responsive:{
          0: {
            items: 1
          },
          450:{
            items:2
          },
          640: {
            items: 1
          },
          1150:{
            items: 2
          },
          1500:{
            items: 3
          }
        },
        onInitialized: function (event) {
          setTimeout(function(){
            $(window).resize();
          }, 500);
        }
      });

      $('#feature-attendees-prev').click(function() {
        owl.trigger('prev.owl.carousel');
      });

      $('#feature-attendees-next').click(function() {
        owl.trigger('next.owl.carousel');
      });
    }
  };
}(Drupal, jQuery));