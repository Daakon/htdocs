
    function saveScrollPositionOnLinkClick(page) {
        var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
        createCookie('Page',page,1);
        createCookie('Scrolly',scrolly,1);

    }

    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }


    function showLink(id) {
        var shareLink = document.getElementById(id);
        if (shareLink.style.display == 'none') {
            shareLink.style.display = 'block';
            shareLink.select();
        }
        else {
            shareLink.style.display = 'none';
        }
        //document.getElementById(id).select();
    }


