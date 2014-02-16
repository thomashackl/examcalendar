<?php
require_once($GLOBALS['RELATIVE_PATH_CALENDAR'] . '/lib/CalendarEvent.class.php');
require_once($GLOBALS['RELATIVE_PATH_CALENDAR'] . '/lib/sync/CalendarWriterICalendar.class.php');

$writer = new CalendarWriteriCalendar();

$ical = '';
$now = time();
foreach ($exams as $exam) {
    $title = $exam['title'];

    // Veranstaltungsnummer hinzufügen, falls vorhanden
    if ($exam['num']) {
        $title = $exam['num'] . ' ' . $title;
    }

    // Termin-Typ hinzufügen, falls mehr als 1 Typ eine Prüfung darstellt
    if ($selected > 1) {
        $title = $GLOBALS['TERMIN_TYP'][$exam['type']]['name'] . ': ' . $title;
    }

    $properties = array(
            'SUMMARY'       => $title,
            // freie Raumangabe falls vorhanden, ansonsten eventuell gebuchter Raum
            'LOCATION'      => empty($exam['alt_room']) ? $exam['room'] : $exam['alt_room'],
            'CREATED'       => $now,
            'LAST-MODIFIED' => $now,
            'DTSTART'       => $exam['begin'],
            'DTEND'         => $exam['end']
    );

    $event = new CalendarEvent($properties);

    $ical .= $writer->write($event);
}

$ical = $writer->writeHeader() . $ical . $writer->writeFooter();

// eigentliche Ausgabe als Download
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename=pruefungskalender.ics');
header('Content-Type: text/calendar');
header('Content-Length: ' . strlen($ical));
echo $ical;
