/*
*
* Backpack Crud / Form
*
*/

jQuery(function($){

    'use strict';
    $('select[name="iata_airline"]').parent('div').hide();
    $('select[name="iata_airport"]').parent('div').hide();
    $('select[name="travel_by"]').change(function() {
        if ($('select[name="travel_by"]').val() == 'flight') {
            $('select[name="iata_airline"]').parent('div').show();
            $('select[name="iata_airport"]').parent('div').show();
        } else {
            $('select[name="iata_airline"]').parent('div').hide();
            $('select[name="iata_airport"]').parent('div').hide();
        }
    });
});
