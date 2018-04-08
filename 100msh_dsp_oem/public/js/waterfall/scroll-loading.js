(function($, undefined) {
	$.fn.scrollLoading = function(options) {
		var o = $.extend({
			range: 200,
			maxNum: 20,
			callback: $.noop
		}, options || {});
		var timer;
		var that = $(this),
			num = 0,
			isWindow = that.selector == '' && that.length !== 0;
		    $wrapp = isWindow ? $(window) : that;
		function loading() {
			var	wrapperHeight = $wrapp.height(),
				wrapperTop = isWindow ? $wrapp.scrollTop() : $wrapp.offset().top;

			that.each(function() {
				var t = $(this),
					totalHeight = $wrapp.height() + wrapperTop,
					post = isWindow ? $(document).height() - o.range : $wrapp.children().last().offset().top + $wrapp.children().last().outerHeight(true) - o.range;
				if(post <= totalHeight) {
					if($.isFunction(o.callback) && num < o.maxNum) {
						num++;
						var call = o.callback.call(this, num);
					}
				}
			});
		}

		$wrapp.unbind('scroll.loadimg');
		loading();

		$wrapp.bind('scroll.loadimg', function() {
			timer && clearTimeout(timer);
			timer = setTimeout(loading, 100);
		});
		return this;
	};
})(jQuery);