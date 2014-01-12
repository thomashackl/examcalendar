/*
 * Persistent headers for any element (CSS + jQuery)
 * 
 * original code taken from http://css-tricks.com/persistent-headers/
 * modified by Alexander Findeis <findeis@fim.uni-passau.de>
 */

function UpdateTableHeaders() {
	$(".persist-area").each(function() {
	    var el             = $(this),
	        offset         = el.offset(),
	        scrollTop      = $(window).scrollTop(),
	        floatingHeader = $(".persist-header", this),
	        studipHeader   = $("#barBottomContainer")
	        
        scrollTop += studipHeader.height();

	    if ((scrollTop > offset.top) ){//}&& (scrollTop <= offset.top + el.height() - floatingHeader.height * 2)) {
	    	floatingHeader.css({
	    		"top": studipHeader.height(),
	            "position": "fixed"
	        });
//	    } else if ((scrollTop < offset.top + el.height() - floatingHeader.height * 2) && (scrollTop <= offset.top + el.height())) {
//	    	floatingHeader.css({
//	    		"top": offset.top + el.height() - floatingHeader.height - scrollTop,
//	            "position": "fixed"
//	        });
	    } else {
	        floatingHeader.css({
	            "position": "relative"
	        });      
	    };
    });
}

// DOM Ready      
$(function() {
    var clonedHeaderRow;

//    $(".persist-area").each(function() {
//        clonedHeaderRow = $(".persist-header", this);
//        clonedHeaderRow
//            .before(clonedHeaderRow.clone())
//            .css("width", clonedHeaderRow.width())
//            .addClass("floatingHeader");
//    });c

    $(window)
        .scroll(UpdateTableHeaders)
        .trigger("scroll");
});
