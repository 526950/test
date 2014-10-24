function fileupload(module) {
	$('#fileupload').fileupload({
		dataType : 'json',
		url : '/config/?cmd=Upload&module=' + module,
		done : function(e, data) {
			$.each(data.result.files, function(index, file) {
				$('<img width="100" /><a class="del"><i class="glyphicon glyphicon-trash"></i>удалить</a>').attr('src', file.url).appendTo('.well');
			});
		}
	});
}

$(document).ready(function() {
	
	//fileupload('config');

	$('.well').on('click', '.del', function() {
		alert ('удалить?');
		return false;
	});
	
	
});