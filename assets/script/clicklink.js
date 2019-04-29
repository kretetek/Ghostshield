var mouse_timeout = null;
var mouse_timeout_target = null;

$(function(){

	if ($(window).width() > 700) {

		$('.clf_abs').addClass("clf_move");
		$('.clf_abs').removeClass("clf_abs");

		$(".click_link").click(function(event){

			event.preventDefault();
			window.location = $('#'+$(this).attr("id")+' .clf_move').attr("href");

		});
	
		$(".click_link").mousemove(function(event){

			clearTimeout(mouse_timeout);
		
			/*var position = $(this).position();
			var container_position = $("#container").position();
			var x = event.pageX - position.left;
			var y = event.pageY - position.top;
			console.log(x+", "+y);*/
		
			mouse_timeout_target = $(this).attr("id");
		
			$('#'+$(this).attr("id")+' .clf_move').css({/*"left":x, "top":y, "bottom":"auto", "right":"auto", */"opacity":0});
		
			mouse_timeout = setTimeout(function() {
				var parent_position = $('#'+mouse_timeout_target).offset();
				var x = Math.max(0, event.pageX - parent_position.left);
				var y = event.pageY - parent_position.top;
				$('#'+mouse_timeout_target+' .clf_move').css({"left":x, "top":y, "bottom":"auto", "right":"auto", "opacity":1});
			}, 1500);

		});

	}

});