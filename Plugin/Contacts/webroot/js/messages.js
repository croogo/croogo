/**
 * document ready
 *
 * @return void
 */
$(function() { 
    $('#MessageCheckAllAuto').click(function() {
        $("INPUT[type='checkbox']").attr('checked', $('#MessageCheckAllAuto').is(':checked'));    
    });
});
