(function() {
    var services = {
        'facebook': {
            'URL': 'https://facebook.com/sharer.php?u=',
            'title' : '&t=',
            'fontello': 'facebook',
            'name' : 'Facebook'
        },
        'twitter' : {
            'URL': 'https://twitter.com/intent/tweet?url=',
            'title': '&text=',
            'fontello': 'twitter',
            'name' : 'Twitter'
        },
        'vk' : {
            'URL': 'http://vk.com/share.php?url=',
            'fontello': 'vkontakte-rect',
            'title' : '&title=',
            'name' : 'VK'
        },
        'googleplus' : {
            'URL': 'https://plus.google.com/share?url=',
            'fontello': 'googleplus-rect',
            'name' : 'Google+'
        },
        'ok' : {
            'URL': 'http://odnoklassniki.ru/dk?st.cmd=addShare&st._surl=',
            'title' : '&title=',
            'fontello': 'odnoklassniki',
            'name' : 'Ok.ru'
        },
        'friendfeed' : {
            'URL': 'http://friendfeed.com/?url=',
            'title' : '&title=',
            'fontello': 'friendfeed',
            'name' : 'FriendFeed'
        },
        'linkedin' : {
            'URL': 'http://linkedin.com/shareArticle?mini=true&url=',
            'title' : '&title=',
            'fontello': 'linkedin',
            'name' : 'LinkedIn'
        },
        'tumblr' : {
            'URL': 'http://tumblr.com/share/link?url=',
            'title' : '&name=',
            'fontello': 'tumblr',
            'name' : 'Tumblr'
        },
        'blogger' : {
            'URL': 'http://blogger.com/blog_this.pyra?u=',
            'title' : '&n=',
            'fontello': 'blogger',
            'name' : 'Blogger'
        },
        'diigo' : {
            'URL': 'https://www.diigo.com/post?url=',
            'title' : '&title=',
            'fontello': 'diigo',
            'name' : 'Diigo'
        },
        'reddit' : {
            'URL': 'http://reddit.com/submit?url=',
            'title' : '&title=',
            'fontello': 'reddit',
            'name' : 'Reddit'
        },
        'delicious' : {
            'URL': 'http://delicious.com/post/?url=',
            'title' : '&title=',
            'fontello': 'delicious',
            'name' : 'Delicious'
        },
        'digg' : {
            'URL': 'http://digg.com/submit?phase=2&url=',
            'title' : '&title=',
            'fontello': 'digg',
            'name' : 'Digg'
        },
        'stumbleupon' : {
            'URL': 'http://www.stumbleupon.com/submit?url=',
            'title' : '&title=',
            'fontello': 'stumbleupon',
            'name' : 'StumbleUpon'
        },
        'pinterest' : {
            'URL': 'http://pinterest.com/pin/create/link/?url=',
            'title' : '&description=',
            'media' :   '&media=',
            'fontello': 'pinterest',
            'name' : 'Pinterest'
        },
        'evernote' : {
            'URL': 'http://www.evernote.com/clip.action?url=',
            'title' : '&title=',
            'fontello': 'evernote',
            'name' : 'Evernote'
        },
        'instapaper' : {
            'URL': 'http://www.instapaper.com/edit?url=',
            'title' : '&title=',
            'fontello': 'instapaper',
            'name' : 'Instapaper'
        },
        'hackernews' : {
            'URL': 'http://news.ycombinator.com/submitlink?u=',
            'title' : '&t=',
            'fontello': 'hackernews',
            'name' : 'Hacker News'
        }
    };

    function activateLink(element, service, dataURL, title, media) {

        if (dataURL) {
            var URL = services[service]['URL'] + encodeURIComponent(dataURL);
        } else {
            var URL = services[service]['URL'] + encodeURIComponent(window.location);
        }

        var titlelink = "";
        if (services[service]['title'] !== undefined && title) {
            var titlelink = services[service]['title'] + encodeURIComponent(title);
        }
        var medialink = "";
        //For pinterest
        if (services[service]['media'] !== undefined && media) {
            medialink = services[service]['media'] + encodeURIComponent(media);
        }

        URL += titlelink;
        URL += medialink;
        element.setAttribute("href", encodeURI(URL));
        element.setAttribute("title", services[service]['name']);
        element.onclick = function() {
            window.open(URL, "popup", "width=600px,height=300px,left=50%,top=50%");
            return false;
        }
    }
    window.SocialSharing = {
        parse: function(root) {
            var $$ = root ? root.find : $;
            $$('.social-sharing-btn').each(function() {
                var url = $(this).data('url'),
                    service = $(this).data('social-network'),
                    title = $(this).data('title'),
                    media = $(this).data('media');
                url = url ? url : window.location.href;
                activateLink($(this)[0], service, url, title, media)
            });
        }
    }
})();