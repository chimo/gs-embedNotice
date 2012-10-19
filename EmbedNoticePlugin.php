<?php
/*
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2010, StatusNet, Inc.
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

/**
 * @package EmbedNoticePlugin
 * @maintainer Stephane Berube <chimo@chromic.org>
 */

if (!defined('STATUSNET')) { exit(1); }

class EmbedNoticePlugin extends Plugin
{

    function onEndShowStatusNetStyles($action) {
        $action->cssLink($this->path('css/embednotice.css'));
        return true;
    }

    function onEndShowScripts($action)
    {
        $action->script($this->path('js/embed.js'));
        return true;
    }

    function onStartShowNoticeItem($item)
//    function onEndShowNoticeOptionItems($item)
    {
        $notice = $item->notice;
        $out = $item->out;

        // TODO: point to a real page for when JS is disabled
        $out->element('a', array('href' => '#', 'class' => 'embed', 'title' => 'Embed this notice'), 'Embed this notice');

        return true;
    }

    /**
     * Provide plugin version information.
     *
     * This data is used when showing the version page.
     *
     * @param array &$versions array of version data arrays; see EVENTS.txt
     *
     * @return boolean hook value
     */
    function onPluginVersion(&$versions)
    {
        $url = 'http://status.net/wiki/Plugin:ShareNotice';

        $versions[] = array('name' => 'EmbedNotice',
            'version' => STATUSNET_VERSION,
            'author' => 'Stephane Berube',
            'homepage' => 'http://github.com/chimo/EmbedNotice',
            'rawdescription' =>
            // TRANS: Plugin description.
            _m('Allows visitors to get the necessary HTML to embed a notice on a webpage.'));

        return true;
    }
}
