$(document).ready(function(){
	$("a").each(function(){
		var attrJump = $(this).attr("jump");
		var attrHref = $(this).attr("href");
		if (attrJump != ""){
			attrHref += "#" + attrJump;
			$(this).attr("href", attrHref);
			$(this).addClass("scroll");
		}
		
	});
});