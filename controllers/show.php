<?php

class ShowController extends AuthenticatedController {

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
//        PageLayout::setTitle('');
    }

    public function index_action() {
        // Studienbereiche auslesen
        $semTree = TreeAbstract::GetInstance("StudipSemTree", array('visible_only' => true));
        $semTree->init();

        $entries = array();
        $children = array();
        foreach ($semTree->getKids('root') as $child) {
            $entries[$child] = htmlReady($semTree->tree_data[$child]['name']);
            if ($semTree->hasKids($child)) {
                $children[$child] = array();
                foreach ($semTree->getKids($child) as $grandchild) {
                    $children[$child][$grandchild] = htmlReady($semTree->tree_data[$grandchild]['name']);
                }
                asort($children[$child]);
            }
        }
        asort($entries);

        $this->entries = $entries;
        $this->children = $children;
    }

    public function output_action() {
        $semesterData = SemesterData::getInstance();
        $selectedSemester = $semesterData->getSemesterData(Request::option('sem_select'));
        // übergib, falls vorhanden, die Semesterbeschreibung, ansonsten den Semesternamen
        $this->semester = empty($selectedSemester['description']) ? $selectedSemester['name'] : $selectedSemester['description'];

        $exams = new Exams();
        $exams->querySQL(Request::option('sem_select'), Request::option('sem_tree'), Request::get('only_own') ? true : false, Request::get('only_own') ? true : false);
        $result = $exams->getExams();

        if (empty($result)) {
            // auf Indexseite umleiten
            $this->index_action();
            $this->no_results = true;
            $this->render_action('index');
            return;
        }

        switch (Request::option('format')) {
            case 'html_list':
                $this->selected = $exams->getSelectedNum();
                $this->exams = $result;
                $this->faculties = $exams->getFaculties();
                $this->render_action('html_list');
                break;

            case 'html_calendar':
                $this->selected = $exams->getSelectedNum();
                $this->exams = $exams->getOrderedExams();
                $this->faculties = $exams->getFaculties();
                $this->render_action('html_calendar');
                break;

            case 'pdf_list': // TODO
                $this->index_action();
                $this->format_error = true;
                $this->render_action('index');
                break;

            case 'pdf_calendar': // TODO
                $this->index_action();
                $this->format_error = true;
                $this->render_action('index');
                break;

            case 'ical':
                // kein Output, nur Download
                $this->selected = $exams->getSelectedNum();
                $this->exams = $result;
                $this->render_template('show/ical');
                break;

            default:
                // auf Indexseite umleiten, Fehler anzeigen
                $this->index_action();
                $this->format_error = true;
                $this->render_action('index');
        }
    }

    public function settings_action() {
        $settings = new Settings();
        $settings->querySQL();
        $this->faculties = $settings->getFaculties();
        $this->exam_types = $settings->getExamTypes();
    }

    public function update_action() {
        $faculties = array();

        // Fakultäts-IDs und Farbwerte zusammenfassen, ungültige Farbwerte mit 000000 ersetzen
        $fac_id_array = Request::optionArray('fac_id');
        $color_array = Request::optionArray('color');

        for ($i = 0; $i < count($fac_id_array); $i++) {
            $color = preg_match('/[0-9A-Fa-f]{6}/', $color_array[$i]) ? $color_array[$i] : '000000';
            $faculties[$fac_id_array[$i]] = $color;
        }

        $this->faculties = $faculties;

        $exam_types = Request::getArray('exam_types');

        $settings = new Settings();
        $settings->updateSQL($faculties, $exam_types);

        // auf Einstellungsseite umleiten
        $this->settings_action();
        $this->update_success = true;
        $this->render_action('settings');
    }

    // customized #url_for for plugins
    function url_for($to) {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }

}
