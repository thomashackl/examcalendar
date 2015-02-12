<?php

require_once('lib/dates.inc.php');

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
        return getWeekday(date("w", $timestamp)) . "., " . date("d.m.Y", $timestamp);
    }

    public static function nice_time($timestamp) {
        return date("H:i", $timestamp);
    }

    public static function create_infobox(&$infobox, $faculties, $controller, $sem_select, $only_own, $deputies, $sem_tree, $format) {
        // Fakultäten-Legende für Infobox
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

        // Einstellungen für den Prüfungskalender
        $settings_box  = '<form action="' . $url . '" method="get">';

        $display_formats = ExamUtil::get_display_formats();
        $onlyOwnPreset = $only_own ? ' checked="checked"' : '';
        $dozentPerms = $GLOBALS['perm']->have_perm('dozent');
        $deputiesPreset = $deputies ? ' checked="checked"' : '';

        $settings_box .= '    <label>';
        $settings_box .=          dgettext('examcalendar', 'Semester') . ':';
        $settings_box .=          SemesterData::getInstance()->GetSemesterSelector(array('onchange' => 'this.form.submit();'), $sem_select, 'semester_id', false);
        $settings_box .= '    </label>';
        $settings_box .= '    <br />';
        $settings_box .= '    <br />';

        $settings_box .= '    <label>';
        $settings_box .= '        <input class="checkbox" type="checkbox" name="only_own"' . $onlyOwnPreset . ' onchange="this.form.submit();" />';
        $settings_box .=          dgettext('examcalendar', 'nur eigene Veranstaltungen');
        $settings_box .= '    </label>';
        if ($dozentPerms) {
            $settings_box .= '    <br />';

            $settings_box .= '    <label>';
            $settings_box .= '        <input class="checkbox" type="checkbox" name="deputies"' . $deputiesPreset . ' onchange="this.form.submit();" />';
            $settings_box .=          dgettext('examcalendar', 'Dozierendenvertretung');
            $settings_box .= '    </label>';
        }
        $settings_box .= '    <br />';
        $settings_box .= '    <br />';

        $settings_box .= '    <label>';
        $settings_box .=          dgettext('examcalendar', 'Anzeigeformat') . ':';
        $settings_box .= '        <select name="format" size="1" onchange="this.form.submit();">';
        foreach ($display_formats as $id => $name) {
            $settings_box .= '            <option value="' . $id .'"' . ($format == $id ? ' selected="selected"' : '') . '>' . $name . '</option>';
        }
        $settings_box .= '        </select>';
        $settings_box .= '    </label>';

        $settings_box .= '    <noscript>';
        $settings_box .= '        <br />';
        $settings_box .= '        <br />';
        $settings_box .=          Studip\Button::create(dgettext('examcalendar', 'aktualisieren'));
        $settings_box .= '    </noscript>';

        $settings_box .= '</form>';

        // Export
        $export_box  = '<form action="' . $url . '" method="post">';

        $export_formats = ExamUtil::get_export_formats();

        $export_box .= '    <input type="hidden" name="sem_select" value="' . $sem_select . '" />';
        $export_box .= '    <input type="hidden" name="only_own" value="' . $only_own . '" />';
        $export_box .= '    <input type="hidden" name="deputies" value="' . $deputies . '" />';

        $export_box .= '    <label>';
        $export_box .=          dgettext('examcalendar', 'Ausgabeformat') . ':';
        $export_box .= '        <select name="format" size="1">';
        foreach ($export_formats as $id => $name) {
            $export_box .= '            <option value="' . $id .'">' . $name . '</option>';
        }
        $export_box .= '        </select>';
        $export_box .= '    </label>';
        $export_box .= '    <br />';

        $export_box .=      Studip\Button::create(dgettext('examcalendar', 'exportieren'));

        $export_box .= '</form>';

        // Zusammensetzen des Inhalts
        if (version_compare($GLOBALS['SOFTWARE_VERSION'], "3.1") >= 0) {
            $sidebar = Sidebar::get();

            $params = array(
                'sem_select' => $sem_select,
                'only_own' => $only_own,
                'deputies' => $deputies,
                'format' => $format
            );

//             $settings_widget = new SidebarWidget();
//             $settings_widget->setTitle(dgettext('examcalendar', 'Einstellungen') . ':');
//             $settings_widget->addElement(new WidgetElement($settings_box));
//             $sidebar->addWidget($settings_widget, 'settings');

            // Semester-Auswahl
            $semester_widget = new SelectWidget(dgettext('examcalendar', 'Semester') . ':', $controller->url_for('show/output', $options), 'sem_select');
            foreach (array_reverse(Semester::getAll()) as $sem) {
                $semester_widget->addElement(new SelectElement($sem->id, $sem->name, $sem_select == $sem));
            }
            $sidebar->addWidget($semester_widget);

            // Einstellungen (Checkboxen)
            $options_widget = new OptionsWidget();
            $options_widget->addCheckbox(dgettext('examcalendar', 'nur eigene Veranstaltungen'),
                                         $only_own,
                                         $controller->url_for('show/output', array_merge($params, array('only_own' => 1))),
                                         $controller->url_for('show/output', array_merge($params, array('only_own' => 0)))
                                        );
            if ($dozentPerms) {
                $options_widget->addCheckbox(dgettext('examcalendar', 'Dozierendenvertretung'),
                                             $deputies,
                                             $controller->url_for('show/output', array_merge($params, array('deputies' => 1))),
                                             $controller->url_for('show/output', array_merge($params, array('deputies' => 0)))
                                            );
            }
            $sidebar->addWidget($options_widget);

            // Ansichten-Auswahl
            $views_widget = new ViewsWidget();
            foreach ($display_formats as $id => $name) {
                $views_widget->addLink($name, $controller->url_for('show/output', array_merge($params, array('format' => $id))))->setActive($format == $id);
            }
            $sidebar->addWidget($views_widget);

            if (!empty($faculties)) {
                // zeige Export nur an, wenn Prüfungen gefunden wurden (also sind Fakultäten in der Legende)
                $export_widget = new SidebarWidget();
                $export_widget->setTitle(dgettext('examcalendar', 'Exportieren') . ':');
                $export_widget->addElement(new WidgetElement($export_box));
                $sidebar->addWidget($export_widget, 'export');

                $faculties_widget = new SidebarWidget();
                $faculties_widget->setTitle(dgettext('examcalendar', 'Fakultäten') . ':');
                $faculties_widget->addElement(new WidgetElement($faculty_box));
                $sidebar->addWidget($faculties_widget, 'faculties');
            }
        } else {
            $infobox_content = array(
                array ('kategorie' => dgettext('examcalendar', 'Einstellungen') . ':',
                       'eintrag'   => array (
                           array ('text' => $settings_box . '<br /><br />')
                      )
                ),
            );

            if (!empty($faculties)) {
                // zeige Export nur an, wenn Prüfungen gefunden wurden (also sind Fakultäten in der Legende)
                $infobox_content[] =
                array ('kategorie' => dgettext('examcalendar', 'Exportieren') . ':',
                       'eintrag'   => array (
                           array ('text' => $export_box . '<br /><br />')
                      )
                );

                $infobox_content[] =
                array ('kategorie' => dgettext('examcalendar', 'Fakultäten') . ':',
                       'eintrag'   => array (
                           array ('text' => $faculty_box)
                      )
                );
            }

            // fertige Infobox
            $infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
        }
    }

}
