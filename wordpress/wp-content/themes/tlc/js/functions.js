jQuery(function($) {
	$('.field, textarea').focus(function() {
        if(this.title==this.value) {
            this.value = '';
        }
    }).blur(function(){
        if(this.value=='') {
            this.value = this.title;
        }
    });

    $('.flexslider').flexslider({
        slideshowSpeed: 7000,     //Integer: Set the speed of the slideshow cycling, in milliseconds
        animation: "fade"         //String: Select your animation type, "fade" or "slide"
    });

    $('#navigation li:last').addClass('last');

    $('#footer .widget:nth-child(4n)').addClass('last');
});