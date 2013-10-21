<?php
Navigation::activateItem('/calendar/pruefungskalender');

include_once('lib/dates.inc.php');

function getMonth($month) {
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

function nice_time($timestamp) {
    return date("H:i", $timestamp);
}
?>

<h2>
    <?= sprintf(_('Prüfungskalender für das %s'), htmlReady($semester)) ?>
</h2>

<?php foreach ($exams as $year => $exams): ?>
    <?php foreach ($exams as $month => $exams): ?>
        <div class="spaced">
            <table class="calendar">
                <caption>
                    <?= getMonth($month) ?> <?= $year ?>
                </caption>
                <thead>
                    <tr>
                        <th class="week">
                            <?= _('KW') ?>
                        </th>
                        <th>
                            <?= _('Mo') ?>
                        </th>
                        <th>
                            <?= _('Di') ?>
                        </th>
                        <th>
                            <?= _('Mi') ?>
                        </th>
                        <th>
                            <?= _('Do') ?>
                        </th>
                        <th>
                            <?= _('Fr') ?>
                        </th>
                        <th>
                            <?= _('Sa') ?>
                        </th>
                        <th>
                            <?= _('So') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        $ts = mktime(0, 0, 0, $month, 1, $year);
                        $kw = date('W', $ts);
                        ?>
                        <td class="week">
                            <?= $kw ?>
                        </td>
                        <?php
                        $days_in_month = date('t', $ts);
                        for ($day = 1; $day <= $days_in_month; $day++) {
                            $ts = mktime(0, 0, 0, $month, $day, $year);

                            $kw2 = date('W', $ts);
                            if ($kw != $kw2) {
                                $kw = $kw2;
                        ?>
                                </tr>
                                <tr>
                                    <td class="week">
                                        <?= $kw ?>
                                    </td>
                        <?php
                            }
                        ?>
                        <?php
                            // leere Tage am Monatsanfang
                            if ($day == 1) {
                                $weekday = date('N', $ts);
                                $days_in_last_month = date('t', $ts - (60 * 60 * 24));
                                for ($i = 1; $i < $weekday; $i++) {
                        ?>
                                    <td class="other_month">
                                        <div class="day">
                                            <?= $days_in_last_month - $weekday + $i + 1 ?>
                                        </div>
                                    </td>
                        <?php
                                }
                            }
                        ?>
                        <td>
                            <div class="day">
                                <?= $day ?>
                            </div>

                            <?php
                            $i = 1;
                            foreach ($exams[$day] as $exam) {
                            ?>
                                <div class="exam">
                                    <div class="<?= $i == 1 ? 'first-': '' ?>time">
                                        <?= nice_time($exam['begin']) ?> - <?= nice_time($exam['end']) ?>
                                    </div>

                                    <div class="num">
                                        <div class="colorbox" style="background: #<?= $faculties[$exam['fac_id']]['color'] ?>">&nbsp;</div>
                                        <?= htmlReady($exam['num']) ?>
                                    </div>

                                    <div class="title">
                                        <?= htmlReady($exam['title']) ?>
                                    </div>

                                    <?php if ($selected > 1): ?>
                                        <div class="type">
                                            <?= htmlReady($GLOBALS['TERMIN_TYP'][$exam['type']]['name']) ?>
                                        </div>
                                    <?php endif ?>

                                    <div class="room">
                                        <?= empty($exam['alt_room']) ? htmlReady($exam['room']) : htmlReady($exam['alt_room']) ?>
                                    </div>
                                </div>
                            <?php
                                $i++;
                            }
                            ?>
                        </td>
                        <?php
                            // leere Tage am Monatsende
                            if ($day == $days_in_month) {
                                $j = 1;
                                for ($i = 7; $i > date('N', $ts); $i--) {
                        ?>
                                    <td class="other_month">
                                        <div class="day">
                                            <?= $j ?>
                                        </div>
                                    </td>
                        <?php
                                    $j++;
                                }
                            }
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endforeach ?>
<?php endforeach ?>

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
