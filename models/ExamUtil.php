<?php

require_once('lib/dates.inc.php');

class ExamUtil {

    public static function get_formats() {
        return array(
            'html_list'     => 'HTML (' . _('Liste') . ')',
            'html_calendar' => 'HTML (' . _('Kalender') . ')',
            'pdf_list'      => 'PDF (' . _('Liste') . ')',
//             'pdf_calendar'  => 'PDF (' . _('Kalender') . ')',
            'ical'          => 'iCal'
        );
    }

    public static function getMonth($month) {
        switch($month) {
            case 1:
                return _('Januar');
            case 2:
                return _('Februar');
            case 3:
                return _('März');
            case 4:
                return _('April');
            case 5:
                return _('Mai');
            case 6:
                return _('Juni');
            case 7:
                return _('Juli');
            case 8:
                return _('August');
            case 9:
                return _('September');
            case 10:
                return _('Oktober');
            case 11:
                return _('November');
            case 12:
                return _('Dezember');
            default:
                return _('ungültiger Monat');
        }
    }

    public static function nice_date($timestamp) {
        return getWeekday(date("w", $timestamp)) . "., " . date("d.m.Y", $timestamp);
    }

    public static function nice_time($timestamp) {
        return date("H:i", $timestamp);
    }

    public static function create_infobox($faculties, $url, $sem_select, $only_own, $deputies, $sem_tree, $format) {
        // Fakultäten-Legende für Infobox
        $faculty_box = '<table>';

        foreach($faculties as $f) {
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
        $settings_box = '<form action="' . $url . '" method="post">';
//         $settings_box .= '<input type="hidden" name="sem_tree" value="' . $sem_tree . '" />';

        $settings_box .= '<table>';

        $formats = ExamUtil::get_formats();
        $onlyOwnPreset = $only_own ? ' checked="checked"' : '';
        $dozentPerms = $GLOBALS['perm']->have_perm('dozent');
        $deputiesPreset = $deputies ? ' checked="checked"' : '';

        $settings_box .= '    <tr><td>';
        $settings_box .= '        <label>';
        $settings_box .=              _('Semester') . ':';
        $settings_box .=              SemesterData::getInstance()->GetSemesterSelector(null, $sem_select, 'semester_id', false);
        $settings_box .= '        </label>';
        $settings_box .= '    </td></tr>';

        $settings_box .= '    <tr><td>';
        $settings_box .= '        <label>';
        $settings_box .= '            <input class="checkbox" type="checkbox" name="only_own"' . $onlyOwnPreset . '/>';
        $settings_box .=              _('nur eigene Veranstaltungen');
        $settings_box .= '        </label>';

        if ($dozentPerms) {
            $settings_box .= '        <br />';

            $settings_box .= '        <label>';
            $settings_box .= '            <input class="checkbox" type="checkbox" name="deputies"' . $deputiesPreset . '/>';
            $settings_box .=              _('Dozierendenvertretung');
            $settings_box .= '        </label>';
        }

        $settings_box .= '    </td></tr>';

        $settings_box .= '    <tr><td>';
        $settings_box .= '        <label>';
        $settings_box .=              _('Format') . ':';
        $settings_box .= '            <select name="format" size="1">';
        foreach ($formats as $id => $name) {
            $settings_box .= '                <option value="' . $id .'"' . ($format == $id ? ' selected="selected"' : '') . '>' . $name . '</option>';
        }
        $settings_box .= '            </select>';
        $settings_box .= '        </label>';
        $settings_box .= '    </td></tr>';

        $settings_box .= '    <tr><td>';
        $settings_box .=          Studip\Button::create(_('aktualisieren'));
        $settings_box .= '    </td></tr>';

        $settings_box .= '</table>';
        $settings_box .= '</form>';

        // Zusammensetzen des Inhalts
        $infobox_content = array(
            array ('kategorie' => _('Einstellungen') . ':',
                   'eintrag'   => array (
                       array ('text' => $settings_box)
                  )
            )
        );

        if (!empty($faculties)) {
            $infobox_content[] =
            array ('kategorie' => _('Fakultäten') . ':',
                   'eintrag'   => array (
                       array ('text' => $faculty_box)
                  )
            );
        }

        // fertige Infobox
        $infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild

        return $infobox;
    }

}
