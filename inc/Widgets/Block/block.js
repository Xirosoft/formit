(function (blocks, element, blockEditor, components) {
    var el = element.createElement;
    var SelectControl = components.SelectControl;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var selectedFormHtml = ''; // Initialize the variable
    var formsData = []; // Store the JSON data here
    var formsDataID = ''; // Store the JSON data here
    var formsStoreageID = localStorage.getItem('selectedFormID'); // Retrieve the selected value from local storage
    // console.log(formsStoreageID);
    var post_id;
    if (formsData.length === 0) {
        fetch('/maxon/wp-json/formit/v1/forms')
            .then(response => response.json())
            .then(data => {
                formsData = data;
            })
            .catch(error => {
                console.log(error);
            });
    }

    
    function htmlForm(id) {
        var SaveSelectData = formsData.find(function (form) {
            // post_id = form.post_id;
            // console.log(form.id);
            return form.id == id;
        });
    
        post_id = SaveSelectData.post_id;
        console.log(SaveSelectData.post_id);
        if (SaveSelectData) {
            var stringWithoutBackslashes = SaveSelectData.form_html.replace(/\\/g, '');
            return stringWithoutBackslashes;
        } else {
            // Handle the case where the form is not found or is undefined
            return ''; // or another appropriate value or error handling
        }
    }

    const redBackground = {
        backgroundColor: '#fff',
        color: '#000',
        padding: '20px',
    };
    const classname = 'formit-form template-basic';

    // Define the form attributes
    const formAttributes = {
        method: 'post',       // Specify the HTTP method (e.g., 'post' or 'get')
        className: 'formit-content',
        id: 'formit-'+ formsStoreageID,   // Replace 'your-form-id' with the desired ID
    };
    const sideAttributes = {
        className: 'block-editor-block-card',
    };
    // console.log(htmlForm());

    blocks.registerBlockType('formit/formit-block', {
        edit: function ({ attributes, setAttributes }) {
            var blockPorps = useBlockProps({className:classname});
            // Fetch the JSON data when the block is edited
            return (
                el(
                    'div',
                    blockPorps,
                    el(
                        InspectorControls, {},
                        el(
                            'h2',
                            blockPorps,
                            'Formit Form'
                        ),
                        el(
                            SelectControl, {
                                label: 'Select Form',
                                value: attributes.SelectControl ? attributes.SelectControl : formsStoreageID,
                                options: formsData.map(form => ({
                                    label: form.form_title,
                                    value: form.id,
                                })),
                                onChange: function (newValue) {
                                    var selectedForm = formsData.find(function (form) {
                                        formsDataID = newValue;
                                        localStorage.setItem('selectedFormID', newValue); // Store the selected value in local storage
                                        return form.id == newValue; 
                                    });
                                    if (selectedForm) {
                                        selectedFormHtml = selectedForm.form_html;
                                        setAttributes({ SelectControl: newValue });
                                    }
                                },
    
                            }
                        ),
                    ),
                    el('form', {
                        ...formAttributes, // Add form attributes here
                        dangerouslySetInnerHTML: {
                            __html: htmlForm(formsDataID ? formsDataID : formsStoreageID)
                        }
                    })
                )

            );
        },
        save: function (attributes) {
        
            // You can save the selected option here if needed
            var blockProps = useBlockProps.save({style: redBackground, className: classname});
        
            return el(
                'div',
                blockProps,
                el('form', {
                    dangerouslySetInnerHTML: {
                        __html: htmlForm(formsStoreageID)
                    }
                })
            );
        },
        
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);

