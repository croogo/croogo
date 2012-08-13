/*
** Funções comuns a todos os arquivos do site
*/
/*(function($){
	$('.dropdown-toggle').dropdown();
})(jQuery);*/

!function ($) {

  $(function(){

  	// $('.dropdown-toggle').dropdown();

    // Disable certain links in docs
    $('a [href^=#]').click(function (e) {
      e.preventDefault()
    })
  })

}(window.jQuery)