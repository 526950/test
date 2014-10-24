$(document).ready(function() {
	$("input[name='login']").blur(function() {
		var ob = $(this);
		if ($(ob).val() != '') {
			$.post("/signup/", {
				checkLogin : true,
				login : $(ob).val()
			}, function(data) {
				$('ul.message').remove();
				if (data != '0') $('#messageError').before('<div class="alert alert-danger">' + data + '</div>');
			}, "text");
		}
		return false;
	});
});