var vent = $('<p/>');

var escapeSelector = function (selector) {
    return selector.replace(/([ \!\"\#\$\%\&\'\(\)\*\+\,\.\/\:\;<\=\>\?\@\[\\\]\^\`\{\|\}\~])/g, '\\$1');
};


JSONForm.fieldTypes['image'] = {
    template: '<div><img class="field-image-preview img-thumbnail <% if(!value) { %>image-not-chosen<% } %>" style="cursor: pointer;" width="150" src="<% if(value) {%><%= window.contentUrl(escape(value)) %><% } else{ %>' + chooseImagePlaceholder + '<% } %>"></div><br><input type="text" ' +
    '<%= (fieldHtmlClass ? "class=\'" + fieldHtmlClass + "\' " : "") %>' +
    'name="<%= node.name %>" value="<%= escape(value) %>" id="<%= id %>"' +
    '<%= (node.disabled? " disabled" : "")%>' +
    '<%= (node.readOnly ? " readonly=\'readonly\'" : "") %>' +
    '<%= (node.schemaElement && node.schemaElement.maxLength ? " maxlength=\'" + node.schemaElement.maxLength + "\'" : "") %>' +
    '<%= (node.schemaElement && node.schemaElement.required && (node.schemaElement.type !== "boolean") ? " required=\'required\'" : "") %>' +
    '<%= (node.placeholder? "placeholder=" + \'"\' + escape(node.placeholder) + \'"\' : "")%>' +
    'style="width: 80%;display: inline-block;margin-right: 10px;" />'+
    '<span class="choose-image btn btn-primary"><i class="fa fa-file-image-o"></i></span>'+
    '<div class="jsonform-image-review-modal modal fade"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title">Image preview</h4></div><div class="modal-body"><img src="" class="modal-preview-img" style="max-width: 100%;"></div></div></div></div>',
    fieldtemplate: true,
    inputfield: true,
    onInsert: function (e, node) {
        var formGroup = e.target;
        formGroup.on('click', '.choose-image,.image-not-chosen', function(){
            //The clicked action element - could be the button or the image preview
            var actionElm = $(this);
            console.log(actionElm.parents('.controls').first());
            var inputField = actionElm.parents('.controls').first().find('input');
            $('#mediaManagerModal').modal();
            var elfOptions = {
                url  : mediaConnectorRoute,
                lang : 'en',
                dragUploadAllow: true,
                useBrowserHistory: false,
                getFileCallback: function(file) {
                    $('#mediaManagerModal').modal('hide');
                    var fileUrl = file.url.replace(ASSET_BASE_PATH, '');
                    inputField.val(fileUrl);
                    inputField.trigger('change');
                }
            };
            var elfinder = new window.elFinder(document.getElementById('elFinder'), elfOptions);
        });
        formGroup.on('click', '.field-image-preview:not(.image-not-chosen)', function(){
            var modal = formGroup.find('.jsonform-image-review-modal');
            modal.find('.modal-preview-img').attr('src', $(this).attr('src'));
            modal.modal('show');
        });
        formGroup.find('input').on('change', function(){
            var val = $(this).val();
            var previewImgElm = formGroup.find('.field-image-preview');
            previewImgElm.attr('src', window.contentUrl(val));
            if(val) {
                previewImgElm.removeClass('image-not-chosen');
            } else {
                previewImgElm.addClass('image-not-chosen').attr('src', chooseImagePlaceholder);
            }
        });
    }
};
JSONForm.fieldTypes['html'] = {
    'template':'<div style="margin-bottom: 10px;" class="clearfix"><span class="tinymce-add-image-btn btn btn-info btn-sm pull-left"><i class="fa fa-image"></i> Add Image</span></div> <textarea class="jsonform-html-textarea" id="<%= id %>" name="<%= node.name %>" style="height:<%= elt.height || "300px" %>;width:<%= elt.width || "100%" %>;"' +
    '<%= (node.disabled? " disabled" : "")%>' +
    '<%= (node.readOnly ? " readonly=\'readonly\'" : "") %>' +
    '<%= (node.schemaElement && node.schemaElement.maxLength ? " maxlength=\'" + node.schemaElement.maxLength + "\'" : "") %>' +
    '<%= (node.schemaElement && node.schemaElement.required ? " required=\'required\'" : "") %>' +
    '<%= (node.placeholder? "placeholder=" + \'"\' + escape(node.placeholder) + \'"\' : "")%>' +
    '><%= value %></textarea>',
    'fieldtemplate': true,
    'inputfield': true,
    'onInsert': function (evt, node) {
        var nodeElm = $(node.el);
        //protect from double init
        if (nodeElm.data("wysiwyg")) return;
        nodeElm.data("wysiwyg",true);
        var elm = $(node.el).find('#' + escapeSelector(node.id));
        var addImgBtn = $(node.el).find('.tinymce-add-image-btn');
        tinymce.init({
            selector: '#' + escapeSelector(node.id),
            menubar: false,
            theme: "modern",
            skin: "lightgray",
            language: "en",
            formats: {
                alignleft: [
                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'left'}},
                    {selector: 'img,table,dl.wp-caption', classes: 'alignleft'}
                ],
                aligncenter: [
                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'center'}},
                    {selector: 'img,table,dl.wp-caption', classes: 'aligncenter'}
                ],
                alignright: [
                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'right'}},
                    {selector: 'img,table,dl.wp-caption', classes: 'alignright'}
                ],
                strikethrough: {inline: 'del'}
            },
            block_formats: "Paragraph=p;Pre=pre;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6",
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            browser_spellcheck: true,
            fix_list_elements: true,
            entities: "38,amp,60,lt,62,gt",
            entity_encoding: "raw",
            keep_styles: false,
            preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
            plugins: "colorpicker,hr,lists,paste,tabfocus,textcolor,fullscreen,code,link,image,charmap,media",
            resize: false,
            indent: false,
            toolbar1: "bold,italic,bullist,numlist,hr,alignleft,aligncenter,alignright,alignjustify,link,image,formatselect",
            toolbar2: "underline,forecolor backcolor,pastetext,removeformat,undo,redo,code,media",
            add_unload_trigger: false,
            force_br_newlines : false,
            force_p_newlines : false,
            forced_root_block : '',
            setup: function(ed){
                addImgBtn.on('click', function(){
                    $('#mediaManagerModal').modal();
                    var elfOptions = {
                        url  : mediaConnectorRoute,
                        lang : 'en',
                        dragUploadAllow: true,
                        useBrowserHistory: false,
                        getFileCallback: function(file) {
                            $('#mediaManagerModal').modal('hide');
                            var fileUrl = file.url.replace(location.protocol+ '//' + location.hostname, '');
                            var range = ed.selection.getRng();                  // get range
                            var newNode = ed.getDoc().createElement ( "img" );  // create img node
                            newNode.src = fileUrl;                           // add src attribute
                            range.insertNode(newNode);
                        }
                    };
                    var elfinder = new window.elFinder(document.getElementById('elFinder'), elfOptions);
                });
                ed.on('change', function(e){
                    nodeElm.find('.jsonform-html-textarea').trigger('change');
                    elm.val(ed.getContent());
                });
            },
            codemirror: {
                path: 'codemirror-4.8', // Path to CodeMirror distribution
                cssFiles: [
                    'theme/xq-light.css'
                ],
                config: {
                    theme : 'xq-light'
                }
            }
        });
    }
};

JSONForm.fieldTypes['checkbox'] = {
    'template':'<input style="visibility: hidden;" type="checkbox" value="true" id="<%= id %>" name="<%= node.name %>"'+
    '<%= (node.disabled? " disabled" : "")%>' +
    '<%= (node.readOnly ? " readonly=\'readonly\'" : "") %>' +
    '<%= (node.schemaElement && node.schemaElement.required ? " required=\'required\'" : "") %>' +
    '<% if(value && value !== "false") { %> checked="checked" <% } %>'+
    '><div class="toggle toggle-light" <% if(value && value !== "false") { %> data-toggle-on="true" <% } %> style="width: 100px; text-align: center;"></div>',
    'fieldtemplate': true,
    'inputfield': true,
    'onInsert': function (evt, node) {
        //protect from double init
        var rootElm = $(node.el);
        //console.log(rootElm, 10, rootElm.find('.toggle'));
        rootElm.find('.toggle').toggles({
            checkbox: rootElm.find('input[type="checkbox"]'),
            width: 60,
            height: 24,
            text: {
                on: 'YES', // text for the ON position
                off: 'NO' // and off
            }
        });
    }
};

JSONForm.fieldTypes['range'] = {
    'template': '<div class="input-group"><input type="range" ' +
    '<%= (fieldHtmlClass ? "class=\'" + fieldHtmlClass + "\' " : "") %>' +
    'name="<%= node.name %>" value="<%= escape(value) %>" id="<%= id %>"' +
    '<%= (node.disabled? " disabled" : "")%>' +
    ' min=<%= range.min %>' +
    ' max=<%= range.max %>' +
    ' step=<%= range.step %>' +
    '<%= (node.schemaElement && node.schemaElement.required ? " required=\'required\'" : "") %>' +
    ' /><span class="input-group-btn"><button class="btn btn-success jsonform-range-val" type="button"><%= escape(value) %></button></span></div>',
    'fieldtemplate': true,
    'inputfield': true,
    'onBeforeRender': function (data, node) {
        data.range = {
            min: 1,
            max: 100,
            step: 1
        };
        if (!node || !node.schemaElement) return;
        if (node.schemaElement && node.schemaElement.step) {
            data.range.step = node.schemaElement.step;
        }
        if (node.formElement && node.formElement.step) {
            data.range.step = node.formElement.step;
        }
        if (typeof node.schemaElement.minimum !== 'undefined') {
            if (node.schemaElement.exclusiveMinimum) {
                data.range.min = node.schemaElement.minimum + data.range.step;
            }
            else {
                data.range.min = node.schemaElement.minimum;
            }
        }
        if (typeof node.schemaElement.maximum !== 'undefined') {
            if (node.schemaElement.exclusiveMaximum) {
                data.range.max = node.schemaElement.maximum + data.range.step;
            }
            else {
                data.range.max = node.schemaElement.maximum;
            }
        }
    },
    onInsert: function(evt, node) {
        var nodeElm = $(node.el);
        var rangeElm = nodeElm.find("input[type='range']");
        var rangeValElm = nodeElm.find('.jsonform-range-val');
        rangeElm.change(function(e) {
            console.log($(this).val(), rangeValElm);
            rangeValElm.html($(this).val());
        });
    }
};

/*Get form view options for jsonform - to diplay specific properties only or to exclude specific properties
 */
function getFormViewOptions(schema, options) {
    options = options || {};
    var events = options.events || {};
    function getFieldOption(field){
        //console.log('getting options for' + field);
        var keyOptions = {};
        if(!events.hasOwnProperty(field)) {
            keyOptions.key = field;
            return keyOptions;
        } else {
            keyOptions.key = field;
            for(var i in events[field]) {
                keyOptions[i] = events[field][i];
            }
            return keyOptions;
        }
    }

    var include = options.include || null;
    var includeOnly = options.includeOnly || null;
    var exclude = options.exclude || [];
    var formOptions = options.formOptions || null;
    var form = [];

    if(includeOnly) {
        //Include only specified properties
        for(var i in includeOnly) {
            form.push(getFieldOption(includeOnly[i]));
        }
    } else {
        for(var keyName in schema) {
            if(exclude.indexOf(keyName) < 0) {
                form.push(getFieldOption(keyName));
            }
        }
        for(var i in include) {
            form.push(getFieldOption(include[i]));
        }
    }
    if(formOptions) {
        for(var i in formOptions) {
            var formKeyOption = _.where(form, {key: i});
            if(!formKeyOption || !formKeyOption.length) {
                var formKeyOption = {key: i};
                for(var j in formOptions[i]) {
                    formKeyOption[j] = formOptions[i][j];
                }
                form.push(formKeyOption);
            } else{
                var formKeyOption = {key: i};
                for(var j in formOptions[i]) {
                    formKeyOption[j] = formOptions[i][j];
                }
                for(var j in form) {
                    if(form[j].key === formKeyOption.key) {
                        form[j] = formKeyOption;
                    }
                }
            }
        }
    }

    form.push({
        "type": "actions",
        "items": [
            {
                "type": "submit",
                "title": "Submit"
            }
        ]
    });
    return form;
}

function guid() {
    var guid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
    return guid;
}

//Item activator
$(function() {
    $('.item-activator input:checkbox').hide();
    var itemActivators = $('.item-activator');
    itemActivators.each(function() {
        var checkbox = $(this).children('input:checkbox');
        var activateEndPoint = $(this).data('end-point');
        var activatingMessage = $(this).data('activating-message') || "Activating";
        var deactivatingMessage = $(this).data('deactivating-message') || "Deactivating";
        var activatedMessage = $(this).data('activated-message') || "Activated";
        var deactivatedMessage = $(this).data('deactivated-message') || "Deactivated";
        var toggle = $(this).find('.toggle');
        var customToggleSettings = JSON.parse($(this).data('toggle-settings') || '{}');
        var toggleSettings = {
            checkbox: checkbox,
            text: {
                on: 'Enabled', // text for the ON position
                off: 'Disabled' // and off
            }
        };
        toggleSettings = $.extend(true, toggleSettings, customToggleSettings);
        toggle.toggles(toggleSettings);
        var togglesObj = toggle.data('toggles');

        toggle.on('toggle', function(e, active) {
            dialogs.loading((active ? activatingMessage : deactivatingMessage) + "");
            $.post(activateEndPoint, {active: active}).success(function () {
                dialogs.success(active ? activatedMessage : deactivatedMessage);
            }).fail(function(jqXhr){
                //Revert state
                togglesObj.toggle(!active, false, true);
                dialogs.error(jqXhr.responseText);
            })
        });
    })
});
