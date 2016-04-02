/**
 * Vocabularies
 *
 * for VocabulariesController
 */
var Vocabularies = {};

/**
 * functions to execute when document is ready
 *
 * only for VocabulariesController
 *
 * @return void
 */
Vocabularies.documentReady = function () {

}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function () {
    if (Croogo.params.controller == 'Vocabularies') {
        Vocabularies.documentReady();
    }
});
