
var popped = false;
var headerFixedHeight=0;
var bannerFixedHeight=0;
var taglineFixedHeight=0;
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
var glossaryTO;

if (!("ontouchstart" in document.documentElement)) {
	$(document.body).addClass("no-touch");
}

$('.page_content').css("opacity", 0);

$(function(){
	/* Disable overscroll / viewport moving on everything but scrollable divs
	$('body').on('touchmove', function (e) {
		if (!$('.scrollable').has($(e.target)).length) e.preventDefault();
	});*/

	$window.on('scroll resize', check_viewport);
	$window.trigger('scroll');

//	$(document.body).append('<div id="overlay"><div id="overlay_document"></div></div>');
	//$(window).bind('scroll', scrollUpdate);
	$(window).bind('resize', resizeUpdate);
	checkSizes();
	valign();
	
	$('#index_video').addClass("index_video_black");
	
	/*if ($(window).scrollTop() < 701) {
		
		$('header h1 a').addClass("index_wait");
		$('#tagline').addClass("index_wait");
		$('#welcome').addClass("index_wait");
		$('#page_nav_container').addClass("index_wait");
		$('#squares').addClass("index_wait");
	
		setTimeout('$("header h1 a").addClass("index_h1_fade");', 1000);
		setTimeout('$("#tagline").addClass("index_tagline_fade");', 1300);
		setTimeout('$("#welcome").addClass("index_fade");', 1500);
		setTimeout('$("#page_nav_container").addClass("index_fade");', 1800);
		setTimeout('$("#gallery").addClass("index_fade");', 2000);
	
	}*/
	
	$('a.back_link').attr("href", "javascript:window.history.go(-1);");

	handleDFN();

	if ($(window).width() > 600) {
		$('#explore_collage').attr("src", "//assets.ghostshield.com/img/collage-desktop.jpg");
	}

	$('.page_content').css("opacity", 1);

	$('.video_container').each(function(i){
		h = '<video id="video'+i+'" class="video_fade_ready" src="'+$(this).attr("data-src-mp4")+'" '+$(this).attr("data-attr")+' ><source src="'+$(this).attr("data-src-mp4")+'" type="video/mp4" />';
		if ($(this).attr("data-src-webm")) h += '<source src="'+$(this).attr("data-src-webm")+'" type="video/webm" />';
		if ($(this).attr("data-src-ogg")) h += '<source src="'+$(this).attr("data-src-ogg")+'" type="video/ogg" />';
		h += '</video><div id="video_overlay"></div>';
		$(this).html(h);
	
		setTimeout('$("#video'+i+'").addClass("video_fade_in");', 300);
	});

	$animation_elements = $('.animate-in');
	$animation_elements.each(function(){
		$(this).removeClass("animate-in");
		$(this).addClass("animatable");
	});
	
	$(window).bind('hashchange', hashevent);

	$('body a').each(function(){
		if ($(this).attr("href") && ($(this).attr("href") == "#product_finder" || $(this).attr("href").indexOf("#pf") > -1)) {
			$(this).click(function(event){
				event.preventDefault();
				var id = $(this).attr("href").replace("#", "").replace(/\/.*/, "") + "_anchor";
		
				if ($('#'+id).length) {
					$scrollto = document.getElementById(id).offsetTop;
					var offset = $(this).attr("href") == "#top" ? 0 : (document.getElementById(id) ? document.getElementById(id).offsetTop : 0);
				}
				if ($(this).attr("href") == "#product_finder") {
					product_finder_status();
					setTimeout('product_finder_go("/product-finder/?pf");', 800);
				}
				ga('send', 'event', {
					eventCategory: 'JumpTo',
					eventAction: 'click',
					eventLabel: window.location + $(this).attr("href")
				});
				$('html, body').animate({ scrollTop:offset }, 1000);
			});
		}
		/*if ($(this).attr("href") && $(this).attr("href").indexOf("#") == 0) $(this).click(function(event){
			event.preventDefault();
			$('#page_nav > ul').removeClass("open");
			var id = $(this).attr("href").replace("#", "").replace(/\/.* /, "") + "_anchor";
			
			if (id == "pf_anchor") window.location = $(this).attr("href");
			if ($('#'+id).length) {
				$scrollto = document.getElementById(id).offsetTop;
				var offset = $(this).attr("href") == "#top" ? 0 : (document.getElementById(id) ? document.getElementById(id).offsetTop : 0) + 150;
			}
			if ($(this).attr("href") == "#product_finder") {
				product_finder_status();
				setTimeout('product_finder_go("/product-finder/?pf");', 800);
			}
			//alert($(this).attr("href")+"_anchor... " +  + "; "+ $(document.body).offset().top);
			//$('html, body').animate({ scrollTop:$($(this).attr("href")+"_anchor").offset().top+100 }, 1000);
			ga('send', 'event', {
				eventCategory: 'JumpTo',
				eventAction: 'click',
				eventLabel: window.location + $(this).attr("href")
			});
			$('html, body').animate({ scrollTop:offset - $('#page_nav_container').height() }, 1000);
		});*/
	});

	$('form').each(function(){
		form_count++;
		if (!$(this).attr("id") || $(this).attr("id").length < 1) $(this).attr("id","form"+form_count);
		id = $(this).attr("id");
		if (id != "gsdropzone" && id != "gallery_captions" && !$(this).hasClass("ignore")) {
			submit = $('#'+id+' input[type=submit]');
			action = $('#'+id).attr("action") ? $('#'+id).attr("action") : '';
			$('#'+id).attr("action", action+"#"+id);
			aid = id+"-submitbutton";
			$('<a id="'+aid+'" data-id="'+id+'" class="hex-button small '+(submit.attr("class"))+' reposition" href="javascript:void();"><span class="hex1 button-svg"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve"><path class="st0" d="M2.1,7.6v16h23.4v-16H2.1z M21.2,9.6l-7.4,4.8L6.4,9.6H21.2z M4.1,21.6V10.5l9.7,6.3l9.7-6.3v11.2H4.1z"/></svg></span><span class="button-text">'+submit.attr("value")+'</span></a>').insertBefore(submit);
			$('#'+aid).click(function(){
				$('#'+$(this).attr("data-id")).submit();
			});
			submit.hide();
		}
	});
	
	$('.main_nav').css({"position":"fixed", "top":"0px"});

	$(".index .product .view a").each(function(){
		var click_div = $(this).parent().parent().parent();
		click_div.attr("data-href", $(this).attr("href"));
		click_div.addClass("clickable");
		//$(this).parent().hide();
	});
	
	// removes popup_menu on outside click
	$(document.body).click(function(){
		if (event.target.parentNode && event.target.parentNode.id && event.target.parentNode.id.indexOf("item-") < 0) {
			$('.row_selected').removeClass("row_selected");
			$("#popup_menu").remove();
		}
	});
	
	//$("#overlay").click(function(){ unpop(); });
	$("#overlay_document").click(function(){ return false; });
	
	$("select.comparison").change(function(){
		
		var name = $(this).find("option:selected").text();
		var index = $(this).parent().parent().parent().attr("class");
	
		var current = window.location.href.replace(/.*product\//, "");
	
		ga('send', 'event', {
			eventCategory: 'ProductComparison',
			eventAction: 'click',
			eventLabel: name + " compared to "+ current
		});
		
		if (name == "Select Product") {
			$('td.'+index).removeClass("different");
			$('td.'+index).html("");
			$(this).parent().attr("class", "unselected");
		} else {
			$(this).parent().attr("class", "product_name selected selectable " + name.toLowerCase().replace(" ","-"));
			for (var key in comparisonData["id"+$(this).val()]) {
				if (comparisonData["id"+$(this).val()].hasOwnProperty(key)) {
					//alert('$(\'#pc_row_'+key+' .'+index+').html('+comparisonData["id"+$(this).val()][key]+')');
					$('#pc_row_'+key+' .'+index).html(comparisonData["id"+$(this).val()][key]);
					if ($('#pc_row_'+key+' .'+index).html() == $('#pc_row_'+key+' .product_1').html()) {
						$('#pc_row_'+key+' .'+index).removeClass("different");
					} else {
						$('#pc_row_'+key+' .'+index).addClass("different");
					}
				}
			}
		}
	});
	
	$("form.contact").submit(function(event) {
		event.preventDefault();
		form_id = "#"+$(this).attr("id");
		$("p.contact_error").remove();
		$.ajax({
			url:"/contact.php?ajax=true",
			cache:false,
			data:$(this).serialize()
		}).done(function(html){
			if (html.indexOf("<!--ERROR-->") > -1) {
				$(html).insertBefore($(form_id));
			} else $(form_id).parent().html(html);
		});
		return false;
	});
	
	originalhtml["nav_product_img"] = $('#nav_product_info .img').html();
	originalhtml["nav_product_name"] = $('#nav_product_info .name').html();
	originalhtml["nav_product_apps"] = $('#nav_product_info .applications').html();
	originalhtml["nav_product_finish"] = $('#nav_product_info .finish').html();
	
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
	
	$('.site_search_form').submit(function(evt){
		evt.preventDefault();
		var $inputs = $(this).find(':input');
		var values = {};
		$inputs.each(function() {
			//values[this.name] = $(this).val();
			if (this.name == "search") window.location = "/search/"+escape($(this).val());
		});
		return false;
	});
	
	hashevent();

});

function hashevent() {
	var hash = location.hash;
	$(document.body).attr("data-hash",hash.replace("#","").replace("/","_"));
	var vals = hash.split("/");
//var firstPart = (vals[0] != "#content" ? '<span>'+vals[0].replace("#","")+" <span class=\"delimiter\">"+string_delimiter+"</span></span> " : "");
//if(vals[0] && vals[1]) $('#location_new').html(firstPart+vals[1].replace("+"," ")!="favorites"?firstPart+vals[1].replace("+"," "):"Guide");
	var count = 1;
	console.log("hash change: "+hash);
	if (hash.indexOf("#menu") == 0) {
		open_menu();
	}else{
		close_menu();
	}
}

function open_menu(){
	$("#main_nav_container").addClass("active");
	$(document.body).addClass("noscroll");
	$('#link-menu .link-text').html("Close");
	$('#link-menu a').attr("href",'javascript:window.history.go(-1);');
}
function close_menu() {
	$("#main_nav_container").removeClass("active");
	$(document.body).removeClass("noscroll");
	$('#link-menu .link-text').html("Menu");
	$('#link-menu a').attr("href","#menu");
}


function handleDFN() {
	
	if (!!('ontouchstart' in window)) { //check for touch device

		// events for touch
		$(document).on('click', 'dfn', function(event){
			if (!$(this).attr("data-word")) $(this).attr("data-word", $(this).html());
			var word = $(this).attr("data-word"); // get the word
			if (!$(this).attr("id")) $(this).attr("id", "word"+Math.floor(Math.random()*1000));
			var word_id = $(this).attr("id"); // get the id
			
			if (!$("#"+word_id).hasClass("touchactive")) {
				$.ajax({ url:"/glossary/"+word+"?ajax=true&mobile=true", cache:true }).done(function(html) {
					mobile_alert(html);
					ga('send', 'event', {
						eventCategory: 'Glossary',
						eventAction: 'click',
						eventLabel: word
					});
				});
			}
			
			// prevent multiple events
			$("#"+word_id).addClass("touchactive");
			glossaryTO = setTimeout('$("#'+word_id+'").removeClass("touchactive");', 1000);
		});
		
	} else {

		$('dfn').hover(function() {
		//$(document).on('hover touchstart', 'dfn', function(event){
		
			if (!$(this).attr("data-word")) $(this).attr("data-word", $(this).html());
			var word = $(this).attr("data-word"); // get the word
			if (!$(this).attr("id")) $(this).attr("id", "word"+Math.floor(Math.random()*1000));
			var word_id = $(this).attr("id"); // get the id
		
			if ($("#"+word_id+" .definition").length < 1) $(this).append('<div class="definition"></div>');
			glossaryTO = setTimeout('$("#'+word_id+' .definition").addClass("active");', 100);
		
			if ($("#"+word_id+" .definition h3").length < 1) {
				$("#"+word_id+" .definition").html('<div class="socket"><div class="gel center-gel"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c1 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c2 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c3 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c4 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c5 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c6 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div></div>');
				$.ajax({ url:"/glossary/"+word+"?ajax=true", cache:true }).done(function(html) {
					$("#"+word_id+" .definition").html(html);
					ga('send', 'event', {
						eventCategory: 'Glossary',
						eventAction: 'click',
						eventLabel: word
					});
				});
			}
		}, function() {
			clearTimeout(glossaryTO);
			var word_id = $(this).attr("id");
			$("#"+word_id+" .definition").removeClass("active");
		});

	}

}

function mobile_alert(html) {

	//html 
	alert(html);

}


$(window).on("load", function() {
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
/*
function scrollUpdate() {

	var scrollTop = $(window).scrollTop();

	var scrollPercentage = scrollTop / ( $(document).height() - $(window).height() ); // 0.0 - 1.0
	
//	if (scrollTop > $('h1').offset().top() && scrollTop <

	// sticky the header //
	
	if (typeof scrollUpdateExtra === "function") { 
		// safe to use the function
		scrollUpdateExtra(scrollTop, scrollPercentage);
	}

	var timeBuffer = 100;
	
	if ($(window).width() > 1099) {
		if (scrollTop > 80) {
			$('nav#main_nav').removeClass("anchored");
		} else $('nav#main_nav').addClass("anchored");
	} else $('nav#main_nav').addClass("anchored");
	
	if (scrollTop >= bannerFixedHeight - timeBuffer && scrollTop >= taglineFixedHeight) {
		if (scrollTop >= bannerFixedHeight && scrollTop >= taglineFixedHeight) {
			$('.page_nav').css({ "z-index":2000 });
		} else {
			$('.page_nav').css({ "z-index":0 });
		}
	}

	
	if (scrollTop >= bannerFixedHeight - 5 && scrollTop >= taglineFixedHeight - 5) {
		$('.page_nav').addClass("fixed");
		$('.page_nav').css({ "position":"fixed", "top":mainNavHeight +"px", "z-index":2000 });
		$('.page_nav a').each(function() {
			
			var id = $(this).attr("href").replace("#","")+"_anchor";
			//var offset = $($(this).attr("href")+"_anchor").offset();
			if (document.getElementById(id)) {
				var offset = document.getElementById(id).offsetTop;
				if (scrollTop+2 >= offset) { //offset.top){
					$('.page_nav a').removeClass("active");
					if (!$(this).hasClass("active")) $(this).addClass("active");
				} else $(this).removeClass("active");
			}
		});
	} else {
		$('.page_nav').removeClass("fixed");
		$('.page_nav').css({ "position":"relative", "top":"auto", "z-index":1000 });
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

	if ($(window).width() > 600) {
		$('#explore_collage').attr("src", "//assets.ghostshield.com/img/collage-desktop.jpg");
	} else $('#explore_collage').attr("src", "//assets.ghostshield.com/img/collage-mobile.jpg");

};
*/

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
			if (!$element.hasClass("index_fade")) {
				$element.addClass('in-view');
				$element.bind("transitionend webkitTransitionEnd animationend webkitAnimationEnd", function(){ $(this).removeClass("in-view"); $(this).removeClass("animatable"); });
			}
		} else {
			//$element.removeClass('in-view');
		}
	});
}


function valign(){
	$('.valign').each(function(i){
		$(this).css("margin-top", (($(this).parent().height()-$(this).height())/2)+"px");
	});
	//scrollUpdate();
	checkSizes();
}

function checkSizes(){
	headerFixedHeight = $('header').height();
	bannerFixedHeight = $('header').height() + $('#page_nav_container').height();
	taglineFixedHeight = $('#tagline').height();
	if ($('#banner').height() > 0) bannerFixedHeight += $('#banner').height();
	if ($('#product').height() > 0) bannerFixedHeight += $('#product').height();
	if ($('#welcome').height() > 0) bannerFixedHeight += $('#welcome').height();
	bannerFixedHeight += 100; // not sure why this is needed, used to be -42
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

/*function pop(url){
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
}*/

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-44071668-1', 'auto');
ga('send', 'pageview');