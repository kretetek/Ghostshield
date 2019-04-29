$(function(){

	$("#gallery_captions").submit(function(event){
		var $data = $(this).serialize();
		$.ajax({
			type:"POST",
			url:"/gallery/edit?ajax&rand="+Math.random(),
			data:$data,
			success:function(data){
				console.log(data);

				$array = JSON.parse(data);
				
				if ($array["removal_confirmation"]) {
				
					$('.gallery_caption_input').html($array["removal_confirmation"]);
				
				} else {
				
					$.each($array,function(id, val){
						//alert(id+": "+val);
						//if (i != "upload_email") {
						$.each(val,function(key, val2){
							$('label[for="upload_'+key+"-"+id+'"]').addClass("saved");
							$('#upload_'+key+"-"+id).attr("data-last",val2);
						});
					});
				
					$('#gallery_save #gallery_save_captions').addClass("disabled");
					$('#gallery_save input#gallery_save_captions').val("Information Saved");
					$('#gallery_save a#gallery_save_captions').html("Information Saved");
				
				}
				
				/*$("#gallery_captions input").each(function(i){
					//$(this).attr("readonly", "readonly");
				});
				$("#gallery_captions textarea").each(function(i){
					//$(this).attr("readonly", "readonly");
				});*/
				//alert(data);
				//console.log(data);
			}
		});
		event.preventDefault();
	});
	
	
	$('.gallery_caption_input input').each(function(i){
		$(this).change(function(){
			if($(this).attr("data-last") != $(this).val()) {
				$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
				$('#gallery_save #gallery_save_captions').removeClass("disabled");
				$('#gallery_save input#gallery_save_captions').val("Save Information");
				$('#gallery_save a#gallery_save_captions').html("Save Information");
			}
		});
		$(this).keyup(function(key){
			if($(this).attr("data-last") != $(this).val()) {
				$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
				$('#gallery_save #gallery_save_captions').removeClass("disabled");
				$('#gallery_save input#gallery_save_captions').val("Save Information");
				$('#gallery_save a#gallery_save_captions').html("Save Information");
			}
		});
	});
	$('.gallery_caption_input textarea').each(function(i){
		$(this).change(function(){
			if($(this).attr("data-last") != $(this).val()) {
				$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
				$('#gallery_save #gallery_save_captions').removeClass("disabled");
				$('#gallery_save input#gallery_save_captions').val("Save Information");
				$('#gallery_save a#gallery_save_captions').html("Save Information");
			}
		});
		$(this).keyup(function(key){
			if($(this).attr("data-last") != $(this).val()) {
				$('label[for="'+$(this).attr("id")+'"]').removeClass("saved");
				$('#gallery_save #gallery_save_captions').removeClass("disabled");
				$('#gallery_save input#gallery_save_captions').val("Save Information");
				$('#gallery_save a#gallery_save_captions').html("Save Information");
			}
		});
	});

});