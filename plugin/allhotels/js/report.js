
jQuery( document ).ready(function($) {

    function get_price(selector) {
        var i = 0;
        var get_price = [];
        $(selector).each(function () {

            var price = $(this).text();
            var n_price;
            try {
                n_price = price.split(' ');

            } catch (ex) {
                console.log(ex);
            }
            get_price[i] = (Number)(n_price[0]);
            i++;
        });
        return get_price;
    }

    $('.row_hotel').each(function () {
        var new_price =  $(this ).find('.hotel_update_price').text();
        var old_price =  $(this ).find('.hotel_price').text();

        try {
            new_price = new_price.split(' ');
            old_price = old_price.split(' ');

            var price_old = parseInt(old_price[0]);
            var price_new = parseInt(new_price[0]);

            if(price_old < price_new){
                $(this).css('background-color', '#D2691E');
            }
            if(price_old == price_new){
                $(this).css('background-color', '#3CB371');
            }
            if(price_old > price_new){
                $(this).css('background-color', '#D2691E');
            }
        } catch (ex) {
            console.log(ex);
        }

    });


    $("tr>.hotel_status").each(function () {
        var self =  $(this);
        if ($(this).text() == 'Отель не найден') {
            $(self ).parent().css('background-color', '#FF7F50');
        }
    });

});