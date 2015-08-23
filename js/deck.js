$(document).ready(function() {
    var defaultPicture = $('.col-md-4 img').attr('src');
    $('.deck').on('mouseover', 'table tbody tr', function() {
        $('.col-md-4 img').attr('src', $(this).find('td:last input[name="cardPictureVO"]').val());
    });
    $('.deck').on('mouseleave', 'table tbody tr', function() {
        $('.col-md-4 img').attr('src', defaultPicture);
    });

    $('select[name="filter"]').on('change', function() {
        filterVal =  $(this).val();
        if (filterVal === 'types') {
            url = filterByTypesUrl;
        } else if (filterVal === 'cost') {
            url = filterByCostUrl;
        } else if (filterVal === 'colors') {
            url = filterByColorsUrl;
        }
        $.ajax({
            type: 'GET',
            url: deckViewUrl,
            data: { deckId: $("#deck-deckid").val(), filter: filterVal }
        })
        .done(function(data) {
            $('.deck').children().remove();
            $(data).appendTo($('.deck'));
        });
    });
});