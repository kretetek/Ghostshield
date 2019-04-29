
var popped = false;
var headerFixedHeight=0;
var bannerFixedHeight=0;
var mainNavHeight=40;
var pageNavHeight=40;
var participateTop=0;
var connectTop=0;
var donateTop=0;
var sponsorsTop=0;
var form_count = 0;
var b2tVisible = false;
var originalhtml = Array();
var $animation_elements=false;
var $window=$(window);


if (!("ontouchstart" in document.documentElement)) {
	$(document.body).addClass("no-touch");
}

$(function(){
	$animation_elements = $('.animate-in');
	$animation_elements.each(function(){
		$(this).removeClass("animate-in");
		$(this).addClass("animatable");
	});
	
	$window.on('scroll resize', check_viewport);
	$window.trigger('scroll');
	
//	$(document.body).append('<div id="overlay"><div id="overlay_document"></div></div>');
	$(window).bind('scroll', scrollUpdate);
	$(window).bind('resize', resizeUpdate);
	checkSizes();
	valign();
	
	$('.main_nav').css({"position":"fixed","top":"0px"});
	
	$(".index .product .view a").each(function(){
		var click_div = $(this).parent().parent().parent();
		click_div.attr("data-href", $(this).attr("href"));
		click_div.addClass("clickable");
		//$(this).parent().hide();
	});
	
	$('.mobile_accordion_content .close').html('<span>Close</span>');
	$('.mobile_accordion_content .close').click(function(){
		$('.index .square').each(function(){
			$(this).removeClass("hover");
			$('#'+$(this).attr("id")+' .line3').removeClass("close");
			$('#'+$(this).attr("id")+' .line3').html($(this).attr("data-line3"));
		});
	});
	
	$(".index .square").each(function(){
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
			
			}
		});
	});
	
	// removes popup_menu on outside click
	$(document.body).click(function(){
		if(event.target.parentNode && event.target.parentNode.id && event.target.parentNode.id.indexOf("item-") < 0) {
			$('.row_selected').removeClass("row_selected");
			$("#popup_menu").remove();
		}
	});
	
	$("#overlay").click(function(){ unpop(); });
	$("#overlay_document").click(function(){ return false; });
	
	/*
	$("#comparison").addClass("compacted");
	$("#comparison").append('<div id="hide-comparison">Hide Comparisons</div>');
	$("#comparison").append('<div id="show-comparison">Show Comparisons</div>');
	$("#show-comparison").click(function(){
		$("#show-comparison").css({"display":"none"});
		$("#comparison").removeClass("compacted");
	});
	$("#hide-comparison").click(function(){
		$("#show-comparison").css({"display":"block"});
		$("#comparison").addClass("compacted");
	});
	*/
	
	$(".pc_name_selectable").click(function(){
		$(this).toggleClass("open");
	});
	
	$(".pc_name_selectable .product_name").click(function(){
		$("#"+ $(this).parent().attr("id") +" .product_name").removeClass("selected");
		$(this).addClass("selected");
		var index = $(this).parent().parent().attr("class");
		for (var key in comparisonData["id"+$(this).attr("data-id")]) {
			if (comparisonData["id"+$(this).attr("data-id")].hasOwnProperty(key)) {
				//alert('$(\'#pc_row_'+key+' .'+index+').html('+comparisonData["id"+$(this).attr("data-id")][key]+')');
				$('#pc_row_'+key+' .'+index).html(comparisonData["id"+$(this).attr("data-id")][key]);
				if ($('#pc_row_'+key+' .'+index).html() == $('#pc_row_'+key+' .product_1').html()) {
					$('#pc_row_'+key+' .'+index).removeClass("different");
				} else {
					$('#pc_row_'+key+' .'+index).addClass("different");
				}
			}
		}
	});
	
	$("#contact_form").submit(function(event) {
		$.ajax({url:"contact.php?ajax=true", cache:false, data: { 'contact_name': $("#contact_name").val(), 'contact_email': $("#contact_email").val(), 'contact_comment': $("#contact_comment").val() }}).done(function(html) {
			$("#contact_form").html(html);
		});
		event.preventDefault;
		return false;
	});
	
	$('.video_container').each(function(){
		h = '<video src="'+$(this).attr("data-src-mp4")+'" '+$(this).attr("data-attr")+' ><source src="'+$(this).attr("data-src-mp4")+'" type="video/mp4" />';
		if ($(this).attr("data-src-webm")) h += '<source src="'+$(this).attr("data-src-webm")+'" type="video/webm" />';
		if ($(this).attr("data-src-ogg")) h += '<source src="'+$(this).attr("data-src-ogg")+'" type="video/ogg" />';
		h += '</video><div id="video_overlay"></div>';
		$(this).html(h);
	});
	
	originalhtml["nav_product_img"] = $('#nav_product_info .img').html();
	originalhtml["nav_product_name"] = $('#nav_product_info .name').html();
	originalhtml["nav_product_apps"] = $('#nav_product_info .applications').html();
	originalhtml["nav_product_finish"] = $('#nav_product_info .finish').html();
	
	$('.submenu #nav_product_list a').each(function() {
		$(this).addClass("choice");
		$(this).attr("data-original-href", $(this).attr("href"));
	});
	$('.submenu #nav_product_list a').hover(function() {
		if ($(this).hasClass("dim") === false) {
			$('#nav_product_info .img').html('<img src="'+$(this).attr("data-image")+'" />');
			$('#nav_product_info .name').html($(this).html());
			$('#nav_product_info .applications').html($(this).attr("data-applications"));
			$('#nav_product_info .finish').html($(this).attr("data-finish"));
		}
	}, function(){
		if ($(this).hasClass("dim") === false) {
			$('#nav_product_info .img').html(originalhtml["nav_product_img"]);
			$('#nav_product_info .name').html(originalhtml["nav_product_name"]);
			$('#nav_product_info .applications').html(originalhtml["nav_product_apps"]);
			$('#nav_product_info .finish').html(originalhtml["nav_product_finish"]);
		}
	});
	
	/*$("#nav_menu_tiers .tier1 > li").each(function(){
		$(this).hover(function(ev){
			//ev.preventDefault();
			if ($(this).attr("data-list") !== false) {
				nav_product_list($(this).attr("data-list"));
			} else nav_product_all();
		},function(){
		});
	}); -- used to be click fyi */
	
	originalhtml["distributor_img"] = $('#distributor-info .img').html();
	originalhtml["distributor_name"] = $('#distributor-info .name').html();
	originalhtml["distributor_text"] = $('#distributor-info .text').html();
	
	$('.submenu #online-distributors a').hover(function() {
		$('#distributor-info .img').html('<img src="'+$(this).attr("data-logo")+'" />');
		$('#distributor-info .name').html($(this).html());
		$('#distributor-info .phone').html('<a href="tel:'+$(this).attr("data-phone")+'">'+$(this).attr("data-phone")+'</a>');
		$('#distributor-info .text').html($(this).attr("data-text"));
	}, function(){
		$('#distributor-info .img').html(originalhtml["distributor_img"]);
		$('#distributor-info .name').html(originalhtml["distributor_name"]);
		$('#distributor-info .phone').html("");
		$('#distributor-info .text').html(originalhtml["distributor_text"]);
	});
	
	$('form').each(function(){
		form_count++;
		$(this).attr("id","form"+form_count);
		submit = $('#form'+form_count+' input[type=submit]');
		action = $('#form'+form_count).attr("action")?$('#form'+form_count).attr("action"):'';
		$('#form'+form_count).attr("action",action+"#form"+form_count);
		$('<a class="hex-button small'+(submit.hasClass("inverse")?" inverse":"")+' reposition" href="javascript:$(\'#form'+form_count+'\').submit()"><span class="hex1 button-svg"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve"><path class="st0" d="M2.1,7.6v16h23.4v-16H2.1z M21.2,9.6l-7.4,4.8L6.4,9.6H21.2z M4.1,21.6V10.5l9.7,6.3l9.7-6.3v11.2H4.1z"/></svg></span><span class="button-text">'+submit.attr("value")+'</span></a>').insertBefore(submit);
		
		submit.hide();
	});

});




$(window).load(function() {
	checkSizes();
	valign();
});

function resizeUpdate() {
	
	valign();
	
	if ($(window).width() >= 900) {
		if ($('#main_nav > ul').hasClass("open")) $(document.body).removeClass("clip");
	} else {
		if ($('#main_nav > ul').hasClass("open")) $(document.body).addClass("clip");
	}
	
}

function scrollUpdate() {
	var scrollTop = $(window).scrollTop();

	var scrollPercentage = scrollTop / ( $(document).height() - $(window).height() ); // 0.0 - 1.0
	
//	if (scrollTop > $('h1').offset().top() && scrollTop <

	// sticky the header //

	var timeBuffer = 100;
	
	if (scrollTop >= bannerFixedHeight - timeBuffer) {
		if (scrollTop >= bannerFixedHeight) {
			$('.page_nav').css({"z-index":2000});
		} else {
			$('.page_nav').css({"z-index":0});
		}
	}
	
	
	if (scrollTop >= bannerFixedHeight - 5) {
		$('.page_nav').addClass("fixed");
		$('.page_nav').css({"position":"fixed", "top":mainNavHeight +"px", "z-index":2000});
		$('.page_nav a').each(function() {
			var offset = $($(this).attr("href")+"_anchor").offset();
			//alert($($(this).attr("href")+"_anchor")+": "+offset);
			if(scrollTop+2 >= offset.top){
				$('.page_nav a').removeClass("active");
				if(!$(this).hasClass("active")) $(this).addClass("active");
			}else $(this).removeClass("active");
		});
	} else {
		$('.page_nav').removeClass("fixed");
		$('.page_nav').css({"position":"relative", "top":"auto", "z-index":1000});
		$('.page_nav a').removeClass("active");
	}

	if (scrollTop > $(window).height()) {
		if (!b2tVisible) {
			b2tVisible = true;
			//$('#b2top').animate({'right':0},{duration:navItemAnimationSpeedIn});
		}
	} else {
		if (b2tVisible) {
			b2tVisible = false;
			//$('#b2top').animate({'right':"-60px"},{duration:navItemAnimationSpeedIn});
		}
	}
};

function check_viewport() {
	var window_height = $window.height();
	var window_top_position = $window.scrollTop();
	var window_bottom_position = (window_top_position + window_height);
	
	$.each($animation_elements, function() {
		var $element = $(this);
		var element_height = $element.outerHeight();
		var element_top_position = $element.offset().top;
		var element_bottom_position = (element_top_position + element_height);

		//check to see if this current container is within viewport
		if ((element_bottom_position >= window_top_position) &&
			(element_top_position <= window_bottom_position)) {
			$element.addClass('in-view');
		} else {
			//$element.removeClass('in-view');
		}
	});
}


function valign(){
	$('.valign').each(function(i){
		$(this).css("margin-top", (($(this).parent().height()-$(this).height())/2)+"px");
	});
	scrollUpdate();
	checkSizes();
}

function checkSizes(){
	headerFixedHeight = $('header').height();
	bannerFixedHeight = $('header').height() + $('#banner').height() + /*$('#welcome').height() + */$('#product').height() + $('#page_nav_container').height() + $('#welcome').height() +100; // -42
	mainNavHeight = $('nav.main_nav').height();
	pageNavHeight = $('nav.page_nav').height();
	
	//participateTop=$('#participate').offset().top-70;
	//connectTop=$('#connect').offset().top-70;
	//donateTop=$('#donate').offset().top-70;
	//sponsorsTop=$('#sponsors').offset().top-70;
}

var currentTab = 1;
function showTab(num){
	$('#tab'+currentTab).css("display","none");
	$('#tab-button'+currentTab).removeClass("active");
	$('#tab'+num).css("display","block");
	$('#tab-button'+num).addClass("active");
	currentTab = num;
}

function pop(url){
	if (!popped) {
		popped = true;
		//$("#main").css("filter", "blur(100px)");
		$("#overlay #overlay_document").html("");
		//$.ajax({ url: url, cache: true }).done(function(html) {
			//if($("#popup_menu")) $("#popup_menu").remove();
			$("#overlay #overlay_document").html($("#"+url+" .popup").html());
			$("#overlay #overlay_document").scrollTop(0);
			$("#overlay #overlay_document").on("click", "a", function() { window.location = $(this).attr("href"); });
			$("#overlay #overlay_document").on("click", ".clickable", function() {  window.location = $(this).attr("data-href"); });
			$(document.body).css("overflow","hidden");
			//alert($("#overlay #document").height());
		//});
		/*
		$("#overlay").removeClass("animate_change");
		$("#overlay").addClass("invisible");
		$("#overlay").addClass("animate_change");
		$("#overlay").removeClass("invisible");
		*/
		$("#overlay").css({"display":"block", "opacity":0});
		$("#overlay").animate({'opacity':1},{
			duration:250,
			complete:function(){
				$('#content').addClass("print_hide");
				$('nav').addClass("print_hide");
				$('<div id="overlay_buttons"><div id="overlay_close">X</div></div>').insertBefore($('#overlay_document'));
				$("#overlay_close").click(function(){ unpop(); });
				$("#overlay_buttons").click(function(){ return false; });
			}
		});
	}
	return false;
}

function unpop(){
	$('#overlay_buttons').remove();
	$("#overlay").animate({'opacity':0},{
		duration:250,
		complete:function(){
			popped = false;
			$(document.body).css("overflow","visible");
			$("#overlay").css("display","none");
			//$('#content').removeClass("print_hide");
			//$('nav').removeClass("print_hide");
		}
	});
}
