<?php
Navigation::activateItem('/calendar/pruefungskalender');

require_once('lib/dates.inc.php');

function nice_date($timestamp) {
    return getWeekday(date("w", $timestamp)) . "., " . date("d.m.Y", $timestamp);
}

function nice_time($timestamp) {
    return date("H:i", $timestamp);
}
?>

<div>
    <table class="default">
        <caption>
            <?= sprintf(_('Prüfungskalender für das %s'), htmlReady($semester)) ?>
        </caption>
        <thead>
            <tr>
                <th>

                </th>
                <th>
                    <?= _('Datum') ?>
                </th>
                <th>
                    <?= _('Veranstaltung') ?>
                </th>
                <?php if ($selected > 1): ?>
                    <th>
                        <?= _('Art') ?>
                    </th>
                <?php endif ?>
                <th>
                    <?= _('Raum') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exams as $exam): ?>
                <tr>
                    <td class="colorbox_exams" style="background: #<?= $faculties[$exam['fac_id']]['color'] ?>">

                    </td>
                    <td>
                        <?= nice_date($exam['begin']) ?><br />
                        <?= nice_time($exam['begin']) ?> - <?= nice_time($exam['end']) ?>
                    </td>
                    <td>
                        <?= htmlReady($exam['num']) ?> <?= htmlReady($exam['title']) ?>
                    </td>
                    <?php if ($selected > 1): ?>
                        <td>
                            <?= htmlReady($GLOBALS['TERMIN_TYP'][$exam['type']]['name']) ?>
                        </td>
                    <?php endif ?>
                    <td>
                        <?= empty($exam['alt_room']) ? htmlReady($exam['room']) : htmlReady($exam['alt_room']) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php
// Fakultäten-Legende für Infobox
$faculty_box = '<table>';

foreach($faculties as $f) {
    $faculty_box .= '        <tr>';
    $faculty_box .= '            <td class="colorbox_info" style="background: #' . $f['color']. '">';
    $faculty_box .= '            </td>';
    $faculty_box .= '            <td>';
    $faculty_box .=                  htmlReady($f['faculty']);
    $faculty_box .= '            </td>';
    $faculty_box .= '        </tr>';
}

$faculty_box .= '</table>';

$infobox_content = array(
    array ('kategorie' => 'Fakultäten:',
           'eintrag'   => array (
               array ('text' => $faculty_box)
          )
    )
);
$infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
