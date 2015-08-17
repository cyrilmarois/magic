$(document).ready(function() {
    /** fetch more cards **/
    function loadMore() {
        $.ajax({
            type: 'GET',
            url: fetchCardsUrl,
            data: { setId: $("#set-setid").val(), offset: $('img.card').length, displayMode: $("input[name='displayMode']").val() }
        }).success(function(data) {
            //alert($(data).length);
            if (data !== "") {
                $(data).insertAfter($('.main').children('.row').last());
                var offset = $('img.card').length;
                $(window).bind('scroll', bindScroll);
            } else {
                //no more cards to fetch
                $('.row .col-md-12 a').hide();
            }
        });
    }

    /** bind/unbind scroll **/
    function bindScroll() {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            $(window).unbind('scroll');
            loadMore();
        }
    }

    $(window).scroll(bindScroll);

    /*
    $('body').on('mouseenter', '.col-md-2.cards', function() {
        console.log('enter');
        if ($('.cardForm').length > 0) {
            $('.cardForm').remove();
        }
        cardImgUrl = $(this).children('img').attr('src');
        self = $(this);
        $.ajax({
            type: 'GET',
            url: addCardFormUrl
        }).done(function(data) {
            self.children('img').attr('src', defaultCardUrl);
            self.append(data);
        });
    });

    $('body').on('mouseleave', '.col-md-2', function() {
        console.log('leave');
        $(this).children('.cardForm').remove();
        $(this).children('img').attr('src', cardImgUrl);
    });
    */

    /** add card into a deck **/
    $('body').on('click', '.addCard', function(evt) {
        parent = $(this).parent();
        var deckId = parent.find('select[name="deckName"]').val();
        var cardId = parent.find('input[name="Card[cardId]"]').val();
        var cardNumber = parent.find('input[name="cardNumber"]').val();
        $.ajax({
            type: 'POST',
            url: addCardUrl,
            data: { deckId: deckId, cardId: cardId, cardNumber: cardNumber }
        })
        .done(function(data) {
            message = $(data);
            message.fadeIn("normal", function() {
                message.insertAfter(parent);
                parent.hide();
            });

            message.delay(1000).fadeOut("normal", function() {
                message.remove();
                parent.show();
            });
        });
    });
});
