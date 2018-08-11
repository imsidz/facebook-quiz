define(['hbs/handlebars'], function(Handlebars){
    "use strict";

    Handlebars.registerHelper ('asset', window.asset);//Setting it to global 'asset' function
    Handlebars.registerHelper ('contentUrl', window.contentUrl);//Setting it to global 'contentUrl' function
})