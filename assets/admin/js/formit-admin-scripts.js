jQuery(document).ready(function ($) {
 
     /**
     * Form Builder Setting value coming from Database
     * @BuilderFormaData from this php method
     */
   
    const demoJson = '[{\"type\":\"text\",\"required\":false,\"label\":\"Text Field\",\"className\":\"form-control\",\"name\":\"text-1695380174875-0\"},{\"type\":\"button\",\"subtype\":\"submit\",\"label\":\"Send\",\"name\":\"button-1695380176384-0\"}]';
    let formJson = formit_scripts_localize.GetBuilderJson ? formit_scripts_localize.GetBuilderJson : demoJson
    const cleanedJsonString = formJson.replace(/\\/g, '') || '';
    const FormJsonBuilder = JSON.parse(cleanedJsonString);

    /**
     * This callback using on form builder option
     * @
     */
    function formDataGenerator(formSelector, type='json') {
        if(typeof formSelector != 'object') return 'Selector define wrong!'
        var jsonData = formSelector.actions.getData("json");
        if(type == 'json'){
            return jsonData
        }else if(type == 'html'){
            const formData = jsonData;
            const $markup = $("<div/>");
            $markup.formRender({ formData });
            return $markup.formRender("html");

        }else{
            return 'type missing';
        }
    }

    // Builder Options
    let subtypes = {
        text: ['url', 'tel']
      }
    let disabledSubtypes =  {
        text: ['password'],
    }
    let fields = [
        {label: "URL/Link", type: "text", subtype: "url", icon: "<i class='formbuilder-icon-link'></i> "},
        {label: "Email", type: "text", subtype: "email", icon:"<i class='formbuilder-icon-mail'></i> "},
        {label: "Time", type: "date", subtype: "time", icon:"<i class='formbuilder-icon-clock'></i> "},
    ];
    const preBuilderFields = ['autocomplete', 'button', 'checkbox-group', 'date', 'file', 'header', 'hidden', 'number', 'paragraph', 'radio-group', 'select', 'text', 'textarea', 'button'];
    const preBuilderAttrs = ['access', 'className', 'description', 'inline', 'label', 'max', 'maxlength', 'min', 'multiple', 'name', 'options', 'other', 'placeholder', 'required', 'rows', 'step', 'style', 'subtype', 'toggle', 'value'];
    let disableFields = ['autocomplete', 'file', 'hidden'];
    let disabledAttrs = ['access', 'description', 'inline', 'multiple', 'name', 'other', 'rows', 'step', 'maxlength', 'style', 'toggle'];

    
    
    // Get builder config from user's modification
    const usersBuilderConfig = formit_scripts_localize.Form_settings_data;

    if (usersBuilderConfig && typeof usersBuilderConfig === 'object' && Object.keys(usersBuilderConfig).length) {
        const enableFields = [];
        const enabledAttrs = [];

        for (const key in usersBuilderConfig) {
            if (key.startsWith('form_option_')) {
                enableFields.push(key.substring('form_option_'.length));
            } else if (key.startsWith('form_attr_')) {
                enabledAttrs.push(key.substring('form_attr_'.length));
            }
        }

        if (enableFields.length) {
            disableFields = preBuilderFields.filter(field => !enableFields.includes(field));
        }

        if (enabledAttrs.length) {
            disabledAttrs = preBuilderAttrs.filter(field => !enabledAttrs.includes(field));
        }
    }
    var options = {
        i18n: {
            locale: 'en-US',
            location: formit_ajax_localize.plugin_url+'/assets/admin/lang/'
        },
        enableEnhancedBootstrapGrid: true,
        enableColumnInsertMenu: true,
        defaultGridColumnClass: 'col-md-12',
        cancelGridModeDistance: 100,
        dataType: "json",
        formData: FormJsonBuilder,
        fields,
        subtypes,
        disabledSubtypes,
        disableFields,
        disabledAttrs,
        disabledActionButtons: ["data", "save", "clear"],
    };

    /**
     * FormBuilder Iitlial
     */
    var fbEditor = $(".build-wrap");
    var formBuilder = $(fbEditor).formBuilder(options);



    /**
     * formit submit action
     */
    $("#publish").on("click", function (e) {
        e.preventDefault();
        // return false
        let htmlData            = formDataGenerator(formBuilder, 'html'); // Builder Dom
        let jsonData            = formDataGenerator(formBuilder, 'json'); // Builder Json
        var JsonUniqueLabelData = JSON.parse(jsonData); // Json Prase for Unique label

        console.log(JsonUniqueLabelData);

        var JsonUniqueLabelStringfy = JSON.stringify(JsonUniqueLabelData); // Json Stringfy 

        $('#publishing-action .spinner').css("visibility", "visible"); // Spiner default disable 
        var formData        = $("#post").serialize(); // Form Data for  get
        var fromTemplate    = tinymce.get('mail_body').getContent();
        console.log(formData);
        $.ajax({
            url: formit_ajax_localize.ajax_url, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'process_form_message_submission',
                formData: formData,
                htmlData: htmlData,
                fromTemplate: fromTemplate,
                jsonData: JsonUniqueLabelStringfy,
                nonce: formit_ajax_localize.nonce
            },
            success: function(response) {
                // [
                //     {
                //         "formit_mail_to": "hello@xirosoft.com",
                //         "formit_sender_mail": "shahzobayer@gmail.com",
                //         "msfrom_mail_subject": "Your Subject",
                //         "formit_mail_additional_headers": "Reply-To:",
                //         "formit_mail_body": " Hi [Recipient\\'s Name], \r\n I hope you\\'re doing well. I wanted to [briefly state the purpose of your email]. [Include any necessary details or requests concisely.] [Optional: Add a closing sentence or call to action.]\r\n   \r\n                                   Best regards\r\n                                   [Your Name]                             ",
                //         "msfrom_redirect": {
                //             "msfrom_popup_message": "Thanks for Submit",
                //             "msfrom_external_url": "http:\/\/localhost\/maxon",
                //             "msfrom_internal_page": "https:\/\/xirosoft.com\/"
                //         }
                //     }
                // ]
                // Check if the response is a JSON object with errors
                console.log(response);
                try {
                    var responseData = JSON.parse(response);
                    if (responseData.errors) {
                        // Handle errors and display them in a popup
                        alert(responseData.errors.join('\n'));
                    } else {
                        // If no errors, proceed with form submission
                        $("#post").submit();
                    }
                } catch (e) {
                    if(response.errors){
                        // displayErrorPopup(response.errors, 'You can updated anything again you need');
                        showToast(response.errors, "toast-error");
                    }else{
                        goEditURL(response.home_url);
                        // displayErrorPopup(response.success, 'You can updated anything again you need');
                        $('#publishing-action .spinner').css("visibility", "hidden");
                        showToast(response.success, "toast-success");
                    }
                }
            }
        });


        return false;
        
    });

    /**
     * Show Toast
     * @param {*} message 
     * @param {*} styleClass  showToast("Warning!", "toast-warning"); showToast("Success!", "toast-success"); showToast("Error!", "toast-error");
     */
    function showToast(message, styleClass) {
        const toast = $("<div>", {
            class: "toast " + styleClass,
            text: message,
        });

        const closeButton = $("<span>", {
            class: "close-button",
            text: "×",
        });

        closeButton.click(function() {
            toast.remove();
        });

        toast.append(closeButton);

        $("#toast-container").append(toast);

        setTimeout(function() {
            toast.css("opacity", 1);
        }, 100);

        setTimeout(function() {
            toast.css("opacity", 0);
        }, 3000);

        setTimeout(function() {
            toast.remove();
        }, 3300);
    }

 
    /**
     * formit submit action
     */
    $("#formit_settings_submit").on("click", function (e) {
        e.preventDefault();
        // Serialize the form data
        var formData = $("#form_settings").serialize();
        console.log(formData);
        $.ajax({
            url: formit_ajax_localize.ajax_url, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'form_settings_data',
                formData: formData,
                nonce: formit_ajax_localize.nonce
            },
            success: function(response) {
                console.log(response);
                showToast(response.success, "toast-success");
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showToast('Something wrong! Please check the setting options', "toast-error");
                console.error('AJAX Error: ' + textStatus, errorThrown);
            }
        });


        return false;
        
    });
 

    function goEditURL(home_url) {
        // Get the current URL
        var queryParams = getQueryParams(); // Parse query parameters
        var post_id = $('#post_ID').val();
            // Check if the 'post' parameter exists
            if (queryParams.post_type == 'formit') {
                var editUrl = `${home_url}/wp-admin/post.php?post=${post_id}&action=edit`;
                // Redirect to the edit URL
                setTimeout(() => {
                    window.location.assign(editUrl);                    
                }, 3000);
            } else {
                console.error('Missing "post" parameter in the query string.');
                // Handle the case where the 'post' parameter is missing
            }
       
    }
    
    // Function to parse query parameters into an object
    function getQueryParams() {
        var queryParams = {};
        var queryString = window.location.search.substring(1); // Exclude the "?" character
        var pairs = queryString.split('&');

        for (var i = 0; i < pairs.length; i++) {
            var pair = pairs[i].split('=');
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1] || '');
            queryParams[key] = value;
        }
        return queryParams;
    }

  
    /**
     * Function to display a custom error popup.
     * @param {String} title 
     * @param {String} message 
     */
    function displayErrorPopup(title, message) {
        // Create a modal or popup element and append it to the body.
        var popupData = $(
            '<div class="formit_popup_messge_content"><h2>' +
                title +
                "</h2><p>" +
                message +
                "</p></div>"
        );
        var popup = $(".formit__popup");

        $(popup).html(popupData);
        popup.show();
        $(popup).css({"visibility": "visible", "opacity": "1"});
        // Optionally, set a timeout to hide the popup after a certain duration.
        setTimeout(function () {
            popup.hide();
        }, 3000); // 5000 milliseconds (5 seconds) in this example.
    }

    /**
     * Window loading method call
     */
    $(window).on("load", function () {
        var popup = $('<div class="formit__popup"></div>  <div id="toast-container"></div>');
        $("body").append(popup);
    });


    /**
     * Function to apply filters
     */
    
    $('#msfrom_redirect').on('change', function() {
        var selectedOption = $(this).val();
        var dynamicFields = $('.dynamic-fields');
        dynamicFields.empty(); // Clear previous fields

        if (selectedOption === 'popup') {
            dynamicFields.append('<textarea name="msfrom_popup_message"></textarea>');
        } else if (selectedOption === 'external') {
            dynamicFields.append('<input type="url" name="msfrom_external_url" placeholder="External URL" requird>');
        } else if (selectedOption === 'internal') {
            getWpPages();            
        }
    });


    function getWpPages(current_url) {
        var dynamicFields = $('.dynamic-fields');
        var pagesDropdown = '<select name="msfrom__internal_page">';
            pagesDropdown += '<option value="">Select a Page</option>';
    
            $.ajax({
                url: formit_ajax_localize.ajax_url,
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'get_wp_pages',
                    nonce: formit_ajax_localize.nonce
                },
                success: function(response) {
                    console.log(response);
                    if (response.pages) {
                        $.each(response.pages, function(index, page) {
                            pagesDropdown += '<option value="' + page.page_link + '" ' + (page.page_link === current_url ? 'selected' : '') + '>' + page.post_title + '</option>';
                        });
                    }
                    pagesDropdown += '</select>';
                    return dynamicFields.append(pagesDropdown);
                },
            });
            
    }


    
    $(window).on('load', function () {
        var from_id         = $('#post_ID').val();
        var dynamicFields   = $('.dynamic-fields');

        if(dynamicFields.length){
            dynamicFields.empty(); // Clear previous fields
            $.ajax({
                url: formit_ajax_localize.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'from_after_submission',
                    from_id,
                    nonce: formit_ajax_localize.nonce
                },
                success: function(response) {
                    if(!response) return;
                    if(response.options == 'popup'){
                        dynamicFields.append(`<textarea name="msfrom_popup_message">${response.msfrom_submission_data}</textarea>`);
                    }else if(response.options == 'external'){
                        dynamicFields.append(`<input type="url" name="msfrom_external_url" placeholder="External URL" requird value="${response.msfrom_submission_data}">`);
                    }else if(response.options == 'internal'){
                        getWpPages(response.msfrom_submission_data);
                    }
                    // console.log();
                },
            });
        }

    })


    /**
     * Make an AJAX request to the export_csv action
     */
    $('#export_csv').click(function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Make an AJAX request to the export_csv action
        $.ajax({
            type: 'POST',
            url: ajaxurl, // WordPress AJAX URL
            data: {
                action: 'export_csv', // The WordPress AJAX action name
                nonce: formit_ajax_localize.nonce
            },
            success: function(response) {
                if (response) {
                    // Create a blob object URL for the response data
                    var blob = new Blob([response]);
                    var url = window.URL.createObjectURL(blob);
                    var min = 1001;  // Minimum value
                    var max = 9999;  // Maximum value

                    var randomInteger = Math.floor(Math.random() * (max - min + 1)) + min;
                    var domainName = window.location.hostname;
                    // Create a temporary link and trigger a click to download the file
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = domainName+'-formit-form-data-'+randomInteger+'.csv';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    
                    // Clean up the temporary link and URL object
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);
                } else {
                    // Handle errors or display a message to the user
                    alert('Export failed');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX error:', error);
            }
        });
    });


    /**
     * Function to apply filters
     */
    $('.view-details').click(function(e) {
        e.preventDefault();
        var submissionId = $(this).data('submission-id');
        // AJAX request to fetch submission details
        $.ajax({
            type: 'POST',
            url: formit_ajax_localize.ajax_url, // WordPress AJAX URL
            data: {
                action: 'get_submission_details',
                submission_id: submissionId,
                nonce: formit_ajax_localize.nonce,

            },
            success: function(response) {
                // Parse the JSON response
                // Call the popup function with the parsed JSON object
                submission_view_popup(response, '');
            },
        });
    });
    
    // Function to create and populate the table
    function submission_view_popup(data, prefix = '') {

        let mailInfo = {
            formTitle: data.form_title,
            formBody: JSON.parse(data.mail_body), 
            userAgent: JSON.parse(data.user_agent),
            userLocation: JSON.parse(data.user_location), 
            userIP: data.ip_address,
            sendingTime: data.created_at,
        }

        var popup__fromdata = `
            <div class="popup_content_area">
                <div class="main">
                    <h2 class="mail-title">Form Data</h2>
                    <table>
                        ${(() => {
                            // Create an empty string to store table rows
                            let tableRows = '';
                            // Loop through the formData array
                            $.each(mailInfo.formBody, function(index, form__data) {
                                // Check if the "type" property is not "hidden"
                                if (form__data.type !== "hidden") {
                                    // Append table rows to the tableRows string
                                    tableRows += `
                                        <tr>
                                            <td>${form__data.label}</td>
                                            <td>${form__data.value}</td>
                                        </tr>
                                    `;
                                }
                            });
                            // Return the generated tableRows
                            return tableRows;
                        })()}
                        <tr>
                            <td>Time:</td>
                            <td>${mailInfo.sendingTime}</td>
                        </tr>
                    </table>
                </div>
            </div>`;


        

        var popup__analytics = `
            <div class="popup_content_area">
                <div class="main">
                    <h2 class="mail-title">Anatlytics / <span>${mailInfo.formTitle}</span></h2>
                    <table>
                        ${(() => {
                            // Create an empty string to store table rows
                            let tableRows = '';
                            // Loop through the formData array
                            $.each(mailInfo.userAgent, function(label, value) {
                                // Check if the "type" property is not "hidden"
                                if (label == "is_mobile" ) {
                                    // Append table rows to the tableRows string
                                    tableRows += `
                                        <tr>
                                            <td>Divice</td>
                                            <td>${value === false ? "Desktop" : "Mobile" }</td>
                                        </tr>
                                    `;
                                }
                                if (label !== "user_agent" && label !== "is_mobile" ) {
                                    // Append table rows to the tableRows string
                                    tableRows += `
                                        <tr>
                                            <td>${label}</td>
                                            <td>${value}</td>
                                        </tr>
                                    `;
                                }
                            });
                            // Return the generated tableRows
                            return tableRows;
                        })()}
                        ${(() => {
                            // Create an empty string to store table rows
                            let tableRows = '';
                            // Loop through the formData array
                            $.each(mailInfo.userLocation, function(label, value) {
                
                                if (label !== 'org') {
                                    // Append table rows to the tableRows string
                                    tableRows += `
                                        <tr>
                                            <td>${label}</td>
                                            <td>${value}</td>
                                        </tr>
                                    `;
                                }
                            });
                            // Return the generated tableRows
                            return tableRows;
                        })()}
                    </table>
                </div>
        </div>`;

        var popup__geolocation = `
            <div class="popup_content_area">
                <div class="main">
                    <h2 class="mail-title">Geolocation / <span>${mailInfo.formTitle}</span></h2>
                    <iframe 
                        style="width:100%;height: 100%; min-height: 350px; border-radius: 0 0 12px 12px"
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="https://maps.google.com/maps?q=${mailInfo.userLocation.lat},${mailInfo.userLocation.lon}&hl=es&z=14&amp;output=embed"
                        >
                        </iframe>
                </div>
        </div>`;

        var popup__header = 
        `<div class="formit__popup__header">
            <ul class="formit__popup__nav">
                <li><button class="btn btn-tab active" type="button" data-target="popup-mail-body">Form Data</button></li>
                <li><button class="btn btn-tab" type="button" data-target="popup-mail-info">Analytics</button></li>
                <li><button class="btn btn-tab" type="button" data-target="popup-mail-geolocation">Geolocation</button></li>
            </ul>
            <button type="button" class="popup__close">&times;</button>
        </div>`;

        var popup__body = `
        <div class="formit__popup__body">
            <div class="formit__popup__tab active" id="popup-mail-body">
                ${popup__fromdata}
            </div>
            <div class="formit__popup__tab" id="popup-mail-info">
                ${popup__analytics}
            </div>
            <div class="formit__popup__tab" id="popup-mail-geolocation">
                ${popup__geolocation}
            </div>
        </div>`

        var popup__footer = `
        <div class="formit__popup__header">
            <div class="main">
                <div class="copyright-message">Powered By: <a href="https://xirosoft.com" target="_blank" title="xirosoft">Xirosoft</a></div>   
            </div>
        </div>`;

        /**
         * Define the popup selector
         */
        var popup = $(".formit__popup");
        $(popup).html(popup__header + popup__body + popup__footer); // html append in popup
        $(popup).addClass('active'); // add class on popup

        $('.post-type-formit').addClass('overlay')

        /**
         * popup close click event triger
         */
        $('.popup__close').on('click', function () {
            $('.post-type-formit').removeClass('overlay')
            popup.removeClass('active');
        })

        /**
         * Click event for Tab trgier inside popup
         */
        $('.btn-tab').on('click', function() {
            console.log('clicking');
            let target = $(this).data('target')
            if(target) {
                $('.btn-tab').removeClass('active')
                $(this).addClass('active')
                $('.formit__popup__tab').removeClass('active')
                $(`#${target}`).addClass('active')
            }
        })
    
        

    }
 
    
    /**
     * 
     * Handle single row delete
     */
    $('.delete-single').click(function(e) {
        e.preventDefault();
        var submissionId = $(this).data('submission-id');

        if (confirm('Are you sure you want to delete this submission?')) {
            // Perform single row deletion
            $.ajax({
                type: 'POST',
                url: formit_ajax_localize.ajax_url,
                data: {
                    action: 'delete_single_submission',
                    submission_id: submissionId,
                    nonce: formit_ajax_localize.nonce
                },
                success: function(response) {
                    if(response.data.status == 400){
                        console.log(response.data.message);
                    }else{
                        location.reload(); // Reload the page or update the table as needed
                    }
                },
            });
        }
    });

      
    /**
    * Function to apply filters
    * Initial table data display
    */
    function applyFilters() {
        var userType = $('#filter-user-type').val() ? $('#filter-user-type').val().toLowerCase() : $('#filter-user-type').val();
        var formTitle = $('#filter-form-title').val() ? $('#filter-form-title').val().toLowerCase() : $('#filter-form-title').val();
        var location = $('#filter-location').val() ? $('#filter-location').val().toLowerCase() : $('#filter-location').val();
        var startDate = $('#filter-start-date').val();
        var endDate = $('#filter-end-date').val();

        $('#submission-table tbody tr').each(function() {
            var row = $(this);
            var rowData = row.text().toLowerCase();

            var userTypeMatch = rowData.includes(userType);
            var formTitleMatch = rowData.includes(formTitle);
            var locationMatch = rowData.includes(location);

            // Check date range
            var date = row.find('.date-and-time').text();
            var dateMatch = true;
            if (startDate && endDate) {
                var submissionDate = new Date(date);
                var startFilterDate = new Date(startDate);
                var endFilterDate = new Date(endDate);
                dateMatch = submissionDate >= startFilterDate && submissionDate <= endFilterDate;
            }

            // Show/hide rows based on filter criteria
            if (userTypeMatch && formTitleMatch && locationMatch && dateMatch) {
                row.show();
            } else {
                row.hide();
            }
        });

        var allChecked = $('#select-all').prop('checked');
        if (allChecked) {
            $('#submission-table tbody tr').find('input[type="checkbox"]').prop('checked', true);
        } else {
            $('#submission-table tbody tr').find('input[type="checkbox"]').prop('checked', false);
        }
    }


    /**
     * Check/uncheck all checkboxes when the "Select All" checkbox is clicked
     */
    $('#select-all').change(function() {
        var isChecked = $(this).prop('checked');
        $('#submission-table tbody tr').find('input[type="checkbox"]').prop('checked', isChecked);
    });

    /**
     * Apply filters when the "Apply Filters" button is clicked
     */
    $('.smform-submisson-filter-form').submit(function(e) {
        e.preventDefault();
        applyFilters();
    });

    // Initial table data display
    applyFilters();


     /**
      * Handle bulk actions. Like at  time you can delete as your selected items
      */
     $('#do-action').click(function(e) {
        e.preventDefault();
        var action = $('#bulk-action').val();
        
        if (action === 'delete') {
            var selectedIds = [];
            $('#submission-table tbody tr').each(function() {
                var checkbox = $(this).find('input[type="checkbox"]');
                if (checkbox.prop('checked')) {
                    selectedIds.push(checkbox.data('submission-id'));
                }
            });
            
            if (confirm('Are you sure you want to delete this submission?')) {
                if (selectedIds.length > 0) {
                    // Perform bulk deletion
                    $.ajax({
                        type: 'POST',
                        url: formit_ajax_localize.ajax_url,
                        data: {
                            action: 'bulk_delete_submissions',
                            submission_ids: selectedIds,
                            nonce: formit_ajax_localize.nonce
                        },
                        success: function(response) {
                            // Reload the page or update the table as needed
                            location.reload();
                        },
                    });
                }
            }
            
        }
    });


    /**
     * Cookie SET for Per page item visible
     */
    $('#items-per-page-form').submit(function (e) {
        e.preventDefault();
        // Get the updated per-page value from the input field
        var newPerPage = $('#items-per-page').val();
        
        document.cookie = `itemsPerPage=${newPerPage}; expires=` + new Date(new Date().getTime() + 24 * 60 * 60 * 1000).toUTCString();
        // Reload the page to reflect the new per-page limit
        location.reload();
    });


    $('.copy_shortcode').on('click', e => copyText(e, 1500));
    /**
     * 
     * @param {*} e as events
     * @param {number} timeout >> after destroying time
     */
    function copyText(e, timeout) {
        if (e && e.target) {
            const innerText = e.target.innerText;
            // Create a temporary textarea to hold the text and copy from it
            const tempTextArea = document.createElement('textarea');
            tempTextArea.value = innerText;
            document.body.appendChild(tempTextArea);

            // Select and copy the text
            tempTextArea.select();
            document.execCommand('copy');

            // Clean up and remove the temporary textarea
            document.body.removeChild(tempTextArea);

            // Show "shortcode copied" message
            const copiedMessage = document.createElement('div');
            copiedMessage.classList.add('copied-message');
            copiedMessage.innerText = 'Shortcode Copied!';
            e.target.appendChild(copiedMessage);
            setTimeout(() => copiedMessage.classList.add('animOpen'), 100)

            // Hide the message after 1 seconds with a fade-out animation
            setTimeout(() => {
                copiedMessage.style.opacity = '0';
                setTimeout(() => {
                    e.target.removeChild(copiedMessage);
                }, 500); // Assuming a 500ms animation
            }, timeout);
        } else {
            console.log('"event" paramiter must be passed by calling this function.')
        }
    }

    $('.copy_shortcode_mbl').on('click', e => copyText2(e, 15000));
    /**
     * 
     * @param {*} e as events
     * @param {number} timeout >> after destroying time
     */
    function copyText2(e, timeout) {
        if (e && e.target) {
            const innerText = e.target.innerText;
            // Create a temporary textarea to hold the text and copy from it
            const tempTextArea = document.createElement('textarea');
            tempTextArea.value = innerText;
            document.body.appendChild(tempTextArea);

            // Select and copy the text
            tempTextArea.select();
            document.execCommand('copy');

            // Clean up and remove the temporary textarea
            document.body.removeChild(tempTextArea);

            // Show "shortcode copied" message
            const copiedMessage = document.createElement('div');
            copiedMessage.classList.add('copied-message');
            copiedMessage.innerText = 'Copied!';
            e.target.appendChild(copiedMessage);
            e.target.style.pointerEvents = 'none';
            setTimeout(() => copiedMessage.classList.add('animOpen'), 100)
            
            // Hide the message after 1 seconds with a fade-out animation
            setTimeout(() => {
                copiedMessage.style.opacity = '0';
                setTimeout(() => {
                    e.target.removeChild(copiedMessage);
                    e.target.style.pointerEvents = '';
                }, 500); // Assuming a 500ms animation
            }, timeout);
        } else {
            console.log('"event" paramiter must be passed by calling this function.')
        }
    }


    function darganddrop_mailbody() {
        if ($('#drag-list').length && $('#mail_body').length) {
            $('#drag-list li').on('dragstart', function (e) {
                e.originalEvent.dataTransfer.setData('text/plain', $(this).text());
            });

            $('#mail_body').on('dragover', function (e) {
                e.preventDefault();
            });

            $('#mail_body').on('drop', function (e) {
                e.preventDefault();
                const draggedText = e.originalEvent.dataTransfer.getData('text/plain');
                const cursorPosition = this.selectionStart;
                const textBeforeCursor = this.value.slice(0, cursorPosition);
                const textAfterCursor = this.value.slice(cursorPosition);
                this.value = textBeforeCursor + draggedText + ' ' + textAfterCursor;
            });
        }
    }
    darganddrop_mailbody();


    // open popup from header
    if($('.wpheader__meta__info').length) {
        $('.wpheader__meta__info').on('click', function() {
            let target = $(this).data('popup')
            console.log(target)
            if($(target).length) {
                $(target).toggleClass('active')
            }
        })
    }


     // Initialize TinyMCE on the textarea with ID 'tinymce-editor'
     if (typeof tinymce !== 'undefined') {
        var content = $('#mail_body').val();
        tinymce.init({
            selector: '#mail_body',
            // mce_buttons: false,
            contextmenu_never_use_native: true,
            height: 400, // Customize the editor height as needed
            // toolbar: 'bold italic | bullist numlist', // Customize the toolbar buttons
            toolbar: 'bold italic underline strikethrough superscript subscript | table | alignleft aligncenter alignright alignjustify | link | formats | removeformat',
            plugins: 'link',
            branding: false,
            menu: {
                // Remove the 'File' menu
                file: false,
        
                // Remove the 'Edit' menu
                edit: false,
        
                // Remove the 'View' menu
                view: false,
        
                // Remove the 'Format' menu
                format:  false
            },
            setup: function(editor) {
                // Set the content of the editor
                editor.setContent(content);
            }
        });
    }
    
    // Remove all meta boxed
    $('.postbox:not(#formit_builder_custom_field)').remove();


    // doc-table-of-contact-toggoler
    if($('.doc-table-of-contact-toggoler').length) {
        $('.doc-table-of-contact-toggoler').on('click', function() {
            $(this).next('.doc__nav').toggleClass('active')
        })
    }

    /**
    * This Tab using on Form and from setting page.
    * @param {} ;
    */
    $(".tab-button").on('click', function(){
        var tabId = $(this).data('tab');
        console.log(tabId);
        $(".tab").removeClass('active');
        $(".tab-button").removeClass('active-tab');
        
        $("#" + tabId).addClass('active');
        $(this).addClass('active-tab');
    });


});



// Docs scroll
document.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('[data-type="section"]'); // Get all sections
    const links = document.querySelectorAll('.post-type-formit .doc__nav a'); // Get all links
  
    sections.forEach((section, index) => {
      const sectionTop = section.offsetTop;
      const sectionBottom = sectionTop + section.clientHeight;
  
      if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
        // Add "active" class to the corresponding link
        links.forEach(link => link.classList.remove('active'));
        links[index].classList.add('active');
      }
    });
  });
  
  // Handle link click to scroll to the corresponding section
  document.querySelectorAll('.post-type-formit .doc__nav a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
  
      const targetId = this.getAttribute('data-target');
      const targetSection = document.getElementById(targetId);
  
      window.scrollTo({
        top: targetSection.offsetTop,
        behavior: 'smooth'
      });
    });
  });
  
