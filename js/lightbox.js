jQuery(document).ready(function($) {
	// Fill and show the box when a picture is clicked
	$("a[href$='.jpg'], a[href$='.png'], a[href$='.gif']").click(function(){
		$("#ara-lightbox-img").attr("src", $(this).attr("href"));
		
		if ($(this).children("img:first").attr('alt') != undefined) {
			$("#ara-lightbox-img").attr("alt", $(this).children("img:first").attr('alt'));
			$("#ara-lightbox-caption").html($(this).children("img:first").attr('alt'));
			$("#ara-lightbox-caption").css("display", "block");
		}
		
		$("#ara-lightbox-box").show(700);
		return false;
	});
	
	// Hide and reset the box when clicked again
	$("#ara-lightbox-box").click(function(){
		$("#ara-lightbox-box").hide(700);
		$("#ara-lightbox-img").attr("src", "#");
		$("#ara-lightbox-img").attr("alt", "#");
		$("#ara-lightbox-caption").html("");
		$("#ara-lightbox-caption").css("display", "none");
		return false;
	});
});