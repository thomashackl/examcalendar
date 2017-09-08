<?php

class SettingsController extends AuthenticatedController {

    public function before_filter(&$action, &$args) {
        $GLOBALS['perm']->check('root');

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        Navigation::activateItem('/calendar/examcalendar/settings');
    }

    public function index_action() {
        $this->redirect('settings/examtypes');
    }

    public function examtypes_action() {
        if (Request::submitted('save')) {
            Settings::updateExamTypes(Request::getArray('exam_types'));
            $this->update_success = true;
        }

        PageLayout::addStylesheet($this->dispatcher->plugin->getPluginURL() . '/assets/settings/examtypes.css');

        $this->exam_types = Settings::getExamTypes();
    }

    public function faculties_action() {
        if (Request::submitted('save')) {
            // Fakultäts-IDs und Farbwerte zusammenfassen, ungültige Farbwerte mit 000000 ersetzen
            $fac_id_array = Request::optionArray('fac_id');
            $color_array = Request::optionArray('color');

            for ($i = 0; $i < count($fac_id_array); $i++) {
                $color = preg_match('/[0-9A-Fa-f]{6}/', $color_array[$i]) ? $color_array[$i] : '000000';
                $faculties[$fac_id_array[$i]] = $color;
            }

            Settings::updateFaculties($faculties);
            $this->update_success = true;
        }

        PageLayout::addScript($this->dispatcher->plugin->getPluginURL() . '/vendor/jscolor/jscolor.js');
        PageLayout::addScript($this->dispatcher->plugin->getPluginURL() . '/assets/settings/faculties.js');

        $this->faculties = Settings::getFaculties();
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
