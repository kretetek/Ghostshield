$(function(){

	handle_product_finder();
	
	$(window).hashchange(function(){
		var hash = location.hash;
		if (hash.indexOf("#pf") == 0) {
			if (hash == "#pf-restart") {
				var string = "/product-finder/";
			} else {
				var vals = hash.split("/");
				var string = "/product-finder/?pf&ajax&";
				var count = 1;
				vals.forEach(function(val){
					if (val != "#pf") {
						string += "q"+count+"="+escape(val)+"&";
						count++;
					}
				});
			}
			product_finder_status();
			product_finder_go(string);
		}
	})

	$(window).hashchange();

});

function handle_product_finder() {

	$('#product_finder a').click(function(ev){
		if (!$(this).hasClass("link_out")) {
			ev.preventDefault();
			var hash = "#" + $(this).attr("href").replace("?", "").replace("/product-finder/", "").replace("&ajax", "").replace(/\&q[0-9]+\=/g, "/").replace(" ", "+").replace("&sqft=","/sqft=").toLowerCase();
			window.location.hash = hash;
			//product_finder_status();
			//product_finder_go($(this).attr("href"));
		}
	});
	
	$('#product_finder form').on('submit', function(ev){
		ev.preventDefault();
		var hash = "#pf" + $(this).attr("data-query").replace("?", "").replace("/product-finder/", "").replace("&ajax", "").replace(/\&q[0-9]+\=/g, "/").replace(" ", "+").replace("&sqft=","/sqft=").toLowerCase();
		if ($('#product_finder_sqft').length) {
			var val = $('#product_finder_sqft').val();
			if (val*1 != val) val = 0;
			hash += "/sqft=" + val;
		}
		//alert(hash);
		window.location.hash = hash;
	});
	
	$('#product_finder form #product_finder_sqft').focus();

}


function product_finder_go(href) {

	$.ajax({ method:"GET", url:href, data:{ajax: "true"} }).done(function(msg){
		$('#product_finder').html(msg);
		handleDFN();
		handle_product_finder();
		try { valign(); } catch(err) { console.log(err); }
		
		var offset = (document.getElementById("product_finder_anchor") ? document.getElementById("product_finder_anchor").offsetTop : 0);
		$('html, body').animate({ scrollTop:offset }, 1000);
	});

}

function product_finder_status() {
	$('#product_finder .status').html('<div class="socket"><div class="gel center-gel"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c1 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c2 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c3 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c4 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c5 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div><div class="gel c6 r1"><div class="hex-brick h1"></div><div class="hex-brick h2"></div><div class="hex-brick h3"></div></div></div>');
	$('#product_finder .status').addClass("status-loading");
}

/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,e,b){var c="hashchange",h=document,f,g=$.event.special,i=h.documentMode,d="on"+c in e&&(i===b||i>7);function a(j){j=j||location.href;return"#"+j.replace(/^[^#]*#?(.*)$/,"$1")}$.fn[c]=function(j){return j?this.bind(c,j):this.trigger(c)};$.fn[c].delay=50;g[c]=$.extend(g[c],{setup:function(){if(d){return false}$(f.start)},teardown:function(){if(d){return false}$(f.stop)}});f=(function(){var j={},p,m=a(),k=function(q){return q},l=k,o=k;j.start=function(){p||n()};j.stop=function(){p&&clearTimeout(p);p=b};function n(){var r=a(),q=o(m);if(r!==m){l(m=r,q);$(e).trigger(c)}else{if(q!==m){location.href=location.href.replace(/#.*/,"")+q}}p=setTimeout(n,$.fn[c].delay)}(/msie|trident/i).test(navigator.userAgent)&&!d&&(function(){var q,r;j.start=function(){if(!q){r=$.fn[c].src;r=r&&r+a();q=$('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){r||l(a());n()}).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow;h.onpropertychange=function(){try{if(event.propertyName==="title"){q.document.title=h.title}}catch(s){}}}};j.stop=k;o=function(){return a(q.location.href)};l=function(v,s){var u=q.document,t=$.fn[c].domain;if(v!==s){u.title=h.title;u.open();t&&u.write('<script>document.domain="'+t+'"<\/script>');u.close();q.location.hash=v}}})();return j})()})(jQuery,this);
