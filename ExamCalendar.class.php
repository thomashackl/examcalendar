<?php
require 'bootstrap.php';

/**
 * ExamCalendar.class.php
 *
 * Erstellt eine Auflistung aller Pr�fungen eines Semesters, wahlweise nur von
 * eigenen Veranstaltungen, bzw. bestimmten Fakult�ten oder Studieng�ngen, und
 * gibt diese in verschiedenen Formaten aus. M�gliche Formate sind derzeit HTML
 * und PDF, jeweils als Liste oder Kalender, und iCal.
 *
 * Dieses Plugin basiert auf dem Pr�fungskalender-Plugin f�r StudIP 1.3 von
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

        $navigation = Navigation::getItem('/calendar'); // Hauptmen� "Planer"
        $examcalendar_navi = new AutoNavigation(dgettext('examcalendar', 'Pr�fungskalender'), PluginEngine::getUrl('examcalendar/show/output'));
        $navigation->addSubNavigation('examcalendar', $examcalendar_navi);
    }

    public function initialize () {
        // do nothing
    }

    public function perform($unconsumed_path) {
        // Einstellungen (und damit komplette Navigation) sieht nur root
        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = Navigation::getItem('/calendar/examcalendar');
            $navi_output = new AutoNavigation(dgettext('examcalendar', 'Pr�fungskalender'), PluginEngine::getUrl('examcalendar/show/output'));
            $navigation->addSubNavigation('output', $navi_output);

            $navi_settings = new AutoNavigation(dgettext('examcalendar', 'Einstellungen'), PluginEngine::getUrl('examcalendar/show/settings'));
            $navigation->addSubNavigation('settings', $navi_settings);
        }

        // Stylesheets
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/style.css');
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/calendar.css');

        // JS Color Picker f�r die Einstellungen
        if ($GLOBALS['perm']->have_perm('root')) {
            PageLayout::addScript($this->getPluginURL() . '/vendor/jscolor/jscolor.js');
            PageLayout::addScript($this->getPluginURL() . '/assets/settings.js');
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
