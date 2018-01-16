
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

    function fill_row(selector, bg) {
        bg = bg || false;
        $(selector).each(function () {
            var new_price =  $(this ).find('.hotel_update_price').text();
            var old_price =  $(this ).find('.hotel_price').text();
            var status = $(this ).find('.link_hotel').text();
            try {
                new_price = new_price.split(' ');
                old_price = old_price.split(' ');

                var price_old = parseInt(old_price[0]);
                var price_new = parseInt(new_price[0]);

                if(price_old < price_new){
                    if(bg){
                        $(this).css('background-color', '#fad67a');
                    }
                    $(this).find('.hotel_status').text('').append('<img src="http://hotels.t.zp.ua/wp-content/uploads/2018/up.png" width="50px">');
                }
                if(price_old > price_new){
                    if(bg){
                        $(this).css('background-color', '#fad67a');
                    }
                    $(this).find('.hotel_status').text('').append('<img src="http://hotels.t.zp.ua/wp-content/uploads/2018/down.png" width="50px">');
                }
                if(price_old == price_new){
                    if(bg){
                        $(this).css('background-color', '#3CB371');
                    }
                    $(this).find('.hotel_status').text('').append('<img src="http://hotels.t.zp.ua/wp-content/uploads/2018/ok.png" width="50px">');
                }
                if(status == 'Link is not defined'){
                    if(bg){
                        $(this).css('background-color', '#CD5C5C');
                    }

                    $(this).find('.hotel_status').text('').append('<img src="http://hotels.t.zp.ua/wp-content/uploads/2018/alert-icon.png" width="50px">');
                }
            } catch (ex) {
                console.log(ex);
            }

        });

    }
    if ( $('table tr').hasClass("row_hotel") ) {
        fill_row('.row_hotel', true);
    }
    if($('table tr').hasClass("row_hotel_updated")){
        fill_row('.row_hotel_updated', false);
    }

});