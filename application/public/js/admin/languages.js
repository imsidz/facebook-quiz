function getLanguageValue(languageId){
    return _.findWhere(languagesData.languages, {'id':languageId});
}

function getLanguageValueAt(languageIndex){
    return languagesData.languages[languageIndex];
}

function setLanguageValue(languageId, value){
    var languageValue = getLanguageValue(languageId);
    languageValue.languages = value;
    return value;
}
function setLanguageValueAt(languageIndex, value){
    languagesData.languages[languageIndex] = value;
    return value;
}

function renderLanguagesEditor() {
    var languagePanelTemplate = $('#languagePanelTemplate').html();
    var panelHtml = _.template(languagePanelTemplate)({languages: languagesData.languages || []});
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
    languagesData.languages.splice(languageIndex, 1);
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
            "strings" : {
                htmlClass: "form-section"
            },
            "direction": {
                titleMap: {
                    "ltr" : "Left to right",
                    "rtl" : "Right to left"
                }
            }
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
            htmlClass: "btn-danger",
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
        value: languageValue.hasOwnProperty('name') ? languageValue : undefined,
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

    $('body').on('click', '.add-new-language-btn', function(){
        languagesData.languages.push({});
        renderLanguagesEditor();
        expandLanguageItem($('.language-item').last());
        $(window).scrollTop($('.language-item').offset().top);
    });

    $('body').on('click', '.language-edit-toggle', function(){
        var languageItem = $(this).parents('.language-item')
        if(languageItem.data('expanded')){
            contractLanguageItem(languageItem);
        } else {
            expandLanguageItem(languageItem);
        }
    });
    $('body').on('change', '#activeLanguageField', function(){
        languagesData.activeLanguage = $(this).val();
    });
});

function renderActiveLanguagesForm() {
    var languages = languagesData.languages;
    var template = $('#activeLanguageFormTemplate').html();
    var html = _.template(template, {
        languages: languages
    });
    $('#activeLanguageFormContainer').html('').append(html);
    if(languagesData.activeLanguage){
        $('#activeLanguageField').val(languagesData.activeLanguage);
    }
}

vent.on('language:editor:render', function(){
    renderActiveLanguagesForm();
});
window.onbeforeunload = function(){
    var newLanguagesDataJson = JSON.stringify(languagesData);
    if(lastSavedLanguagesDataJson != newLanguagesDataJson) {
        return "You have made some changes in the languages that are not saved. You have to click the 'Save changes' button at the bottom to save the changes.";
    }
};

vent.on('languages-form-submitted', function(){
    $.post(BASE_PATH + '/admin/config/languages', {
        languages: languagesData
    }).success(function(res){
        if(res.success) {
            lastSavedLanguagesDataJson= JSON.stringify(languagesData);
            dialogs.success('Languages Saved');
        } else if(res.error) {
            dialogs.error('Error occured\n' + res.error);
        } else {
            dialogs.error('Some Error occured\n' + res);
        }
    }).fail(function(res){
        dialogs.error(res.responseText);
    });
})