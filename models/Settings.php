<?php

class Settings {

    private $faculties;
    private $exam_types;

    public static function bin_encode($input) {
        $result = 0;

        foreach ($input as $i) {
            if ($i > 0) {
                $result |= (1 << ($i - 1));
            }
        }

        return $result;
    }

    public static function bin_decode($input) {
        $result = array();

        for ($i = 1; $i <= count($GLOBALS['TERMIN_TYP']); $i++) {
            if ($input & (1 << ($i - 1))) {
                $result[] = $i;
            }
        }

        return $result;
    }

    public function querySQL() {
        $db = DBManager::get();

        // Fakultäten und eingetragene Farben abrufen
        $select = "SELECT DISTINCT f.Institut_id as fac_id, f.Name as faculty, c.color";
        $from  = " FROM Institute i
                   JOIN Institute f ON (i.fakultaets_id = f.Institut_id)
                   LEFT JOIN exam_calendar_faculty_colors c ON (f.Institut_id = c.fakultaets_id)";
        $order = " ORDER BY faculty ASC";

        $preparation = $db->prepare($select . $from . $order, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();

        $result = $preparation->fetchAll();
        $this->faculties = $result;

        // Termintypen für den Prüfungskalender abrufen
        $select = "SELECT value";
        $from  = " FROM exam_calendar_settings";
        $where = " WHERE setting = 'exam_types'";

        $preparation = $db->prepare($select . $from . $where, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();

        $result = $preparation->fetchAll();
        $this->exam_types = Settings::bin_decode($result[0]['value']);
    }

    public function updateSQL($faculties, $exam_types) {
        $db = DBManager::get();

        // Fakultäten und eingetragene Farben updaten
        foreach ($faculties as $fac_id => $color) {
            $insert  = "INSERT INTO exam_calendar_faculty_colors (fakultaets_id, color)"; // TODO REPLACE INTO
            $values = " VALUES (:id, '" . $color . "')";
            $update = " ON DUPLICATE KEY UPDATE color = '" . $color . "'";

            $preparation = $db->prepare($insert . $values . $update, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $preparation->execute(array('id' => $fac_id));
        }

        // Termintypen für den Prüfungskalender updaten
        $exam_values = Settings::bin_encode($exam_types);

        $update = "UPDATE exam_calendar_settings";
        $set   = " SET value = '" . $exam_values . "'";
        $where = " WHERE setting = 'exam_types'";

        $preparation = $db->prepare($update . $set . $where, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();
    }

    public function getExamTypes() {
        return $this->exam_types;
    }

    public function getFaculties() {
        return $this->faculties;
    }

}
