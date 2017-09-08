<?php

class ExamExport {

    public static function exportPDFlist($semester, $selected, $exams, $faculties) {
        $export = new ExportDoc();
        $export->setTitle(dgettext('examcalendar', 'Prüfungskalender'));
        $export->setSubtitle($semester);
        $export->setFilename(dgettext('examcalendar', 'Prüfungskalender') . ' ' . $semester);

        // TODO Fakultäten
        // Tabelle: Farbe | Fakultät

        // Prüfungstabelle
        $ex_table = $export->add('table');

        $ex_table_header = array();
        // TODO Farbe
        $ex_table_header[] = dgettext('examcalendar', 'Datum');
        $ex_table_header[] = dgettext('examcalendar', 'Veranstaltung');
        if ($selected > 1) $ex_table_header[] = dgettext('examcalendar', 'Art'); // TODO statt IF Blacklist implementieren
        $ex_table_header[] = dgettext('examcalendar', 'Raum');
        $ex_table->header = $ex_table_header;

        $ex_table_content = array();
        foreach ($exams as $exam) {
            $ex_table_row = array();
            // TODO Farbe
            $ex_table_row[] = ExamUtil::nice_date($exam['begin']) . ",\n" . ExamUtil::nice_time($exam['begin']) . " - " . ExamUtil::nice_time($exam['end']);
            $ex_table_row[] = $exam['num'] . " " . $exam['title'];
            if ($selected > 1) $ex_table_row[] = $GLOBALS['TERMIN_TYP'][$exam['type']]['name']; // TODO statt IF Blacklist implementieren
            $ex_table_row[] = empty($exam['alt_room']) ? $exam['room'] : $exam['alt_room'];

            $ex_table_content[] = $ex_table_row;
        }
        $ex_table->content = $ex_table_content;

        if ($selected < 2) $ex_table->blacklist[] = dgettext('examcalendar', 'Art');

        // Dokument ausgeben
        $export->export('PDF');
    }

}
