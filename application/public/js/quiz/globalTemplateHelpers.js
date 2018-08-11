define(['hbs/handlebars'], function(Handlebars){
    "use strict";

    //Add translate helper to marionette renderer
    Handlebars.registerHelper ('__', __);//Setting it to global __ (translate function)
    Handlebars.registerHelper ('asset', window.asset);//Setting it to global 'asset' function
    Handlebars.registerHelper ('contentUrl', window.contentUrl);//Setting it to global 'contentUrl' function
})