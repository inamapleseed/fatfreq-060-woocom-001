var VPC_CONFIG = (function ($, vpc_config) {
  'use strict';
  var canvases = window.__canvases = {};
  var canvasViews = window._canvasViews = [];
  window.initialise_merge_canvas = function () {
    if (typeof vpc != 'undefined') {

      var _width = vpc.images_sizes.width  ;
      var _height = vpc.images_sizes.height ;

      if (vpc.views) {
        var all_canvases = "";
        var activeViews = JSON.parse(active_views);
        if (typeof (activeViews) !== 'undefined') {
          $.each(activeViews, function (key, view) {
            all_canvases += '<canvas id="merged_imgs_' + key + '" class="ViewsCanvas" style="" width="' + _width + '" height="' + _height + '" data-view-key="'+ key +'"></canvas>';
          });
          $('#vpc_preview_wrapper').html(all_canvases);
        }
      } else {
        $('#vpc_preview_wrapper').html('<canvas id="merged_imgs" style="" class="ViewsCanvas" width="' + _width + '" height="' + _height + '"></canvas>');
      }

      $('canvas.ViewsCanvas').each(function () {
        var fabricCanvasObj = window._canvas = new fabric.Canvas(this, { enableRetinaScaling: false });
        fabricCanvasObj.setDimensions({
          width: _width,
          height: _height,
        }, {backstoreOnly: true});
        if (typeof $(this).data('view-key') !== 'undefined' && $(this).data('view-key') !== '') {
          canvasViews[$(this).data('view-key')] = fabricCanvasObj;
        }else {
          canvasViews.push(fabricCanvasObj);
        }
        fabricCanvasObj.renderAll();
      });
    }
  }

  $(document).ready(function () {
    $(window).on('load', function () {
      if (typeof vpc != "undefined" && vpc != null) {
        if (typeof (active_views) !== 'undefined') {
          var activeViews = JSON.parse(active_views);
        }
        var multi_views = false;
        if (typeof (vpc.config) !== 'undefined') {
          $.each(vpc.config, function (config_key, config_value) {
            if (config_key === 'multi-views' && config_value === 'Yes') {
              multi_views = true;
            }
          });
        }

        var z_index = 1;
        if (typeof (vpc.config) !== 'undefined' && typeof (vpc.config.components) !== 'undefined') {
          $.each(vpc.config.components, function (key, component) {
            if (typeof (component) !== 'undefined') {
              $.each(component, function (ind, val) {
                if (ind === 'c_index') {
                  if (val !== '' && val >= z_index) {
                    z_index = val;
                  }
                }
              });
            }
          });
        }

        var all_canvases_id = {};

        if ($('[id^="userfile_upload_form"]').length > 0 || $('[id$="-field"]').length > 0) {
          if ($('[id^="userfile_upload_form"]').length > 0) {
            $('[id^="userfile_upload_form"]').each(function () {
              if (multi_views) {
                var key = $(this).data('key');
                if (typeof key !== 'undefined') {
                  all_canvases_id[key] = $(this).data('canvas-id');
                }

              } else {
                all_canvases_id[0] = $(this).data('canvas-id');
              }
            });
          }

          if ($('[id$="-field"]').length > 0) {
            $('[id$="-field"]').each(function () {
              if (multi_views) {
                var key = $(this).data('key');
                if (typeof key !== 'undefined') {
                  all_canvases_id[key] = $(this).data('canvas-id');
                }
              } else {
                all_canvases_id[0] = $(this).data('canvas-id');
              }
            });
          }
        } else {
          if (multi_views) {
            $.each(activeViews, function (key, view) {
              all_canvases_id[key] = 'text_and_upload_panel_' + key;
            });
          } else {
            all_canvases_id[0] = 'text_and_upload_panel';
          }
        }

        vpc_set_text_and_upload_canvases(multi_views, z_index, all_canvases_id);
      }
    });

    //var canvasViews = window._canvasViews = [];
   // var nb_images = 0;
    $('#vpc-container').append('<div id="vpc_preview_wrapper" class="" style="position: absolute;"></div>');
    $(window).on("load", function () {
      initialise_merge_canvas();
      var intvl = setInterval(vpc_loading_check, 500);
      function vpc_loading_check() {
        if ($("#vpc-preview img").length || $(".vpc-preview img").length) {
          clearInterval(intvl);
          $('#vpc-preview img, .vpc-preview img').oImageLoad(function () {
            var time = 0;
            var interval = setInterval(function () {
              time = time + .5;
              if (($(".vpc-selected-icon img").length && $(".vpc-selected-icon img").attr('src').length !== 0) || time == 3) {
                clearInterval(interval);
                $("#vpc-loader-container").addClass("fadeOut");
              }
            }, 500);
          });
        }
      }
    });

    var new_variation_attributes = "";
    if (typeof vpc != 'undefined') {
      accounting.settings = {
        currency: {
          symbol: vpc.currency, // default currency symbol is '$'
          format: vpc.price_format, // controls output: %s = symbol, %v = value/number (can be object: see below)
          decimal: vpc.decimal_separator, // decimal point separator
          thousand: vpc.thousand_separator, // thousands separator
          precision: vpc.decimals   // decimal places
        },
        number: {
          precision: vpc.decimals, // default precision on numbers is 0
          thousand: vpc.thousand_separator,
          decimal: vpc.decimal_separator
        }
      }

      $.each(vpc.body_classes, function (key, value) {
        $("body").addClass(value);
      });


    }

    if (typeof wc_cart_fragments_params != 'undefined') {

      var $fragment_refresh = {
        url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
        type: 'POST',
        success: function (data) {
          if (data && data.fragments) {

            $.each(data.fragments, function (key, value) {
              $(key).replaceWith(value);
            });

            $(document.body).trigger('wc_fragments_refreshed');
          }

        }
      };
    }

    function vpc_set_text_and_upload_canvases(multi_views, z_index, all_canvases_id) {
      if (multi_views) {
        if (typeof (active_views) !== 'undefined') {
          var activeViews = JSON.parse(active_views);
        }
        if (typeof (activeViews) !== 'undefined') {
          $.each(activeViews, function (key, view) {
            var canvas_id = all_canvases_id[key];
            if (typeof canvas_id !== 'undefined') {
              var view_preview = setInterval(function () {
                if ($('#preview_' + view).not(".bx-clone").find('#vpc-preview' + key + ' img').length !== 0 && $('#wrapper_' + canvas_id).length === 0) {
                  clearInterval(view_preview);
                  var this_preview = $('#preview_' + view).not(".bx-clone").find('#vpc-preview' + key + ' img');
                  var global_width = this_preview[0].clientWidth;
                  var global_height = this_preview[0].clientHeight;
                  $('#preview_' + view).not(".bx-clone").prepend('<div id="wrapper_' + canvas_id + '" class="" style="z-index:' + z_index + ';"><canvas id="' + canvas_id + '" width="' + global_width + '" height="' + global_height + '"></canvas></div>');
                  canvases[canvas_id] = new fabric.Canvas(canvas_id, { enableRetinaScaling: false });
                  var natural_width = $('#preview_' + view).not(".bx-clone").find('#vpc-preview' + key + ' img')[0].naturalWidth;
                  var natural_height = $('#preview_' + view).not(".bx-clone").find('#vpc-preview' + key + ' img')[0].naturalHeight;


                  canvases[canvas_id].setDimensions({
                    width: natural_width,
                    height: natural_height,
                  }, {backstoreOnly: true});
                  canvases[canvas_id] = wp.hooks.applyFilters('vpc.after_creating_canvas', canvases[canvas_id], canvas_id, multi_views, z_index, key, view);

                }
              });
            }
          });
        }
      } else {
        var canvas_id = all_canvases_id[0];
        var vpc_preview = setInterval(function () {
          if ($('#vpc-preview img').length !== 0 && $('#wrapper_' + canvas_id).length === 0) {
            clearInterval(vpc_preview);
            if ($('.vpc-global-preview').length === 0) {
              $('#vpc-preview').before('<div class="vpc-global-preview"></div>');
              $('.vpc-global-preview').html($('#vpc-preview'));
            }
            var global_width = $("#vpc-preview img")[0].clientWidth;
            var global_height = $("#vpc-preview img")[0].clientHeight;
            $('.vpc-global-preview').prepend('<div id="wrapper_' + canvas_id + '" class="" style="z-index:' + z_index + ';"><canvas id="' + canvas_id + '" width="' + global_width + '" height="' + global_height + '"></canvas></div>');
            canvases[canvas_id] = new fabric.Canvas(canvas_id, { enableRetinaScaling: false });
            var natural_width = $('#vpc-preview img')[0].naturalWidth;
            var natural_height = $('#vpc-preview img')[0].naturalHeight;



            canvases[canvas_id].setDimensions({
              width: natural_width,
              height: natural_height,
            }, {backstoreOnly: true});
            canvases[canvas_id] = wp.hooks.applyFilters('vpc.after_creating_canvas', canvases[canvas_id], canvas_id, multi_views, z_index, '', '');
          }
        });
      }
    }

    window.vpc_oriontip = function () {
      $("[data-oriontip]").oriontip();
    }

    window.vpc_build_preview = function () {
      if (typeof vpc == 'undefined' || (!$("#vpc-add-to-cart").length))
      return;
      $("#vpc-preview").html("");
      if ($("#vpc-add-to-cart").data("price")) {
        if (vpc.decimal_separator == ',')
        var total_price = parseFloat($("#vpc-add-to-cart").data("price").toString().replace(',', '.'));
        else
        var total_price = parseFloat($("#vpc-add-to-cart").data("price"));
      }
      var form_data = $('form.formbuilt').serializeJSON();
      var form_price = get_form_total('form.formbuilt', form_data);
      var total_option_price = 0;
      var configurator_array = [];
      if (!total_price)
      total_price = 0;
      wp.hooks.doAction('vpc.before_preview_builder', selected_items_selector, total_price);
      var selected_items_selector = wp.hooks.applyFilters('vpc.items_selected', vpc.vpc_selected_items_selector);
      var default_preview_builder_process = wp.hooks.applyFilters('vpc.default_preview_builder_process', true);
      if (default_preview_builder_process) {
        $(selected_items_selector).each(function () {
          var component_id = $(this).attr('data-component-id');
          var src = $(this).data("img");
          var option_price = $(this).data("price");
          if (option_price)
          total_option_price += parseFloat(option_price);
          if (src) {
            $("#vpc-preview").append("<img src='" + src + "' style='z-index:" + $(this).data("index") + "' data-component-id='" + component_id + "'>");
            configurator_array.push(src);
          }
        });
        total_price += total_option_price;
        total_price += form_price;
        total_price = wp.hooks.applyFilters('vpc.total_price', total_price);
        $("#vpc-price").html(accounting.formatMoney(total_price));
        wp.hooks.doAction('vpc.after_preview_builder', selected_items_selector, total_price);
      } else
      wp.hooks.doAction('vpc.default_preview_builder_process', selected_items_selector);
    }



    window.vpc_apply_rules = function (selector) {

      if (typeof vpc == 'undefined')
      return;
      if (typeof selector == "undefined")
      selector = vpc.vpc_selected_items_selector;
      var check_selections = false;

      $(selector).each(function (i, e) {
        var item_id = $(this).attr("id");
        var rules_triggered_by_item = vpc.wvpc_conditional_rules[item_id];

        //If there is no rule attached to that component we skip this iteration
        if (typeof rules_triggered_by_item == 'undefined')
        return true;
        $.each(rules_triggered_by_item, function (index, groups_arr) {
          $.each(groups_arr, function (group_index, rules_groups) {
            var group_verified = true;
            $.each(rules_groups.rules, function (rule_index, rule) {
              if (typeof rules_groups.conditions_relationship == "undefined")
              rules_groups.conditions_relationship = "and";
              //Some jquery versions don't return true in these two cases
              var is_selected = $(".vpc-options input[data-oid='" + rule.option + "']").is(':checked');
              if (!is_selected)
              is_selected = $(".vpc-options input[data-oid='" + rule.option + "']").is(':checked');
              if ($("option#" + rule.option).length) {
                is_selected = $("option#" + rule.option).is(':selected');
              }

              is_selected = wp.hooks.applyFilters('vpc.is_option_selected', is_selected, rule.option);

              //If it's an OR relationship, we only need one rule to be true
              if (rules_groups.conditions_relationship == "or" && ((rule.trigger == "on_selection" && is_selected) || (rule.trigger == "on_deselection" && !is_selected))) {
                group_verified = true;
                return false;
              }
              //If it's an or relation and the condition is not met
              else if (rules_groups.conditions_relationship == "or") {
                group_verified = false;
              } else if (rules_groups.conditions_relationship == "and" && ((rule.trigger == "on_selection" && !is_selected) || (rule.trigger == "on_deselection" && is_selected))) {
                group_verified = false;
                return false;
              }
            });

            //
            //If all rules of the group are true
            if (group_verified) {
              //We make sure that the group action has not been applied yet before applying it to avoid infinite loops
              if (rules_groups.result.action == "hide") {
                check_selections = true;
                hide_options_or_component(rules_groups);
              } else if (rules_groups.result.action == "show") {
                check_selections = true;
                show_options_or_component(rules_groups);
              } else if (rules_groups.result.action == "select") {
                check_selections = true;
                select_options_or_component(rules_groups);
              }
            } else if (rules_groups.apply_reverse == "on") {
              if (rules_groups.result.action == "hide") {
                check_selections = true;


                show_options_or_component(rules_groups);
              } else if (rules_groups.result.action == "show") // && $("#" + rules_groups.result.apply_on).not("[style*='display: none;']").length)
              {
                check_selections = true;
                hide_options_or_component(rules_groups);
              } else if (rules_groups.result.action == "select") {
                check_selections = true;
                unselect_options_or_component(rules_groups);
              }
            }


          });

        });
      });
      if (check_selections)
      vpc_build_preview();
    }


    window.vpc_load_options = function () {
      if (typeof vpc !== 'undefined') {
        setTimeout(function () {
          $(vpc.reverse_triggers).each(function (i, e) {
            vpc_apply_rules("#" + e);
          });
          $(vpc.vpc_selected_items_selector).each(function () {
            $(this).trigger('change');
          });
        }, 2000);
      }
    }
    //
    $(document).on('click', '#vpc-qty-container .plus, #vpc-qty-container .minus', function () {

      // Get values
      var $qty = $("#vpc-qty");
      var currentVal = parseFloat($qty.val());
      var max = parseFloat($qty.attr('max'));
      var min = parseFloat($qty.attr('min'));
      var step = $qty.attr('step');

      // Format values
      if (!currentVal || currentVal === '' || currentVal === 'NaN')
      currentVal = 0;
      if (max === '' || max === 'NaN')
      max = '';
      if (min === '' || min === 'NaN')
      min = 0;
      if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN')
      step = 1;

      // Change the value
      if ($(this).is('.plus')) {

        if (max && (max == currentVal || currentVal > max)) {
          $qty.val(max);
        } else {
          $qty.val(currentVal + parseFloat(step));
        }

      } else {

        if (min && (min == currentVal || currentVal < min)) {
          $qty.val(min);
        } else if (currentVal > 0) {
          $qty.val(currentVal - parseFloat(step));
        }

      }

      // Trigger change event
      //            $qty.trigger('change');
    });

    function after_add_to_cart_ajax_result(data) {
      $("#debug").html(data);
      $.ajax($fragment_refresh);
      switch (vpc.action_after_add_to_cart) {
        case 'refresh':
        setTimeout(function () {
          window.location.reload(true);
        }, 50);
        break;
        case 'redirect':
        window.location.href = vpc.cart_url;
        break;
        case 'redirect_to_product_page':
        window.location.href = vpc.current_product_page;
        break;
        default:
        break;
      }
      $('#vpc-add-to-cart').removeClass('disabledClick');
      $('#vpc-add-to-cart').addClass('add-to-cart-button-text-color');
      wp.hooks.doAction('vpc.after_add_to_cart', data);
    }

    $(document).on('click', '#vpc-add-to-cart', function () {
      var form_data = {};
      var div = get_vpc_div_capture();
      var product_id = $(this).data("pid");
      var alt_products = [];
      $('#vpc-container input:checked,#vpc-container option:selected').each(function (i) {
        if ($(this).data("product")) {
          if ($.inArray($(this).data("product"), alt_products) === -1) {
            alt_products.push($(this).data("product"));
          }
        }
      });
      alt_products = wp.hooks.applyFilters('vpc.filter_alt_products', alt_products);

      var quantity = $("#vpc-qty").val();
      var recap = $('#vpc-container').find(':input').serializeJSON();//.serializeJSON();
      recap = wp.hooks.applyFilters('vpc.filter_recap', recap);
      if (recap.id_ofb)
      delete recap.id_ofb;
      var custom_vars = {};

      if (typeof vpc.query_vars["edit"] !== 'undefined')
      custom_vars["item_key"] = vpc.query_vars["edit"];

      custom_vars = wp.hooks.applyFilters('vpc.custom_vars', custom_vars);
      if (vpc.isOfb === true) {
        var form_is_valid = $('form.formbuilt').validationEngine('validate', {showArrow: false});
        if (form_is_valid) {
          form_data = $('form.formbuilt').serializeJSON();
          var process = wp.hooks.applyFilters('vpc.proceed_default_add_to_cart', true);
        }
      } else
      var process = wp.hooks.applyFilters('vpc.proceed_default_add_to_cart', true);

      if (process) {
        $('#vpc-add-to-cart').removeClass('add-to-cart-button-text-color');
        $('#vpc-add-to-cart').addClass('disabledClick');

        if (vpc.views) {
          var multiview = true;
          var imgs = get_finish_image_by_view(vpc.vpc_selected_items_selector);
        } else {
          var multiview = false;
          var imgs = get_finish_image(vpc.vpc_selected_items_selector);
        }

        merge_all_images_of_preview(imgs).then(function (status) {
          if (status === 'completed') {
            setTimeout(function () {
              var canvas_data = {};
              if (typeof window.__canvases !== 'undefined' && window.__canvases.length !== 0) {
                add_custom_texts_and_uploads_datas_on_canvas();
                var canvases = window.__canvases;
                canvas_data = get_canvas_data(canvases);
                recap['canvas_data'] = JSON.stringify(canvas_data);
              }

              custom_vars['preview_imgs_merged'] = JSON.stringify(generate_canvas_images());
              var data_object = {
                action: "add_vpc_configuration_to_cart",
                product_id: product_id,
                alt_products: alt_products,
                quantity: quantity,
                multiview: multiview,
                imgs_by_views: imgs,
                recap: JSON.stringify(recap),
                custom_vars: custom_vars,
                form_data: form_data
              };
              $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: data_object,
                success: function (data) {
                  // console.log(data);
                  nb_images = 0;
                  after_add_to_cart_ajax_result(data);
                }
              });

            }, 200 * nb_images);
          }
        });
      } else
      wp.hooks.doAction('vpc.proceed_default_add_to_cart', custom_vars);

    });

    // function get_canvas_data(canvases) {
    window.get_canvas_data = function (canvases) {
      var continue_process = wp.hooks.applyFilters('vpc.get_canvas_data_process', true);
      if (continue_process) {
        var canvas_data = {};
        $.each(canvases, function (key, val) {
          canvas_data[key] = {};
          var my_objects = val.toJSON(['id', 'name', 'sanitized_name', 'color_name']).objects;
          val.getObjects().map(function (data, index) {
            canvas_data[key][data.id] = my_objects[index];
            if (typeof data.type !== 'undefined' && (data.type === 'group' || data.type === 'textbox' || data.type === 'text')) {
              canvas_data[key][data.id]['text_component_id'] = vpc.text_component_data[data.sanitized_name + '-field'];

              var text = "";
              if (data.type === "text" || data.type === 'textbox') {
                text = canvas_data[key][data.id]['text'];
              } else if (data.type === "group") {
                text = canvas_data[key][data.id]['objects'][1]['text'];
              }

              if (typeof text !== 'undefined' && text !== '' && text.split('\n').length > 1) {
                var text_value = '';
                $.each(text.split('\n'), function (ind, value) {
                  if (ind === 0) {
                    text_value += value;
                  } else {
                    text_value += '|||' + value;
                  }
                });
                if (data.type === "text" || data.type === 'textbox') {
                  canvas_data[key][data.id]['text'] = text_value;
                } else if (data.type === "group") {
                  canvas_data[key][data.id]['objects'][1]['text'] = text_value;
                }
              }

            } else if (typeof data.type !== 'undefined' && data.type === 'image') {
              canvas_data[key][data.id]['upload_component_id'] = vpc.upload_component_data[data.sanitized_name + '-field'];
            }
          });
        });
        canvas_data = wp.hooks.applyFilters('vpc.get_canvas_data', canvas_data, canvases);
        return canvas_data;
      } else {
        wp.hooks.doAction('vpc.get_canvas_data_process', canvases);
      }
    }

    window.get_finish_image_by_view = function (items) {
      var decoded_active_views = JSON.parse(active_views);
      var imgs_by_view = [];
      var i = 0;
      $.each(decoded_active_views, function (index, value) {
        var items_view_selected = [];
        var id = "#vpc-preview" + i;
        $(items).each(function () {
          var ind;

          if ($(this).attr("data-index"))
          ind = $(this).attr("data-index");
          else
          ind = 1;
          if (typeof $(this).attr("data-" + value) !== 'undefined')
          {
            if (typeof (items_view_selected[ind]) !== 'undefined') {
              var arr = items_view_selected[ind];
              arr.push($(this).attr("data-" + value));
              items_view_selected[ind] = arr;
            } else {
              var new_arr = [];
              new_arr.push($(this).attr("data-" + value));
              items_view_selected[ind] = new_arr;
            }
          }
        });
        var items_view = [index, items_view_selected];
        imgs_by_view.push(items_view);
        i++;
      });
      return imgs_by_view;
    }


    /**
    * Function to get finished image.
    *
    * @param  {string} items The key to find the selected options on the configurator.
    * @return {array}        All images of all the selected options.
    */
    window.get_finish_image = function (items) {
      var items_selected_imgs = [];
      $(items).each(function (index) {
        var ind;
        if ($(this).attr("data-index"))
        ind = $(this).attr("data-index");
        else
        ind = 1;
        if (typeof $(this).attr("data-img") !== 'undefined') {
          if (typeof (items_selected_imgs[ind]) !== 'undefined') {
            var arr = items_selected_imgs[ind];
            arr.push($(this).attr("data-img"));
            items_selected_imgs[ind] = arr;
          } else {
            var new_arr = [];
            new_arr.push($(this).attr("data-img"));
            items_selected_imgs[ind] = new_arr;
          }
        }
      });
      return items_selected_imgs;
    }

    function vpc_add_product_attributes_to_btn() {
      var attributes = {};
      var options = $("select[name^='attribute_']");
      var product_id = $("[name='variation_id']").val();
      var new_options = {};
      $.each(options, function () {
        var option_name = $(this).attr("name");
        new_options[option_name] = $(this).find("option:selected").val();
      });
      attributes[product_id] = new_options;
      return attributes;
    }

    $(".single_variation_wrap").on("show_variation", function (event, variation) {
      // Fired when the user selects all the required dropdowns / attributes
      // and a final variation is selected / shown
      var variation_id = $("input[name='variation_id']").val();
      if (variation_id) {
        new_variation_attributes = vpc_add_product_attributes_to_btn();
        $("select[name^='attribute_']").on('change', function () {
          new_variation_attributes = vpc_add_product_attributes_to_btn();
        });
        $(".vpc-configure-button").hide();
        $(".vpc-configure-button[data-id='" + variation_id + "']").show();
      }
    });

    function get_vpc_div_capture() {
      var div;
      if (vpc.views) {
        var target = $(".vpc-preview:not(.bx-clone)");
        target.each(function () {
          var target_id = $(this).attr('id');
          div = $("#" + target_id + ":not(.bx-clone)")[0];
          return false;
        });
      } else {
        if ($(".vpc-global-preview").length > 0)
        div = $(".vpc-global-preview")[0];
        else
        div = $("#vpc-preview")[0];
      }
      return div;
    }


    function hide_options_or_component(rules_groups) {
      //Check the scope and apply the rule if it is required
      if (rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").not("[style*='display: none;']").length)) {
        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").hide();
        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find('input:checked').removeAttr('checked').trigger('change');
      } else if (rules_groups.result.scope == "option" && $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").not("[style*='display: none;']").length) {
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").hide();
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "'] input:checked").removeAttr('checked').trigger('change');
        //We automatically select the next element available
        if (!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length && vpc.select_first_elt == "Yes")
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input").first().prop("checked", true).trigger("change");
      } else if (rules_groups.result.scope == "groups" && $(".vpc-options div.vpc-group div.vpc-group-name").not("[style*='display: none;']").length) {
        $.each($(".vpc-options div.vpc-group div.vpc-group-name"), function () {
          if ($(this).html() == rules_groups.result.apply_on)
          $(this).parent().hide();
        });
      } else if (rules_groups.result.scope == "group_per_component") {
        var split_apply_value = rules_groups.result.apply_on.split('>');
        var component = split_apply_value[0];
        var group = split_apply_value[1];
        $.each($('#' + component + ' .vpc-options .vpc-group .vpc-group-name'), function () {
          if ($(this).html() == group && $(this).not("[style*='display: none;']").length) {
            $(this).parent().hide();
            if ($(this).parent().find("input:checked").length)
            $(this).parent().find("input").removeAttr('checked').trigger('change');
          } else if ($(this).parent().find("option:selected").length) {
            $(this).parent().find("select option:selected").attr("selected", false);
          }
        });
      }

      if (rules_groups.result.scope == "option" && $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").not(":disabled").length) {
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").prop('disabled', true).trigger('change');
        var next_val = $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").siblings("option").not(":disabled").first().val();
        if ($(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").siblings("option").not(":disabled").length) {
          $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").siblings("option").not(":disabled").each(function () {
            if ($(this).is(':selected')) {
              next_val = $(this).val();
            }
          })
        }
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").parent("select").val(next_val).trigger("change");
        //                $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "'] input:checked").removeAttr('checked').trigger('change');
      } else if (rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find("select").length)) {
        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options select > option:selected").prop('selected', false);
        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find("select").prop('disabled', true);
      }
      //            else if (rules_groups.result.scope == "group" && $(".vpc-options div.vpc-group").find("select").length){
      //                $.each($(".vpc-options div.vpc-group div.vpc-group-name"), function(){
      //                    if($(this).html() == rules_groups.result.apply_on)
      //                        $(this).parent().find("select").prop('disabled', true);
      //                });
      //            }

      wp.hooks.doAction('vpc.hide_options_or_component', rules_groups);
    }

    function show_options_or_component(rules_groups) {
      //Check the scope and apply the rule if it is required
      if (rules_groups.result.scope == "component" && $("#vpc-container div.vpc-component[data-component_id='" + rules_groups.result.apply_on + "'][style*='display: none;']").length) {
        //if ((rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "][style*='display: none;']").length))
        //  || !(rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").is(":visible")))) {

        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").show();
        if ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find('.vpc-options select').length) {
          if ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options select > option[data-default]").length) {
            $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options select > option[data-default]").prop("selected", true).trigger("change");
          }else if ((!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find("select").not("[style*='display: none;']").find("option:selected").length) && vpc.select_first_elt == "Yes") {
            var first = $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options select > option").first();
            $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options select").select(first);
          }
        }else {
          if ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input[data-default]").length){
            $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input[data-default]").prop("checked", true).trigger("change");
          }else if ((!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length) && vpc.select_first_elt == "Yes"){
            $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input").first().click();
          }
        }

      } else if (rules_groups.result.scope == "option" && $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "'][style*='display: none;']").length) {
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").show();
        //If there is no element checked, we automatically slect the next element available
        if (!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length) {
          if (!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length && vpc.select_first_elt == "Yes")
          $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input").first().prop("checked", true).trigger("change");
        }
      } else if (rules_groups.result.scope == "groups" && $(".vpc-options div.vpc-group[style*='display: none;']").length) {
        $.each($(".vpc-options div.vpc-group div.vpc-group-name"), function () {
          if ($(this).html() == rules_groups.result.apply_on) {
            $(this).parent().show();
            $(this).parents(".vpc-options").find("input").first().click();
          }
        });
      } else if (rules_groups.result.scope == "group_per_component") {
        var split_apply_value = rules_groups.result.apply_on.split('>');
        var component = split_apply_value[0];
        var group = split_apply_value[1];
        $.each($('#' + component + ' .vpc-options .vpc-group .vpc-group-name'), function () {
          if ($(this).html() == group) {
            $(this).parent().show();
            if (!$(this).parents(".vpc-options").find("input:checked").length && vpc.select_first_elt == "Yes") {
              $(this).parent().find("input").first().click()
            } else if (!$(this).parents(".vpc-options").find("input:checked").length && vpc.select_first_elt == "No") {
              $(this).parent().find("input[data-default=1]").click()
            }

            if ($(this).parent().find("select option:eq(1)").length) {
              $(this).parent().find("select option:eq(1)").attr("selected", true);
              $(this).parents('.vpc-component').find('.vpc-selected-icon img').attr('src', $(this).parent().find("select option:eq(1)").data('icon'));
              $(this).parents('.vpc-component').find('.vpc-selected').html($(this).parent().find("select option:eq(1)").val());
            }
            //$(this).parents(".vpc-options").find("input").first().click();
          }
        });
      }
      if (rules_groups.result.scope == "option" && $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']:disabled").length) {
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").prop('disabled', false);
      } else if (rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find("select").length)) {
        $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find("select").prop('disabled', false);
      }
      //            else if (rules_groups.result.scope == "group" && $(".vpc-options div.vpc-group")){
      //                $.each($(".vpc-options div.vpc-group div.vpc-group-name"), function(){
      //                    if($(this).html() == rules_groups.result.apply_on)
      //                        $(this).parent().find("select").prop('disabled', false);
      //                });
      //            }
      wp.hooks.doAction('vpc.show_options_or_component', rules_groups);
    }

    function select_options_or_component(rules_groups) {
      //Check the scope and apply the rule if it is required
      //            if (rules_groups.result.scope == "component" && $("#vpc-container div.vpc-component[data-component_id='" + rules_groups.result.apply_on + "'][style*='display: none;']").length)
      //            {
      //                $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").show();
      //                if ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input[data-default]").length)
      //                    $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input[data-default]").click();
      //                else
      //                    $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find(".vpc-options input").first().click();
      //            } else
      if (rules_groups.result.scope == "option" && $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").length) {
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").show();
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']>input").prop('checked', true).trigger('change');

        //                console.log("showing "+rules_groups.result.apply_on);
        //                console.log($(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parent(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length)
        //If there is no element checked, we automatically slect the next element available
        //                if (!$(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input:checked").length)
        //                {
        //                    $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").parents(".vpc-options").find(".vpc-single-option-wrap").not("[style*='display: none;']").find("input").first().prop("checked", true).trigger("change");
        //                }
      } else if (rules_groups.result.scope == "option" && $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").length) {
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").show();
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").prop('selected', true);
        var option_value = $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").val();
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").parents('.vpc-component').find('.vpc-component-header .txt').html(option_value);
      }
      wp.hooks.doAction('vpc.select_options_or_component', rules_groups);
    }


    function unselect_options_or_component(rules_groups) {
      //Check the scope and apply the rule if it is required
      //            if (rules_groups.result.scope == "component" && ($("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").not("[style*='display: none;']").length))
      //            {
      //                $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").hide();
      //                $("#vpc-container div.vpc-component[data-component_id=" + rules_groups.result.apply_on + "]").find('input:checked').removeAttr('checked').trigger('change');
      //            } else
      if (rules_groups.result.scope == "option" && $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']").not("[style*='display: none;']").length) {
        $(".vpc-options div[data-oid='" + rules_groups.result.apply_on + "']>input").prop('checked', false).trigger('change');
      } else if (rules_groups.result.scope == "option" && $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").not("[style*='display: none;']").length) {
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").prop('selected', false);
        var option_value = 'none';
        if ($(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").parents('select').find('option:selected').val().length) {
          option_value = $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").parents('select').find('option:selected').val();
        }
        $(".vpc-options option[data-oid='" + rules_groups.result.apply_on + "']").parents('.vpc-component').find('.vpc-component-header .txt').html(option_value);
      }
      wp.hooks.doAction('vpc.hide_options_or_component', rules_groups);
    }

    //We're in ajax mode
    if ($("#vpc-ajax-container").length) {
      //            console.log("editor loading");
      $.post(
        ajax_object.ajax_url,
        {
          action: "get_vpc_editor",
          vpc: vpc,
        },
        function (data) {
          //console.log(data);

          vpc_load_options();
          removeEmptyImage();
          wp.hooks.doAction('vpc.ajax_loading_complete');
          /* $("#vpc-ajax-container").append(data);
          setTimeout(function(){
          vpc_ds_load_tooltips();
          $('#vpc-ajax-loader-container').hide();
        },2000);*/

        $("#vpc-ajax-container").append(data).promise().done(function () {
          // vpc_ds_load_tooltips();
          vpc_oriontip();
          $('#vpc-ajax-loader-container').hide();
        });
      });
    }

    $(document).on("click", ".cart .vpc-configure-button", function (e) {
      e.preventDefault();
      var product_id = $("[name='variation_id']").val();
      if (!product_id)
      product_id = $(this).parents().find('[name="add-to-cart"]').val();
      var qty = $(this).parents().find("input[name='quantity']").val();
      if (!qty)
      qty = 1;
      var process = wp.hooks.applyFilters('vpc.proceed_default_build_your_own', true);
      if (process) {
        $.post(
          ajax_object.ajax_url,
          {
            action: "get_vpc_product_qty",
            prod_id: product_id,
            qty: qty,
            new_variation_attributes: new_variation_attributes
          },
          function (data) {
            //console.log(data);
            // e.parents().find('.vpc-configure-button').attr('href',data);
            window.location = data;
          });
        } else {
          wp.hooks.doAction('vpc.proceed_default_build_your_own', product_id, qty);
        }
      });

      var global_previously_selected = "";
      $(document).on("change", ".vpc-options select", function (e) {

        vpc_build_preview();
        vpc_apply_rules();
        var img = $(this).find("option:selected").data('img');
        var val = $(this).val();
        var id = $(this).attr("id");
        $(this).parents('.vpc-component').find('.vpc-selected-icon img').attr('src', img);
        $(this).parents('.vpc-component').find('.vpc-selected').html(val);
        //Reverse rules management
        if (global_previously_selected && id != global_previously_selected) {
          //                console.log(global_previously_selected);
          vpc_apply_rules("#" + global_previously_selected);
        }
      }).on('click', ".vpc-options select", function (e) {
        global_previously_selected = $(this).find("option:selected").attr('id');
      });

      $(document).on("change", ".vpc-options select>option", function (e) {
        $(this).parent().trigger("change");
      });

      $(document).on("change", ".vpc-options input", function (e) {
        $('.vpc-component-header > span.vpc-selected-icon img[src=""]').hide();
        $('.vpc-component-header > span.vpc-selected-icon img:not([src=""])').show();
      });

      $(document).on("change", 'form.formbuilt input', function (e) {
        window.vpc_build_preview();
      });

      $(document).on("change", 'form.formbuilt textarea', function (e) {
        window.vpc_build_preview();
      });

      $(document).on("change", 'form.formbuilt select', function (e) {
        window.vpc_build_preview();
      });

      /*wp.hooks.addFilter('vpc.total_price', update_total_price);

      function update_total_price(price) {
      var form_data = $('form.formbuilt').serializeJSON();
      var form_price = get_form_total('form.formbuilt', form_data);
      price += form_price;
      return price;
    }*/

    window.get_form_total = function (form_id, fields) {
      var total_price = 0;
      $(form_id).find('[name]').each(function (index, value) {
        var that = $(this),
        name = that.attr('name'),
        type = that.prop('type');
        if (type == 'select-one') {
          $(that).find('[value]').each(function (index, value) {
            var option = $(this);
            var price = option.attr('data-price');
            var value = option.attr('value');
            for (var i in fields) {
              if (name == i && value == fields[i]) {
                if (undefined !== price && '' !== price) {

                  total_price += parseFloat(price);
                }
              }
            }
          });
        } else if (type == 'radio') {
          var price = that.attr('data-price');
          for (var i in fields) {
            if (name == i) {
              if (typeof (fields[i]) == 'object') {
                var options = fields[i];
                for (var j in options) {
                  if (value.value == options[j]) {
                    if (undefined !== price && '' !== price) {
                      total_price += parseFloat(price);
                      // console.log(total_price);
                    }
                  }
                }
              } else {
                if (value.value == fields[i]) {
                  if (undefined !== price && '' !== price) {
                    total_price += parseFloat(price);
                    // console.log(total_price);
                  }
                }
              }
            }
          }
        } else if (type == 'checkbox') {
          var price = that.attr('data-price');
          for (var i in fields) {
            if (name == i + '[]') {
              if (typeof (fields[i]) == 'object') {
                var options = fields[i];
                for (var j in options) {
                  if (value.value == options[j]) {
                    if (undefined !== price && '' !== price) {
                      total_price += parseFloat(price);
                      // console.log(total_price);
                    }
                  }
                }
              } else {
                if (value.value == fields[i]) {
                  if (undefined !== price && '' !== price) {
                    total_price += parseFloat(price);
                    // console.log(total_price);
                  }
                }
              }
            } else {
              if (value.value == fields[i]) {
                if (undefined !== price && '' !== price) {
                  total_price += parseFloat(price);
                  // console.log(total_price);
                }
              }
            }
          }
        } else if (type == 'file') {
          var price = that.attr('data-price');
          var file = that.prop('files');
          var files = get_files_in_ofb();
          if (file[0]) {
            for (var i in files) {
              if (name == i) {
                if (undefined !== price && '' !== price) {
                  total_price += parseFloat(price);
                  // console.log(total_price);
                }
              }
            }
          }
        } else {
          var price = that.attr('data-price');
          var value = that.val();
          if (value.length > 0) {
            for (var i in fields) {
              if (name == i) {
                if (undefined !== price && '' !== price) {
                  total_price += parseFloat(price);
                  // console.log(total_price);
                }
              }
            }
          }
        }

      });
      return total_price;
    }

    window.get_files_in_ofb = function () {
      var files = [];
      $('form.formbuilt').find('[name]').each(function (index, value) {
        var that = $(this),
        name = that.attr('name'),
        value = that.attr('value'),
        type = that.prop('type');
        if (type == 'file') {
          if (value != null)
          files[name] = value;
        }
      });
      return files;
    }
    removeEmptyImage();
    function removeEmptyImage() {
      $('.vpc-selected-icon').each(function (index, value) {
        if (typeof $(this).find('img').attr('src') != "undefined" && $(this).find('img').attr('src').length == 0) {
          $(this).find('img').hide();
        }
      });

    }

    $(document).on("click", ".reset_variations", function (e) {
      $('.variations_button .vpc-configure-button.button').hide();
    });

    /****** canvas merge code Begin******/

    // add canvas on preview
    window.nb_images = 0;


// add image on canvas

window.add_image_on_canvas = function (images, canvas) {
  return new Promise(function (resolve, reject) {
    var a;
    for (var i = 0; i < images.length; i++) {
      if ($.isArray(images[i])) {
        for (var j = 0; j < images[i].length; j++) {
          if ($.isArray(images[i][j])) {
            nb_images += images[i][j].length;
            for (var k = 0; k < images[i][j].length; k++) {
              fabric.Image.fromURL(images[i][j][k], function (myImg) {
                myImg.set({
                  originX: "center",
                  originY: "center"
                });
                canvas.add(myImg);
                canvas.centerObject(myImg);
              });
            }
          } else {
            if (typeof images[i][j] !== 'undefined') {
              nb_images = images[i].length;
              fabric.Image.fromURL(images[i][j], function (myImg) {
                myImg.set({
                  originX: "center",
                  originY: "center"
                });
                canvas.add(myImg);
                canvas.centerObject(myImg);
              });
            }
          }
        }
      }
      canvas.renderAll();
      a = i;
    }
    if (a === (images.length - 1)) {
      resolve('completed');
    }
  });
}

// merge canvas images


window.merge_all_images_of_preview = function (imgs) {
  if (typeof vpc != 'undefined') {
    if (vpc.views) {
      return new Promise(function (resolve, reject) {
        $.each(imgs, function (ind, data) {
          if (typeof canvasViews[data[0]] !== 'undefined' && canvasViews[data[0]] !== '') {
            add_image_on_canvas(data[1], canvasViews[data[0]]).then(function (result) {
              if (result === 'completed' && ind === (imgs.length - 1)) {
                resolve(result);
              }
            });
          }
        });
      });
    } else {
      return new Promise(function (resolve, reject) {
        add_image_on_canvas(imgs, canvasViews[0]).then(function (result) {
          if (result === 'completed') {
            resolve(result);
          }
        });
      });
    }
  }
}
// ADD custom texts and custom uploads datas on canvas
window.add_custom_texts_and_uploads_datas_on_canvas = function () {
  var canvases = window.__canvases;
  $.each(canvases, function (index, canvas) {
    var new_index = index.replace('text_and_upload_panel_', '');
    if (new_index === 'text_and_upload_panel') {
      new_index = 0;
    }
    if (typeof canvasViews[new_index] !== 'undefined') {
      canvas.getObjects().map(function (data, key) {
        canvasViews[new_index].add(data);
      });
      canvasViews[new_index].renderAll();
    }
  });
}
// generate canvas images
window.generate_canvas_images = function () {
  var preview_imgs_merged = {};
  $.each(canvasViews, function (index, canvas) {
    if (typeof canvas !== 'undefined' && canvas !== '') {
      preview_imgs_merged[index] = canvas.toDataURL({
        format: 'png',
        quality: 1.0,
      }).replace('data:image/png;base64,', '');
    }
  });
  return preview_imgs_merged;
}



});
return vpc_config;
}(jQuery, VPC_CONFIG));
