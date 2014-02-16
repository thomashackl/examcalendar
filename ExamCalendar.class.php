<?php
require 'bootstrap.php';

/**
 * Pruefungskalender.class.php
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
 * @version 0.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
class ExamCalendar extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();

        $navigation = Navigation::getItem('/calendar'); // Hauptmen� "Planer"
        $examcalendar_navi = new AutoNavigation(_('Pr�fungskalender'), PluginEngine::getUrl('examcalendar/show/output'));
        $navigation->addSubNavigation('examcalendar', $examcalendar_navi);
    }

    public function initialize () {
        // do nothing
    }

    public function perform($unconsumed_path) {
        $navigation = Navigation::getItem('/calendar/examcalendar');
        $navi_index = new AutoNavigation(_('Pr�fungskalender'), PluginEngine::getUrl('examcalendar/show/output'));
        $navigation->addSubNavigation('output', $navi_index);

//         $navi_index = new AutoNavigation(_('Erweitert'), PluginEngine::getUrl('pruefungskalender/show/index'));
//         $navigation->addSubNavigation('index', $navi_index);

        // Einstellungen sieht nur root
         if ($GLOBALS['perm']->have_perm('root')) {
            $navi_settings = new AutoNavigation(_('Einstellungen'), PluginEngine::getUrl('examcalendar/show/settings'));
            $navigation->addSubNavigation('settings', $navi_settings);
        }

        // Stylesheets
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/style.css');
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/calendar.css');

        // JS Color Picker f�r die Einstellungen
        if ($GLOBALS['perm']->have_perm('root')) {
            PageLayout::addScript($this->getPluginURL() . '/assets/jscolor/jscolor.js');
        }

        // Persistent Headers f�r lange Tabellenausgaben
//         PageLayout::addStylesheet($this->getPluginURL() . '/assets/persistent-headers/style.css');
//         PageLayout::addScript($this->getPluginURL() . '/assets/persistent-headers/ph.js');



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