jQuery(document).ready(function ($) {

    if($('.formit-form').length > 0) {

   
        // Get the form element
        let $formElement = $('.formit-form form');
        const loader = $('.xiroform-circle');
        var formMsg = $('.xiroform-msg');

        if ($formElement.length) {
        // Handle form submission
        $formElement.submit(function (e) {
            e.preventDefault(); // Prevents the form from submitting in the traditional way
            loader.show();
            formMsg.show();
            // Get form data with labels
            let formData = formDataWithLabel($formElement);
            $.ajax({
            url: formit_ajax_object.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'formit_submit_ajax_function', // The same action name you used in wp_ajax_ action hook
                data: formData
            },
            success: function (response) {
                // Handle the response here
                let mailConfigData = JSON.parse(response.data.config.form_configs)[0];
                let redirect = mailConfigData.msfrom_redirect;

                if (redirect.options == 'popup') {
                $(formMsg).html(redirect.msfrom_submission_data).addClass('success');
                setTimeout(() => {
                    formMsg.hide();
                }, 5000);
                } else if (redirect.options == 'external' || redirect.options == 'internal') {
                window.location.assign(redirect.msfrom_submission_data);
                }

                loader.hide();
                // Clear all form fields
                $($formElement).find('input[type="text"],input[type="email"],input[type="number"],input[type="tel"], input[type="checkbox"], input[type="radio"], textarea').val('');
                // For example, resetting the selected option in a <select> element
                $($formElement).find('select').prop('selectedIndex', 0);

            }
            });
            // Create and handle AJAX post request here
        });
        }

        /**
        * Get form data with labels.
        * @param {jQuery} formElement - The form element.
        * @returns {Object} - Form data with labels.
        */
        function formDataWithLabel(formElement) {
        // Set default form element if not provided
        if (!formElement) {
            formElement = $('.ms-builder-post form');
        }

        let formData = [];

        // Iterate over form elements
        $(formElement).find('input, select, textarea').each(function () {
            let fieldName = $(this).attr('name');
            let fieldType = $(this).attr('type');
            let fieldValue = $(this).val();
            let fieldId = $(this).attr('id');
            let fieldLabel = $(this).closest('.form-group').children('label').first().text().replace('*', '');
            let fieldPlaceholder = $(this).attr('placeholder');


            /** @if fieldLabel is undifind or null*/
            if (!fieldLabel) {
            /**
                * @if input has placeholder
                * @if input hasn't field label
                * @do we set the fieldLabel as placeholder
                */
            if (fieldPlaceholder) {
                fieldLabel = !fieldLabel ? fieldPlaceholder : fieldLabel;
            } else {
                fieldLabel = 'Untitled_' + fieldType;
            }

            /**
                * @if input type is hidden
                * @if input has not label
                * @do we set the fieldLabel as input name
                */
            if (fieldType == 'hidden') {
                fieldLabel =  'Hidden_' + fieldName;
            }

            }

            if (fieldType == 'radio' || fieldType == 'checkbox') {
            formData.push({
                type: fieldType,
                name: fieldName,
                label: fieldLabel,
                value: fieldValue,
                isChecked: $(this).prop('checked') || false
            });
            } else {
            formData.push({
                type: fieldType,
                name: fieldName,
                label: fieldLabel,
                value: fieldValue
            });
            }
        });
        /**
            * Radio input group has multiple field with single name
            * but we receive just single value. 
            * radio value return "on" value. here we replace the feild label
            * 
            * checkbox input group has multiple field with single name
            * here we can receive multiple value. so we make a {COMMA SEPARATE} string like 'apple,banana,cake'
            * 
            * NEED TO IMPROVE:: lastly we filter the unnessesarry radio options. //its can be more usable. 
            */
        let finalFormData = formData.reduce((acc, item) => {
            if (item.type === 'radio' && item.isChecked) {
            acc.push({
                type: item.type,
                name: item.name,
                label: item.label,
                value: item.value
            });
            } else if (item.type === 'checkbox') {
            const existingCheckbox = acc.find(
                (accItem) => accItem.type === 'checkbox' && accItem.name === item.name
            );

            if (existingCheckbox) {
                existingCheckbox.value += `, ${item.value}`;
            } else if (item.isChecked) {
                acc.push({
                type: item.type,
                name: item.name,
                label: item.label,
                value: item.value
                });
            }
            } else {
            acc.push(item);
            }
            return acc;
        }, []).filter(item => !(item.type === 'radio' && item.isChecked == false));


        return finalFormData;
        }


        function formitDomMerge() {
        var mergedContent = {};

        // Iterate through elements with a specific class or attribute
        $(".rendered-form .row").each(function() {
            var id = $(this).attr("id");
            var content = $(this).html();

            // Store or merge the content based on ID
            if (mergedContent[id]) {
                mergedContent[id] += content;
            } else {
                mergedContent[id] = content;
            }
        });

        // Clear existing elements
        $(".rendered-form").empty();

        // Rebuild the DOM with merged content
        for (var id in mergedContent) {
            $("<div>")
                .attr("id", id)
                .addClass("row")
                .html(mergedContent[id])
                .appendTo(".rendered-form");
        }
        }

        formitDomMerge();

        $('label.formbuilder-text-label').each(function() {
            if (!$(this).text().trim()) {
                $(this).css('display', 'none');
            }
        });
     }
});