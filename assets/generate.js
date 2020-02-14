(function ($) {

    var $form = $( '#add-to-cal-form' );

    $form.on( 'submit', function (e) {
        e.preventDefault();

        generate();

        return false;
    } );

    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    /**
     * Generate the link and shortcode via ajax.
     */
    function generate() {

        var data = $form.serializeFormJSON();

        data.action = 'generate_add_to_calendar';

        var ajaxCall = $.ajax({
            type: "post",
            url: ajaxurl,
            dataType: 'json',
            data: data,
            success: function ( response ) {

                if ( typeof response.success == 'undefined' ){
                    return false;
                }

                $( '#rendered' ).show();

                $( '#shortcode' ).html( response.data.shortcode );
                $( '#link' ).html( response.data.link );
                $( '#html' ).html( response.data.html );

            }
        });

    }

})(jQuery);