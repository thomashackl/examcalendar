<?php

class ShowController extends AuthenticatedController {

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $this->set_content_type('text/html; charset=windows-1252');

        PageLayout::addStylesheet($this->dispatcher->plugin->getPluginURL() . '/assets/show/style.css');
    }

    private function saveRequestParams() {
        if (Request::option('sem_select')) {
            $this->sem_select = Request::option('sem_select');
            $this->only_own = Request::get('only_own') ? true : false;
            $this->deputies = Request::get('deputies') ? true : false;
            $this->previous = Request::get('previous') ? true : false;
            $this->sem_tree = Request::option('sem_tree') ? Request::option('sem_tree') : 'all';
            $this->format = Request::option('format');
        } else {
            $this->sem_select = SemesterData::getInstance()->GetSemesterIdByDate(time());
            $this->only_own = !$GLOBALS['perm']->have_perm('admin');
            $this->deputies = false;
            $this->previous = false;
            $this->sem_tree = 'all';
            $this->format = 'html_list';
        }
    }

    private function initSemTree() {
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

        $this->sem_tree_data['entries'] = $entries;
        $this->sem_tree_data['children'] = $children;
    }

    public function index_action() {
        $this->saveRequestParams();
        $this->initSemTree();

        $selectedSemester = SemesterData::getInstance()->getSemesterData($this->sem_select);
        // �bergib, falls vorhanden, die Semesterbeschreibung, ansonsten den Semesternamen
        $this->semester = $selectedSemester['description'] ? : $selectedSemester['name'];

        $exams = new ExamDB();
        $exams->querySQL($this->sem_select, $this->only_own, $this->deputies, $this->previous, $this->sem_tree);
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
                PageLayout::addStylesheet($this->dispatcher->plugin->getPluginURL() . '/assets/show/html_calendar.css');

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
