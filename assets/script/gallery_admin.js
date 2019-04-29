var afterPrint = function() {
	$("#before_print").removeClass("default_button");
	$("#after_print").addClass("default_button");
};

function pop(url) {
	window.location = url;
}

var qar_timeout = Array();

function handleQAR(el) {
	var that = this;
	if (typeof(qar_timeout[el])) clearTimeout(qar_timeout[el]);
	$("#qar_response_"+el).remove();
	//$("#qar_form_"+el).submit();
	$.post($("#qar_form_"+el).attr("action"), $("#qar_form_"+el).serialize()).done(function(){
		//alert("Test:"+that);
		$("#qar_form_"+el).append(' <span class="qar_reponse" id="qar_response_'+el+'">(Saved)</span>');
		$('#qar_response_'+el).fadeIn();
		qar_timeout[el] = setTimeout('$("#qar_response_'+el+'").fadeOut(function(){$(this).remove();});', 2500);
	});
}

$(function(){

	$('#gallery_tags').tagEditor(/*{removeDuplicates:false}*/);

	$('form.quick_ajax_response').each(function(el){
		//alert("test"+el+$(this).attr("class"));
		$(this).attr("id", "qar_form_"+el);
		$("#qar_form_"+el+' input[type="submit"]').css("display", "none");
		$("#qar_form_"+el+' .hex-button.reposition').css("display", "none");
		
		$("#qar_form_"+el+' input[type="checkbox"]').on("change", function(){ handleQAR(el) });
		
		$("#qar_form_"+el+' select').on("change", function(){ handleQAR(el) });
		
		//alert("test:"+$("#qar_form_"+el+' input[type="submit"]'));
	});

	$("select.actions").each(function(){
		$(this).change(function(event){
			event.preventDefault();
			if ($(this).val() > "") {
				if ($('option:selected', this).attr("data-pop") != "false") {
					pop($(this).val());
				} else window.location = $(this).val();
			}
			$(this).val("...");
		});
	});
	
	$("table.actionable tr").click(function(event){
		var menu = $(this).attr("id").replace("item-","action-");
		//(menu);
		var html = "<ul>";
		var last_group = "";
		
		$('.row_selected').removeClass("row_selected");
		$(this).addClass("row_selected");
		
		$("#"+menu+" option").each(function(){
			if ($(this).val() != "...") {
				group = $(this).parent().attr("label");
				if(last_group != group) {
					if(last_group != "") html += "</ul></li>";
					html += '<li><span class="optgroup">' + group + '</span><ul>';
				}
				html += '<li>';
				if($(this).attr("disabled")!="disabled" && $(this).val() != ""){
					html += '<a href="' + $(this).val() + '"';
					if(!$(this).attr("data-pop") || $(this).attr("data-pop")=="true") html+=' class="pop"';
					html += '>' + $(this).text() + '</a>';
				}else html += '<span class="disabled">' + $(this).text() + '</span>';
				html += '</li>';
				
				last_group = group;
			}
		});
		html += '</ul></li></ul>';
		
		if($("#popup_menu")) $("#popup_menu").remove();
		$(document.body).append('<div id="popup_menu"></div>');
		$('#popup_menu').css('left',event.pageX-10);
		$('#popup_menu').css('top',$('.row_selected').offset().top -26); /*event.pageY*/
		$("#popup_menu").html(html);
		
		$("#popup_menu").click(function(e){
			e.stopPropagation();
		});
		
		$('#popup_menu a').click(function(event){
			if($("#popup_menu")) $("#popup_menu").remove();
			$('.row_selected').removeClass("row_selected");
		
			if($(this).hasClass("pop")){
				event.preventDefault();
				pop($(this).attr("href"));
			}
		});
	});
	$('table .td_actions').css("display","none");
	
	$('#check_all').click(function(){
		check_all(this);
	});
	
	// removes popup_menu on outside click
	$(document.body).click(function(){
		if(event.target.parentNode.id.indexOf("item-") < 0) {
			$('.row_selected').removeClass("row_selected");
			$("#popup_menu").remove();
		}
	});
	
	
	/*$("#gallery_captions").submit(function(event){
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
				//alert(data);
				//console.log(data);
			}
		});
		event.preventDefault();
	});*/

});

function check_all() {
	$('input[type="checkbox"]').prop("checked", true);
}

// jQuery tagEditor v1.0.20
// https://github.com/Pixabay/jQuery-tagEditor
!function(t){t.fn.tagEditorInput=function(){var e=" ",i=t(this),a=parseInt(i.css("fontSize")),r=t("<span/>").css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:i.css("fontSize"),fontFamily:i.css("fontFamily"),fontWeight:i.css("fontWeight"),letterSpacing:i.css("letterSpacing"),whiteSpace:"nowrap"}),l=function(){if(e!==(e=i.val())){r.text(e);var t=r.width()+a;20>t&&(t=20),t!=i.width()&&i.width(t)}};return r.insertAfter(i),i.bind("keyup keydown focus",l)},t.fn.tagEditor=function(e,a,r){function l(t){return t.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#39;")}var n,o=t.extend({},t.fn.tagEditor.defaults,e),c=this;if(o.dregex=new RegExp("["+o.delimiter.replace("-","-")+"]","g"),"string"==typeof e){var s=[];return c.each(function(){var i=t(this),l=i.data("options"),n=i.next(".tag-editor");if("getTags"==e)s.push({field:i[0],editor:n,tags:n.data("tags")});else if("addTag"==e){if(l.maxTags&&n.data("tags").length>=l.maxTags)return!1;t('<li><div class="tag-editor-spacer">&nbsp;'+l.delimiter[0]+'</div><div class="tag-editor-tag"></div><div class="tag-editor-delete"><i></i></div></li>').appendTo(n).find(".tag-editor-tag").html('<input type="text" maxlength="'+l.maxLength+'">').addClass("active").find("input").val(a).blur(),r?t(".placeholder",n).remove():n.click()}else"removeTag"==e?(t(".tag-editor-tag",n).filter(function(){return t(this).text()==a}).closest("li").find(".tag-editor-delete").click(),r||n.click()):"destroy"==e&&i.removeClass("tag-editor-hidden-src").removeData("options").off("focus.tag-editor").next(".tag-editor").remove()}),"getTags"==e?s:this}return window.getSelection&&t(document).off("keydown.tag-editor").on("keydown.tag-editor",function(e){if(8==e.which||46==e.which||e.ctrlKey&&88==e.which){try{var a=getSelection(),r="BODY"==document.activeElement.tagName?t(a.getRangeAt(0).startContainer.parentNode).closest(".tag-editor"):0}catch(e){r=0}if(a.rangeCount>0&&r&&r.length){var l=[],n=a.toString().split(r.prev().data("options").dregex);for(i=0;i<n.length;i++){var o=t.trim(n[i]);o&&l.push(o)}return t(".tag-editor-tag",r).each(function(){~t.inArray(t(this).text(),l)&&t(this).closest("li").find(".tag-editor-delete").click()}),!1}}}),c.each(function(){function e(){!o.placeholder||c.length||t(".deleted, .placeholder, input",s).length||s.append('<li class="placeholder"><div>'+o.placeholder+"</div></li>")}function i(i){var a=c.toString();c=t(".tag-editor-tag:not(.deleted)",s).map(function(e,i){var a=t.trim(t(this).hasClass("active")?t(this).find("input").val():t(i).text());return a?a:void 0}).get(),s.data("tags",c),r.val(c.join(o.delimiter[0])),i||a!=c.toString()&&o.onChange(r,s,c),e()}function a(e){for(var a,n=e.closest("li"),d=e.val().replace(/ +/," ").split(o.dregex),g=e.data("old_tag"),f=c.slice(0),h=!1,u=0;u<d.length;u++)if(v=t.trim(d[u]).slice(0,o.maxLength),o.forceLowercase&&(v=v.toLowerCase()),a=o.beforeTagSave(r,s,f,g,v),v=a||v,a!==!1&&v&&(o.removeDuplicates&&~t.inArray(v,f)&&t(".tag-editor-tag",s).each(function(){t(this).text()==v&&t(this).closest("li").remove()}),f.push(v),n.before('<li><div class="tag-editor-spacer">&nbsp;'+o.delimiter[0]+'</div><div class="tag-editor-tag">'+l(v)+'</div><div class="tag-editor-delete"><i></i></div></li>'),o.maxTags&&f.length>=o.maxTags)){h=!0;break}e.attr("maxlength",o.maxLength).removeData("old_tag").val(""),h?e.blur():e.focus(),i()}var r=t(this),c=[],s=t("<ul "+(o.clickDelete?'oncontextmenu="return false;" ':"")+'class="tag-editor"></ul>').insertAfter(r);r.addClass("tag-editor-hidden-src").data("options",o).on("focus.tag-editor",function(){s.click()}),s.append('<li style="width:1px">&nbsp;</li>');var d='<li><div class="tag-editor-spacer">&nbsp;'+o.delimiter[0]+'</div><div class="tag-editor-tag"></div><div class="tag-editor-delete"><i></i></div></li>';s.click(function(e,i){var a,r,l=99999;if(!window.getSelection||""==getSelection())return o.maxTags&&s.data("tags").length>=o.maxTags?(s.find("input").blur(),!1):(n=!0,t("input:focus",s).blur(),n?(n=!0,t(".placeholder",s).remove(),i&&i.length?r="before":t(".tag-editor-tag",s).each(function(){var n=t(this),o=n.offset(),c=o.left,s=o.top;e.pageY>=s&&e.pageY<=s+n.height()&&(e.pageX<c?(r="before",a=c-e.pageX):(r="after",a=e.pageX-c-n.width()),l>a&&(l=a,i=n))}),"before"==r?t(d).insertBefore(i.closest("li")).find(".tag-editor-tag").click():"after"==r?t(d).insertAfter(i.closest("li")).find(".tag-editor-tag").click():t(d).appendTo(s).find(".tag-editor-tag").click(),!1):!1)}),s.on("click",".tag-editor-delete",function(){if(t(this).prev().hasClass("active"))return t(this).closest("li").find("input").caret(-1),!1;var a=t(this).closest("li"),l=a.find(".tag-editor-tag");return o.beforeTagDelete(r,s,c,l.text())===!1?!1:(l.addClass("deleted").animate({width:0},o.animateDelete,function(){a.remove(),e()}),i(),!1)}),o.clickDelete&&s.on("mousedown",".tag-editor-tag",function(a){if(a.ctrlKey||a.which>1){var l=t(this).closest("li"),n=l.find(".tag-editor-tag");return o.beforeTagDelete(r,s,c,n.text())===!1?!1:(n.addClass("deleted").animate({width:0},o.animateDelete,function(){l.remove(),e()}),i(),!1)}}),s.on("click",".tag-editor-tag",function(e){if(o.clickDelete&&(e.ctrlKey||e.which>1))return!1;if(!t(this).hasClass("active")){var i=t(this).text(),a=Math.abs((t(this).offset().left-e.pageX)/t(this).width()),r=parseInt(i.length*a),n=t(this).html('<input type="text" maxlength="'+o.maxLength+'" value="'+l(i)+'">').addClass("active").find("input");if(n.data("old_tag",i).tagEditorInput().focus().caret(r),o.autocomplete){var c=t.extend({},o.autocomplete),d="select"in c?o.autocomplete.select:"";c.select=function(e,i){d&&d(e,i),setTimeout(function(){s.trigger("click",[t(".active",s).find("input").closest("li").next("li").find(".tag-editor-tag")])},20)},n.autocomplete(c)}}return!1}),s.on("blur","input",function(d){d.stopPropagation();var g=t(this),f=g.data("old_tag"),h=t.trim(g.val().replace(/ +/," ").replace(o.dregex,o.delimiter[0]));if(h){if(h.indexOf(o.delimiter[0])>=0)return void a(g);if(h!=f)if(o.forceLowercase&&(h=h.toLowerCase()),cb_val=o.beforeTagSave(r,s,c,f,h),h=cb_val||h,cb_val===!1){if(f)return g.val(f).focus(),n=!1,void i();try{g.closest("li").remove()}catch(d){}f&&i()}else o.removeDuplicates&&t(".tag-editor-tag:not(.active)",s).each(function(){t(this).text()==h&&t(this).closest("li").remove()})}else{if(f&&o.beforeTagDelete(r,s,c,f)===!1)return g.val(f).focus(),n=!1,void i();try{g.closest("li").remove()}catch(d){}f&&i()}g.parent().html(l(h)).removeClass("active"),h!=f&&i(),e()});var g;s.on("paste","input",function(){t(this).removeAttr("maxlength"),g=t(this),setTimeout(function(){a(g)},30)});var f;s.on("keypress","input",function(e){o.delimiter.indexOf(String.fromCharCode(e.which))>=0&&(f=t(this),setTimeout(function(){a(f)},20))}),s.on("keydown","input",function(e){var i=t(this);if((37==e.which||!o.autocomplete&&38==e.which)&&!i.caret()||8==e.which&&!i.val()){var a=i.closest("li").prev("li").find(".tag-editor-tag");return a.length?a.click().find("input").caret(-1):!i.val()||o.maxTags&&s.data("tags").length>=o.maxTags||t(d).insertBefore(i.closest("li")).find(".tag-editor-tag").click(),!1}if((39==e.which||!o.autocomplete&&40==e.which)&&i.caret()==i.val().length){var l=i.closest("li").next("li").find(".tag-editor-tag");return l.length?l.click().find("input").caret(0):i.val()&&s.click(),!1}if(9==e.which){if(e.shiftKey){var a=i.closest("li").prev("li").find(".tag-editor-tag");if(a.length)a.click().find("input").caret(0);else{if(!i.val()||o.maxTags&&s.data("tags").length>=o.maxTags)return r.attr("disabled","disabled"),void setTimeout(function(){r.removeAttr("disabled")},30);t(d).insertBefore(i.closest("li")).find(".tag-editor-tag").click()}return!1}var l=i.closest("li").next("li").find(".tag-editor-tag");if(l.length)l.click().find("input").caret(0);else{if(!i.val())return;s.click()}return!1}if(!(46!=e.which||t.trim(i.val())&&i.caret()!=i.val().length)){var l=i.closest("li").next("li").find(".tag-editor-tag");return l.length?l.click().find("input").caret(0):i.val()&&s.click(),!1}if(13==e.which)return s.trigger("click",[i.closest("li").next("li").find(".tag-editor-tag")]),o.maxTags&&s.data("tags").length>=o.maxTags&&s.find("input").blur(),!1;if(36!=e.which||i.caret()){if(35==e.which&&i.caret()==i.val().length)s.find(".tag-editor-tag").last().click();else if(27==e.which)return i.val(i.data("old_tag")?i.data("old_tag"):"").blur(),!1}else s.find(".tag-editor-tag").first().click()});for(var h=o.initialTags.length?o.initialTags:r.val().split(o.dregex),u=0;u<h.length&&!(o.maxTags&&u>=o.maxTags);u++){var v=t.trim(h[u].replace(/ +/," "));v&&(o.forceLowercase&&(v=v.toLowerCase()),c.push(v),s.append('<li><div class="tag-editor-spacer">&nbsp;'+o.delimiter[0]+'</div><div class="tag-editor-tag">'+l(v)+'</div><div class="tag-editor-delete"><i></i></div></li>'))}i(!0),o.sortable&&t.fn.sortable&&s.sortable({distance:5,cancel:".tag-editor-spacer, input",helper:"clone",update:function(){i()}})})},t.fn.tagEditor.defaults={initialTags:[],maxTags:0,maxLength:50,delimiter:",;",placeholder:"",forceLowercase:!0,removeDuplicates:!0,clickDelete:!1,animateDelete:175,sortable:!0,autocomplete:null,onChange:function(){},beforeTagSave:function(){},beforeTagDelete:function(){}}}(jQuery);

// http://code.accursoft.com/caret - 1.3.3
!function(e){e.fn.caret=function(e){var t=this[0],n="true"===t.contentEditable;if(0==arguments.length){if(window.getSelection){if(n){t.focus();var o=window.getSelection().getRangeAt(0),r=o.cloneRange();return r.selectNodeContents(t),r.setEnd(o.endContainer,o.endOffset),r.toString().length}return t.selectionStart}if(document.selection){if(t.focus(),n){var o=document.selection.createRange(),r=document.body.createTextRange();return r.moveToElementText(t),r.setEndPoint("EndToEnd",o),r.text.length}var e=0,c=t.createTextRange(),r=document.selection.createRange().duplicate(),a=r.getBookmark();for(c.moveToBookmark(a);0!==c.moveStart("character",-1);)e++;return e}return t.selectionStart?t.selectionStart:0}if(-1==e&&(e=this[n?"text":"val"]().length),window.getSelection)n?(t.focus(),window.getSelection().collapse(t.firstChild,e)):t.setSelectionRange(e,e);else if(document.body.createTextRange)if(n){var c=document.body.createTextRange();c.moveToElementText(t),c.moveStart("character",e),c.collapse(!0),c.select()}else{var c=t.createTextRange();c.move("character",e),c.select()}return n||t.focus(),e}}(jQuery);
