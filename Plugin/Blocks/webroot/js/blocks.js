/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
    $('#BlockCheckAllAuto').click(function() {
        $("INPUT[type='checkbox']").attr('checked', $('#BlockCheckAllAuto').is(':checked'));    
    });
});
