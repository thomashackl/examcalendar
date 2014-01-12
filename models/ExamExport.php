<?php

class ExamExport {

    public static function exportPDFlist($semester, $selected, $exams, $faculties) {
        $export = new exportDoc();
        $export->setTitle(_('Prüfungskalender'));
        $export->setSubtitle($semester);
        $export->setFilename('Pruefungskalender_' . $semester);

        // TODO Fakultäten
        // Tabelle: Farbe | Fakultät

        // Prüfungstabelle
        $ex_table = $export->add('table');

        $ex_table_header = array();
        // TODO Farbe
        $ex_table_header[] = _('Datum');
        $ex_table_header[] = _('Veranstaltung');
        $ex_table_header[] = _('Art');
        $ex_table_header[] = _('Raum');
        $ex_table->header = $ex_table_header;

        $ex_table_content = array();
        foreach ($exams as $exam) {
            $ex_table_row = array();
            // TODO Farbe
            $ex_table_row[] = ExamUtil::nice_date($exam['begin']) . ",\n" . ExamUtil::nice_time($exam['begin']) . " - " . ExamUtil::nice_time($exam['end']);
            $ex_table_row[] = $exam['num'] . " " . $exam['title'];
            $ex_table_row[] = $GLOBALS['TERMIN_TYP'][$exam['type']]['name'];
            $ex_table_row[] = empty($exam['alt_room']) ? $exam['room'] : $exam['alt_room'];

            $ex_table_content[] = $ex_table_row;
        }
        $ex_table->content = $ex_table_content;

        if ($selected < 2) $ex_table->blacklist[] = 'Art';

        // Dokument ausgeben
        $export->export('PDF');
    }

}