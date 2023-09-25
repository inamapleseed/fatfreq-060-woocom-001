(function( $ ) {
	'use strict';
	$(document).ready(function () {

		if (typeof vpc !== 'undefined') {
			wp.hooks.addAction('vpc.lazy_options_loaded', 'vpc', vpc_upload_init_custom_component_select2);
		}

		// Trigger the function to apply the new select2 technology to the select fields of the new line in the modal window when the user click on the button
		$(document).on("click", ".vpc-component-row .omodal .add-rf-row", function (e) {
			setTimeout(function () {
				vpc_upload_init_custom_component_select2();
			}, 200);
		});

		/*********** déclencher le traitement à l'ajout d'un composant **************/
		$(document).on('click', '#vpc-config-container a.add-rf-row:last', function () {
			setTimeout(function () {
				$('.vpc-behaviour').trigger('change');
			},10);
		});

		/*********** déclencher le traitement à l'ajout d'un composant **************/
		$(document).on('click', '#vpc-config-container a.add-rf-row:last', function () {
			setTimeout(function () {
				$('.vpc-behaviour').trigger('change');
			}, 10);
		});

		add_upload_custom_class();

		/*********** traitement sur la selection du behavior **************/
		$(document).on("change", ".vpc-behaviour", function () {
			add_upload_custom_class();
		});

		$(document).on('change','input[name*="vpc-config[multi-views]"]:checked',function () {
			add_upload_custom_class();
		});

		$(document).on("click",".button.mg-top.add-rf-row", function () {
			add_upload_custom_class();
		});

		$(document).on("click",".o-modal-trigger",function () {
			var that = $(this);
			var modal_opened = setInterval(function () {
				if (that.next().find('.omodal-body tbody.ui-sortable tr').length) {
					clearInterval(modal_opened);
					vpc_upload_init_custom_component_select2();
				}
			},100);
		});
	});

	// Function to check if multiviews is active or not
	function vpc_upload_get_multi_views_state() {
		var multi_views = false;
		if (typeof $("input[name*='vpc-config[multi-views]']:checked") !== "undefined" && $("input[name*='vpc-config[multi-views]']:checked").val() === "Yes") {
			multi_views = true;
		}
		return multi_views;
	}

	// Function to apply the new select2 technology to the select fields
	function vpc_upload_init_custom_component_select2()
	{
		$(".vpc-upload-component-selector,.vpc-views-upload-component-selector").each(function () {
			$(this).select2({
				data: vpc_upload_components
			});
		});
	}

	//*********** ajout des classes sur la fenetre modal en fonction du behaviour selectionné **************/
	function add_upload_custom_class() {
		var multi_views = vpc_upload_get_multi_views_state();
		if (!$('.vpc-behaviour').length)
		return false;
		$('.vpc-behaviour').each(function ()
		{
			$(this).parent().parent().find('.omodal-body').removeClass('custom_upload_bloc');
			$(this).parent().parent().find('.omodal-body').removeClass('custom_multiple_upload_bloc');
			$(this).parent().parent().find('.omodal-body').removeClass('default_bloc');

			if (!multi_views && $(this).val() === 'upload')
			$(this).parent().parent().find('.omodal-body').addClass('custom_upload_bloc');

			if (multi_views && $(this).val() === 'upload')
			$(this).parent().parent().find('.omodal-body').addClass('custom_multiple_upload_bloc');

			if ($(this).val() === 'radio' || $(this).val() === 'checkbox' || $(this).val() === 'dropdown')
			$(this).parent().parent().find('.omodal-body').addClass('default_bloc');
		});
	}
})( jQuery );
