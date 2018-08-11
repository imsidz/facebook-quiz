<script>
    (function(){
        var objectName = '{{str_replace(array(" ", ".", "-", "_"), '', $_SERVER['HTTP_HOST']) . "-quizzesWidget"}}';
        window[objectName] = {
            showQuizzesList: function (container) {
                var containerId = 'quizWidget-' + (new Date()).getTime();
                container.setAttribute('id', containerId);
                container.innerHTML = '<iframe style="width:100%;" src="{{route('quizesIframe')}}?elm=' + encodeURIComponent('#' + containerId) + '&limit=' + container.getAttribute('data-limit') + '&stream=' + container.getAttribute('data-stream') + '" frameborder="0"></iframe>';
                window.addEventListener('message', function(e){
                    var data = e.data;
                    try{
                        data = JSON.parse(data);
                        if(data.type == "quizzes-iframe-height-change") {
                            var containerElement = document.querySelector(data.element);
                            var iframe = containerElement.children[0];
                            iframe.style.height = parseInt(data.height) + "px";
                        }
                    } catch (ex){
                        console.log(ex.message);
                    }
                });
            }
        };
        var widgets = document.querySelectorAll('.x-quizzes-list-widget');
        for(var i = 0; i < widgets.length; i++){
            window[objectName].showQuizzesList(widgets[i]);
        }
    })();
</script>