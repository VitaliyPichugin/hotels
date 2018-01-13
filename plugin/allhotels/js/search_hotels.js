jQuery( document ).ready(function() {
    jQuery("#tags").autocomplete({
        minLength: 3,
        source:hotel_name.obj,
        select:function(event,ui){
            jQuery("#tags_id").val(ui.item.id);
            window.sessionStorage['loc'] = jQuery("#tags_id").val();
        }
    });

    alert('ok');
});




