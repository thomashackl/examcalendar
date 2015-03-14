<?php
require 'bootstrap.php';

/**
 * ExamCalendar.class.php
 *
 * Erstellt eine Auflistung aller Prüfungen eines Semesters, wahlweise nur von
 * eigenen Veranstaltungen, bzw. bestimmten Fakultäten oder Studiengängen, und
 * gibt diese in verschiedenen Formaten aus. Mögliche Formate sind derzeit HTML
 * und PDF, jeweils als Liste oder Kalender, und iCal.
 *
 * Dieses Plugin basiert auf dem Prüfungskalender-Plugin für StudIP 1.3 von
 * Thomas Hackl <thomas.hackl@uni-passau.de>.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * @author  Alexander Findeis <findeis@fim.uni-passau.de>
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
class ExamCalendar extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();

        bindtextdomain('examcalendar', __DIR__.'/locale');

        $navigation = Navigation::getItem('/calendar');
        $examcalendar_navi = new AutoNavigation(dgettext('examcalendar', 'Prüfungskalender'), PluginEngine::getUrl('examcalendar/show/index'));
        $navigation->addSubNavigation('examcalendar', $examcalendar_navi);
    }

    public function initialize() {
        // nichts zu tun
    }

    public function perform($unconsumed_path) {
        Sidebar::Get()->setImage('sidebar/schedule-sidebar.png');

        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = Navigation::getItem('/calendar/examcalendar');
            $navi_show = new AutoNavigation(dgettext('examcalendar', 'Prüfungskalender'), PluginEngine::getUrl('examcalendar/show/index'));
            $navigation->addSubNavigation('show', $navi_show);

            $navi_settings = new AutoNavigation(dgettext('examcalendar', 'Einstellungen'), PluginEngine::getUrl('examcalendar/settings/examtypes'));
            $navigation->addSubNavigation('settings', $navi_settings);
        }

        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload() {
        if (class_exists("StudipAutoloader")) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }

}
