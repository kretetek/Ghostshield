$(function(){

	$('.link_list_interior.accordian').each(function(){
		//'.($active?" active":"").'" id="cat-acc'.$i.'"
		//alert($(this).attr("id"));
		$("#"+$(this).attr("id")+" h3").click(function(){
			if (!$(this).hasClass("origin"))  {
				$(this).parent().addClass("clicked");
				$('.link_list_interior.accordian.active').each(function(){
					//alert($(this).attr("data-clicked"));
					if (!$(this).hasClass("origin") && !$(this).hasClass("clicked")) $(this).removeClass("active");
				});
				$(this).parent().removeClass("clicked");
				$(this).parent().toggleClass("active");
			}
		});
	});

	$('.link_list .filters.hook').click(function(){
		$(this).parent().toggleClass("active");
		if ($(window).scrollTop() < $(window).height() / 2) {
			$('.link_list .filters.hook').removeClass("visible");
		}
		ga('send', 'event', {
			eventCategory: 'BrowseFiltersTab',
			eventAction: 'click',
			eventLabel: 'toggleFilters'
		});
	});
	
	$('.filters.static').click(function(){
		$('.link_list').addClass("active");
		if (!$('.link_list .filters.hook').hasClass("visible")) {
			$('.link_list .filters.hook').addClass("visible");
		}
		ga('send', 'event', {
			eventCategory: 'BrowseFiltersLink',
			eventAction: 'click',
			eventLabel: 'toggleFilters'
		});
	});
	
	if ($(window).width() < 800) {
		$('#tagline a.btn').remove();
		$('#tagline').append('<div class="btn hex-button tall">View Filters</div>');
		$('#tagline .btn').click(function(event){
			$('.link_list').addClass("active");
			if (!$('.link_list .filters.hook').hasClass("visible")) {
				$('.link_list .filters.hook').addClass("visible");
			}
			ga('send', 'event', {
				eventCategory: 'BrowseFiltersButton',
				eventAction: 'click',
				eventLabel: 'toggleFilters'
			});
		});
	}

	$('.filters.static').click(function(){
		$('.link_list').addClass("active");
		if (!$('.link_list .filters.hook').hasClass("visible")) {
			$('.link_list .filters.hook').addClass("visible");
		}
		ga('send', 'event', {
			eventCategory: 'BrowseFiltersLink',
			eventAction: 'click',
			eventLabel: 'toggleFilters'
		});
	});
	
	/*
		<section class="mobile filters_static">
			<div class="filters static"><span class="burger"></span> View Filters</div>
		</section>*/

});

scrollUpdateExtra = function(scrollTop, scrollPercentage) {

	if (!$('.link_list').hasClass("active")) {
		if (scrollTop > $(window).height() / 2) {
			$('.link_list .filters.hook').addClass("visible");
		} else {
			$('.link_list .filters.hook').removeClass("visible");
		}
	}

}