<?php

class Settings {

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
        for ($i = 1; $i <= count($GLOBALS['TERMIN_TYP']); $i++) {
            if ($input & (1 << ($i - 1))) {
                $result[] = $i;
            }
        }

        return $result;
    }

    public static function getExamTypes() {
        $db = DBManager::get();

        // Termintypen für den Prüfungskalender abrufen
        $select = "SELECT value";
        $from  = " FROM exam_calendar_settings";
        $where = " WHERE setting = 'exam_types'";

        $preparation = $db->prepare($select . $from . $where, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();

        $result = $preparation->fetchAll();
        return self::bin_decode($result[0]['value']);
    }

    public static function getFaculties() {
        $db = DBManager::get();

        // Fakultäten und eingetragene Farben abrufen
        $select = "SELECT DISTINCT f.Institut_id as fac_id, f.Name as faculty, c.color";
        $from  = " FROM Institute i
                   JOIN Institute f ON (i.fakultaets_id = f.Institut_id)
                   LEFT JOIN exam_calendar_faculty_colors c ON (f.Institut_id = c.fakultaets_id)";
        $order = " ORDER BY faculty ASC";

        $preparation = $db->prepare($select . $from . $order, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();

        return $preparation->fetchAll();
    }

    public function updateExamTypes($exam_types) {
        $db = DBManager::get();

        // Termintypen für den Prüfungskalender updaten
        $exam_values = self::bin_encode($exam_types);

        $update = "UPDATE exam_calendar_settings";
        $set   = " SET value = '" . $exam_values . "'";
        $where = " WHERE setting = 'exam_types'";

        $preparation = $db->prepare($update . $set . $where, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();
    }

    public static function updateFaculties($faculties) {
        $db = DBManager::get();

        // Fakultäten und eingetragene Farben updaten
        foreach ($faculties as $fac_id => $color) {
            $insert  = "REPLACE INTO exam_calendar_faculty_colors (fakultaets_id, color)";
            $values = " VALUES (:id, '" . $color . "')";

            $preparation = $db->prepare($insert . $values, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $preparation->execute(array('id' => $fac_id));
        }
    }

}
