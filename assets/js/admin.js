(function ($) {
    $(function () {

        // Check to make sure the input box exists
        if (0 < $('#wpse_date').length) {
            $('#wpse_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0
            });
        } // end if

    });
}(jQuery));
