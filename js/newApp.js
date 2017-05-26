
$(function(){

	$('[type="circleMenu"]').on('click', function(){
		if(!$(this).hasClass('active')) {
			circle[$(this).attr('pos')]($(this).attr('id'));
		}
	});

	$('.download').on('click', function(){
		$(this).addClass('active');
		var elem = $(this);
		setTimeout(function(){
			elem.removeClass('active');
		}, 500);
	});
	
	var circle = {
		left: function(obj){
			$('.mainContent').css({opacity: 0});
			$('[type="circleMenu"]').removeClass('active');
			$(`#${obj}`).attr('pos', 'center').addClass('active');
			$('[pos="right"]').attr('hide', '');
			$('[pos="center"]').attr('hide', '');
			setTimeout(function(){
				$('.mainContent.active').removeClass('active');
				$(`.mainContent.${obj}`).addClass('active');
				$('[pos="center"]').attr('animoff', '').attr('pos', 'left');
				setTimeout(function(){
					$('[pos="left"]').removeAttr('animoff').removeAttr('hide');
					$('[pos="right"]').removeAttr('animoff').removeAttr('hide');
					$(`.mainContent.${obj}`).css({opacity: 1});
				}, 100);
			}, 600);
		},
		right: function(obj){
			$('.mainContent').css({opacity: 0});
			$('[type="circleMenu"]').removeClass('active');
			$(`#${obj}`).attr('pos', 'center').addClass('active');
			$('[pos="left"]').attr('hide', '');
			$('[pos="center"]').attr('hide', '');
			setTimeout(function(){
				$('.mainContent.active').removeClass('active');
				$(`.mainContent.${obj}`).addClass('active');
				$('[pos="center"]').attr('animoff', '').attr('pos', 'right');
				setTimeout(function(){
					$('[pos="left"]').removeAttr('animoff').removeAttr('hide');
					$('[pos="right"]').removeAttr('animoff').removeAttr('hide');
					$(`.mainContent.${obj}`).css({opacity: 1});
				}, 100);
			}, 600);
		},
	}
	
	$('#changeScene .background').css( { background: `url( ${ $('#change a:first').attr('href') } )` } );
	$('#changeScene img').attr( 'src', $('#change a:first').attr('href') );

	$('#change a').each(function(){
		var src = $(this).attr('href');
		var pic = new Image();
		pic.src = $(this).attr(src);
	});
	
	$('#change').on('click', function(e){
		$('#change').find('.active').removeClass('active');
		$(e.target).parent().addClass('active');
		
		
		var src = $(e.target).parent().attr('href');
		$('#changeScene .background').css( { background: `url( ${src} )` } );
		$('#changeScene img').attr( 'src', src );
		return false;
	});
	
	$('[type="tabsPanel"]').on('click', function(e){
		$(this).find('.active').removeClass('active');
		$(e.target).addClass('active');
		let tabsbody = $('[tabsbody="' + $(this).attr('id') + '"]');
		tabsbody.find(`> .tab.active`).removeClass('active');
		tabsbody.find(`> .tab`).eq( $(e.target).index() ).addClass('active');
	});

});
































