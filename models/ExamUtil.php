<?php
class ExamUtil {

    public static function get_display_formats() {
        return array(
            'html_list'     => dgettext('examcalendar', 'Liste'),
            'html_calendar' => dgettext('examcalendar', 'Kalender')
        );
    }

    public static function get_export_formats() {
        return array(
            'pdf_list'      => 'PDF (' . dgettext('examcalendar', 'Liste') . ')',
            'ical'          => 'iCal'
        );
    }

    private static function getWeekday($day) {
        switch($day) {
            case 0:
                return dgettext('examcalendar', 'So');
            case 1:
                return dgettext('examcalendar', 'Mo');
            case 2:
                return dgettext('examcalendar', 'Di');
            case 3:
                return dgettext('examcalendar', 'Mi');
            case 4:
                return dgettext('examcalendar', 'Do');
            case 5:
                return dgettext('examcalendar', 'Fr');
            case 6:
                return dgettext('examcalendar', 'Sa');
            default:
                return dgettext('examcalendar', 'ungültiger Wochentag');
        }
    }

    public static function getMonth($month) {
        switch($month) {
            case 1:
                return dgettext('examcalendar', 'Januar');
            case 2:
                return dgettext('examcalendar', 'Februar');
            case 3:
                return dgettext('examcalendar', 'März');
            case 4:
                return dgettext('examcalendar', 'April');
            case 5:
                return dgettext('examcalendar', 'Mai');
            case 6:
                return dgettext('examcalendar', 'Juni');
            case 7:
                return dgettext('examcalendar', 'Juli');
            case 8:
                return dgettext('examcalendar', 'August');
            case 9:
                return dgettext('examcalendar', 'September');
            case 10:
                return dgettext('examcalendar', 'Oktober');
            case 11:
                return dgettext('examcalendar', 'November');
            case 12:
                return dgettext('examcalendar', 'Dezember');
            default:
                return dgettext('examcalendar', 'ungültiger Monat');
        }
    }

    public static function nice_date($timestamp) {
        return self::getWeekday(date("w", $timestamp)) . "., " . date("d.m.Y", $timestamp);
    }

    public static function nice_time($timestamp) {
        return date("H:i", $timestamp);
    }

    public static function create_show_sidebar($controller, $sem_select, $only_own, $deputies, $previous, $filter, $filters, $format, $faculties) {
        $sidebar = Sidebar::Get();

        $params = array(
            'sem_select' => $sem_select,
            'only_own' => $only_own,
            'deputies' => $deputies,
            'previous' => $previous,
            'filter' => $filter,
            'format' => $format
        );

        // Semester-Auswahl
        $semester_widget = new SelectWidget(dgettext('examcalendar', 'Semester'), $controller->url_for('show/index', $params), 'sem_select');
        foreach (array_reverse(Semester::getAll()) as $sem) {
            $semester_widget->addElement(new SelectElement($sem->id, $sem->name, $sem->id == $sem_select));
        }
        $sidebar->addWidget($semester_widget);

        // Einstellungen (Checkboxen)
        $options_widget = new OptionsWidget();
        $options_widget->addCheckbox(
            dgettext('examcalendar', 'nur eigene Veranstaltungen'),
            $only_own,
            $controller->url_for('show/index', array_merge($params, array('only_own' => 1))),
            $controller->url_for('show/index', array_merge($params, array('only_own' => 0)))
        );
        if (Config::get()->DEPUTIES_ENABLE && $GLOBALS['perm']->have_perm('dozent')) {
            $options_widget->addCheckbox(
                dgettext('examcalendar', 'ich bin Dozierendenvertretung'),
                $deputies,
                $controller->url_for('show/index', array_merge($params, array('deputies' => 1))),
                $controller->url_for('show/index', array_merge($params, array('deputies' => 0)))
            );
        }
        $options_widget->addCheckbox(
            dgettext('examcalendar', 'vergangene Prüfungstermine'),
            $previous,
            $controller->url_for('show/index', array_merge($params, array('previous' => 1))),
            $controller->url_for('show/index', array_merge($params, array('previous' => 0)))
        );
        $sidebar->addWidget($options_widget);

        // Fakultät eingrenzen
        $filter_widget = new SelectWidget(dgettext('examcalendar', 'Fakultät eingrenzen'), $controller->url_for('show/index', $params), 'filter');
        $filter_widget->addElement(new SelectElement('all', dgettext('examcalendar', 'alle Fakultäten'), $filter == 'all'));
        foreach ($filters as $f) {
            $filter_widget->addElement(new SelectElement($f['fac_id'], $f['faculty'], $f['fac_id'] == $filter));
        }
        $sidebar->addWidget($filter_widget, 'filter');

        // Ansichten-Auswahl
        $views_widget = new ViewsWidget();
        foreach (ExamUtil::get_display_formats() as $id => $name) {
            $views_widget->addLink($name, $controller->url_for('show/index', array_merge($params, array('format' => $id))))->setActive($format == $id);
        }
        $sidebar->addWidget($views_widget);

        // zeige Export und Legende nur an, wenn Prüfungen gefunden wurden (also sind Fakultäten in der Legende)
        if (!empty($faculties)) {
            $export_widget = new ExportWidget();
            foreach (ExamUtil::get_export_formats() as $id => $name) {
                if (strpos($id, 'pdf') !== false) {
                    $icon = 'file-pdf';
                } else if (strpos($id, 'ical') !== false) {
                    $icon = 'timetable';
                } else {
                    $icon = 'file-text';
                }
                $export_widget->addLink($name, $controller->url_for('show/index', array_merge($params, array('format' => $id))), Icon::create($icon, 'clickable'));
            }
            $sidebar->addWidget($export_widget, 'export');

            // Fakultäten-Legende
            $faculties_widget = new SidebarWidget();
            $faculties_widget->setTitle(dgettext('examcalendar', 'Fakultäten'));

            $faculty_box = '<table>';
            if (!empty($faculties)) foreach($faculties as $f) {
                $faculty_box .= '    <tr>';
                $faculty_box .= '        <td class="colorbox_info" style="background: #' . $f['color']. '">';
                $faculty_box .= '        </td>';
                $faculty_box .= '        <td>';
                $faculty_box .=              htmlReady($f['faculty']);
                $faculty_box .= '        </td>';
                $faculty_box .= '    </tr>';
            }
            $faculty_box .= '</table>';

            $faculties_widget->addElement(new WidgetElement($faculty_box));
            $sidebar->addWidget($faculties_widget, 'faculties');
        }
    }

    public static function create_settings_sidebar($controller, $view) {
        $sidebar = Sidebar::Get();

        $views_widget = new ViewsWidget();
        $views_widget->addLink(dgettext('examcalendar', 'Termintypen'), $controller->url_for('settings/examtypes'))->setActive($view == 'examtypes');
        $views_widget->addLink(dgettext('examcalendar', 'Fakultätsfarben'), $controller->url_for('settings/faculties'))->setActive($view == 'faculties');

        $sidebar->addWidget($views_widget);
    }

}
