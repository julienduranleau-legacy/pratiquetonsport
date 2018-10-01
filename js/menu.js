(function($) {

    $(domReady)

    function domReady() {
        $('.menu .item').on('click', menuBtClickHandler)
    }

    function menuBtClickHandler(e) {
        e.preventDefault();

        var topOffset = $($(this).attr('href')).offset().top - 100

        $("html, body").animate({ scrollTop: topOffset + 'px' });
    }

})(jQuery)