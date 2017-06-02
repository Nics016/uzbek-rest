$(document).ready(function(){
	// main menu
	$("enter.visible-sm .menu a").each(function(){
		var attrJump = $(this).attr("jump");
		var attrHref = $(this).attr("href");
		if (attrJump != ""){
			attrHref += "#" + attrJump;
			$(this).attr("href", attrHref);
			// $(this).addClass("scroll");
			$(this).bind("click", function(){
				var hash = window.location.hash;
				if (hash === "#bar"){
					$(".cat-tabs .cat-tab").eq(0).click();
				} else if (hash === "#kitchen"){
					$(".cat-tabs .cat-tab").eq(1).click();
				}
				window.location = attrHref;
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
			$(this).bind("click", function(){
				window.location = attrHref;
			});
		}		
	});

	// gotop-streaks
	$(".gotop-streaks a").each(function(){
		var attrHref = $(this).attr("href");
		$(this).bind("click", function(){
			$(".gotop-streaks").slideUp();
			window.location = attrHref;
		});		
	});

	// language
	$("enter.visible-sm .language a").each(function(){
		var attrHref = $(this).attr("href");
		$(this).bind("click", function(){
			$(".gotop-streaks").slideUp();
			window.location = attrHref;
		});		
	});
	// coolinary menu
    $("#coolinary a").each(function(){
    	var attrHref = $(this).attr("href");
		$(this).bind("click", function(){
			window.location = attrHref;
		});	
    });
    $(".download a").each(function(){
    	var attrHref = $(this).attr("href");
		$(this).bind("click", function(){
			window.location = attrHref;
		});	
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

	// show bottom streaks
    $("#content div").on("touchmove", function(){
		$(".gotop-streaks").slideDown();
    });
    $("enter").on("touchmove", function(){
		$(".gotop-streaks").slideUp();
    });

    function elementInViewport(elJQ) {
      var el = $(elJQ)[0];
	  var top = el.offsetTop;
	  var left = el.offsetLeft;
	  var width = el.offsetWidth;
	  var height = el.offsetHeight;

	  while(el.offsetParent) {
	    el = el.offsetParent;
	    top += el.offsetTop;
	    left += el.offsetLeft;
	  }

	  return (
	    top >= window.pageYOffset &&
	    left >= window.pageXOffset &&
	    (top + height) <= (window.pageYOffset + window.innerHeight) &&
	    (left + width) <= (window.pageXOffset + window.innerWidth)
	  );
	}
});