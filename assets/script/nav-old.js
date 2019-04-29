/*********
*
*   GHOSTSHIELD PRODUCT FINDER
*
*/

var product_names = {
	'9500': 'Lithi-Tek LS <span>9500</span>',
	'8505': 'Siloxa-Tek <span>8505</span>',
	'8500': 'Siloxa-Tek <span>8500</span>',
	'5505': 'Cryli-Tek <span>5505</span>',
	'5500': 'Cryli-Tek <span>5500</span>',
	'5105': 'Gem-Tek <span>5105</span>',
	'5100': 'Gem-Tek <span>5100</span>',
	'4500': 'Lithi-Tek <span>4500</span>',
	'3500': 'Sila-Tek <span>3500</span>',
	'770':  'Countertop <span>770</span>',
	'745':  'Polyaspartic <span>745</span>',
	'660':  'Countertop <span>660</span>',
	'645':  'Urethane <span>645</span>',
	'325':  'Epoxy <span>325</span>'
};

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
};

var choices = {
	'applications': '',
	'substrates': '',
	'chemistry': '',
	'finish': ''
};

var categories = {
	'applications' : {
		'Basement' : [
			{
				'product': 9500,
				'applications': "Floor, Poured Wall"
			},
			{
				'product': 8500,
				'applications': "Floor, Poured Wall, Cinderblock Wall"
			}
		],
		'Concrete_Countertop' : [
			{
				'product': 770,
				'applications': "Concrete Countertop"
			},
			{
				'product': 745,
				'applications': "Concrete Countertop"
			},
			{
				'product': 660,
				'applications': "Concrete Countertop"
			}
		],
		'Driveway__Sidewalk' : [
			{
				'product': 9500,
				'applications': "Concrete Driveway/Sidewalk"
			},
			{
				'product': 8505,
				'applications': "Concrete, Exposed Aggregate, or Stamped Concrete Driveway/Sidewalk"
			},
			{
				'product': 8500,
				'applications': "Concrete, Exposed Aggregate, or Stamped Concrete Driveway/Sidewalk"
			},
			{
				'product': 5505,
				'applications': "Concrete, Exposed Aggregate, or Stamped Concrete Driveway/Sidewalk"
			},
			{
				'product': 5500,
				'applications': "Concrete, Exposed Aggregate, or Stamped Concrete Driveway/Sidewalk"
			},
			{
				'product': 5105,
				'applications': "Stamped Concrete Driveway/Sidewalk"
			},
			{
				'product': 5100,
				'applications': "Stamped Concrete Driveway/Sidewalk"
			}
		],
		'Garage__Shop_Floor' : [
			{
				'product': 9500,
				'applications': "Garage, Shop Floor"
			},
			{
				'product': 8505,
				'applications': "Garage, Shop Floor"
			},
			{
				'product': 8500,
				'applications': "Garage, Shop Floor"
			},
			{
				'product': 745,
				'applications': "Garage, Shop Floor"
			},
			{
				'product': 645,
				'applications': "Garage, Shop Floor"
			},
			{
				'product': 325,
				'applications': "Garage, Shop Floor"
			}
		],
		'Patio__Pool_Deck' : [
			{
				'product': 8505,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			},
			{
				'product': 8500,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			},
			{
				'product': 5505,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			},
			{
				'product': 5500,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			},
			{
				'product': 5105,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			},
			{
				'product': 5100,
				'applications': "Poured Concrete, Stamped Concrete, or Exposed Aggregate Patio/Pool Deck"
			}
		],
		'Warehouse__Industrial' : [
			{
				'product': 9500,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 8505,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 8500,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 4500,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 3500,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 745,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 645,
				'applications': "Warehouse/Industrial"
			},
			{
				'product': 325,
				'applications': "Warehouse/Industrial"
			}
		]
	},
	'substrates' : {
		'Brick' : [
			{
				'product': 8500,
				'applications': "Interior &amp; Exterior Brick"
			},
			{
				'product': 8505,
				'applications': "Interior &amp; Exterior Brick"
			},
			{
				'product': 5100,
				'applications': "Interior Brick"
			},
			{
				'product': 5105,
				'applications': "Interior Brick"
			},
			{
				'product': 5500,
				'applications': "Interior Brick"
			},
			{
				'product': 5505,
				'applications': "Interior Brick"
			}
		],
		'Concrete' : [
			{
				'product': 325,
				'applications': "Garage, Warehouse"
			},
			{
				'product': 645,
				'applications': "Garage, Warehouse"
			},
			{
				'product': 745,
				'applications': "Garage, Warehouse"
			},
			{
				'product': 660,
				'applications': "Concrete Countertops"
			},
			{
				'product': 745,
				'applications': "Concrete Countertops"
			},
			{
				'product': 770,
				'applications': "Concrete Countertops"
			},
			{
				'product': 3500,
				'applications': "Warehouse"
			},
			{
				'product': 5500,
				'applications': "Driveway, Patio, Pool Deck"
			},
			{
				'product': 5505,
				'applications': "Driveway, Patio, Pool Deck"
			},
			{
				'product': 8500,
				'applications': "All Applications (Except Countertops)"
			},
			{
				'product': 8505,
				'applications': "Driveway, Garage, Patio, Pool Deck, Warehouse"
			},
			{
				'product': 9500,
				'applications': "Basement Floor, Poured Wall, Driveway, Garage, Warehouse"
			}
		],
		'Cinderblock' : [
			{
				'product': 8500,
				'applications': "All Applications"
			}
		],
		'Exposed_Aggregate' : [
			{
				'product': 8500,
				'applications': "All Applications"
			},
			{
				'product': 8505,
				'applications': "All Applications"
			},
			{
				'product': 5100,
				'applications': "All Applications"
			},
			{
				'product': 5105,
				'applications': "All Applications"
			},
			{
				'product': 5500,
				'applications': "All Applications"
			},
			{
				'product': 5505,
				'applications': "All Applications"
			}
		],
		'Paver' : [
			{
				'product': 8500,
				'applications': "All Applications"
			},
			{
				'product': 8505,
				'applications': "All Applications"
			},
			{
				'product': 5100,
				'applications': "All Applications"
			},
			{
				'product': 5105,
				'applications': "All Applications"
			},
			{
				'product': 5500,
				'applications': "All Applications"
			},
			{
				'product': 5505,
				'applications': "All Applications"
			}
		],
		'Slate' : [
			{
				'product': 8500,
				'applications': "All Applications"
			},
			{
				'product': 8505,
				'applications': "All Applications"
			},
			{
				'product': 5100,
				'applications': "All Applications"
			},
			{
				'product': 5105,
				'applications': "All Applications"
			},
			{
				'product': 5500,
				'applications': "All Applications"
			},
			{
				'product': 5505,
				'applications': "All Applications"
			}
		],
		'Stone' : [
			{
				'product': 8500,
				'applications': "All Applications"
			},
			{
				'product': 8505,
				'applications': "All Applications"
			},
			{
				'product': 5100,
				'applications': "All Applications"
			},
			{
				'product': 5105,
				'applications': "All Applications"
			},
			{
				'product': 5500,
				'applications': "All Applications"
			},
			{
				'product': 5505,
				'applications': "All Applications"
			}
		],
		'Stucco' : [
			{
				'product': 8500,
				'applications': "All Applications"
			}
		],
		'Stamped_Concrete' : [
			{
				'product': 8500,
				'applications': "All Applications"
			},
			{
				'product': 8505,
				'applications': "All Applications"
			},
			{
				'product': 5100,
				'applications': "All Applications"
			},
			{
				'product': 5105,
				'applications': "All Applications"
			},
			{
				'product': 5500,
				'applications': "All Applications"
			},
			{
				'product': 5505,
				'applications': "All Applications"
			}
		]
	},
	'chemistry' : {
		'Acrylic' : [
			{
				'product': 5500,
				'applications': product_specs[5500]
			},
			{
				'product': 5505,
				'applications': product_specs[5505]
			},
			{
				'product': 660,
				'applications': product_specs[660]
			}
		],
		'Epoxy' : [
			{
				'product': 325,
				'applications': product_specs[325]
			}
		],
		'Polyaspartic' : [
			{
				'product': 745,
				'applications': product_specs[745]
			}
		],
		'Silane__Siloxane' : [
			{
				'product': 8500,
				'applications': product_specs[8500]
			},
			{
				'product': 8505,
				'applications': product_specs[8505]
			},
			{
				'product': 770,
				'applications': product_specs[770]
			}
		],
		'Silicate' : [
			{
				'product': 9500,
				'applications': product_specs[9500]
			},
			{
				'product': 4500,
				'applications': product_specs[4500]
			},
			{
				'product': 3500,
				'applications': product_specs[3500]
			}
		],
		'Siliconate' : [
			{
				'product': 9500,
				'applications': product_specs[9500]
			}
		],
		'Silicone' : [
			{
				'product': 5100,
				'applications': product_specs[5100]
			},
			{
				'product': 5105,
				'applications': product_specs[5105]
			}
		],
		'Urethane' : [
			{
				'product': 645,
				'applications': product_specs[645]
			}
		]
	},
	'finish' : {
		'Natural' : [
			{
				'product': 9500,
				'applications': product_specs[9500]
			},
			{
				'product': 8505,
				'applications': product_specs[8505]
			},
			{
				'product': 8500,
				'applications': product_specs[8500]
			},
			{
				'product': 3500,
				'applications': product_specs[3500]
			},
			{
				'product': 770,
				'applications': product_specs[770]
			}
		],
		'High_Gloss' : [
			{
				'product': 5505,
				'applications': product_specs[5505]
			},
			{
				'product': 5105,
				'applications': product_specs[5105]
			},
			{
				'product': 745,
				'applications': product_specs[745]
			},
			{
				'product': 645,
				'applications': product_specs[645]
			}
		],
		'Low_Sheen' : [
			{
				'product': 5500,
				'applications': product_specs[5500]
			},
			{
				'product': 5100,
				'applications': product_specs[5100]
			},
			{
				'product': 660,
				'applications': product_specs[660]
			},
			{
				'product': 325,
				'applications': product_specs[325]
			}
		]
	}
};

$(function(){
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
	});

	$('.has_submenu').hover(function(){ $(this).addClass("hover"); }, function(){ $(this).removeClass("hover"); });
	$('.submenu').append('<div class="nav_close"><span>Close</span></div>');
	$('.nav_close').click(function(){ $('.has_submenu').removeClass("hover"); });
	
	$(".submenu").scroll(function() {
		$(".submenu .text-section").css("margin-top", $(this).scrollTop()+"px");
		$(".submenu .nav_close").css("margin-top", $(this).scrollTop()+"px");
	});
});


function nav_product_filter_new(choice, list) {
	
	if (choice) {
	
		choices[list] = choice; // ?
	
		// manage list of products and update 'applications" text for filter
		//$('#nav_product_list a').attr("data-applications", "No applications advised for current filter.");
	
		$('#nav_product_list a.choice').each(function(){
			$(this).removeClass("choice");
		
			// loop through json and hilite compatible options
			for (var i=0; i<categories[list][choice].length; i++) {
			
				if (categories[list][choice][i]["product"] == $(this).attr("data-model")) {
			
					$(this).addClass("choice");
					$(this).attr("data-applications",categories[list][choice][i]["applications"]);
			
				}
			
			}
		
			// hover for product info
			$(this).hover(function(){
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
	
//	$('.tier1 li').each(function(){ $('#'+$(this).attr("id")+' span').html($(this).attr("data-original"))});
	$('#nav_product_list a').addClass("choice");
	$('#nav_product_list a').removeClass("dim");
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

function nav_product_filter(choice, list) {
	
	choices[list] = choice;

	// highlight chosen menu option
	$('.tier2 li').removeClass("active");
	$('#nmt_'+list+'_list .type_'+choice).addClass("active");
	
	// manage list of products and update 'applications" text for filter
	$('#nav_product_list a').removeClass("choice");
	$('#nav_product_list a').attr("data-applications", "No applications advised for current filter.");
	
	$('#nav_product_list a.choice').each(function(){
		$(this).removeClass("choice");
		
		// loop through json and hilite compatible options
		for (var i=0; i<categories[list][choice].length; i++) {
			
			if (categories[list][choice][i]["product"] == $(this).attr("data-model")) {
			
				$(this).addClass("choice");
				$(this).attr("data-applications",categories[list][choice][i]["applications"]);
			
			}
			
		}
		
		// hover for product info
		$(this).hover(function(){
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
	
//	$('.tier1 li').each(function(){ $('#'+$(this).attr("id")+' span').html($(this).attr("data-original"))});
	$('#nmt_'+list+' span').html(/*$('#nmt_'+list).attr("data-original")+": "+*/choice.replace("__","/").replace("_"," "));
	
	// update header
	//$('#product_list_h4').html(choice.replace("__","/").replace("_"," ")+" Products");
	$('#product_list_h4').html("Filtered Product Options");

}

function nav_product_all(){
	
//	$('.tier1 li').each(function(){ $('#'+$(this).attr("id")+' span').html($(this).attr("data-original"))});
	$('#nav_product_list a').addClass("choice");
	$('#nav_product_list a').removeClass("dim");
	/*
	$('#nav_product_list a').each(function(){ $(this).attr("data-applications", product_specs[$(this).attr("data-model")])});
	$('#product_list_h4').html("All Products");
	$('#nav_menu_tiers > ul > li').removeClass("active");
	$('#nav_menu_tiers .tier2').removeClass("active");
	$('#nmt_all').addClass("active");*/
	
}

function nav_product_sort(list) {
	
	products = categories[list];
	
	var html = '';
	
	$('#nav_menu_tiers > ul > li').removeClass("active");
	$('#nmt_'+list).addClass("active");
	
	if (list == "all") {
		
		$('#product_list_h4').html("All Products");
	
		html += '<div class="subset"><ul>';
		
		for (var key in product_names) {
			if (!product_names.hasOwnProperty(key)) continue;
			html += '<li><a href="product.php?model='+key+'">'+product_names[key]+'</a></li>';
		}
		
		html += '</ul></div>';
	
	} else {
	
		for (var key in products) {
			if (!products.hasOwnProperty(key)) continue;
		
			html += '<div class="subset"><h4>'+key+'</h4><ul>';
		
			var obj = products[key];
			for (var i=0; i<products[key].length; i++) {
				//if (!obj.hasOwnProperty(property)) continue; // skip loop if the property is from prototype
			
				html += '<li><a href="product.php?model='+products[key][i]["product"]+'">'+product_names[products[key][i]["product"]]+'</a></li>';
			}
		
			html += '</ul></div>';
		}

	}
	
	$('#nav_product_list').html(html);
}
