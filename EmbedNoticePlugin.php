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
    const VERSION = '0.1';

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/embednotice.css'));
        return true;
    }

    function onEndShowScripts($action)
    {
        $action->script($this->path('js/embed.js'));
        return true;
    }

    function onStartShowNoticeItem($item)
    {
        $url = common_local_url('embed', array('id' => $item->notice->id));
        // TODO: TRANS
        $item->out->element('a', array('href' =>  $url, 'class' => 'embed', 'title' => 'Embed this notice'), 'Embed this notice');

        return true;
    }

    /**
     * Map URLs to actions
     *
     * @param Net_URL_Mapper $m path-to-action mapper
     *
     * @return boolean hook value; true means continue processing, false means stop.
     */
    function onRouterInitialized($m)
    {
        $m->connect('embed/:id',
            array('action' => 'embed'),
            array('id' => '[0-9]+'));

        return true;
    }

    /**
     * Load related modules when needed
     *
     * Most non-trivial plugins will require extra modules to do their work. Typically
     * these include data classes, action classes, widget classes, or external libraries.
     *
     * This method receives a class name and loads the PHP file related to that class. By
     * tradition, action classes typically have files named for the action, all lower-case.
     * Data classes are in files with the data class name, initial letter capitalized.
     *
     * Note that this method will be called for *all* overloaded classes, not just ones
     * in this plugin! So, make sure to return true by default to let other plugins, and
     * the core code, get a chance.
     *
     * @param string $cls Name of the class to be loaded
     *
     * @return boolean hook value; true means continue processing, false means stop.
     */
    function onAutoload($cls)
    {
        $dir = dirname(__FILE__);

        switch ($cls)
        {
        case 'EmbedAction':
            include_once $dir . '/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
            return false;
        default:
            return true;
        }
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
        $versions[] = array('name' => 'EmbedNotice',
            'version' => self::VERSION,
            'author' => 'Stephane Berube',
            'homepage' => 'http://github.com/chimo/EmbedNotice',
            'rawdescription' =>
            // TRANS: Plugin description.
            _m('Allows visitors to get the necessary HTML to embed a notice on a webpage.'));

        return true;
    }
}
