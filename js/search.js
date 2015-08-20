function fetch(self) {
    var val = self.val();
    $.ajax({
        type: 'post',
        url: fetchCardUrl,
        data: {formData: val}
    })
    .success(function(data) {
        $('.dropdown-menu').children().remove();
        if (data.length == 0) {
            var str = '<li>No results</li>'
            $('.dropdown-menu').append(str);
        } else {
            var cards = []
            $.each(data, function (ind, element) {
                console.log(element.setIconUrl);
                var card = '<li>' +
                    '<a href="'+cardUrl+'&cardId=' + element.cardId + '">' + element.cardNameVO + '</a> --' +
                    '<img src="'+ element.setIconUrl + '" alt="" width="40" height="30" />' +
                    '</li>';
                cards.push(card);
            });
            $('.dropdown-menu').append(cards);
        }
        $('.dropdown-menu').show();
    });
}



$(document).ready(function() {
    $('#search').on('keyup', function() {
        fetch($(this));
    });

    $('body').on('click', function() {
        if ($('.dropdown-menu').children().length > 0) {
            $('.dropdown-menu').hide();
        }
    });
});
