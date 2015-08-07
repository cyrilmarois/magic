$(document).ready(function() {
    function loadMore() {
        $.ajax({
            type: 'GET',
            url: fetchCardsUrl,
            data: { setId: $("#set-setid").val(), offset: $('img.card').length, displayMode: $("input[name='displayMode']").val() }
        }).success(function(data) {
            //alert($(data).length);
            if (data !== "") {
                $(data).insertBefore($('.container').children('.row').last());
                var offset = $('img.card').length;
                $('.row .col-md-12 a').data('offset', offset);
                $(window).bind('scroll', bindScroll);
            } else {
                //no more cards to fetch
                $('.row .col-md-12 a').hide();
            }
        });
    }

    function bindScroll() {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            $(window).unbind('scroll');
            loadMore();
        }
    }

    $(window).scroll(bindScroll);
});
