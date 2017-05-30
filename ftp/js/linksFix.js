$(document).ready(function(){
	// main menu
	$("enter.visible-sm a").each(function(){
		var attrJump = $(this).attr("jump");
		var attrHref = $(this).attr("href");
		if (attrJump != ""){
			attrHref += "#" + attrJump;
			$(this).attr("href", attrHref);
			$(this).addClass("scroll");
			$(this).bind("click", function(){
				var hash = window.location.hash;
				if (hash === "#bar"){
					$(".cat-tabs .cat-tab").eq(0).click();
				} else if (hash === "#kitchen"){
					$(".cat-tabs .cat-tab").eq(1).click();
				}
			});
		}		
	});

	// circle
	$(".circleBox #circle a").each(function(){
		var attrJump = $(this).attr("jump");
		var attrHref = $(this).attr("href");
		if (attrJump != ""){
			attrHref += "#" + attrJump;
			$(this).attr("href", attrHref);
			$(this).addClass("scroll");
		}		
	});

	// halls
	$("#halls .rightSide").attr("id", "halls-galleries-id");
	$("#halls .textBox ul a").each(function(){
		var attrHref = $(this).attr("href")
			+ "halls-galleries-id";
		$(this).attr("href", attrHref);
	});

	// kitchen and Bar
	if (window.location.hash === "#bar"){
		$(".cat-tabs .cat-tab").eq(1).click();
	} else if (window.location.hash === "#kitchen"){
		$(".cat-tabs .cat-tab").eq(0).click();
	}
});