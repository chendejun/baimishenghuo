/**
 * waterfall
 * 2013-7-24
 */
(function($) {
	'use strict';
	$.fn.waterfall = function(options) {
		var o = $.extend({
			item: '.item',
			margin: {
				right: 'cell',
				bottom: 0
			}
		}, options || {});
		return this.each(function() {
			var wf = {};
			var $this = $(this);
			wf.width = $this.width();
			wf.resize = function() {
				var pos = [],
					top = [],
					$item = $this.find(o.item),
					boxWidth = $this.width(),
					itemWidth = $item.eq(0).outerWidth(true),
					num = Math.floor(boxWidth/itemWidth),
					right = o.margin.right,
					marginRight = right === 'cell' ? ((boxWidth%itemWidth) / (num - 1)) : right,
					num = right === 'cell' ? num : Math.floor((boxWidth + right) / (itemWidth + right)),
					num = num <= 1 ? 1 : num,
					marginRight = num === 1 ? 0 : marginRight;

				for(var i = 0; i < num; i++) {
					pos.push([(itemWidth + marginRight) * i, 0]);
				}
				$item.each(function() {
					var idx = 0,
						$t = $(this),
						height = $t.outerHeight(true) + o.margin.bottom;
					for(var i = 0; i < num; i++) {
						if(pos[i][1] < pos[idx][1]) {
							idx = i;
						}
					}
					$t.css({'left': pos[idx][0], 'top': pos[idx][1]});
					pos[idx][1] += height;
				});
				for(var i = 0; i < num; i++) {
					top.push(pos[i][1]);
				}
				top.sort(function(a, b) {
					return a - b;
				});
				$this.height(top[num - 1]);
				wf.minTop = top[num - 1] - top[0];	//最大高度 - 最小高度
			}
			wf.resize();
			$(window).bind({
				'resize': function() {
					setTimeout(function() {
						if(wf.width != $this.width()) {
							wf.resize();
							wf.width = $this.width();
						}
					}, 200);
				}
			});
			$this.data('waterfall', wf);
		});
	};
})(jQuery);