<?php
/**
 * PHP version 5
 *
 * @category Plugin
 * @package  StatusNet
 * @author   Stéphane Bérubé <chimo@chromic.org>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://github.com/chimo/EmbedNotice
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR.'/actions/shownotice.php';

class EmbedAction extends Action
{
    /**
     * Take arguments for running
     *
     * This method is called first, and it lets the action class get
     * all its arguments and validate them. It's also the time
     * to fetch any relevant data from the database.
     *
     * Action classes should run parent::prepare($args) as the first
     * line of this method to make sure the default argument-processing
     * happens.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare($args)
    {
        parent::prepare($args);
        $this->notice = Notice::staticGet('id', $this->trimmed('id'));

        // From: /actions/shownotice.php
        if (empty($this->notice)) {
            // Did we used to have it, and it got deleted?
            $deleted = Deleted_notice::staticGet($this->trimmed('id'));
            if (!empty($deleted)) {
                // TRANS: Client error displayed trying to show a deleted notice.
                $this->clientError(_('Notice deleted.'), 410);
            } else {
                // TRANS: Client error displayed trying to show a non-existing notice.
                $this->clientError(_('No such notice.'), 404);
            }

            return false;
        }

        return true;
    }

    /**
     * Handle request
     *
     * This is the main method for handling a request. Note that
     * most preparation should be done in the prepare() method;
     * by the time handle() is called the action should be
     * more or less ready to go.
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle($args)
    {
        $this->showPage();
    }

    /**
     * Title of this page
     *
     * Override this method to show a custom title.
     *
     * @return string Title of the page
     */
    function title()
    {
        return _m('Embed Notice');
    }

    /**
     * Show content in the content area
     *
     * The default StatusNet page has a lot of decorations: menus,
     * logos, tabs, all that jazz. This method is used to show
     * content in the content area of the page; it's the main
     * thing you want to overload.
     *
     * This method also demonstrates use of a plural localized string.
     *
     * @return void
     */
    function showContent()
    {
        // TODO: TRANS
        // TODO: Some of these xpath queries can probably be combined
        // TODO: Better CSS support for different notice types (bookmarks, etc)

        // Get HTML
        $out = new htmlstr();
        $nli = new SingleNoticeItem($this->notice, $out);
        $nli->show();
        $notice_str = $out->xw->outputMemory();

        // Build DOM
        $dom = new DOMDocument();
        $notice_str = mb_convert_encoding($notice_str, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($notice_str);
        $xpath = new DomXPath($dom);

        // Remove 'embed' link
        $elm = $xpath->query('//a[contains(@class, "embed")]');
        if($elm->length !== 0) {
            $elm = $elm->item(0);
            $elm->parentNode->removeChild($elm);
        }

        // Remove 'reply', 'favor', etc
        $elm = $xpath->query('//div[contains(@class, "notice-options")]');
        if($elm->length !== 0) {
            $elm = $elm->item(0);
            $elm->parentNode->removeChild($elm);
        }

        // Add parentheses around "in context"
        $elm = $xpath->query('//a[contains(@class, "response")]');
        if($elm->length !== 0) {
            $elm = $elm->item(0);
            $elm->nodeValue = '(' . $elm->nodeValue . ')';
        }

        // Remove location
        $elm = $xpath->query('//span[contains(@class, "location")]');
        if($elm->length !== 0) {
            $elm->item(0)->parentNode->removeChild($elm->item(0));
        }

        // Make timestamp absolute
        $elm = $xpath->query('//abbr[contains(@class, "published")]');
        if($elm->length !== 0) {
            $elm = $elm->item(0);
            $date_str = explode('T', $elm->getAttribute('title'));
            $elm->nodeValue = 'on ' . $date_str[0];
        }

        // Add triangle
        $elm = $xpath->query('//span[contains(@class, "author")]/a');
        if($elm->length !== 0) {
            $triangle = $dom->createElement('span');
            $triangle->setAttribute('style', 'border: 3px solid transparent; border-left-color: #000; display: inline-block; height: 0; margin: 0 3px 2px 5px; width: 0; line-height: 8px;');
            $elm->item(0)->appendChild($triangle);
        }

        // Remove all classes (reduce chances of clashing with foreign CSS)
        $elm = $xpath->query('//*[@class]');
        foreach($elm as $el) {
            $el->removeAttribute('class');
        }

        // Avatar styles
        $elm = $xpath->query('//img[contains(@class, "avatar")]');
        if($elm->length !== 0) {
            $elm->item(0)->setAttribute('style', 'position: absolute; left: 5px; top: 7px;');
        }

        // entry-title styles
        $elm = $xpath->query('//div[contains(@class, "entry-title")]');
        if($elm->length !== 0) {
            $elm->item(0)->setAttribute('style', 'margin: 2px 7px 0 59px;');
        }

        // entry-content styles
        $elm = $xpath->query('//div[contains(@class, "entry-content")]');
        if($elm->length !== 0) {
            $elm->item(0)->setAttribute('style', 'margin: 2px 7px 0 59px;');
        }

        // Nodes to string
        $embed_str = '<blockquote style="position: relative;">';
        $elm = $xpath->query('//body/li');
        $elm = $elm->item(0)->childNodes;
        foreach($elm as $el) {
            $embed_str .= $dom->saveHTML($el);
        }
        $embed_str .= '</blockquote>';
        $embed_str = htmlspecialchars($embed_str, ENT_NOQUOTES);

        // Remove lines containing only whitespace
        $embed_str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $embed_str);

        // Spit out code
        $this->element('h2', null, 'HTML Code');
        $this->elementStart('textarea', array('id' => 'ch-ta'));
        $this->raw($embed_str);
        $this->elementEnd('textarea');

        // Render the notice for reference
        $this->element('h2', null, 'Corresponding Notice');
        $this->elementStart('ol', array('class' => 'notices xoxo'));
        $this->raw($notice_str);
        $this->elementEnd('ol');
    }

    /**
     * Return true if read only.
     *
     * Some actions only read from the database; others read and write.
     * The simple database load-balancer built into StatusNet will
     * direct read-only actions to database mirrors (if they are configured),
     * and read-write actions to the master database.
     *
     * This defaults to false to avoid data integrity issues, but you
     * should make sure to overload it for performance gains.
     *
     * @param array $args other arguments, if RO/RW status depends on them.
     *
     * @return boolean is read only action?
     */
    function isReadOnly($args)
    {
        return true;
    }
}

class htmlstr extends HTMLOutputter {
    function __construct() {
        $this->xw = new XMLWriter();
        $this->xw->openMemory();
        $indent = common_config('site', 'indent');
        $this->xw->setIndent($indent);
    }
}

