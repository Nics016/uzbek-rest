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
	// $("#halls .rightSide").attr("id", "halls-galleries-id");
	// $("#halls .textBox ul a").each(function(){
	// 	var attrHref = $(this).attr("href")
	// 		+ "halls-galleries-id";
	// 	$(this).attr("href", attrHref);
	// });
	
	// halls
	$("#halls .rightSide").attr("id", "halls-galleries-id");
	$("#halls .textBox ul a").each(function(){
		var attrAttitude = $(this).attr("attitude");
		$(this).bind("click", function(){
			switch(attrAttitude){
				case "gallery-1":
					$(".tabsBodyGallery .gallery .gImage #gallery-1").eq(0).click();
					break;
				case "gallery-2":
					$(".tabsBodyGallery .gallery .gImage #gallery-2").eq(0).click();
					break;
				case "gallery-3":
					$(".tabsBodyGallery .gallery .gImage #gallery-3").eq(0).click();
					break;
				case "gallery-4":
					$(".tabsBodyGallery .gallery .gImage #gallery-4").eq(0).click();
					break;
			}
		});
	});

	// kitchen and Bar
	if (window.location.hash === "#bar"){
		$(".cat-tabs .cat-tab").eq(1).click();
	} else if (window.location.hash === "#kitchen"){
		$(".cat-tabs .cat-tab").eq(0).click();
	}
});