
$(function(){

	/*$('#gallery .gallery a').each(function(event) {
		
		event.preventDefault;
		
	});*/

	// execute above function
	initPhotoSwipeFromDOM('.cpgallery');
	
//alert("test: "+$(".adropzone").length);
//setTimeout('alert("test: "+$("form").length);',5000);
/////var myDropzone = new Dropzone("form.adropzone", { url: $('#gs-dropzone').attr("action") });
//$("#gs-dropzone").dropzone({ url: $('#gs-dropzone').attr("action") });
/*Dropzone.autoDiscover = false;
	//var myDropzone = new Dropzone("form#gsdropzone");
	//$("form.dropzone1").dropzone();

	var myDropzone = $("form.dropzone1").dropzone({});

	myDropzone.on("addedfile", function(file) {
		caption = file.caption == undefined ? "" : file.caption;
		file._captionLabel = Dropzone.createElement("<p>Caption:</p>")
		file._captionBox = Dropzone.createElement("<textarea class='caption' id='"+file.filename+"' type='text' name='caption' class='dropzone_caption'>"+caption+"</textarea>")
		file.previewElement.appendChild(file._captionLabel);
		file.previewElement.appendChild(file._captionBox);
	});
	
	$("#gsdropzone").on('focusout', 'input.dropzone_caption', function() {
		var filename = $(this).attr('id');
		var caption = $(this).attr('value');
		return $.post("/images/" + filename + "/save_caption?caption=" + caption);
	});
	*/

	$('#email_input_button').html('<a href="#" class="hex-button xsmall">Continue</a>');
	
	$('#gsdropzone').on('submit', function(event){ submitEmail(event); });
	
	$('#email_input_button a').click(function(event){ submitEmail(event); });
	
});

function submitEmail(event) {
	event.preventDefault();
	
	if (validateEmail($("#email_input_input").val())){
		if ($('#model_selector') && $('#model_selector').val() < 1) {
			alert("Please select a Ghostshield product to associate your uploads with to continue.");
		} else {
			$('#email_input_input').attr("type","hidden");
			$('#gsdropzone .email_input').attr("data-original-height", $('#gsdropzone .email_input').css("height"));
			$('#gsdropzone .email_input').css("height", 0);

			$('#gsdz-email-confirm').html('Uploading as <em>'+$("#email_input_input").val()+'</em> <span id="expiringEmailChange">(<a href="javascript:changeEmail();">Change</a>)</span>');
			$('#gsdz-email-confirm').addClass("active");
			$('.dz-preview').css("display", $('.dz-preview').attr("data-original-display"));
		}
	} else {
		alert("The email address entered is not valid.");
	}
}

function changeEmail() {
	if ($('#gsdropzone').hasClass("dz-started")) {
		if (window.confirm("Changing your email address will abandon uploaded files. Continue?")) resetEmail();
	} else resetEmail();
}

function resetEmail() {
	$('#email_input_input').attr("type", "text");
	$('#gsdropzone .email_input').css("height", $('#gsdropzone .email_input').attr("data-original-height"));
	$('#gsdz_add').addClass("gsdz_hidden");
	$('.dz-preview').attr("data-original-display", $('.dz-preview').attr("display"));
	$('.dz-preview').css("display", "none");
	$('#gsdz-close').remove();
	$('#gallery_save').removeClass("active");
	$('#gallery_save_all').remove();
	$('#gsdz_confirm').addClass("gsdz_hidden");
	$('#gsdropzone').removeClass("dz-started");
	$('#gsdz-email-confirm').html("");
	$('#gsdz-email-confirm').removeClass("active");
}

function validateEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

var saveCaptions = function(event){
	if (event) {
		var $data = $("#gsdropzone").serialize();
	} else {
		var $data = "";
		$("#gallery_captions-"+arguments[1]+" input").each(function(i){ // #gallery_success
			$data += $(this).attr("name")+"="+encodeURIComponent($(this).val())+"&";
		});
		$("#gallery_captions-"+arguments[1]+" textarea").each(function(i){
			$data += $(this).attr("name")+"="+encodeURIComponent($(this).val())+"&";
		});
	}
	$.ajax({
		type:"POST",
		url:$('#gsdropzone').attr("action") + "?dropzone&gallery_captions&rand="+Math.random(),
		data:$data,
		success:function(data){
			
			if (data.length > 2) {
		
				if (!$('#gsdz-email-confirm').hasClass("notice-served")) {
					$('#gsdz_confirm').removeClass("gsdz_hidden");
				}

				gallerySaveText("<em>Success:</em> Captions and rotation saved. Thanks again for sharing!");
			
				$array = JSON.parse(data);
			
				$.each($array,function(id, val){
					//alert(id+": "+val);
					//if (i != "upload_email") {
					$.each(val,function(key, val2){
						if (key == "rotate") {
							$('#preview'+id).attr("data-saved-rotate", val2);
						} else {
							$('label[for="upload_'+key+"-"+id+'"]').addClass("saved");
							$('#upload_'+key+"-"+id).attr("data-last",val2);
						}
					});
			
					$('#preview'+id).removeClass("dz-edit");
					$('#preview'+id).addClass("dz-saved");
					$('#save-'+id).removeClass("dz-save");
					$('#save-'+id).addClass("dz-editor");
					setTimeout('$("#preview'+id+'").removeClass("dz-saved");', 3300);
				
					/*$("#gallery_success-"+id).addClass("gallery_toggle_closed");
					$("#gallery_success-"+id).removeClass("gallery_toggle_open");
					$('#gallery_toggle_button-'+id).html("+");*/
				});
			
				$('#gallery_save_all').addClass("disabled");
				$('#gallery_save_all').val("Save Complete");
			
			} else alert("An error has occurred saving information about this upload.\n\nPlease contact us if you continue to encounter this error.");			
			
			console.log(data);
			
		}
	});
	if (event) event.preventDefault();
}

//var dropzone;

Dropzone.options.gsdropzone = {
	url: $('#gsdropzone').attr("action") + "?dropzone=true&rand="+Math.random(),
	clickable:'.dz-clickable',
	addRemoveLinks: true,
	acceptedFiles: "video/mp4,video/x-m4v,video/webm,video/mpeg,video/*,image/*",
	thumbnailWidth: 150,
	thumbnailHeight: 150,
	dictDefaultMessage: "Drop files here or click to upload.",
	dictRemoveFileConfirmation: "Are you sure you wish to delete this upload?",
	dictCancelUpload: "Cancel",
	dictRemoveFile: "Delete",
	init: function() {
		var that = this;
		
		this.on("addedfile", function(file) {
			if (!$('#gsdz-email-confirm').hasClass("notice-served")) {
				$('#gsdz_confirm').removeClass("gsdz_hidden");
				//$('#gsdz_confirm').attr("data-timeout", 15000);
				//$('#gsdz_confirm_close span').html("15");
				//setTimeout("timeoutUploadThanks();", 1000);
			}
		
			if (!$('#gsdz-close').length > 0) {
				$('#gsdropzone').append('<a href="javascript:void();" id="gsdz-close"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 11 11" style="enable-background:new 0 0 11 11;" xml:space="preserve"><path class="x1" d="M5.5,3.9l2.1-2.1l1.7,1.7L7.3,5.7l2.1,2.1L7.8,9.2L5.7,7.2L3.5,9.4L1.8,7.7L4,5.5L2,3.4l1.6-1.6L5.5,3.9z"/></svg></a>');
				$('#gsdz-close').click(function(event){
					event.preventDefault();
					if ($('#gsdropzone').hasClass("dz-started")) {
						if (window.confirm("Finished uploading?\n\nYou may manage prior uploads via the confirmation emails.")) {
							$('#email_input_input').val("");
							resetEmail();
						}
					} else {
						$('#email_input_input').val("");
						resetEmail();
					}
				});
			}
			
			//$('#gallery_status').attr("data-total", $('#gallery_status').attr("data-total")*1+1);
			$('#gsdz_add').removeClass("gsdz_hidden");
		}),
		
		this.on("sending", function(file, xhr, formData) {
			// Will send the filesize along with the file as POST data.
			this.options.url += "&random="+Math.random();
			// not working ^^
		}),
		
		this.on("canceled", function(file) {
			/*
				e.preventDefault();
				// Referencing file here as closure
				that.cancelUpload(file);
				$('a#file-'+file.name.replace(/[^A-z0-9\-\_]+/g, "")).remove();*/
			//$('#gallery_status').attr("data-total", $('#gallery_status').attr("data-total")*1-1);
		}),
		
		this.on("removedfile", function(file) {
			//$('a#file-'+file.name.replace(/[^A-z0-9\-\_]+/g, "")).remove();
			//var $that = $(this);
			//event.preventDefault();
			$.ajax({
				type:"POST",
				url:this.options.url,// should be: http://dev.ghostshield.com/gallery/edit/?e=0hHla%2BwTqXuW84v%2Bbk%2BTHQzFlyXMUVHyksPLI3CGDXg%3D&k=wBlTSHi9PpqJW6ICkZdyLegI4gaCFA4sIy2l54gdNrOdUndnGXqY4BRO1%2Bl4UEeB&remove
				data:{ ajax:"true", e:file._email, k:file._key, rand:Math.random(), remove:"remove" },
				success:function(data){
					$array = JSON.parse(data);
			
					gallerySaveText("<em>Notice:</em> A file has been removed.");
					
					if ($array["remove"]){
						// remove the editing portion
						//$that.parent().parent().remove();
						if($('#gallery_captions').children().length < 2) {
							$('#gallery_save_all').remove();
							$('#gallery_captions').removeClass("active");
						}
					} else {
						alert($array["removal_confirmation"]);
					}
//					$.each($array,function(id, val){
//						alert(id+": "+val);
						//if (i != "upload_email") {
//						$.each(val,function(key, val2){
//							$('label[for="upload_'+key+"-"+id+'"]').addClass("saved");
//							$('#upload_'+key+"-"+id).attr("data-last",val2);
//						});
//					});
				}
			});
		
		}),
		
		this.on("error", function(file, response) {
			$('#gallery_status').attr("data-error", $('#gallery_status').attr("data-error")*1+1);
			
			gallerySaveText("<em>Error:</em> Our apologies, but an error occurred. Try again?");
		}),
		
		this.on("success", function(file, response) {
			//console.log(response);
			
			gallerySaveText("<em>Success:</em> Upload finished; thank you! Please consider adding captions?");

			$('#gallery_status').attr("data-total", $('#gallery_status').attr("data-total")*1+1);
			
			var html_message = '<div class="gallery_status_inner"><em>You have uploaded '+$('#gallery_status').attr("data-total")+' file'+($('#gallery_status').attr("data-total") > 1 ? 's' : '');
				if ($('gallery_status').attr("data-error") > 0) html_message += ' with '+$('#gallery_status').attr("data-error")+' error'+($('#gallery_status').attr("data-error") > 1 ? 's' : '');
				html_message += '.</em> You may continue to upload files or provide public information about your submissions below.</div>';
//			$('#gallery_status').html(html_message);
			//alert(file);
			//console.debug(file);
			
			//$('.page_content').addClass("dim");
			//if (!$('#gsmodal').length) $(document.body).append('<div id="gsmodal"><div id="gsmodal_inner"></div></div>');
			$('a#file-'+file.name.replace(/[^A-z0-9\-\_]+/g, "")).remove();
			
/*			if (!$('#gallery_captions').hasClass("active")) {
				$('#gallery_captions').addClass("active");
				$('#gallery_captions').append('<div id="gallery_save"><input type="submit" value="Save Information" class="hex-button xsmall" id="gallery_save_captions" /></div>');
				$("#gallery_captions").submit(function(event) { saveCaptions(event); });
			}*/
			
			$response = JSON.parse(response); 
			
			file._id	= $response["id"];
			file._email	= $response["email"];
			file._key	= $response["key"];
			file._type	= $response["type"];
			
			//console.log(file);
			$(file.previewElement).append('<a class="dz-save" id="save-'+file._id+'" href="javascript:void(\'save\');" data-id='+file._id+'>Save</a>');
			if ($response["type"] != "Video") $(file.previewElement).append('<a class="dz-rotate" id="rotate-'+file._id+'" href="javascript:void(\'rotate\');" data-id='+file._id+'>Rotate</a>');
			
			file.previewElement.id = "preview"+file._id;
			$(file.previewElement).attr("data-rotate", "0");
			
			$('#save-'+file._id).click(function(){
				if ($(this).hasClass("dz-save")) {
					saveCaptions(false, $(this).attr("data-id"));
					$(this).removeClass("dz-save");
					$(this).addClass("dz-editor");
				} else {
					//saveCaptions(false, $(this).attr("data-id"));
					$('#preview'+$(this).attr("data-id")).addClass("dz-edit");
					$(this).removeClass("dz-editor");
					$(this).addClass("dz-save");
				}
			});
			
			$('#rotate-'+file._id).click(function(){
				var currot = $("#preview"+$(this).attr("data-id")).attr("data-rotate") * 1 + 90;
				$("#preview"+$(this).attr("data-id")+" .dz-image img").animate({rotate: currot}, 200, 'linear');
				if (currot == 360) currot = 0;
				$("#preview"+$(this).attr("data-id")).attr("data-rotate", currot);
				$('#upload_rotate-'+$(this).attr("data-id")).val(currot);
				// might be nice to check $('#preview'+id).attr("data-saved-rotate") to see if
				// rotation is same as saved and dim the buttons when it's unchanged but
				// seems like a total headache.
				$('#save-'+$(this).attr("data-id")).removeClass("dz-editor");
				$('#save-'+$(this).attr("data-id")).addClass("dz-save");
				$('#gallery_save_all').removeClass("disabled");
				$('#gallery_save_all').val("Save All");
			});
			
			file._form = $($response["form"]);
			//alert(file.previewElement.id);
			$(file.previewElement).append($response["form"]);
			setTimeout('$("#'+file.previewElement.id+'").addClass("dz-edit");', 2000);
			
			if (!$('#gallery_save_all').length > 0) {
				//$('<div id="gallery_save"><input type="submit" value="Save All" class="hex-button xsmall" id="gallery_save_all" /></div>').insertBefore("#gsdz_confirm");
				$('#gallery_save').append('<input type="submit" value="Save All" class="hex-button xsmall" id="gallery_save_all" />');
				$('#gallery_save').addClass("active");
				$("#gallery_save_all").click(function(event) { saveCaptions(event); });
				$("#gsdropzone").submit(function(event) { saveCaptions(event); });
			}
			
/*			$($response["html"]).insertBefore($('#gallery_save'));
			
			$('a.remove_a').each(function(i){
				$(this).on('click', function(event) {
					var $that = $(this);
					event.preventDefault();
					$.ajax({
						type:"POST",
						url:$(this).attr("href"),
						data:{ ajax:"true", rand:Math.random(), remove:"remove" },
						success:function(data){
							$array = JSON.parse(data);
							if ($array["remove"]){
								$that.parent().parent().remove();
								if($('#gallery_captions').children().length < 2) {
									$('#gallery_save').remove();
									$('#gallery_captions').removeClass("active");
								}
							} else {
								alert($array["removal_confirmation"]);
							}
		//					$.each($array,function(id, val){
		//						alert(id+": "+val);
								//if (i != "upload_email") {
		//						$.each(val,function(key, val2){
		//							$('label[for="upload_'+key+"-"+id+'"]').addClass("saved");
		//							$('#upload_'+key+"-"+id).attr("data-last",val2);
		//						});
		//					});
						}
					});
				});
				$(this).removeClass("remove_a");
			});
			$('.gallery_toggle.noclickevent').click(function(){
				$(this).parent().toggleClass("gallery_toggle_closed");
				$(this).parent().toggleClass("gallery_toggle_open");
				if ($(this).parent().hasClass("gallery_toggle_open")) {
					$('#gallery_toggle_button-'+$(this).attr("data-id")).html("-");
				} else {
					$('#gallery_toggle_button-'+$(this).attr("data-id")).html("+");
					saveCaptions(false, $(this).attr("data-id"));
				}
			});
			$('.gallery_toggle.noclickevent').removeClass("noclickevent");*/
		}),
		
		this.on("queuecomplete", function(a) {
			$('.gallery_caption_input input').each(function(i){
				if ($(this).attr("hasOnChange") != "true") {
					$(this).change(function(){
						if($(this).attr("data-last") != $(this).val()) {
							$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
							$('#gallery_save_all').removeClass("disabled");
							$('#gallery_save_all').val("Save All");
						}
					});
					$(this).keyup(function(key){
						if($(this).attr("data-last") != $(this).val()) {
							$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
							$('#gallery_save_all').removeClass("disabled");
							$('#gallery_save_all').val("Save All");
						//	console.log(key.which);
						}
					});
					$(this).attr("hasOnChange", "true");
				}
			});
			$('.gallery_caption_input textarea').each(function(i){
				if ($(this).attr("hasOnChange") != "true") {
					$(this).change(function(){
						if($(this).attr("data-last") != $(this).val()) {
							$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
							$('#gallery_save_all').removeClass("disabled");
							$('#gallery_save_all').val("Save All");
						}
					});
					$(this).keyup(function(key){
						if($(this).attr("data-last") != $(this).val()) {
							$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
							$('#gallery_save_all').removeClass("disabled");
							$('#gallery_save_all').val("Save All");
						//	console.log(key.which);
						}
					});
					$(this).attr("hasOnChange", "true");
				}
			});
		}),
		
		this.on('focusout', 'textarea.dropzone_caption', function() {
			/*var filename = $(this).attr('id');
			var caption = $(this).attr('value');
			alert(filename);*/
			///return $.post("/images/" + filename + "/save_caption?model="+  +"&caption=" + caption);
		})
	},
	accept: function(file, done) {
		//alert("test: "+file);
		done();
	}
};

function gallerySaveText(text) {
	$('#gallery_save_text').html(text);
	$('#gallery_save_text').removeClass("inactive");
	//$('#gsdz_confirm_close span').html("15");
	$('#gallery_save_text').attr("data-timeout", 10000);
	setTimeout("timeoutUploadThanks();", 1000);
}

function timeoutUploadThanks() {

	var to = $('#gallery_save_text').attr("data-timeout");

	if (to > 999) {
		//$('#gsdz_confirm_close span').html(Math.floor(to/1000));
		to = Math.floor(to - 1000);
		$('#gallery_save_text').attr("data-timeout", to);
		setTimeout("timeoutUploadThanks();", 1000);
	} else {
		$('#gallery_save_text').addClass("inactive");
	}

}

var initPhotoSwipeFromDOM = function(gallerySelector) {

	var removeVideo = function() {
		if ($('.videoHolder').length > 0) { 
			if ($('#video').length > 0) {
				//$('video')[0].pause();
				//$('video')[0].src = "";
				$('.videoHolder').remove();
				$('.pswp__zoom-wrap img').css('visibility','visible');
			} else {
				$('.videoHolder').remove();
			}
		}
	}

	var detectVideo = function(gallery) {
		//var src = gallery.currItem.src;
		//if (src.indexOf('video')>= 0) {
		//console.log(gallery.currItem);
		if (gallery.currItem.video) {
			addVideo(gallery.currItem);
			updateVideoPosition(gallery);
		}
	}

	var addVideo = function(item, vp) {
		var videofile = item.src.split(".");
		var v = $('<div />', {
			class:'videoHolder',
			css : ({ 'position': 'absolute', 'width':item.w, 'height':item.h })
		});
		
		var is_mobile = false;

		if( $('#mobile-check').css('display') == 'none') {
			is_mobile = true;
		}
		
		if (is_mobile) {
		
			v.one('click touchstart', (function() {
				/*var playerCode = '<video id="video" width="'+item.w+'" height="'+item.h+'" autoplay controls>' +
				'<source src="'+item.video+'.mp4" type="video/mp4"></source>' +
				'<source src="'+item.video+'.webm" type="video/webm"></source>' +
				'</video>';*/
				playerCode = '<iframe id="video" width="560" height="315" src="'+item.video+'" frameborder="0" allowfullscreen></iframe>';
				$(this).html(playerCode);
				$('.pswp__zoom-wrap img').css('visibility', 'hidden');

			}));
			
		} else {
		
			playerCode = '<iframe id="video" width="560" height="315" src="'+item.video+'" frameborder="0" allowfullscreen></iframe>';
			v.html(playerCode);
			$('.pswp__zoom-wrap img').css('visibility', 'hidden');
			
		}
		
		v.appendTo('.pswp__scroll-wrap');
	}

	var updateVideoPosition = function(o) {
		var item = o.currItem;
		var vp = o.viewportSize;
		var top = (vp.y - item.h)/2;
		var left = (vp.x - item.w)/2;
		$('.videoHolder').css({ position:'absolute', top:top, left:left });
	}

	// parse slide data (url, title, size ...) from DOM elements 
	// (children of gallerySelector)
	var parseThumbnailElements = function(el) {
		var thumbElements = el.childNodes,
			numNodes = thumbElements.length,
			items = [],
			figureEl,
			linkEl,
			size,
			item;

		for (var i = 0; i < numNodes; i++) {

			figureEl = thumbElements[i]; // <figure> element

			// include only element nodes 
			if (figureEl.nodeType !== 1) {
				continue;
			}

			linkEl = figureEl.children[0]; // <a> element

			size = linkEl.getAttribute('data-size').split('x');

			// create slide object // modified to handle videos
			item = {
				src: (linkEl.getAttribute('data-videoplaceholder') ? linkEl.getAttribute('data-videoplaceholder') : linkEl.getAttribute('href')),
				w: parseInt(size[0], 10),
				h: parseInt(size[1], 10)
			};
			
			if (linkEl.getAttribute('data-videoplaceholder')) item.video = linkEl.getAttribute('href');

			if (figureEl.children.length > 1) {
				// <figcaption> content
				item.title = figureEl.children[1].innerHTML;
			}

			if (linkEl.children.length > 0) {
				// <img> thumbnail element, retrieving thumbnail url
				item.msrc = linkEl.children[0].getAttribute('src');
			} 

			item.el = figureEl; // save link to element for getThumbBoundsFn
			items.push(item);
		}

		return items;
	};

	// find nearest parent element
	var closest = function closest(el, fn) {
		return el && ( fn(el) ? el : closest(el.parentNode, fn) );
	};

	// triggers when user clicks on thumbnail
	var onThumbnailsClick = function(e) {
		e = e || window.event;
		e.preventDefault ? e.preventDefault() : e.returnValue = false;

		var eTarget = e.target || e.srcElement;

		// find root element of slide
		var clickedListItem = closest(eTarget, function(el) {
			return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
		});

		if (!clickedListItem) {
			return;
		}

		// find index of clicked item by looping through all child nodes
		// alternatively, you may define index via data- attribute
		var clickedGallery = clickedListItem.parentNode,
			childNodes = clickedListItem.parentNode.childNodes,
			numChildNodes = childNodes.length,
			nodeIndex = 0,
			index;

		for (var i = 0; i < numChildNodes; i++) {
			if (childNodes[i].nodeType !== 1) { 
				continue; 
			}

			if (childNodes[i] === clickedListItem) {
				index = nodeIndex;
				break;
			}
			nodeIndex++;
		}
		
		if (index >= 0) {
			// open PhotoSwipe if valid index found
			openPhotoSwipe( index, clickedGallery );
		}
		return false;
	};

	// parse picture index and gallery index from URL (#&pid=1&gid=2)
	var photoswipeParseHash = function() {
		var hash = window.location.hash.substring(1),
		params = {};

		if (hash.length < 5) {
			return params;
		}

		var vars = hash.split('&');
		for (var i = 0; i < vars.length; i++) {
			if (!vars[i]) {
				continue;
			}
			var pair = vars[i].split('=');  
			if (pair.length < 2) {
				continue;
			}           
			params[pair[0]] = pair[1];
		}

		if (params.gid) {
			params.gid = parseInt(params.gid, 10);
		}

		return params;
	};

	var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
		var pswpElement = document.querySelectorAll('.pswp')[0],
			gallery,
			options,
			items;

		items = parseThumbnailElements(galleryElement);

		// define options (if needed)
		options = {

			// define gallery index (for URL)
			galleryUID: galleryElement.getAttribute('data-pswp-uid'),

			getThumbBoundsFn: function(index) {
				// See Options -> getThumbBoundsFn section of documentation for more info
				var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
					pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
					rect = thumbnail.getBoundingClientRect(); 

				return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			}

		};

		// PhotoSwipe opened from URL
		if (fromURL) {
			if (options.galleryPIDs) {
				// parse real index when custom PIDs are used 
				// http://photoswipe.com/documentation/faq.html#custom-pid-in-url
				for (var j = 0; j < items.length; j++) {
					if(items[j].pid == index) {
						options.index = j;
						break;
					}
				}
			} else {
				// in URL indexes start from 1
				options.index = parseInt(index, 10) - 1;
			}
		} else {
			options.index = parseInt(index, 10);
		}

		// exit if index not found
		if ( isNaN(options.index) ) {
			return;
		}

		if (disableAnimation) {
			options.showAnimationDuration = 0;
		}

		// Pass data to PhotoSwipe and initialize it
		gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
		gallery.init();
		
		/* custom video stuff here down */
		gallery.listen('afterChange', function() {
			detectVideo(gallery);
		});

		gallery.listen('beforeChange', function() {
			removeVideo();
		});
		
		gallery.listen('resize', function() { 
			if ($('.videoHolder').length > 0) updateVideoPosition(gallery);
		});
		
		gallery.listen('close', function() {
			removeVideo();
		});

		detectVideo(gallery);
	};

	// loop through all gallery elements and bind events
	var galleryElements = document.querySelectorAll( gallerySelector );

	for (var i = 0, l = galleryElements.length; i < l; i++) {
		galleryElements[i].setAttribute('data-pswp-uid', i+1);
		galleryElements[i].onclick = onThumbnailsClick;
	}

	// Parse URL and open gallery if it contains #&pid=3&gid=1
	var hashData = photoswipeParseHash();
	if (hashData.pid && hashData.gid) {
		openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
	}

};

/* 
 * JQuery CSS Rotate property using CSS3 Transformations
 * Copyright (c) 2011 Jakub Jankiewicz  <http://jcubic.pl>
 * licensed under the LGPL Version 3 license.
 * http://www.gnu.org/licenses/lgpl.html
 */
(function($) {
    function getTransformProperty(element) {
        var properties = ['transform', 'WebkitTransform',
                          'MozTransform', 'msTransform',
                          'OTransform'];
        var p;
        while (p = properties.shift()) {
            if (element.style[p] !== undefined) {
                return p;
            }
        }
        return false;
    }
    $.cssHooks['rotate'] = {
        get: function(elem, computed, extra){
            var property = getTransformProperty(elem);
            if (property) {
                return elem.style[property].replace(/.*rotate\((.*)deg\).*/, '$1');
            } else {
                return '';
            }
        },
        set: function(elem, value){
            var property = getTransformProperty(elem);
            if (property) {
                value = parseInt(value);
                $(elem).data('rotatation', value);
                if (value == 0) {
                    elem.style[property] = '';
                } else {
                    elem.style[property] = 'rotate(' + value%360 + 'deg)';
                }
            } else {
                return '';
            }
        }
    };
    $.fx.step['rotate'] = function(fx){
        $.cssHooks['rotate'].set(fx.elem, fx.now);
    };
})(jQuery);
