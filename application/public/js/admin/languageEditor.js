function getLanguageValue(languageId){
    return _.findWhere(languagesData, {'id':languageId});
}

function getLanguageValueAt(languageIndex){
    return languagesData[languageIndex];
}

function setLanguageValue(languageId, value){
    var languageValue = getLanguageValue(languageId);
    languageValue.languages = value;
    return value;
}
function setLanguageValueAt(languageIndex, value){
    languagesData[languageIndex].strings = value;
    return value;
}

function renderLanguagesEditor() {
    var languagePanelTemplate = $('#languagePanelTemplate').html();
    var panelHtml = _.template(languagePanelTemplate)({languages: languagesData || []});
    //alert(panelHtml);
    $('#languageEditorPanel').html(panelHtml);
    vent.trigger('language:editor:render');
}
function contractLanguageItem(languageItem){
    languageItem.data('expanded', false);
    var panelBody = languageItem.children('.panel-body');
    panelBody.addClass('hidden');
}
function removeLanguageItem(languageItem){
    var languageId = languageItem.data('languageId');
    var languageIndex = languageItem.data('language-index');
    languagesData.splice(languageIndex, 1);
    renderLanguagesEditor();
}
function expandLanguageItem(languageItem) {
    //$('.panel-body').addClass('hidden');
    languageItem.data('expanded', true);
    var panelBody = languageItem.children('.panel-body');
    panelBody.removeClass('hidden');
    var languageForm = languageItem.find('.language-form');

    var languageIndex = languageItem.data('language-index');
    var languageId = languageItem.data('languageId');
    var languageValue = getLanguageValueAt(languageIndex) || {};

    var formOptions = getFormViewOptions(languageItemSchema, {
        events: {
            results: {
                onChange: function(e, node){

                },
                onInsert: function(e, node){

                }
            }
        },
        formOptions:{

        }
    });

    _.findWhere(formOptions, {type: "actions"}).items.push({
            type: "button",
            title: '<i class="fa fa-times"></i> Cancel',
            onClick: function(evt){
                //If value is empty. ie, on canceling a blank form()eg: 'add new language' form  - remove it
                if($.isEmptyObject(languageValue)){
                    removeLanguageItem(languageItem);
                }
                evt.preventDefault();
                contractLanguageItem(languageItem);
            }
        },
        {
            type: "button",
            title: '<i class="fa fa-trash-o"></i> Delete',
            htmlClass: "btn-danger bg-red",
            onClick: function(evt){
                evt.preventDefault();
                removeLanguageItem(languageItem);
                return false;
            }
        }
    );

    languageForm.html('');
    languageForm.jsonForm({
        schema: languageItemSchema,
        form: formOptions,
        value: languageValue.hasOwnProperty('strings') ? languageValue.strings : undefined,
        onSubmit: function (errors, values) {
            if (errors) {
                $('#languagesFormResult').html('<p>I beg your pardon?</p>');
            }
            else {
                setLanguageValueAt(languageIndex, values);
                vent.trigger('hideForm', 'languagesForm');
            }
            vent.trigger('languages-form-submitted');
        }
    });
    languageForm.find('input').first().focus();
}
vent.on('languages-form-submitted', function(){
    renderLanguagesEditor();
});
$('.save-changes-btn').click(function(){
    vent.trigger('languages-form-submitted');
});

$(function(){
    renderLanguagesEditor();
    $('body').on('click', '.language-edit-toggle', function(){
        var languageItem = $(this).parents('.language-item')
        if(languageItem.data('expanded')){
            contractLanguageItem(languageItem);
        } else {
            expandLanguageItem(languageItem);
        }
    });
});

window.onbeforeunload = function(){
    var newLanguagesDataJson = JSON.stringify(languagesData);
    if(lastSavedLanguagesDataJson != newLanguagesDataJson) {
        return "You have made some changes in the languages that are not saved. You have to click the 'Save changes' button at the bottom to save the changes.";
    }
};