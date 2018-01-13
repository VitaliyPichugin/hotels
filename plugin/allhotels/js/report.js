jQuery( document ).ready(function($) {

    $("tr").each(function () {
        var self =  $(this);
        if ($(this).hasClass('row_hotel')) {
            $(self).css('background-color', '#98FB98');
        }
    });
    var old_price=[];
    var new_price=[];

    var i=0;
    $("tr>.hotel_price").each(function () {
        var price = $(this).text();
        var n_price;
            try{
                n_price = price.split(' ');

            }catch (ex){
                console.log(ex);
            }
        old_price[i] = parseInt(n_price[0]);
        i++;

    });
    $("tr>.hotel_update_price").each(function () {
        var price = $(this).text();
        var new_price;
        try{
            new_price = price.split(' ');

        }catch (ex){
            console.log(ex);
        }
        new_price[i] = parseInt(new_price[0]);
        i++;

    });

   // for(var k = 0; k<old_price.length; i++){
       // console.log(old_price);
   // }

    for(var i=0; i<new_price.length; i++){
        if(new_price[i] < old_price[i]){
            $(".row_hotel:eq('+i+')").parent().css('background-color', 'brown');
        }
/*        if(new_price[i] > old_price[i]){
            $(".row_hotel:eq('+i+')").parent().css('background-color', 'blue');
        }
        if(new_price[i] == old_price[i]){
            $(".row_hotel:eq('+i+')").parent().css('background-color', 'yellow');
        }*/
    }

    $("tr>.hotel_status").each(function () {
        var self =  $(this);
        if ($(this).text() == 'Отель не найден') {
            $(self ).parent().css('background-color', '#FF7F50');
            //   i++;
        }
    });

/*    for(var i=0; i<links.length; i++){hotel_update_price
        links.style('color', 'red');
    }*/

});