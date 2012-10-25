(function($) {
    // TODO: we can probably make the code snippet look prettier if it's not in a textarea
    //       ex: syntax highlighting, etc.
    var $dialog   = $('<div id="ch-embed"><textarea style="width: 100%; height: 95%;"></textarea></div>')
                        .appendTo($('body'))
                        .dialog({ 
                                autoOpen: false, 
                                modal: true, 
                                width: 530,
                                height: 230,
                                title: 'Embed this notice',
                                close: function() {
                                    $textarea.val('');
                                } 
                        }),
        $textarea = $dialog.children('textarea'),
        template  = '<blockquote style="position: relative;" cite="${url}">${title} ${thumbs} ${content}</blockquote>';

    $('#notices_primary').delegate('.embed', 'click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $notice = $(this).closest('li.notice').clone(),
            // Make relative timestamp absolute
            ndate   = 'on ' + $notice.find('.published').attr('title').split('T')[0] + ' '; 
            $notice.find('.published')
                .text(ndate)
                .css('border', 'none');
            // Remove location
            $notice.find('.location').remove();
            // CSS
            $notice.find('a[href]').css('text-decoration', 'none');
            $notice.find('img.avatar').css({
                'position': 'absolute',
                'left': '5px',
                'top': '7px'
            });
            // Add triangle separator
            $notice.find('.author .fn').after('<span style="border: 3px solid transparent; border-left-color: #000; display: inline-block; height: 0; margin: 0 3px 2px 5px; width: 0; line-height: 8px;"></span>');

        // TODO: Might be simpler just to dump $notice.html() in the container...
        var content = template.replace('${url}', $notice.find('.timestamp').attr('href'))
                        .replace('${title}', '<div style="margin: 2px 7px 0 59px;">' + ($notice.children('.entry-title').html() || '') + '</div>')
                        .replace('${thumbs}', '<div style="margin: 2px 7px 0 59px;">' + ($notice.children('.thumbnails').html() || '') + '</div>')
                        .replace('${content}', '<div style="margin: 2px 7px 0 59px;">' + ($notice.children('div.entry-content').last().html() || '') + '</div>') // ;
                        .replace(/>\s+</g,'><'); // Clean (most) whitespace between tags

        $textarea.val(content);
        $dialog.dialog('open'); 
    });
})(jQuery);
