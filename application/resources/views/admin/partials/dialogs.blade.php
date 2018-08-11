<script>
    (function (swal) {
        var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
        //Function to convert hex format to a rgb color
        function rgb2hex(rgb) {
            rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
        }
        function hex(x) {
            return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
        }
        function getClassBackgroundColor(className) {
            var tempElm = $("<div/>").appendTo("body").addClass("hide " + className);
            var bgcolor = rgb2hex(tempElm.css("background-color"));
            tempElm.remove();
            return bgcolor;
        }
        function getBootstrapButtonColor(buttonType) {
            return getClassBackgroundColor('btn-' + buttonType);
        }

        window.originalSwal = swal;
        window.swal = function(arg1) {
            var params = {
                confirmButtonText: 'Okay',
                cancelButtonText: '{{__('cancelBtn')}}',
                animation: 'anim-open'
            };
            var isOptionsCall = false;
            switch (typeof arg1) {
                // Ex: swal("Hello", "Just testing", "info");
                case 'string':
                    params.title = arg1;
                    params.text = arguments[1] || '';
                    params.type = arguments[2] || '';
                    break;

                // Ex: swal({ title:"Hello", text: "Just testing", type: "info" });
                case 'object':
                    isOptionsCall = true;
                    params = $.extend(true, params, arg1);
                    break;
            }
            var customConfirmColor = null;
            if(params.confirmButtonClass) {
                customConfirmColor = getBootstrapButtonColor(params.confirmButtonClass);
            }
            customConfirmColor && (params.confirmButtonColor = customConfirmColor);
            if(isOptionsCall) {
                arguments[0] = params;
                return window.originalSwal.apply(this, arguments);
            }
            else
                return window.originalSwal(params);
        }
    })(window.swal);
    swal.prototype = window.originalSwal.prototype;
</script>
<script>
    window.dialogs = swal;
    dialogs.error = function(msg, callback){
        callback = callback || function(){};
        msg = msg.replace('\n', '<br>');
        swal({
            title: "Error",
            text: msg,
            type: "error",
            confirmButtonText: "Okay!",
            html: true
        }, callback);
    }
    dialogs.success = function(msg, callback){
        callback = callback || function(){};
        msg = msg.replace('\n', '<br>');
        swal({
            title: "Success",
            text: msg,
            type: "success",
            confirmButtonText: "Okay!",
            html: true
        }, callback);
    }
    dialogs.loading = function (msg) {
        swal({
            title: msg,
            text: '<i class="fa fa-3x fa-spinner fa-pulse text-success" style="color: #FF832B;"></i>',
            showConfirmButton: false,
            html: true
        });
    }
</script>