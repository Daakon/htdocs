function showHideMenu() {
    $('window').scrollDown(function(){$('#menu').hide()});

    $('window').scrollUp(function(){ $('#menu').show() });
}