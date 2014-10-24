"use strict";
$(document).ready(function($) {
	$('#tree').sortable({
		items : '.sort'
	}).bind('sortupdate', function() {
		// https://github.com/farhadi/html5sortable
		var arr = new Array();
		$(this).find('li').each(function() {
			arr.push($(this).attr('id'));
		});
		$.post($(this).data('url'), {
			sortTree : arr
		}, function(d) {})
	});
	$('a.publishAjax').click(function() {
		var ob = $(this);
		$.get(ob.attr('href'), function(data) {
			if (data == 1) $(ob).toggleClass('glyphicon glyphicon-eye-open glyphicon glyphicon-eye-close');
		});
		return false;
	});
	if ($('.adminMenu').length) {
		var menu_cookie = getCookie('admin_menu');
		if (menu_cookie == 'fixed') {
			$('.navbar').addClass('navbar-fixed-top');
			$('.pin').addClass('glyphicon-record');
			$('body').css('padding-top', '70px');
		} else {
			$('.navbar').removeClass('navbar-fixed-top');
			$('.pin').addClass('glyphicon-pushpin');
			$('body').css('padding-top', '0px');
		}
		$('.pin').click(function() {
			menu_cookie = getCookie('admin_menu');
			$('.navbar').toggleClass('navbar-fixed-top');
			$(this).toggleClass('glyphicon-record glyphicon-pushpin');
			
			menu_cookie = (menu_cookie == 'fixed') ? '' : 'fixed';
			$('body').css('padding-top', (menu_cookie == '') ? '0px' : '70px');
			setCookie('admin_menu', menu_cookie, {path:'/'});
		});
	}
});
// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}
function setCookie(name, value, options) {
	/*
	 * options Объект с дополнительными свойствами для установки cookie: expires
	 * Время истечения cookie. Интерпретируется по-разному, в зависимости от типа:
	 * Число — количество секунд до истечения. Например, expires: 3600 — кука на
	 * час. Объект типа Date — дата истечения. Если expires в прошлом, то cookie
	 * будет удалено. Если expires отсутствует или 0, то cookie будет установлено
	 * как сессионное и исчезнет при закрытии браузера. path Путь для cookie.
	 * domain Домен для cookie. secure Если true, то пересылать cookie только по
	 * защищенному соединению.
	 */
	options = options || {};
	var expires = options.expires;
	if (typeof expires == "number" && expires) {
		var d = new Date();
		d.setTime(d.getTime() + expires * 1000);
		expires = options.expires = d;
	}
	if (expires && expires.toUTCString) {
		options.expires = expires.toUTCString();
	}
	value = encodeURIComponent(value);
	var updatedCookie = name + "=" + value;
	for ( var propName in options) {
		updatedCookie += "; " + propName;
		var propValue = options[propName];
		if (propValue !== true) {
			updatedCookie += "=" + propValue;
		}
	}
	document.cookie = updatedCookie;
}
/*
 * $(document).on('click', 'a.delete, a.delete_ajax, a.decline', function() {
 * var href = $(this).attr('href'); var title = $(this).attr('title'); if (title == '' ||
 * typeof title == 'undefined') title = 'Удаление'; jConfirm('Вы уверены, что
 * хотите удалить?', title, function(r) { if (r) window.location = href; });
 * return false; });
 * 
 * $(document).on('click', 'a.reload_captcha', function() {
 * $('img.captcha').attr('src', $('img.captcha').attr('src') + '?reload');
 * return false; });
 * 

 */