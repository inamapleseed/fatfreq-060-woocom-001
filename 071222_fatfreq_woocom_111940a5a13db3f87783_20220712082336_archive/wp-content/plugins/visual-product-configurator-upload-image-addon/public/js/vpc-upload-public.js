(function ($) {
	'use strict';
	var all_images = window._all_images = {};

	$(document).ready(function () {
		$(window).load(function () {
			vpc_upload_create_container();
		});

		if (typeof vpc !== "undefined") {
			wp.hooks.addFilter('vpc.total_price', update_total_price);
		}

		// Call the window to get item to upload when the user clicks on a custom upload's option.
		// Just removed .acd-upload-info
		$(document).on("click", '.drop label', function () {
			var canvases = window.__canvases;
			var continue_process = wp.hooks.applyFilters('vpc.default_upload_file_process', true);
			if (continue_process) {
				$(this).addClass('upload_in_pause');
				var name = $(this).parents('.userfile_upload_form').data('name');
				var sanitized_name = name + '-container';
				var canvas_id = $(this).parents('.userfile_upload_form').data('canvas-id');
				if ( typeof canvases[canvas_id] !== 'undefined' && typeof all_images[sanitized_name] !== 'undefined') {
					canvases[canvas_id].remove(all_images[sanitized_name]);
					var new_tab = {};
					$.each(all_images,function ( key, val ) {
						if ( key !== sanitized_name ) {
							new_tab[key] = val;
						}
					});
					all_images = window._all_images = new_tab;
				}
				window.vpc_build_preview();
				var view_to_focus = $(this).parents('.userfile_upload_form').data('view-focus');
				vpc_upload_put_view_on_focus(view_to_focus);
				$(this).parent('.drop').find('input').click();
			}else {
				wp.hooks.doAction('vpc.default_upload_file_process', $(this));
			}
		});

		// Get the new file uploaded, read it and set it on the preview.
		$(document).on("change", '.userfile_upload_form input[type="file"]', function () {
			var div = $(this).parent('.drop').find('.acd-upload-info');
			readURL(this, div);
		});

		//Clear the upload fields when the upload option or component is hidden.
		if (typeof vpc !== "undefined") {
			wp.hooks.addAction('vpc.hide_options_or_component', vpc_upload_hide_component_selector);
		}

		// Empty all potential data when the user clicks on the an option.
		$(document).on("click", '.acd-upload-info.choosed', function () {
			$(this).find('img').removeAttr('src');
			$(this).find('img').hide();
			// $(this).removeClass('choosed');
			var form_id = $(this).parents().closest('form.custom-uploader').attr('id');
			$('#img_' + form_id).attr('src', '');
			$('#' + form_id).find('input[type="hidden"]').val('');
		});

		window.vpc_load_options = function () {
			if (typeof vpc !== 'undefined') {
				$(vpc.vpc_selected_items_selector).each(function () {
					$(this).trigger('change');
				});
			}
		}


		// if (typeof vpc !== "undefined") {
		// 	wp.hooks.addAction('vpc.ajax_loading_complete', function () {
		// 		setTimeout(function () {
		// 			vpc_upload_create_container();
		// 		}, 2000);
		// 	});
		// }
	});

	// Funtion to get the mutiple views state.
	function vpc_upload_get_multi_views_state(config) {
		var multi_views = false;
		if (typeof (config) !== 'undefined') {
			$.each(config, function (config_key, config_value) {
				if (config_key === 'multi-views' && config_value === 'Yes') {
					multi_views = true;
				}
			});
		}
		return multi_views;
	}

	// Function to create the custom upload container on the preview.
	function vpc_upload_create_container() {
		if (typeof vpc != "undefined" && vpc != null) {
			var canvases = window.__canvases;
			if (typeof (active_views) !== 'undefined') {
				var activeViews = JSON.parse(active_views);
			}
			$('[id^="userfile_upload_form"]').each(function () {
				var that = $(this);
				var form_id = $(this).attr('id');
				var canvas_id = $(this).data('canvas-id');
				var multi_views = vpc_upload_get_multi_views_state(vpc.config);
				var img_url = $(this).find('.drop img').attr('src');
				$(this).find('.drop .acd-upload-info').addClass('choosed');
				if (multi_views) {
					if (typeof (activeViews) !== 'undefined') {
						$.each(activeViews, function (index, value) {
							if (form_id.includes(index)) {
								var view_preview = setInterval(function () {
									if (typeof canvases !== 'undefined' && typeof canvases[canvas_id] !== 'undefined') {
										clearInterval(view_preview);
										check_img_url_and_add_to_preview( that, canvas_id, img_url );
									}
								});
							}
						});
					}
				} else {
					var vpc_preview = setInterval(function () {
						if (typeof canvases !== 'undefined' && typeof canvases[canvas_id] !== 'undefined') {
							clearInterval(vpc_preview);
							check_img_url_and_add_to_preview( that, canvas_id, img_url );
						}
					});
				}
			});
		}
	}


	function check_img_url_and_add_to_preview( that, canvas_id, img_url ) {
		if ( typeof img_url !== 'undefined') {
			that.find('img').show();
			that.find('label').removeClass('upload_in_pause');
			vpc_upload_add_image_on_preview( that, canvas_id, img_url, 'loading' );
			that.find('.acd-upload-info').addClass('choosed');
		}else {
			that.find('img').hide();
		}

		wp.hooks.doAction('vpc.after_image_added_on_preview', that, canvas_id, img_url);
	}

	window.vpc_upload_add_image_on_preview = function ( that, canvas_id, img_url, status ) {
		var canvases = window.__canvases;
		var opt_name = that.data('bare-name');
		var data_name = that.data('name');
		var sanitized_name = data_name + '-container';
		var secondary_parameters = vpc_upload_get_secondary_parameters(that, canvas_id, canvases);
		var parameters_to_load = vpc_upload_get_image_parameters_to_load(secondary_parameters, canvas_id, sanitized_name, status);
		var image_control_parameters = vpc_upload_get_image_control_parameters(that);
		new fabric.Image.fromURL(img_url, function(img) {
			var continue_process = wp.hooks.applyFilters('vpc.add_image_on_preview_process', true);
			if (continue_process) {
				img.set({
					id : 'image-'+sanitized_name,
					name: opt_name,
					sanitized_name: data_name,
					left: parameters_to_load.left,
					top: parameters_to_load.top,
					angle: parameters_to_load.angle,
					z_index: parameters_to_load.z_index,
					editable: false,
					lockRotation: image_control_parameters.lock_rotation,
					lockScalingX: image_control_parameters.lock_scaling_x,
					lockScalingY: image_control_parameters.lock_scaling_y,
					lockMovementX: image_control_parameters.lock_movement_x,
					lockMovementY: image_control_parameters.lock_movement_y,
				});
				const widthFactor = parameters_to_load.width / img.width;
				const heightFactor = parameters_to_load.height / img.height;
				const minFactor = Math.min(widthFactor, heightFactor);
				img.scale(minFactor);
				all_images[sanitized_name] = img;
				canvases[canvas_id].add(img).renderAll();
				window.vpc_build_preview();
			}else {
				wp.hooks.doAction('vpc.add_image_on_preview_process', that, canvas_id, img_url, status);
			}
		});
	}

	window.vpc_upload_get_image_control_parameters = function (that) {
		var lock_rotation = false;
		if ( that.data('lock-rotation') && that.data('lock-rotation') === 'yes') {
			lock_rotation = true;
		}

		var lock_scaling_x = false;
		if ( that.data('lock-scaling-x') && that.data('lock-scaling-x') === 'yes') {
			lock_scaling_x = true;
		}

		var lock_scaling_y = false;
		if ( that.data('lock-scaling-y') && that.data('lock-scaling-y') === 'yes') {
			lock_scaling_y = true;
		}

		var lock_movement_x = false;
		if ( that.data('lock-movement-x') && that.data('lock-movement-x') === 'yes') {
			lock_movement_x = true;
		}

		var lock_movement_y = false;
		if ( that.data('lock-movement-y') && that.data('lock-movement-y') === 'yes') {
			lock_movement_y = true;
		}
		return { 'lock_rotation':lock_rotation, 'lock_scaling_x':lock_scaling_x, 'lock_scaling_y':lock_scaling_y, 'lock_movement_x':lock_movement_x, 'lock_movement_y':lock_movement_y };
	}

	window.vpc_upload_get_image_parameters_to_load = function (secondary_parameters, canvas_id, sanitized_name, status) {
		if (typeof status !== 'undefined' && status === 'loading' && vpc.canvas_data !== 'undefined' && vpc.canvas_data.length !== 0 && typeof vpc.canvas_data[canvas_id] !== 'undefined') {
			var object_data = vpc.canvas_data[canvas_id]['image-'+sanitized_name];
			if (typeof object_data !== 'undefined' ) {
				secondary_parameters = vpc_upload_set_object_data(secondary_parameters, object_data, canvas_id);
			}
		}
		return secondary_parameters;
	}

	function vpc_upload_set_object_data(secondary_parameters, data, canvas_id) {
		secondary_parameters.left = parseFloat(data.left);
		secondary_parameters.top = parseFloat(data.top);
		secondary_parameters.angle = parseFloat(data.angle);
		secondary_parameters.width = parseFloat(data.width) * parseFloat(data.scaleY);
		secondary_parameters.height = parseFloat(data.height) * parseFloat(data.scaleX);
		return secondary_parameters;
	}

	window.vpc_upload_get_secondary_parameters = function (that,canvas_id,canvases) {
		var top = (parseFloat(that.data('top')) / 100) * canvases[canvas_id].height;
		var left = (parseFloat(that.data('left')) / 100) * canvases[canvas_id].width;
		var height = (parseFloat(that.data('height')) / 100) * canvases[canvas_id].height;
		var width = (parseFloat(that.data('width')) / 100) * canvases[canvas_id].width;
		var z_index = parseInt( that.data('index') );
		var angle = parseInt(that.data('angle'));
		return { 'top':top, 'left':left, 'height':height, 'width':width, 'angle':angle, 'z_index':z_index};
	}

	//Function to hide a custom upload component.
	function vpc_upload_hide_component_selector(rules_groups) {
		var canvases = window.__canvases;
		if (rules_groups.result.scope == "option") {
			$('.vpc-single-option-wrap').find('form').each(function () {
				if (rules_groups.result.apply_on == $(this).parent().attr('data-oid')) {
					var name = $(this).data('name');
					var sanitized_name = name + '-container';
					var canvas_id = $(this).data('canvas-id');
					if ( typeof canvases[canvas_id] !== 'undefined' && typeof all_images[sanitized_name] !== 'undefined') {
						canvases[canvas_id].remove(all_images[sanitized_name]);
						var new_tab = {};
						$.each(all_images,function ( key, val ) {
							if ( key !== sanitized_name ) {
								new_tab[key] = val;
							}
						});
						all_images = window._all_images = new_tab;
					}
					var img_choosen = $(this).find(".acd-upload-info.choosed");
					img_choosen.find('img').removeAttr('src');
					img_choosen.find('img').hide();
					img_choosen.removeClass('choosed');
					var form_id = img_choosen.parents().closest('form.custom-uploader').attr('id');
					$('#' + form_id).find('input[type="hidden"]').val('');
				}
			}
		);
	}
	else if (rules_groups.result.scope == "component") {
		$('.vpc-single-option-wrap').find('form').each(function () {
			var component_id = $(this).parent().parent().parent().parent().attr('data-component_id');
			if (rules_groups.result.apply_on == component_id) {
				var name = $(this).data('name');
				var sanitized_name = name + '-container';
				var canvas_id = $(this).data('canvas-id');
				if ( typeof canvases[canvas_id] !== 'undefined' && typeof all_images[sanitized_name] !== 'undefined') {
					canvases[canvas_id].remove(all_images[sanitized_name]);
					var new_tab = {};
					$.each(all_images,function ( key, val ) {
						if ( key !== sanitized_name ) {
							new_tab[key] = val;
						}
					});
					all_images = window._all_images = new_tab;
				}
				var img_choosen = $(this).find(".acd-upload-info.choosed");
				img_choosen.find('img').removeAttr('src');
				img_choosen.find('img').hide();
				img_choosen.removeClass('choosed');
				var form_id = img_choosen.parents().closest('form.custom-uploader').attr('id');
				$('#' + form_id).find('input[type="hidden"]').val('');
			}
		}
	);
}
}

//Function to read the uploaded file and to save the image on the server.
function readURL(input, div) {
	var continue_process = wp.hooks.applyFilters('vpc.read_file_url_process', true);
	if (continue_process) {
		if (input.files && input.files[0]) {
			$(div).parents('label').removeClass('upload_in_pause');
			$(div).parent().parent().parent().addClass('disabledClick');
			var image_type = input.files[0].type;
			var reader = new FileReader();
			var file_name = input.files[0].name;
			var extension = image_type.replace('image/','');
			var valids_extensions = ['png', 'jpeg', 'jpg', 'JPG', 'PNG'];
			if ($.inArray(extension, valids_extensions) == -1) {
				alert('Incorrect extension');
				var that = $(input).parents('.userfile_upload_form');
				that.find('label').addClass('upload_in_pause');
				that.removeClass('disabledClick');
				$(input).val('');
				return;
			}
			reader.onload = function (e) {
				$.ajax({
					type: 'POST',
					url:ajax_object.ajax_url,
					data:{
						action: "vpc_upload_save_image",
						extension: extension,
						data_url: e.target.result.replace('data:' + image_type + ';base64,',''),
					},
					success: function (new_src) {
						new_src = $.trim(new_src);
						var canvas_id = $(input).parents('.userfile_upload_form').data('canvas-id');
						var name = $(input).parents('.userfile_upload_form').data('name');
						var sanitized_name = name + '-container';
						var that = $(input).parents('.userfile_upload_form');
						if (typeof new_src !== 'undefined') {
							that.find('.img_link').val(new_src);
							that.find('img').show();
							that.find('img').attr('src',new_src);
							vpc_upload_add_image_on_preview( that, canvas_id, new_src, 'new_adding' );
							that.find('.acd-upload-info').addClass('choosed');
							that.removeClass('disabledClick');
							$(input).val('');
						}
					}
				});
			}
			reader.readAsDataURL(input.files[0]);
		}
	}else {
		wp.hooks.doAction('vpc.read_file_url_process', input, div);
	}
}

//Function to update the configuration's total price.
function update_total_price(price) {
	if ($('form .choosed').length > 0) {
		$('form .choosed').each(function () {
			if ($(this).length > 0 && typeof ($($(this).children()[0]).attr('src')) !== 'undefined' && $($(this).children()[0]).attr('src') !== '') {
				if ($(this).attr('data-price'))
				price += parseFloat($(this).attr('data-price'));
			}
		});
	} else {
		$('[id^="userfile_upload_form"]').each(function () {
			if ($(this).length > 0 && $($(this).find('.acd-upload-info').children()[0]).attr('src') !== '' && typeof ($($(this).find('.acd-upload-info').children()[0]).attr('src')) !== 'undefined') {
				if ($(this).find('.acd-upload-info').attr('data-price'))
				price += parseFloat($(this).find('.acd-upload-info').attr('data-price'));
			}
		});
	}
	return price;
}

// Function to put a view on focus.
window.vpc_upload_put_view_on_focus = function (view_to_focus) {
	if (typeof active_views != "undefined") {
		if (view_to_focus !== "none" && view_to_focus.length > 0) {
			var current_view = $('#mva-bx-pager').find('.bx-pager a.bx-pager-link.active').data('view');
			if (typeof current_view != "undefined" && current_view.length > 0) {
				if (view_to_focus.toLowerCase() !== current_view.toLowerCase()) {
					$('#mva-bx-pager .bx-pager ').find("a[data-view=" + view_to_focus + "]").trigger("click");
				}
			}
		}
	}
}

})(jQuery);
