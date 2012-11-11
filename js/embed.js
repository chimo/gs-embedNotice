(function($) {
    var $dialog = $('<div id="ch-embed"></div>')
                      .appendTo($('body'))
                      .dialog({ 
                              autoOpen: false, 
                              modal: true, 
                              width: 530,
                              height: 230,
                              title: 'Embed this notice',
                              close: function() {
                                  $(this).empty();
                              } 
                        });

    $('#content_inner').delegate('.embed', 'click', function (e) {
        if(!e.ctrlKey && !e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();

            $dialog.load($(this).attr('href') + ' #ch-ta', function(respsonse, status, xhr) {
                if (status === 'error') {
                    // TODO: Proper error message
                    $dialog.html('<p>Error :(</p>' + xhr.status + ' ' + xhr.statusText);
                }
            });

            $dialog.dialog('open'); 
        }
    });
})(jQuery);
