<?php

class ExamDB {

    private $selected = 0;
    private $exams = array();
    private $ordering_done = false;
    private $ordered = array();
    private $faculties_done = false;
    private $faculties = array();

    public function querySQL($semester_id, $onlyOwn = false, $deputies = false, $previous = false, $faculty = 'all') {
        $this->ordering_done = false;
        $this->faculties_done = false;

        $db = DBManager::get();

        // alle Termin-Typen, die im Prüfungskalender ausgewertet werden sollen
        $select = "SELECT value";
        $from  = " FROM exam_calendar_settings";
        $where = " WHERE setting = 'exam_types'";

        $preparation = $db->prepare($select . $from . $where, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute();

        $result = $preparation->fetchAll();
        $exam_types = Settings::bin_decode($result[0]['value']);

        $this->selected = count($exam_types);

        // Prüfungen abfragen, die den gewählten Einstellungen entsprechen
        $select = "SELECT DISTINCT i.fakultaets_id AS fac_id,
                                   s.Seminar_id AS sem_id,
                                   s.VeranstaltungsNummer AS num,
                                   s.Name AS title,
                                   t.date AS begin,
                                   t.end_time AS end,
                                   ro.name AS room,
                                   t.raum AS alt_room,
                                   t.date_typ AS type";

        $from = " FROM termine t
                  JOIN seminare s ON (t.range_id = s.Seminar_id)
                  JOIN semester_data sd ON (s.start_time BETWEEN sd.beginn AND sd.ende)
                  JOIN Institute i ON (s.Institut_id = i.Institut_id)
                  LEFT JOIN resources_assign ra ON (t.termin_id = ra.assign_user_id)
                  LEFT JOIN resources_objects ro ON (ra.resource_id = ro.resource_id)";

        $where = " WHERE t.date_typ IN ('" . implode("', '", $exam_types) . "')
                     AND sd.semester_id = :semester_id";

        $order = " ORDER BY begin ASC, end ASC, num ASC, room ASC";

        $inputs = array('semester_id' => $semester_id);

        if ($faculty != 'all') {
            $where .= " AND i.fakultaets_id = :faculty";

            $inputs['faculty'] = $faculty;
        }

        if ($onlyOwn) {
            $from .= " JOIN seminar_user su ON su.Seminar_id = s.Seminar_id";
            $where .= " AND su.user_id = :user_id";

            $inputs['user_id'] = $GLOBALS['user']->id;
        }

        if (Config::get()->DEPUTIES_ENABLE && $deputies) {
            $from .= " JOIN deputies d ON d.range_id = s.Seminar_id";
            $where .= " AND d.user_id = :user_id";

            $inputs['user_id'] = $GLOBALS['user']->id;
        }

        if (!$previous) {
            $where .= " AND t.end_time > :now";

            $inputs['now'] = time();
        }

        $preparation = $db->prepare($select . $from . $where . $order, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute($inputs);

        $resultBySeminarDate = $preparation->fetchAll();

        // Prüfung, ob Termine von Veranstaltungen anderer Semester in das jeweilige Semester fallen

        $from = " FROM termine t
                  JOIN seminare s ON (t.range_id = s.Seminar_id)
                  JOIN semester_data sd ON (t.date BETWEEN sd.beginn AND sd.ende)
                  JOIN Institute i ON (s.Institut_id = i.Institut_id)
                  LEFT JOIN resources_assign ra ON (t.termin_id = ra.assign_user_id)
                  LEFT JOIN resources_objects ro ON (ra.resource_id = ro.resource_id)";

        $where = " WHERE t.date_typ IN ('" . implode("', '", $exam_types) . "')
                     AND sd.semester_id = :semester_id";

        $order = " ORDER BY begin ASC, end ASC, num ASC, room ASC";

        $inputs = array('semester_id' => $semester_id);

        if ($faculty != 'all') {
            $where .= " AND i.fakultaets_id = :faculty";

            $inputs['faculty'] = $faculty;
        }

        if ($onlyOwn) {
            $from .= " JOIN seminar_user su ON su.Seminar_id = s.Seminar_id";
            $where .= " AND su.user_id = :user_id";

            $inputs['user_id'] = $GLOBALS['user']->id;
        }

        if (Config::get()->DEPUTIES_ENABLE && $deputies) {
            $from .= " JOIN deputies d ON d.range_id = s.Seminar_id";
            $where .= " AND d.user_id = :user_id";

            $inputs['user_id'] = $GLOBALS['user']->id;
        }

        if (!$previous) {
            $where .= " AND t.end_time > :now";

            $inputs['now'] = time();
        }

        $preparation = $db->prepare($select . $from . $where . $order, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $preparation->execute($inputs);

        $resultByExamDate = $preparation->fetchAll();

        $result = array_map("unserialize", array_unique(array_map("serialize", array_merge($resultBySeminarDate, $resultByExamDate)))) ? : array();
        usort($result, function($a, $b){
            return $a['begin'] <=> $b['begin'];
        });

        $this->exams = $result ? : array();
    }

    public function getSelectedNum() {
        return $this->selected;
    }

    public function getExams() {
        return $this->exams;
    }

    public function getOrderedExams() {
        if (!$this->ordering_done) {
            if (!empty($this->exams)) {
                // Prüfungen nach Jahr, Monat und Tag sortieren
                foreach ($this->exams as $exam) {
                    $year  = date("Y", $exam['begin']);
                    $month = date("n", $exam['begin']);
                    $day   = date("j", $exam['begin']);

                    $this->ordered[$year][$month][$day][] = $exam;
                }
            }

            $this->ordering_done = true;
        }

        return $this->ordered;
    }

    public function getFaculties() {
        if (!$this->faculties_done) {
            // welche Fakultäten kommen vor?
            if (!empty($this->exams)) {
                $db = DBManager::get();

                foreach ($this->exams as $exam) {
                    $faculties[] = $exam['fac_id'];
                }
                $faculties = array_unique($faculties);

                $select = "SELECT i.Institut_id as fac_id, i.Name as faculty, c.color";
                $from  = " FROM exam_calendar_faculty_colors as c
                           RIGHT JOIN Institute i ON (c.fakultaets_id = i.Institut_id)";
                $where = " WHERE Institut_id IN ('" . implode("', '", $faculties) . "')";
                $order = " ORDER BY faculty ASC";

                $preparation = $db->prepare($select . $from . $where . $order, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $preparation->execute();

                $result = $preparation->fetchAll();

                foreach ($result as $r) {
                    $this->faculties[$r['fac_id']] = array('faculty' => $r['faculty'], 'color' => empty($r['color']) ? '000000' : $r['color']);
                }
            }

            $this->faculties_done = true;
        }

        return $this->faculties;
    }

}
