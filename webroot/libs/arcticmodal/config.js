(function($) {
    $(function() {
      if (!$.cookie('smartCookies')) {

        function getWindow(){
          $('.offer').arcticmodal({
            closeOnOverlayClick: true,
            closeOnEsc: true
          });
        };

        setTimeout (getWindow, 3000); //opens after 3 seconds
        // setTimeout (getWindow, 15000); //opens after 15 seconds
      }

       $.cookie('smartCookies', true, {
         expires: 1,
         path: '/'
       });

    })
  })(jQuery)

