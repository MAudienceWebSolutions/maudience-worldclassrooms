;(function($, window, document, undefined) {
	var $win = $(window);
	var $doc = $(document);

	$doc.ready(function() {
		$('.accordion-section:first-child').toggleClass('accordion-expanded');
		(function(){
		    // This class will be added to the expanded item
		    var activeItemClass = 'accordion-expanded';
		    var accordionItemSelector = '.accordion-section';
		    var toggleSelector = '.accordion-head';
		 
		    $(toggleSelector).on('click', function() {
		 
		        $(this)
		            .closest(accordionItemSelector) // go up to the accordion item element
		            .toggleClass(activeItemClass)
		                .siblings()
		                .removeClass(activeItemClass);
		    });
		 
		})();

		$('.intro').each(function() {
		   var bg = $('img', this).attr('src')

		   $(this).css({
		     'background': 'url(' + bg + ') no-repeat center center',
		     'backgroundSize': 'cover',
		   })

		 });

		$('.btn-menu').on('click', function (event) {
		    $(this).toggleClass('active');  
		    
		    //Show/hide your navigation here
		    
		    event.preventDefault();
		});
		
		$.fn.lastWord = function() {
		  var text = this.text().trim().split(" ");
		  var last = text.pop();
		  this.html(text.join(" ") + (text.length > 0 ? " <span>" + last + "</span>" : last));
		};

		$(".intro-secondary .intro-title").lastWord();
		$(".intro-handle .intro-title").lastWord();

		$('.step a').click(function() {
	   	     var $this = $(this),
	   	       _href = $this.attr('href'),
			    	  dest  = $(_href).offset().top;
	   	         $("html:not(:animated),body:not(:animated)").animate({  scrollTop: dest}, 600 );
	  		    return false;
	   	 });
	});
})(jQuery, window, document);
