gs-embedNotice
===========

GNU social plugin that adds an icon to each notice.  
Clicking the icon allows visitors to get the necessary HTML to embed a notice on a webpage.

## Screenshots

The extra icons on the timeline:  
![Timeline icons](https://static.chromic.org/repos/gs-embedNotice/embed-notice-button.png)

----

The dialog with the HTML code to copy/paste:  
![Generated HTML](https://static.chromic.org/repos/gs-embedNotice/embed-notice-html.png)

## Installation

1. Navigate to your /local/plugins directory (create it if it doesn't exist)
2. `git clone https://github.com/chimo/gs-embedNotice.git EmbedNotice`
3. Tell `/config.php` to use it with: `addPlugin('EmbedNotice');`  

The embed icon should appear in each notice.
