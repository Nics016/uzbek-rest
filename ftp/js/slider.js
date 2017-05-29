$(document).ready(function(){
	$('.slider').each(function(){
		$(this).slider({
			offset: '.sliderBox',
			slide: '.item',
			backward: '.backward',
			forward: '.forward',
			dots: '.dots',
			list: '.contentsList'
		});
	})
});

(function($){
	$.fn.slider = function(param) {
		obj = {
			offset: param.offset || '[sl-offset]',
			slide: param.slide || '[sl-item]',
			dots: param.dots || '[sl-dots]',
			backward: param.backward || '[sl-backward]',
			forward: param.forward || '[sl-forward]',
			list: param.list || '[sl-list]'
		}
		var wsize = $(window).width();
		var elem = this;
		engineSlider.data.itemSize = elem.find(obj.slide).innerWidth();
		if(!this.find('[sl-active]').length){
			this.find(obj.slide + ':first').attr('sl-active', '');
		} else {
			engineSlider.offset(elem);
		}
		
		this.find(obj.slide).each(function(){
			$(this).width(elem.width());
			elem.find(obj.dots).append('<li><a href="#"></a></li>');
		});
		
		this.find('.early').on('click', function(){
			engineSlider.index( elem, obj, 0 );
			return false;
		});
		
		this.find(obj.list + ' li').on('click', function(){
			engineSlider.index( elem, obj, $(this).index() + 1 );
			return false;
		});
		
		this.find(obj.backward).on('click', function(){
			engineSlider.backward(elem, obj);
			return false;
		});
		this.find(obj.forward).on('click', function(){
			engineSlider.forward(elem, obj);
			return false;
		});
	}
	var engineSlider = {
		data: {},
		offset: function(elem){
			var elemIndex = elem.find('[sl-active]').index();
			var offset = engineSlider.data.itemSize * elemIndex;			
			elem.find(obj.offset).css({marginLeft: -offset});
			elem.find(obj.dots).find('.active').removeClass('active');
			elem.find(obj.dots).children().eq(elemIndex).addClass('active');
		},
		index: function(elem, obj, index){
			elem.find('[sl-active]').removeAttr('sl-active');
			elem.find(obj.slide).eq(index).attr('sl-active', '');
			this.offset(elem);
		},
		backward: function(elem, obj){
			var block = elem.find('[sl-active]');
			if(block.index()){
				block.prev().attr('sl-active', '');
				block.removeAttr('sl-active');
			} else {
				elem.find(obj.slide + ':last').attr('sl-active', '');
				block.removeAttr('sl-active');
			}
			this.offset(elem);
		},
		forward: function(elem, obj){
			var block = elem.find('[sl-active]');
			if(block.next().index() != -1){
				block.next().attr('sl-active', '');
				block.removeAttr('sl-active');
			} else {
				elem.find(obj.slide + ':first').attr('sl-active', '');
				block.removeAttr('sl-active');
			}
			this.offset(elem);
		}
	}
})(jQuery);

