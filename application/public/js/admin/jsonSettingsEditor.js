(function(root) {
    var SettingsForm = function (schema, data) {
        this.schema = schema;
        this.data = data;
        this.vent = $('<p/>');
        this.on = this.vent.on.bind(this.vent);
        this.trigger = this.vent.trigger.bind(this.vent);
    };
    SettingsForm.prototype.render = function render(containerElement, formViewOptions) {
        var self = this;
        formViewOptions = formViewOptions || {};
        var formOptions = window.getFormViewOptions(this.schema, formViewOptions);
        containerElement.prepend('<form class="list-form-common" action=""></form> <div class="form-results-box"></div>');
        var formElm = containerElement.find('form');
        var resultBox = containerElement.find('.form-results-box');

        formElm.html('');
        resultBox.html('');

        formElm.jsonForm({
            schema: this.schema,
            form: formOptions,
            value: this.data,
            onSubmit: function (errors, values) {
                if (errors) {
                    resultBox.html('<p>I beg your pardon?</p>');
                }
                else {
                    self.data = values;
                }
                self.trigger('submitted', self.data);
            }
        });
    };
    root.SettingsForm = SettingsForm;
})(window);