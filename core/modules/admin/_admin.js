$(document).ready(function() {

	function optionSelect() {
		$('a.option-section').editable({
			placement : 'right',
			display : false,
			toggle : 'manual',
			savenochange : true,
			send : 'always',
			success : function() {
				$('.dropdown.open').trigger('hide.bs.dropdown');
				$('.dropdown.open').removeClass('open');
				$.post('/admin/?ADMIN&cmd=Show', function(data) {
					$("#sections").replaceWith(data);
				});
			}
		});
	}

	optionSelect();

	$('a.add-section, a.edit-section').editable({
		placement : 'right',
		validate : function(value) {
			if (value.name == '') return 'Поле "Название" должно быть заполненно!';
			if (value.module == 'NULL') return 'Выберите модуль!';
		},
		display : false,
		toggle : 'manual',
		success : function(response, newValue) {
			$.post('/admin/?ADMIN&cmd=Show', function(data) {
				$("#sections").replaceWith(data);
				$.post('/admin/?ADMIN&cmd=Sort', {
					'list' : window.JSON.stringify($('#nestable').nestable('serialize')),
					'table' : '_section'
				});
				$('a.option-section').editable('destroy');
				optionSelect();
			});
		}
	});

	$('a.add-section, a.edit-section, a.option-section').click(function(e) {
		$(this).editable('show');
		$('.dropdown li.active').removeClass('active');
		$(this).closest('li').addClass('active');
		return false;
	});

	$('.dropdown').on('hide.bs.dropdown', function() {
		$('a.add-section, a.edit-section, a.option-section').editable('hide');
		$('.dropdown li.active').removeClass('active');
	});

	$('a.option-section').on('shown', function(e, editable) {
		var id1 = $(this).data('pk');
		$.post('/admin/?ADMIN&cmd=Show', {
			id_section : id1
		}, function(data) {
			$.each(data, function(key, val) {
				$('#option-section input[name="id2[' + val.id2 + ']"]').attr('checked', 'checked');
				if (id1 == val.id2) $('#option-section input[name="id2[' + val.id2 + ']"]').hide();
				$('#option-section input[name="params[' + val.id2 + ']"]').val(val.params);
			});
		}, 'json');
		$('#option-section input[type="text"]').on('keyup', function() {
			var checkbox = $('#id2_' + $(this).data('id'));
			if ($(this).val() != '' && !checkbox.prop('checked')) {
				checkbox.attr("checked", "checked");
			}
		});
	});

	$('a.edit-section').on('shown', function(e, editable) {
		var ob = e.target;
		$(editable.input.$input).each(function(i, o) {
			$(o).val($(ob).data($(o).attr('name')));
		});
		$(editable.input.$select).each(function(i, o) {
			$('[value="' + $(ob).data($(o).attr('name')) + '"]', o).attr("selected", "selected");
		});
		$(editable.input.$menu).each(function(i, o) {
			var ar = $(ob).data('param').split('&');
			if ($.inArray($(o).data('name'), ar) > -1) {
				$(o).attr("checked", "checked");
			}
		});
		$('#section-link input[type="radio"]').removeAttr('checked').parent().removeClass('active');
		$('#section-link input#link-' + $(ob).data('link')).parent().addClass('active');
		if (!$(ob).data('active')) $('#active').removeAttr('checked');

	});

	$('#nestable').nestable().on('change', function(e) {
		var list = e.length ? e : $(e.target);
		$.post('/admin/?ADMIN&cmd=Sort', {
			'list' : window.JSON.stringify(list.nestable('serialize')),
			'table' : '_section'
		});
	});

});

(function($) {
	var Multi = function(options) {
		this.init('multi', options, Multi.defaults);
	};

	// inherit from Abstract input
	$.fn.editableutils.inherit(Multi, $.fn.editabletypes.abstractinput);

	$.extend(Multi.prototype, {
		/**
		 * Renders input from tpl
		 * 
		 * @method render()
		 */
		render : function() {
			this.$input = this.$tpl.find('input');
			this.$select = this.$tpl.find('select[name="module"]');
			this.$menu = this.$tpl.find('#select-menu input');
		},
		/**
		 * Default method to show value in element. Can be overwritten by display
		 * option.
		 * 
		 * @method value2html(value, element)
		 */
		value2html : function(value, element) {
			// if (!value) {
			// $(element).empty();
			// return;
			// }
			// var html = $('<div>').text(value.name).html() + ', ' +
			// $('<div>').text(value.alias).html() + ' st., bld. ' +
			// $('<div>').text(value.building).html();
			// $(element).html(html);
		},
		/**
		 * Gets value from element's html
		 * 
		 * @method html2value(html)
		 */
		html2value : function(html) {
			/*
			 * you may write parsing method to get value by element's html e.g.
			 * "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina",
			 * building: "15"} but for complex structures it's not recommended. Better
			 * set value directly via javascript, e.g. editable({ value: { city:
			 * "Moscow", street: "Lenina", building: "15" } });
			 */
			return null;
		},
		/**
		 * Converts value to string. It is used in internal comparing (not for
		 * sending to server).
		 * 
		 * @method value2str(value)
		 */
		value2str : function(value) {
			var str = '';
			if (value) {
				for ( var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},
		/*
		 * Converts string to value. Used for reading value from 'data-value'
		 * attribute.
		 * 
		 * @method str2value(str)
		 */
		str2value : function(str) {
			/*
			 * this is mainly for parsing value defined in data-value attribute. If
			 * you will always set value by javascript, no need to overwrite it
			 */
			return str;
		},
		/**
		 * Sets value of input.
		 * 
		 * @method value2input(value)
		 * @param {mixed}
		 *          value
		 */
		// value2input : function(value) {
		// if (!value) { return; }
		// // this.$input.filter('[name="name"]').val(value.name);
		// // this.$input.filter('[name="alias"]').val(value.alias);
		// },
		/**
		 * Returns value of input.
		 * 
		 * @method input2value()
		 */
		input2value : function() {
			var params = [];
			$.each(this.$input.filter('#select-menu input:checked'), function(key, value) {
				params.push($(value).data('name'));
			});
			params = params.join('&');

			return {
				name : this.$input.filter('[name="name"]').val(),
				alias : this.$input.filter('[name="alias"]').val(),
				module : $(':selected', this.$select).val(),
				params : params,
				link : $('#section-link label.active input').data('link'),
				active : $('#active').prop('checked')
				
			};

		},
		/**
		 * Activates input: sets focus on the first field.
		 * 
		 * @method activate()
		 */
		activate : function() {
			// this.$input.filter('[name="name"]').focus();
		},
		/**
		 * Attaches handler to submit form in case of 'showbuttons=false' mode
		 * 
		 * @method autosubmit()
		 */
		autosubmit : function() {
			this.$input.keydown(function(e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	var tpl = '<div><label><span class="name">Название: </span></label><input type="text" name="name" class="form-control" value=""></div>' + '<div><label><span class="name">Алиас: </span></label><input type="text" name="alias" class="form-control" value=""></div>'
			+ '<div><label><span class="name">Модуль: </span></label><select id="select-modules" name="module" class="form-control">' + $('#tpl-select-modules').html() + '</select></div>' + '<div class="row"><strong class="name col-md-2">Меню: </strong><div id="select-menu" class="col-md-10">'
			+ $('#tpl-select-menu').html() + '</div></div>' + '<div><label><span class="name">Вызывать: </span></label> <div id="section-link">' + $('#tpl-section-link').html() + '<label><input id="active" type="checkbox" checked="checked"/> Включен</label></div></div>';

	Multi.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl : tpl,
		inputclass : ''
	});

	$.fn.editabletypes.multi = Multi;
}(window.jQuery));

(function($) {
	var Multi2 = function(options) {
		this.init('multi2', options, Multi2.defaults);
	};

	// inherit from Abstract input
	$.fn.editableutils.inherit(Multi2, $.fn.editabletypes.abstractinput);

	$.extend(Multi2.prototype, {
		/**
		 * Renders input from tpl
		 * 
		 * @method render()
		 */
		render : function() {
			this.$input = this.$tpl.find('input');
			// console.log(this.$input);
		},
		/**
		 * Default method to show value in element. Can be overwritten by display
		 * option.
		 * 
		 * @method value2html(value, element)
		 */
		value2html : function(value, element) {
			// if (!value) {
			// $(element).empty();
			// return;
			// }
			// var html = $('<div>').text(value.name).html() + ', ' +
			// $('<div>').text(value.alias).html() + ' st., bld. ' +
			// $('<div>').text(value.building).html();
			// $(element).html(html);
		},
		/**
		 * Gets value from element's html
		 * 
		 * @method html2value(html)
		 */
		html2value : function(html) {
			/*
			 * you may write parsing method to get value by element's html e.g.
			 * "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina",
			 * building: "15"} but for complex structures it's not recommended. Better
			 * set value directly via javascript, e.g. editable({ value: { city:
			 * "Moscow", street: "Lenina", building: "15" } });
			 */
			return null;
		},
		/**
		 * Converts value to string. It is used in internal comparing (not for
		 * sending to server).
		 * 
		 * @method value2str(value)
		 */
		value2str : function(value) {
			var str = '';
			if (value) {
				for ( var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},
		/*
		 * Converts string to value. Used for reading value from 'data-value'
		 * attribute.
		 * 
		 * @method str2value(str)
		 */
		str2value : function(str) {
			/*
			 * this is mainly for parsing value defined in data-value attribute. If
			 * you will always set value by javascript, no need to overwrite it
			 */
			return str;
		},
		/**
		 * Sets value of input.
		 * 
		 * @method value2input(value)
		 * @param {mixed}
		 *          value
		 */
		value2input : function(value) {
			if (!value) { return; }
		},
		/**
		 * Returns value of input.
		 * 
		 * @method input2value()
		 */
		input2value : function() {
			// console.log(this.$input);
			var checkbox = [], params = {};
			$.each(this.$input.filter('input[type="checkbox"]:checked'), function(key, value) {
				checkbox.push($(value).val());
			});
			$.each(this.$input.filter('input[type="text"]'), function(key, value) {
				if ($.trim($(value).val()) != '') params[$(value).data('id')] = $(value).val();
			});
			return {
				id2 : checkbox,
				params : params
			};
		},
		/**
		 * Activates input: sets focus on the first field.
		 * 
		 * @method activate()
		 */
		activate : function() {
			// this.$input.filter('[name="params"]').focus();
		},
		/**
		 * Attaches handler to submit form in case of 'showbuttons=false' mode
		 * 
		 * @method autosubmit()
		 */
		autosubmit : function() {
			this.$input.keydown(function(e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	var tpl = '<div><span class="name">Разделы: </span><ul id="option-section">' + $('#tpl-option-section').html() + '</ul></div>';

	Multi2.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl : tpl,
		inputclass : ''
	});

	$.fn.editabletypes.multi2 = Multi2;
}(window.jQuery));