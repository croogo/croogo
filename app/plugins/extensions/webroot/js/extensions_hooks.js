/**
 * ExtensionsHooks
 *
 * for ExtensionsHooksController (extensions plugin)
 */
var ExtensionsHooks = {};

/**
 * functions to execute when document is ready
 *
 * @return void
 */
ExtensionsHooks.documentReady = function() {
    ExtensionsHooks.hookToggle();
}

/**
 * Toggle hooks (enable/disable)
 *
 * @return void
 */
ExtensionsHooks.hookToggle = function() {
    $('img.hook-toggle').unbind();
    $('img.hook-toggle').click(function() {
        var rel = $(this).attr('rel');

        // show loader
        $(this).attr('src', Croogo.basePath+'img/ajax/circle_ball.gif');

        // prepare loadUrl
        var loadUrl = Croogo.basePath+'admin/extensions/extensions_hooks/toggle/';
        loadUrl    += rel+'/';

        // now load it
        $(this).parent().load(loadUrl, function() {
            ExtensionsHooks.hookToggle();
        });

        return false;
    });
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
    if (Croogo.params.controller == 'extensions_hooks') {
        ExtensionsHooks.documentReady();
    }
});