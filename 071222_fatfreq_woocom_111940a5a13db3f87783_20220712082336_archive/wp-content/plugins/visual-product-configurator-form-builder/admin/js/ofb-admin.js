(function ($) {
    'use strict';
    $(document).ready(function ($) {
        setTimeout(function ()
        {
            //check if at least one component has been created
            if ($('body.post-type-ofb').length > 0)
            {
                // call function to reindex components
                reindex_components_table();
                $("#ofb-config-components-table > tbody").sortable({
                    update: function (event, ui) {
                        reindex_components_table();
                    }
                });
            }

            add_sortable_options_callback();
            update_components_id();
            update_options_id();

        }, 3000);

        function reindex_components_table()
        {
            $("#ofb-config-components-table > tbody > tr").each(function (i, e)
            {
                //Replace the inputs names
                var prefix = "ofb[components][" + i + "]";
                $(this).find(":input[name^='ofb[components]']").each(function (i2, e2)
                {
                    var new_name = this.name.replace(/ofb\[components\]\[\d+\]/, prefix);
//                    console.log(this.name+" => "+new_name);
                    $(this).attr("name", new_name);
                });
            });
        }

        function reindex_options_table()
        {
            $(".o-modal .omodal-body>.table-fixed-layout> tbody > tr:visible").each(function (i, e)
            {
                //Replace the inputs names
                var replacement = "[options][" + i + "]";
                $(this).find(":input[name*='[options]']").each(function (i2, e2)
                {
                    var new_name = this.name.replace(/\[options\]\[\d+\]/, replacement);
//                    console.log(this.name+" => "+new_name);
                    $(this).attr("name", new_name);
                });
            });
        }

        function add_sortable_options_callback()
        {
            if ($(".o-modal .table-fixed-layout>tbody").length > 0)
            {
                $(".o-modal .table-fixed-layout>tbody").sortable({
                    update: function (event, ui) {
                        reindex_options_table();
                    }
                });//.disableSelection();
            }
        }

        $("#ofb-config-container>div>table>tbody>tr>td>.add-rf-row").click(function ()
        {
            setTimeout(add_sortable_options_callback, 1000);
        });


        function update_components_id() {
            $('.ofb-component-id[value=""]').each(function (index, component_id_field) {
                var component_id = o_uniqid("component-");
                $(component_id_field).val(component_id);
                var old_component_id = 'component_' + sanitize_title($(component_id_field).parent().parent().find('.vpc-cname').val().replace(/ /g, ''));

                $('option[value="' + old_component_id + '"]').attr('value', component_id);
            })
        }

        function update_options_id() {
            $('.ofb-option-value[value=""]').each(function (index, option_id_field) {
                var old_option_id = 'component_' + sanitize_title($(option_id_field).parents('.omodal').parents().parents().find('.vpc-cname').val().replace(/ /g, '')) + '_group_' + sanitize_title($(option_id_field).parents().parent().find('.vpc-option-group').val().replace(/ /g, '')) + '_option_' + sanitize_title($(option_id_field).parent().parent().find('.vpc-option-name').val().replace(/ /g, ''));

                var option_id = o_uniqid("option-");
                $('option[value="' + old_option_id + '"]').attr('value', option_id);
                $(option_id_field).val(option_id);
            });

        }

        $(document).on("click", ".add-rf-row", function (e) {
            setTimeout(function () {
                update_components_id();
                update_options_id();
            }, 200);
        });


        $(document).on("change", ".default-config", function (e) {
            $(this).parents(".omodal-body").find("input[type=radio]").not($(this)).attr('checked', false);
        });

        $(document).on("click", ".default-config", function (e) {
            if ($('.default-config').is(':checked')) {
                this.checked = true;
            }
        });

        $(document).on("click", ".add-rf-row", function () {

            var rowCount = $('#ofb-config-components-table>tbody>tr').length;
            for (var i = 0; i < rowCount; i++) {
                switch ($("select[name='ofb[components][" + i + "][type]']").val()) {
                    case "text":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();

                        break;

                    case "textarea":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();
                        break;

                    case "email":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();
                        break;

                    case "file":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();
                        break;

                    case "checkbox":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();
                        break;
                    case "password":
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();
                        break;

                    default:
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.o-modal-trigger').show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-length').hide();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-required').parent().show();
                        $("select[name='ofb[components][" + i + "][type]']").parent().parent().find('.ofb-name').show();

                        break;
                }
            }


        });


        var rowCount = $('#ofb-config-components-table>tbody>tr').length;
        for (var i = 0; i < rowCount; i++) {
            switch ($('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').val()) {
                case "text":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;

                case "textarea":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;

                case "email":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;

                case "file":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;

                case "checkbox":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-price').hide();
                    break;

                case "radio":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-price').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    break;

                case "select":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-price').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    break;

                case "password":
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;

                default:
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.o-modal-trigger').show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-length').hide();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-required').parent().show();
                    $('#ofb-config-components-table>tbody>tr:eq(' + i + ') select').parent().parent().find('.ofb-name').show();
                    break;
            }
        }


        $(document).on("change", "#ofb_types", function (e) {

            switch (e.currentTarget.value) {
                case "text":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').show();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;
                case "file":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;
                case "textarea":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;

                case "email":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;

                case "file":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;

                case "checkbox":
                    $(this).parent().parent().find('.o-modal-trigger').show();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-price').hide();
                    break;

                case "select":
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.o-modal-trigger').show();
                    $(this).parent().parent().find('.ofb-price').hide();
                    break;

                case "radio":
                    $(this).parent().parent().find('.o-modal-trigger').show();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-price').hide();
                    break;

                case "password":
                    $(this).parent().parent().find('.o-modal-trigger').hide();
                    $(this).parent().parent().find('.ofb-length').show();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;

                default:
                    $(this).parent().parent().find('.o-modal-trigger').show();
                    $(this).parent().parent().find('.ofb-length').hide();
                    $(this).parent().parent().find('.ofb-required').parent().show();
                    $(this).parent().parent().find('.ofb-name').show();
                    $(this).parent().parent().find('.ofb-price').show();
                    break;
            }
        });

        // sanitize title function
        function sanitize_title(value) {
            var rExps = [
                {re: /[\xC0-\xC6]/g, ch: 'A'},
                {re: /[\xE0-\xE6]/g, ch: 'a'},
                {re: /[\xC8-\xCB]/g, ch: 'E'},
                {re: /[\xE8-\xEB]/g, ch: 'e'},
                {re: /[\xCC-\xCF]/g, ch: 'I'},
                {re: /[\xEC-\xEF]/g, ch: 'i'},
                {re: /[\xD2-\xD6]/g, ch: 'O'},
                {re: /[\xF2-\xF6]/g, ch: 'o'},
                {re: /[\xD9-\xDC]/g, ch: 'U'},
                {re: /[\xF9-\xFC]/g, ch: 'u'},
                {re: /[\xC7-\xE7]/g, ch: 'c'},
                {re: /[\xD1]/g, ch: 'N'},
                {re: /[\xF1]/g, ch: 'n'}];

            // converti les caractères accentués en leurs équivalent alpha
            for (var i = 0, len = rExps.length; i < len; i++)
                value = value.replace(rExps[i].re, rExps[i].ch);

            // 1) met en bas de casse
            // 2) remplace les espace par des tirets
            // 3) enleve tout les caratères non alphanumeriques
            // 4) enlève les doubles tirets
            // 6) enlève les tirets en début de chaine
            // 6) enlève les tirets en fin de chaine
            return value.toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '')
                    .replace(/\-{2,}/g, '-')
                    .replace(/^\-/g, '')
                    .replace(/\-$/, '');
        }

    });

})(jQuery);
