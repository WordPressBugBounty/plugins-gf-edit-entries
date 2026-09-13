(function ($) {
    'use strict';
  
    $(document).ready(function () {

        var foundClasses = [];

        function BpgfeeClickSubmit() {
            $('form[name="bpgf-update-entries"]').on('submit', function () {
                 var form_data = jQuery(this).serializeArray();
        
              
                // Here is the ajax petition.
                $.ajax({
                    url: gf_edit_parms.ajax_url,
                    type: 'POST',
                    data: {
                        //action name (must be consistent with your php callback)
                        action: 'bpgf_update_entry',
                        fdata: form_data,
                        nonce: gf_edit_parms.ajax_nonce_update
                    },
                    async: false,
                    success: function (response) {
                        // You can craft something here to handle the message return
                        console.log(response);
                    },
                    fail: function (err) {
                        // You can craft something here to handle an error if something goes wrong when doing the AJAX request.
                        console.log("There was an error: " + err);
                    }
                });
                console.log(gf_edit_parms.ajax_url);
                // This return prevents the submit event to refresh the page.
                return false;
            });
        }
        $('thead th[class*="column-field_id-"]').each(function () {
            var classes = this.className.split(/\s+/),
                $this = $(this);

            $.each(classes, function (i, name) {
                if (name.indexOf('column-field_id-') === 0) {
                    $this.removeClass(name);
                    foundClasses.push(name.replace('column-field_id-', ''));
                }
            });
        });


        $(".bp_gf_quick_view").each(function () {
            var entryRow = $(this).closest(".entry_row");

            $(this).on("click", function (e) {
                var entryId = $(this).attr('data-lead_id'),
                    formId = $(this).attr('data-form_id');
                
                console.log(gf_edit_parms.ajax_url);
                /*Ajax request URL being stored*/
                jQuery.ajax({
                    url: gf_edit_parms.ajax_url,
                    type: "POST",
                    data: {
                        //action name (must be consistent with your php callback)
                        action: 'gf_form_edit',
                        formId: formId,
                        entryId: entryId,
                        ids: foundClasses,
                        nonce: gf_edit_parms.ajax_nonce
                    },
                    async: false,
                    success: function (data) {
                     
                        $('body').find('#bpgf-quick-edit > .bp-entry-details').append(data);
                        $.fancybox.open({
                            src: '<div id="bpgf-quick-edit">' + data + '</div>',
                            type: 'inline',
                            opts:{
                               afterShow : function () {
                                    BpgfeeClickSubmit();
                               }
                            }
                            
                        });
                    }
                });
            });
        });


        $(".bp_gf_save_entry").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault(); 
                var entry_id = $(this).attr("data-entry_id");
                var lead_id = $(this).attr("data-lead_id");
                /*Ajax request URL being stored*/
                jQuery.ajax({
                    url: gf_edit_parms.ajax_url,
                    type: "POST",
                    data: {
                        //action name (must be consistent with your php callback)
                        action: 'gf_form_edit',
                        entry_id: entry_id,
                        nonce: gf_edit_parms.ajax_nonce
                    },
                    async: false,
                    success: function (data) {
                        console.log(data);
                    }
                });

            });
        });
    });
   


})(jQuery);

// Other code using $ as an alias to the other library