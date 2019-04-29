var gallery_animating = false;
$(function(){

	$(".product .view a").each(function(){
		var click_div = $(this).parent().parent().parent();
		click_div.attr("data-href", $(this).attr("href"));
		click_div.addClass("clickable");
		//$(this).parent().hide();
	});
	
	$('#hexagons .hexagon').click(function(event){
		event.preventDefault();
		window.location = $(this).find("a").attr("href");
	});

	$('.mobile_accordion_content .close').html('<span>Close</span>');
	$('.mobile_accordion_content .close').click(function(){
		$('.square').each(function(){
			$(this).removeClass("hover");
			$('#'+$(this).attr("id")+' .line3').removeClass("close");
			$('#'+$(this).attr("id")+' .line3').html($(this).attr("data-line3"));
		});
	});

	$(".square").each(function(){

		$(this).attr("data-line3", $('#'+$(this).attr("id")+' .line3').html());

		$('#'+$(this).attr("id")+' .button').click(function(event){
			event.preventDefault();
			//pop($(this).attr("id"));
			if ($(this).parent().attr("data-href") != "#") {

				window.location = $(this).parent().attr("data-href");

			} else {

				$('.accordion_content').removeClass("open");
				//$('.mobile_accordion_content').removeClass("open");

				if ($(this).parent().hasClass("hover")) {

					$(this).parent().removeClass("hover");
					$('#'+$(this).parent().attr("id")+' .line3').removeClass("close");
					$('#'+$(this).parent().attr("id")+' .line3').html($(this).parent().attr("data-line3"));

				} else {

					$('.index .square').each(function(){
						$(this).removeClass("hover");
						$('#'+$(this).attr("id")+' .line3').removeClass("close");
						$('#'+$(this).attr("id")+' .line3').html($(this).attr("data-line3"));
					});

					$(this).parent().addClass("hover");
					$('#'+$(this).parent().attr("id")+' .line3').addClass("close");
					$('#'+$(this).parent().attr("id")+' .line3').html("Close");

					if ($(this).parent().attr("data-position").indexOf("top") > -1) {
						target = "top_row_accordion_content";

					} else if ($(this).parent().attr("data-position").indexOf("middle") > -1) {
						target = "middle_row_accordion_content";

					} else target = "bottom_row_accordion_content";

					$('#'+target).addClass("open");

					$('#'+target).html($("#"+ $(this).parent().attr("id") +" .mobile_accordion_content").html());

					$('#'+target+' .close').click(function(){
						$('.accordion_content').removeClass("open");
						//$('.mobile_accordion_content').removeClass("open");
						$('.index .square').each(function(){
							$(this).removeClass("hover");
							$('#'+$(this).attr("id")+' .line3').removeClass("close");
							$('#'+$(this).attr("id")+' .line3').html($(this).attr("data-line3"));
						});
					});

				}
				
				square_name = ($(this).parent().attr("data-href") == "#" ? "/#square="+$(this).parent().attr("data-position") : $(this).parent().attr("data-href"));
				$.ajax({ url:"/analytics/square?square="+escape(square_name)+"&ajax=true", cache:true }).done(function(html) { });

			}
		});
	});

	function load_image(image,id) {
		var my_image = new Image();
		my_image.id = id;
		my_image.onload = function(){
			gallery_display(this.id);
		}
		my_image.src = image;
	}

	function gallery_open(id) {

		var debug = true;
		if (debug) console.log("gellery_open("+id+"), gallery_animating: "+gallery_animating);
		var that = $("#gallery_thumb" + id);

		if (!gallery_animating && !that.hasClass("active")) {

			gallery_animating = true;
			$("#gallery_item1").addClass("loading");
			$("#gallery_thumbs .gallery_thumb").removeClass("active");
			that.addClass("active");

			load_image(that.attr("data-src"),id);
		}
	
	}
	
	function gallery_display(id) {

		var debug = true;
		
		var that = $("#gallery_thumb" + id);

		//$(this).width(width).height(height).appendTo(target);
		$("#gallery_item2").css("background-image", "url("+that.attr("data-src")+")");
		$("#gallery_item2").attr("data-src", that.attr("data-src"));
		$("#gallery_item2").attr("data-id", id);

		if (parseInt($("#gallery_item1").attr("data-id")) > parseInt(that.attr("data-id"))) {
			$("#gallery_item2").removeClass("gallery_right");
			if (!$("#gallery_item2").hasClass("gallery_left")) $("#gallery_item2").addClass("gallery_left");
			if (debug) console.log("from left? from " +$("#gallery_item1").attr("data-id")+" > to "+that.attr("data-id"));
		} else if (parseInt($("#gallery_item1").attr("data-id")) <= parseInt(that.attr("data-id"))) {
			$("#gallery_item2").removeClass("gallery_left");
			if (!$("#gallery_item2").hasClass("gallery_right")) $("#gallery_item2").addClass("gallery_right");
			if (debug) console.log("from right? from " +$("#gallery_item1").attr("data-id")+" <= to "+that.attr("data-id"));
		}

		$("#gallery_item2").hide().show(0);

		$("#gallery_item2").on("transitionend", function(){
			$("#gallery_item1").css("background-image", "url("+that.attr("data-src")+")");
			$("#gallery_item2").removeClass("gallery_shift");
			$("#gallery_item1").attr("data-id", $("#gallery_item2").attr("data-id"));
			$("#gallery_item1").removeClass("loading");

			$("#gallery_caption").html($('#gallery_caption'+id).html());
			$("#gallery_caption").addClass("visible");
			
			gallery_animating = false;
			if (debug) console.log("gallery done animating");
		});
		
		$("#gallery_item2").addClass("gallery_shift");
		$("#gallery_caption").removeClass("visible");

	}

	$("#gallery_thumbs .gallery_thumb").click(function(){

		gallery_open($(this).attr("data-id"));

	});

	$("#gc_right").click(function() {

		var id, new_id;

		$("#gallery_thumbs .gallery_thumb").each(function(){
			if ($(this).hasClass("active")) {
				id = $(this).attr("data-id");
			}
		});

		if ($('#gallery_thumb' + (id * 1 + 1)).length) {
			new_id = id * 1 + 1;
		} else new_id = 1;

		gallery_open(new_id);

	});

	$("#gc_left").click(function() {

		var id, new_id;

		$("#gallery_thumbs .gallery_thumb").each(function(){
			if ($(this).hasClass("active")) {
				id = $(this).attr("data-id");
			}
		});

		if ($('#gallery_thumb' + (id * 1 - 1)).length) {
			new_id = id * 1 - 1;
		} else new_id = $('.gallery_thumb').length;

		gallery_open(new_id);

	});

$('.portfolio-miami-thumb').click(function() {
	$('.portfolio-miami-thumb').fadeOut( "slow" );
	var videoElement = $('.portfolio-miami-thumb').next();
	var videoSrc = videoElement.attr("src");
	videoElement.attr('src', videoSrc + '&autoplay=1');
});

$('.portfolio-432-thumb').click(function() {
	$('.portfolio-432-thumb').fadeOut( "slow" );
	var videoElement = $('.portfolio-432-thumb').next();
	var videoSrc = videoElement.attr("src");
	videoElement.attr('src', videoSrc + '&autoplay=1');
});

$('.portfolio-home-thumb').click(function() {
	$('.portfolio-home-thumb').fadeOut( "slow" );
	var videoElement = $('.portfolio-home-thumb').next();
	var videoSrc = videoElement.attr("src");
	videoElement.attr('src', videoSrc + '&autoplay=1');
});

});