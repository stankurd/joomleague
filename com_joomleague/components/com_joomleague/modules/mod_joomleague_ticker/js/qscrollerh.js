
document.addEventListener('DOMContentLoaded', function() {
	var opt = {
	  duration: 3000,
	  delay: 2000,
	  auto:true,
	  direction: 'h',
	  onMouseEnter: function(){this.stop();},
	  onMouseLeave: function(){this.play();}
	};
	var scroller = new QScroller('qscroller',opt);
	scroller.load();
	});

