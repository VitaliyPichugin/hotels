window.addEventListener('load', function() {
    var input = document.getElementsByClassName('hotel_search')[0];

    var rows = $('.search_visible');

    input.addEventListener('input', function(e) {
        var value = e.target.value.toLowerCase();
        var pattern = new RegExp(value, 'g');
        if (value.trimLeft() != '') {
            // for (var el of rows) {
            //     el.style.display = 'table-row';
            // }
            rows.each(function(i, el) {
                el.style.display = 'table-row';
            });
            // for (var el of rows) {
            //     var td = el.getElementsByClassName('search_visible_data')[0];
            //     var data = td.innerHTML.toLowerCase();
            //     if (!data.match(pattern)) {
            //         el.style.display = 'none';
            //     }
            //}
            rows.each(function(i, el) {
                var td = el.getElementsByClassName('search_visible_data')[0];
                var data = td.innerHTML.toLowerCase();
                if (!data.match(pattern)) {
                    el.style.display = 'none';
                }
            });
        } else {
            // for (var el of rows) {
            //     el.style.display = 'table-row';
            // }
            rows.each(function(i, el) {
                el.style.display = 'table-row';
            });
        }
    });
});