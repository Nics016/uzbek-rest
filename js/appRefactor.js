
$(function(){
  document.createElement('enter');
  document.createElement('paralax');

  var o = {
    content: $('#content'),
    enter: $('enter'),
    window: $(window),
    circle: $('#circle'),
    paralax: $('paralax'),
    paralaxObj: {},
    twinkObj: {},
  }

  var lockEnter = false;
  var scroll = 0;
  var timer;
  var contentOffset = o.content.position().top;
  var contentHeight = o.content.height();
  var windowHeight = o.window.height();

  var onScroll = function(){};

  var paralaxSetting = {
    multiplier: 1.5, // 1.5 for parallax efffect
  }

  if( sessionStorage.getItem('lastload') == null ) {
    $('.bg-round').remove();
      $('#path3821').css({opacity: '1'}); // убираем обводку с вензелями
      $('#g3918').css({opacity: '1'}); // убираем овал
      $('#g3925').css({opacity: '1'}); // убираем "ресторан"
      $('.logo').css({opacity: '1'});
      $("#path3825").css({transform: 'translateY(22px)'});
      $('#g3847').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3877').css({opacity: '0'});
      $('#g3897').css({opacity: '0'});
      $('#path3823').css({opacity: '0'}); //Sun
      $('.rm-hide').css({opacity: '0'});
      $('#logo-name-old').css({opacity: '0'}); 
      $('#logo-restoran-old').css({opacity: '0'});
      $('#path3825').css({opacity: '1'});
      $('#res01').css({opacity: '1'});
      $('.st5').css({opacity: '0'});
      $('#circle3915').css({opacity: '.85'});
    //$('enter').css({position: 'relative'});
    $('enter .menuBox').css({
          backgroundColor: 'transparent'
        });
    if(document.getElementById('bgvid')){
      $('.enter .menuBox').css({
          backgroundColor: 'transparent'
        });
        var bgVideo = document.getElementById('bgvid');
        bgVideo.onloadeddata = function() {
            bgVideo.play();
            $(bgVideo).fadeIn(1500);
        };  
    }
    $('.bg-round').show(0);
    o.enter.on('mousewheel', function(event) {
      o.enter.attr('fix', '');
      scroll++;
      o.enter.css({transform: 'translateY(' + (scroll * -2) + 'vh) translateZ(0)'});
      if(scroll >= 3){
        o.enter.unbind();
        hideEnter();
      } else {
        timerRun();
      }
    });
		
		o.enter.swipe({
			swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
				o.enter.attr('fix', '');
				scroll++;
				o.enter.css({transform: 'translateY('+ (scroll * -2) + 'vh) translateZ(0)'});
				o.enter.unbind();
				hideEnter();
			},
		});
		
    $('#run').on('click', function(){
      hideEnter();
      o.enter.unbind();
    });
    o.enter.attr('show', '');
    o.content.attr('sync', '');   
  }else{
    $('.bg-round').remove();
      $('#path3821').css({opacity: '1'}); // убираем обводку с вензелями
      $('#g3918').css({opacity: '1 !important'}); // убираем овал
      $('#g3925').css({opacity: '1'}); // убираем "ресторан"
      $('.logo').css({opacity: '1'});
      $("#path3825").css({transform: 'translateY(22px)'});
      $('#g3847').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3877').css({opacity: '0'});
      $('#g3897').css({opacity: '0'});
      $('#path3823').css({opacity: '0'}); //Sun
      $('.rm-hide').css({opacity: '0'});
      $('#logo-name-old').css({opacity: '0'}); 
      $('#logo-restoran-old').css({opacity: '0'});
      $('#path3825').css({opacity: '1'});
      $('#circle3915').css({opacity: '.85'});
      $('#res01').css({opacity: '1'});
      $('.st5').css({opacity: '0'});
  }

  var hideEnter = function(){
    
    o.enter.removeAttr('style');
    o.content.removeAttr('sync');
    o.enter.attr('animate', '');
    if(document.getElementById('bgvid')){
      setTimeout(function(){
        var bgVideo = document.getElementById('bgvid');
        bgVideo.pause();
        bgVideo.style.display = 'none';
        $('enter').css({position: 'fixed'});
      }, 1500);     
    }

    $('.bg-round').remove();
      $('#path3821').css({opacity: '1'}); // убираем обводку с вензелями
      $('#g3918').css({opacity: '1 !important'}); // убираем овал
      $('#g3925').css({opacity: '1'}); // убираем "ресторан"
      $('.logo').css({opacity: '1'});
      $("#path3825").css({transform: 'translateY(22px)'});
      $('#g3847').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3877').css({opacity: '0'});
      $('#g3897').css({opacity: '0'});
      $('#path3823').css({opacity: '0'}); //Sun
      $('.rm-hide').css({opacity: '0'});
      $('#logo-name-old').css({opacity: '0'}); 
      $('#logo-restoran-old').css({opacity: '0'});
      $('#path3825').css({opacity: '1'});
      $('#res01').css({opacity: '1'});
      $('.st5').css({opacity: '0'});
      $('#circle3915').css({opacity: '.85'});
    
    $('enter .menuBox').css({
          backgroundColor: 'rgba(6, 25, 41, 0.8)'
        });
    sessionStorage.setItem("lastload", true);
    $('.bg-round').fadeOut(400);
    setTimeout(function(){
      o.enter.removeAttr('animate');
      o.enter.removeAttr('show');
    }, 2000);
  }

  var timerRun = function(){
    clearTimeout(timer);
    timer = setTimeout(function(){
      o.enter.removeAttr('style');
      scroll = 0;
    }, 300);
  }

  $('[type="circleMenu"]').on('click', function(){
    let parent = $('#circleContent').parent().attr('id')
    o.content.mCustomScrollbar( "scrollTo",  $('#'+ parent).position().top);
  });
	
	$('[type="tabsPanel"]').on('click', function(){
		o.content.mCustomScrollbar( "scrollTo",  0);
	});
	
	$('#totalGallery a').on('click', function(){
		$('#' + $(this).attr('attitude')).click();
	});
	
	$('#return').on('click', function(){
		sessionStorage.setItem('jump', null);
	});

  var jump = function(selector){
    if(selector != null) {
			if($(selector).attr('type') == 'circleMenu'){
				$('.circleBox').addClass('hide');
				let parent = $('#circleContent').parent().attr('id')
				//$(selector).click();
			} else {
				if($(selector).length){
          console.log(sessionStorage.getItem('_kitchen'))
          if(sessionStorage.getItem('_kitchen') == 'bar'){
            //alert('BAR in session');
            $('#bar').trigger('click');
          }
          setTimeout(function(){
            o.content.mCustomScrollbar( "scrollTo", - $(selector).position().top);
          }, 20);
				}
			}
    }
  }
  if(window.location.pathname.indexOf('kuhnya_bar') && sessionStorage.getItem('_kitchen') == 'bar'){
    setTimeout(function(){
      $('#bar').trigger('click');
    }, 100);
    console.info(window.location.pathname, sessionStorage.getItem('_kitchen'));
  }
	
	$('#goTop').on('click', function(){
		o.content.mCustomScrollbar( "scrollTo", 0);
	});

  $('.jump a').on('click', function(){
    //let selector = `#${$(this).attr('jump')}`;  
    console.info(sessionStorage.getItem('_kitchen'));
    let selector = '#' + $(this).attr('jump');	
    if(window.location.pathname == $(this).attr('href') || $(this).attr('href') == '#') {
      jump(selector);
      if(window.location.pathname.indexOf('kuhnya_bar')){
        if($(this).attr('jump') === 'bar'){
          sessionStorage.setItem('_kitchen', 'bar');
          $('#bar').trigger('click');
        }else{
          $('#kitchen').trigger('click');
          sessionStorage.setItem('_kitchen', 'kitchen');
        }
      }else{
        sessionStorage.setItem('_kitchen', $(this).attr('jump'));
      }
      return false;
    } else {
      if($(this).attr('jump') == 'bar' || $(this).attr('jump') == 'kitchen'){
        sessionStorage.setItem('_kitchen', $(this).attr('jump'));
        //alert('WRITE BAR: ' + $(this).attr('jump'));
      }
      sessionStorage.setItem('jump', selector);
    }
  });
	
	$('.gImage img').each(function(){
		var orig = this;
		var pic = new Image();
		pic.src = $(this).attr('src');
		$(pic).load(function(){
			if($(this).width() < $(this).height()){
				$(orig).addClass('portrait');
			}
		});
	});
	
	if( $('#menu').length ) {
		$('.mainContent').each(function(){
			$(this).find('.content').css({ paddingTop: $(this).find('.tabsHead').height() });
			$(this).find('.leftBox').css({ top: $(this).find('.tabsHead').height() + 70 })
			$(this).addClass('hide');
		});
		$('.circleBox').on('mousewheel', function(event) {
			$(this).addClass('hide');
		});
		
		$('.circleBox').swipe({
			swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
				$(this).addClass('hide');
			},
		});


//Swipe prevent for iPad;
/*$(function() {
  $(document.body).swipe( {
    //Generic swipe handler for all directions
    swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
      $(this).html("You swiped " + direction );  
    }
  });
});*/


		
		o.content.mCustomScrollbar({
			scrollButtons:{enable: false},
			theme: "dark-thin",
			scrollbarPosition: "outside",
			mouseWheel:{ 
				normalizeDelta: true,
				deltaFactor: 100,
				scrollAmount: 300
			},
			callbacks:{
				onScroll:function(){
					unlock = true;
					onScroll();
					onScroll = function(){};
				},
				whileScrolling:function(){
					app.paralaxRender(this.mcs.top);
				}
			}
		});
	} else {
		o.content.mCustomScrollbar({
			scrollButtons:{enable: false},
			theme: "dark-thin",
			scrollbarPosition: "outside",
			scrollInertia: 1500,
			documentTouchScroll: true,
			snapAmount: o.content.height() + 450,
			snapOffset: 450,
			mouseWheel:{ 
				normalizeDelta: true,
				scrollAmount: o.content.height() + 450,
			},
			callbacks:{
				onScroll:function(){
					unlock = true;
					onScroll();
					onScroll = function(){};
				},
				whileScrolling:function(){
					app.paralaxRender(this.mcs.top);
				}
			}
		});
	}


  var app = {
    init: function(){
      let numItems = o.paralax.find('div').length;
			
      o.content.find('.circleBox').height(contentHeight);
      o.content.find('[type="paralax"]').each(function(){
        let item = $(this);
        o.paralaxObj[item.attr('id')] = item;
      });
      o.paralax.find('div').each(function(){
        let item = $(this);
				if(item.attr('apply') == 'none'){
					item.css({ background: 'url('+item.attr('src')+')' });
				} else {
					let pos = o.paralaxObj[item.attr('apply')].position().top - item.height();
          



          //IE SYNTAX!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!




					item.css({ background: 'url(' + item.attr('src') +')' });
					item.css({ transform: 'translateY(' + (pos + contentOffset) + 'px) translateZ(0)' });
					item.css({ zIndex: numItems-- });
				}
      });
      o.content.find('[type="twink"]').each(function(){
        let item = $(this);
        o.twinkObj[item.index('[type="twink"]')] = item;
      });
			setTimeout(function(){
      	jump(sessionStorage.getItem('jump'));
			}, 700);
    },
    paralaxEngine: function(obj, cell){
      let from = obj.position().top - windowHeight + 200;
      let to = obj.position().top + windowHeight * 2;
      if( to >= cell && from <= windowHeight + cell ){
        let fix = cell - from;
        let offset = fix / paralaxSetting.multiplier - fix;
        let img = $('paralax').find('[apply=' + obj.attr('id') + ']');
        //let img = $('paralax').find(`[apply="${ obj.attr('id') }"]`);
        img.css({transform: 'translateY(' + (obj.offset().top - img.height() - offset) + 'px)  translateZ(0)'});
        //img.css({transform: `translateY(${ obj.offset().top - img.height() - offset}px)  translateZ(0)`});
      }
    },
    jump: function(obj, cell){
      let from = obj.offset().top + 200;
      let item = obj.attr('id');
      if(obj.offset().top < contentHeight / 2){
        $('.jump .active').removeClass('active');
        $('.jump').find('[jump=' + item + ']').parent().addClass('active');
      }
    },
    twinkEngine: function(obj, cell){
      
      let from = obj.position().top + 200;
      let to = obj.position().top + obj.height();
      if(from <= windowHeight + cell && to >= cell && obj.offset().top < contentHeight/1.7){
        if( obj.height() - obj.offset().top < windowHeight / 2){
          let result = contentHeight / 1.7 / obj.offset().top;
          obj.css({opacity: Math.abs(result) - 1});
        }
      }
    },
    circle: function(cell){
      o.circle.css({opacity: 1 - cell / (contentHeight / 2)});
    },
    paralaxRender: function(cell) {
      cell = Math.abs(cell);
      for(key in o.paralaxObj) {
        this.paralaxEngine(o.paralaxObj[key], cell);
        this.jump(o.paralaxObj[key], cell);
      }
      for(key in o.twinkObj){
        this.twinkEngine(o.twinkObj[key], cell);
      }
    },
  }
	
  app.init();

// Временный код

  document.onkeypress = function(e){
    if(e.keyCode == 32){
//      o.content.mCustomScrollbar( "scrollTo",  $('#menu').position().top);
//      alert( $('#menu').position().top );
      sessionStorage.clear();
      alert('succes')
      return false;
    }

    if(e.keyCode == 49){
      // Old logo
      $('.bg-round').remove();
      $('#path3821').css({opacity: '1'}); // убираем обводку с вензелями
      $('#g3918').css({opacity: '1'}); // убираем овал
      $('#g3925').css({opacity: '1'}); // убираем "ресторан"
      $('.logo').css({opacity: '1'});
      $("#path3825").css({transform: 'translateY(22px)'});
      $('#g3847').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3877').css({opacity: '0'});
      $('#g3897').css({opacity: '0'});
      $('#path3823').css({opacity: '0'}); //Sun
      $('.rm-hide').css({opacity: '0'});
      $('#logo-name-old').css({opacity: '0'}); 
      $('#logo-restoran-old').css({opacity: '0'});
      $('#path3825').css({opacity: '1'});
      $('#res01').css({opacity: '1'});
      $('.st5').css({opacity: '0'});
      $('#circle3915').css({opacity: '.85'});
      return false;
    }
    if(e.keyCode == 50){
      // New logo
      $('.bg-round').remove();

      $('#path3821').css({opacity: '1'}); // убираем обводку с вензелями
      $('#g3918').css({opacity: '1'}); // убираем овал
      $("#path3825").css({transform: 'translateY(0px)'});
      $('#g3925').css({opacity: '0'}); // убираем "ресторан"
      $('.logo').css({opacity: '1'});
      $('#g3847').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3865').css({opacity: '0'});
      $('#g3877').css({opacity: '0'});
      $('#g3897').css({opacity: '0'});
      $('#path3823').css({opacity: '0'}); //Sun
      $('.rm-hide').css({opacity: '0'});
      $('#logo-name-old').css({opacity: '1'}); 
      $('#logo-restoran-old').css({opacity: '1'});
      $('#res01').css({opacity: '0'});
      $('.st5').css({opacity: '1'});

      $('#path3825').css({opacity: '0'});
      $('#circle3915').css({opacity: '.85'});
      return false;
    }
  }



var scrollTest = document.createElement('scrollTest');
  Object.assign(scrollTest.style,{
    position: 'fixed',
    top: '200px',
    left: '200px',
    zIndex: '9999',
    border: '3px solid #000',
    backgroundColor: '#f00',
    color: '#fff',
    padding: '10px 20px',
    fontSize: '2em',
    opacity: '.3'
  });
  scrollTest.innerHTML = 'TEST SCROLL';

  scrollTest.addEventListener('click', function(){
      o.content.mCustomScrollbar( "scrollTo",  $('#halls').position().top);
  });

  //document.body.appendChild(scrollTest);


});




