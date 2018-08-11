(function(){
    window.addEventListener('message', function(e){
        try{
            data = JSON.parse(e.data);
            if(data.type == "quiz-embed-iframe-height-change") {
                var iframe = document.querySelector('#' + data.elmPrefix + data.quizId);
                iframe.style.height = parseInt(data.height) + "px";
            }

            if(data.type == "quiz-embed-scroll-top") {
                var iframe = document.querySelector('#' + data.elmPrefix + data.quizId);
                $(window).trigger('quiz-embed-scroll-top', {
                    elm: iframe,
                    offset: data.topPos
                });
            }

        } catch (ex){}
    });
})();