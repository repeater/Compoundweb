jQuery(document).ready(function ($) {
	$( ".bw_video_widget" ).each(function( index ) {
		obj = $(this).find(" > div:first");
		if ( obj.hasClass( "bwv_grid_layout" )) {
			$(this).css("float","left");

		}
	});
});