<?php

class ShowController extends AuthenticatedController {

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $this->set_content_type('text/html; charset=windows-1252');
    }

    private function saveRequestParams() {
        if (Request::option('sem_select')) {
            $this->sem_select = Request::option('sem_select');
//             $this->sem_tree = Request::option('sem_tree') ? Request::option('sem_tree') : 'all';
            $this->only_own = Request::get('only_own') ? true : false;
            $this->deputies = Request::get('deputies') ? true : false;
            $this->format = Request::option('format');
        } else {
            $this->sem_select = SemesterData::getInstance()->GetSemesterIdByDate(time());
//             $this->sem_tree = 'all';
            $this->only_own = !$GLOBALS['perm']->have_perm('admin');
            $this->deputies = false;
            $this->format = 'html_list';
        }
    }

    public function output_action() {
        $this->saveRequestParams();

        $selectedSemester = SemesterData::getInstance()->getSemesterData($this->sem_select);
        // übergib, falls vorhanden, die Semesterbeschreibung, ansonsten den Semesternamen
        $this->semester = empty($selectedSemester['description']) ? $selectedSemester['name'] : $selectedSemester['description'];

        $exams = new ExamDB();
        $exams->querySQL($this->sem_select, $this->sem_tree, $this->only_own, $this->deputies);
        $result = $exams->getExams();

        if (empty($result)) {
            $this->no_results = true;
            $this->render_action('error');

            return;
        }

        switch ($this->format) {
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

            case 'pdf_list':
                ExamExport::exportPDFlist($this->semester, $exams->getSelectedNum(), $result, $exams->getFaculties());

                $this->render_nothing();
                break;

            case 'ical':
                $this->selected = $exams->getSelectedNum();
                $this->exams = $result;

                $this->render_template('show/ical');
                break;

            default:
                $this->format_error = true;
                $this->render_action('error');
        }
    }

    public function settings_action() {
        $settings = new Settings();
        $settings->querySQL();
        $this->faculties = $settings->getFaculties();
        $this->exam_types = $settings->getExamTypes();
    }

    public function update_action() {
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
