$(function(){

	handleTriColumns();
	$(window).bind('resize', handleTriColumns);

});

function handleTriColumns() {

	if ($(document).width() > 900) {

		$('#benefits .tri_column').css({"width":"auto", "padding-left":"0"});
	
		bw = $('#benefits h2').width();
		tc3 = Math.ceil($('#benefits .tri_column.left').width()) + Math.ceil($('#benefits .tri_column.middle').width()) + Math.ceil($('#benefits .tri_column.right').width());
		diff = (bw - tc3) / 2;
	
		$('#benefits .tri_column.left').css("padding-right", diff + 'px');
		$('#benefits .tri_column.middle').css("padding-right", diff + 'px');
	
	} else {
		$('#benefits .tri_column').css({"width":"", "padding-left":""});
		$('#benefits .tri_column.left').css("padding-right", '');
		$('#benefits .tri_column.middle').css("padding-right", '');
	}

}
