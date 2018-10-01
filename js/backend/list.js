;(function($) {

    $('.show-more').on('click', togglePaiementInfos)

    function togglePaiementInfos() {
        var parent = $(this).parents('.sale')
        parent.toggleClass('show-paiement')
    }

})(jQuery);