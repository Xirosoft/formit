jQuery(document).ready(function ($) {

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
          action: 'msfrom_submit_ajax_function', // The same action name you used in wp_ajax_ action hook
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


      // Find the direct label element within the same .form-group
      // let fieldSelector = $(this).closest('.form-group').children('label').first();

      // if($(fieldSelector).text() == '*'){
      //     var fieldLabel = 'Field ID: ' + $(fieldSelector).attr('for') || $(fieldSelector).attr('name');
      // }else{
      //     var fieldLabel = $(fieldSelector).text() || $(fieldSelector).next().attr('placeholder') || 'Field ID: ' + $(fieldSelector).attr('for')
      // }



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




});
