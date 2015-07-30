function bw_slideToggle(id){
	obj = jQuery("#prev_"+id);
	if (obj.length==1){
		if (obj.is(':visible')){
			obj.hide("slow");
		}else{
			obj.show("slow");
			if (jQuery("#prev_window_"+id).length==1){
			}else{
				jQuery.ajax({
					type: "POST",
					url: bwAjax.ajaxurl,
					data: {vid:id, action: "fetch_video_embedd_html"},
					cache: false,
					success: function(data){
						obj.html(data);
					}
				});  
			}
		}
	}
}
function bw_infobarToggle(){
	obj = jQuery("#bw_infobar");
	if (obj.length==1){
		if (obj.is(':visible')){
			obj.hide("slow");
		}else{
			obj.show("slow");
		}
	}
}
