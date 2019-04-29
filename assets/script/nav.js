/*********
*
*   GHOSTSHIELD PRODUCT FINDER
*
*/

var product_names = {
	'9500': 'Lithi-Tek 9500',
	'8505': 'Siloxa-Tek 8505',
	'8500': 'Siloxa-Tek 8500',
	'5505': 'Cryli-Tek 5505',
	'5500': 'Cryli-Tek 5500',
	'5105': 'Gem-Tek 5105',
	'5100': 'Gem-Tek 5100',
	'4500': 'Lithi-Tek 4500',
	'3500': 'Sila-Tek 3500',
	'770':  'Countertop 770',
	'745':  'Polyaspartic 745',
	'660':  'Countertop 660',
	'645':  'Urethane 645',
	'325':  'Epoxy 325'
};
/*
var product_specs = {
	'9500': "Water-based Densifier &amp; Water Repellent<br />(Actives: 50%)",
	'8505': "Water-based Water, Salt &amp; Oil Repellent<br />(Actives: 40%)",
	'8500': "Water-based Water &amp; Salt Repellent<br />(Actives: 40%)",
	'5505': "Solvent-based Deep Color-Enhancer<br />(Actives: 25%)",
	'5500': "Water-based Mild Color-Enhancer<br />(Actives: 25%)",
	'5105': "Durable Deep Color-Enhancer<br />(Actives: 100%)",
	'5100': "Water-based Mild Color-Enhancer<br />(Actives: 25%)",
	'4500': "Water-based Densifier<br />(Actives: 100%)",
	'3500': "Water-based Densifier<br />(Actives: 100%)",
	'770':  "Water Repellent &amp; Stain-Guard",
	'745':  "Industrial Coating",
	'660':  "Water Repellent &amp; Stain-Guard",
	'645':  "Industrial Coating",
	'325':  "Industrial Coating"
};*/

var categories = {
	'applications' : {
		'Basement' : [
			9500, 8510, 8500
		],
		'Concrete_Countertop' : [
			880, 770, 745, 660
		],
		'Driveway__Sidewalk' : [
			9500, 8510, 8505, 8500,
			5505, 5500, 5105,
			5100
		],
		'Garage__Shop_Floor' : [
			9500, 8510, 8505, 8500,
			745, 645, 325
		],
		'Patio__Pool_Deck' : [
			8510, 8505, 8500, 5505,
			5500, 5105, 5100
		],
		'Warehouse__Industrial' : [
			9500, 8510, 8505, 8500,
			4500, 3500, 745,
			645, 325
		]
	},
	'substrates' : {
		'Brick' : [
			8500, 8510, 8505, 5100,
			5105, 5500, 5505
		],
		'Concrete' : [
			325, 645, 745, 880,
			660, 745, 770,
			3500, 5500, 5505,
			8500, 8505, 9500, 8510
		],
		'Cinderblock' : [
			8500, 8510
		],
		'Exposed_Aggregate' : [
			8500, 8505, 5100,
			5105, 5500, 5505, 8510
		],
		'Paver' : [
			8500, 8505, 5100,
			5105, 5500, 5505, 8510
		],
		'Slate' : [
			8500, 8505, 5100,
			5105, 5500, 5505, 8510
		],
		'Stone' : [
			8500, 8505, 5100,
			5105, 5500, 5505, 8510
		],
		'Stucco' : [
			8500, 8510
		],
		'Stamped_Concrete' : [
			8500, 8505, 5100,
			5105, 5500, 5505, 8510
		]
	},
	'chemistry' : {
		'Acrylic' : [
			5500, 5505, 660
		],
		'Epoxy' : [
			325
		],
		'Polyaspartic' : [
			745
		],
		'Silane__Siloxane' : [
			8500, 8505, 770
		],
		'Silicate' : [
			9500, 4500, 3500
		],
		'Siliconate' : [
			9500
		],
		'Silicone' : [
			5100, 5105
		],
		'Urethane' : [
			645
		]
	},
	'finish' : {
		'Natural' : [
			9500, 8505, 8500,
			3500, 770
		],
		'High_Gloss' : [
			5505, 5105, 745,
			645
		],
		'Low_Sheen' : [
			5500, 5100, 660,
			325
		]
	}
};

$(function(){

	/*h = $('#main_nav_button').html();
	$('#main_nav_button').parent().html('<a href="javascript:$(\'#main_nav > ul\').toggleClass(\'open\');" id="main_nav_button">'+h+'</a>');*/

	$('#main_nav_button').css('cursor','pointer');
	$(document).on('click', '#main_nav_button', function(event){
		event.preventDefault();
		$('#main_nav > ul').toggleClass("open");
		$(document.body).toggleClass("clip");
	});

	$('#page_nav_open').css('cursor','pointer');
	$('#page_nav_open').click(function(event){
		event.preventDefault();
		$('#page_nav > ul').toggleClass("open");
	});
	
	$('.page_nav a').click(function(event){
		event.preventDefault();
		$('#page_nav > ul').removeClass("open");
		$('html, body').animate({ scrollTop:$($(this).attr("href")+"_anchor").offset().top+100 }, 1000);
	});

	$("#nav_all_label").attr("data-original-label",$("#nav_all_label").html());

	$("#nav_filters input").change(function(ev) {
		if (ev.target.defaultValue == "all") {
			$("input[name='applications']:checked").prop('checked', false);
			$("input[name='substrates']:checked").prop('checked', false);
			$("input[name='finish']:checked").prop('checked', false);
			$("#nav_all_label").html($("#nav_all_label").attr("data-original-label"));
			nav_product_all_new();
		} else {
			$('#nav_product_list a').addClass("choice");
			$("input[name='all']:checked").prop('checked', false);
			nav_product_filter_new($("input[name='applications']:checked").val(), "applications");
			nav_product_filter_new($("input[name='substrates']:checked").val(), "substrates");
			nav_product_filter_new($("input[name='finish']:checked").val(), "finish");
			$("#nav_all_label").html($("#nav_all_label").attr("data-original-label")+" / Reset");
		}
		ga('send', 'event', {
			eventCategory: 'NavFilters',
			eventAction: 'click',
			eventLabel: ev.target.defaultValue
		});
	});
	
/*	$(".scrollable").niceScroll({
		cursorcolor: "#00F",
		autohidemode: false,
		railalign: 'left',
		railvalign: 'bottom',
		horizrailenabled: false,
		cursorcolor: "rgba(0,0,0,0.3)",
		cursorwidth: '20px',
		cursorborder: '0px solid #000',
		cursorborderradius: 0
	});
	thumb = '<div class="scroll-thumb"><hr /><hr /><hr /></div>';
	$('.nicescroll-rails div').html(thumb);
	$("#ascrail2000").appendTo("#scrollable1");
	$("#ascrail2001").appendTo("#scrollable2");
	//$(".scrollable").getNiceScroll().hide();
	
	new ResizeSensor(jQuery('#search'), function() {
		$("#scrollable2").getNiceScroll().resize();
	});
	*/
	$('<div id="show_filters">Show Filters</div>').appendTo('.nav-menu-products .scrollable');
	$('#show_filters').click(function() {
		$('.nav-menu-products .scrollable').toggleClass("open");
		$(this).html($('.nav-menu-products .scrollable').hasClass("open")?"Hide Filters":"Show Filters");
		$('#nav_products .submenu').toggleClass('dimmed');
	});
	
//	if ($(document.body).hasClass("no-touch")) {
	
		$('.has_submenu > a').click(function(ev) {
			ev.preventDefault();
			close = $(this).parent().hasClass("hover") ? true : false;
			$("#main_nav > ul > li").removeClass("hover");
			if (!$('#main_nav > ul').hasClass("open")) {
				if (!close) { $(document.body).addClass("clip"); } else $(document.body).removeClass("clip");
			}
			if (!close) $(this).parent().addClass("hover");
			//if ($(document.body).hasClass("no-touch")) 
//			$(".scrollable").getNiceScroll().resize();
		});
		
		
		/*$('.has_submenu').click(function(ev) {
			ev.preventDefault();
			if ($(window).width() > 1099) {
//			alert("test");
				$(this).toggleClass("hover");
				//$(".scrollable").getNiceScroll().show();
				$(".scrollable").getNiceScroll().resize();
				//$(".scrollable").getNiceScroll().hide();
			}
		});*/
	
/*	} else {
	
		$('.has_submenu > a').touchstart(function(ev) {
			ev.preventDefault();
			close = $(this).parent().hasClass("hover") ? true : false;
			$("#main_nav ul.open > li").removeClass("hover");
			if (!close) $(this).parent().addClass("hover");
		});
	
	}
*/
	$('.submenu').append('<div class="nav_close"><span>Close</span></div>');
	$('.nav_close').click(function(){
		//$(".scrollable").getNiceScroll().hide();
		$('.has_submenu').removeClass("hover");
	});
	
	$(".submenu").scroll(function() {
		$(".submenu .text-section").css("margin-top", $(this).scrollTop()+"px");
		$(".submenu .nav_close").css("margin-top", $(this).scrollTop()+"px");
	});
	
});


function nav_product_filter_new(choice, list) {
	
	ga('send', 'event', {
		eventCategory: 'NavFiltersApply',
		eventAction: 'click',
		eventLabel: list+" "+choice
	});
	
	if (choice) {

		// manage list of products and update 'applications" text for filter
		//$('#nav_product_list a').attr("data-applications", "No applications advised for current filter.");
	
		$('#nav_product_list a.choice').each(function(){
			$(this).removeClass("choice");
		
			// loop through json and hilite compatible options
			for (var i=0; i<categories[list][choice].length; i++) {
			
				if (categories[list][choice][i] == $(this).attr("data-model")) {
			
					$(this).addClass("choice");
				//	$(this).attr("data-applications",categories[list][choice][i]["applications"]);
			
				}
			
			}
		
			// hover for product info
			/*$(this).hover(function(){
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
			});*/
		
		}); 
	
		// dim non-compatible options
		$('#nav_product_list a').addClass("dim");
		$('#nav_product_list a.choice').each(function(){
			if ($(this).attr("data-original-href") > "") $(this).attr("href", $(this).attr("data-original-href"));
		});
		$('#nav_product_list a.choice').removeClass("dim");
		$('#nav_product_list a.dim').each(function(){
			$(this).attr("href", "javascript:void(0);");
		});
	
		$("#nmt_"+list+" label").addClass("dim");
		$(".menu_tier input[name="+list+"]:checked").parent().removeClass("dim");
	
	//	$('.tier1 li').each(function(){ $('#'+$(this).attr("id")+' span').html($(this).attr("data-original"))});
		//$('#nmt_'+list+' span').html(/*$('#nmt_'+list).attr("data-original")+": "+*/choice.replace("__","/").replace("_"," "));
	
		// update header
		//$('#product_list_h4').html(choice.replace("__","/").replace("_"," ")+" Products");
		//$('#product_list_h4').html("Filtered Product Options");

	}
	
}

function nav_product_all_new(){
	ga('send', 'event', {
		eventCategory: 'NavFiltersApply',
		eventAction: 'click',
		eventLabel: "all"
	});
	
//	$('.tier1 li').each(function(){ $('#'+$(this).attr("id")+' span').html($(this).attr("data-original"))});
	$('#nav_product_list a').addClass("choice");
	$('#nav_product_list a').removeClass("dim");
	$('#nav_product_list a.choice').each(function(){
		if ($(this).attr("data-original-href") > "") $(this).attr("href", $(this).attr("data-original-href"));
	});
	$(".menu_tier input").parent().removeClass("dim");
	/*
	$('#nav_product_list a').each(function(){ $(this).attr("data-applications", product_specs[$(this).attr("data-model")])});
	$('#product_list_h4').html("All Products");
	$('#nav_menu_tiers > ul > li').removeClass("active");
	$('#nav_menu_tiers .tier2').removeClass("active");
	$('#nmt_all').addClass("active");*/
	
}




function nav_product_list(list) {
	if (!$('#nmt_'+list+'_list').hasClass("active")) {
	
		$('#nav_menu_tiers ul.tier2').removeClass("active"); // aka id "nmt_{name}_list"
		
		// actual menu buttons
		$('#nav_menu_tiers > ul > li').removeClass("active");
		$('#nmt_'+list).addClass("active");
	
		var html = '';
		
		// create menu header from button -- kinda sloppy but whatever.
		html += '<li class="clickable type_any selected"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 61.8 62.7" style="enable-background:new 0 0 61.8 62.7;" xml:space="preserve">'+$('#nmt_'+list+' svg').html()+'</svg>Any '+$('#nmt_'+list).attr("data-original")+'</li>';
		
		// build menu from json
		for (var key in categories[list]) {
			if (!categories[list].hasOwnProperty(key)) continue; // skip loop if the property is from prototype
		
			html += '<li class="clickable type_'+key+'" onclick="nav_product_filter(\''+key+'\',\''+list+'\')">'+key.replace("__","/").replace("_"," ");
		
		}
		$('#nmt_'+list+'_list').html(html);
		
		// open menu
		$('#nmt_'+list+'_list').addClass("active");
		
	} else $('#nmt_'+list+'_list').removeClass("active"); // toggle menu closed if open
}



/**
 * Copyright Marc J. Schmidt. See the LICENSE file at the top-level
 * directory of this distribution and at
 * https://github.com/marcj/css-element-queries/blob/master/LICENSE.
 */
;
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define(factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.ResizeSensor = factory();
    }
}(this, function () {

    // Only used for the dirty checking, so the event callback count is limted to max 1 call per fps per sensor.
    // In combination with the event based resize sensor this saves cpu time, because the sensor is too fast and
    // would generate too many unnecessary events.
    var requestAnimationFrame = window.requestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        function (fn) {
            return window.setTimeout(fn, 20);
        };

    /**
     * Iterate over each of the provided element(s).
     *
     * @param {HTMLElement|HTMLElement[]} elements
     * @param {Function}                  callback
     */
    function forEachElement(elements, callback){
        var elementsType = Object.prototype.toString.call(elements);
        var isCollectionTyped = ('[object Array]' === elementsType
            || ('[object NodeList]' === elementsType)
            || ('[object HTMLCollection]' === elementsType)
            || ('undefined' !== typeof jQuery && elements instanceof jQuery) //jquery
            || ('undefined' !== typeof Elements && elements instanceof Elements) //mootools
        );
        var i = 0, j = elements.length;
        if (isCollectionTyped) {
            for (; i < j; i++) {
                callback(elements[i]);
            }
        } else {
            callback(elements);
        }
    }

    /**
     * Class for dimension change detection.
     *
     * @param {Element|Element[]|Elements|jQuery} element
     * @param {Function} callback
     *
     * @constructor
     */
    var ResizeSensor = function(element, callback) {
        /**
         *
         * @constructor
         */
        function EventQueue() {
            var q = [];
            this.add = function(ev) {
                q.push(ev);
            };

            var i, j;
            this.call = function() {
                for (i = 0, j = q.length; i < j; i++) {
                    q[i].call();
                }
            };

            this.remove = function(ev) {
                var newQueue = [];
                for(i = 0, j = q.length; i < j; i++) {
                    if(q[i] !== ev) newQueue.push(q[i]);
                }
                q = newQueue;
            }

            this.length = function() {
                return q.length;
            }
        }

        /**
         * @param {HTMLElement} element
         * @param {String}      prop
         * @returns {String|Number}
         */
        function getComputedStyle(element, prop) {
            if (element.currentStyle) {
                return element.currentStyle[prop];
            } else if (window.getComputedStyle) {
                return window.getComputedStyle(element, null).getPropertyValue(prop);
            } else {
                return element.style[prop];
            }
        }

        /**
         *
         * @param {HTMLElement} element
         * @param {Function}    resized
         */
        function attachResizeEvent(element, resized) {
            if (!element.resizedAttached) {
                element.resizedAttached = new EventQueue();
                element.resizedAttached.add(resized);
            } else if (element.resizedAttached) {
                element.resizedAttached.add(resized);
                return;
            }

            element.resizeSensor = document.createElement('div');
            element.resizeSensor.className = 'resize-sensor';
            var style = 'position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;';
            var styleChild = 'position: absolute; left: 0; top: 0; transition: 0s;';

            element.resizeSensor.style.cssText = style;
            element.resizeSensor.innerHTML =
                '<div class="resize-sensor-expand" style="' + style + '">' +
                    '<div style="' + styleChild + '"></div>' +
                '</div>' +
                '<div class="resize-sensor-shrink" style="' + style + '">' +
                    '<div style="' + styleChild + ' width: 200%; height: 200%"></div>' +
                '</div>';
            element.appendChild(element.resizeSensor);

            if (getComputedStyle(element, 'position') == 'static') {
                element.style.position = 'relative';
            }

            var expand = element.resizeSensor.childNodes[0];
            var expandChild = expand.childNodes[0];
            var shrink = element.resizeSensor.childNodes[1];

            var reset = function() {
                expandChild.style.width  = 100000 + 'px';
                expandChild.style.height = 100000 + 'px';

                expand.scrollLeft = 100000;
                expand.scrollTop = 100000;

                shrink.scrollLeft = 100000;
                shrink.scrollTop = 100000;
            };

            reset();
            var dirty = false;

            var dirtyChecking = function() {
                if (!element.resizedAttached) return;

                if (dirty) {
                    element.resizedAttached.call();
                    dirty = false;
                }

                requestAnimationFrame(dirtyChecking);
            };

            requestAnimationFrame(dirtyChecking);
            var lastWidth, lastHeight;
            var cachedWidth, cachedHeight; //useful to not query offsetWidth twice

            var onScroll = function() {
              if ((cachedWidth = element.offsetWidth) != lastWidth || (cachedHeight = element.offsetHeight) != lastHeight) {
                  dirty = true;

                  lastWidth = cachedWidth;
                  lastHeight = cachedHeight;
              }
              reset();
            };

            var addEvent = function(el, name, cb) {
                if (el.attachEvent) {
                    el.attachEvent('on' + name, cb);
                } else {
                    el.addEventListener(name, cb);
                }
            };

            addEvent(expand, 'scroll', onScroll);
            addEvent(shrink, 'scroll', onScroll);
        }

        forEachElement(element, function(elem){
            attachResizeEvent(elem, callback);
        });

        this.detach = function(ev) {
            ResizeSensor.detach(element, ev);
        };
    };

    ResizeSensor.detach = function(element, ev) {
        forEachElement(element, function(elem){
            if(elem.resizedAttached && typeof ev == "function"){
                elem.resizedAttached.remove(ev);
                if(elem.resizedAttached.length()) return;
            }
            if (elem.resizeSensor) {
                if (elem.contains(elem.resizeSensor)) {
                    elem.removeChild(elem.resizeSensor);
                }
                delete elem.resizeSensor;
                delete elem.resizedAttached;
            }
        });
    };

    return ResizeSensor;

}));