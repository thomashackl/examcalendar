<h2>
    <?= sprintf(_('Prüfungskalender für das %s'), htmlReady($semester)) ?>
</h2>

<?php foreach ($exams as $year => $exams): ?>
    <?php foreach ($exams as $month => $exams): ?>
        <table class="calendar">
            <caption>
                <?= ExamUtil::getMonth($month) ?> <?= $year ?>
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
                    $cw = date('W', $ts);
                    ?>
                    <td class="week">
                        <?= $cw ?>
                    </td>
                    <?php
                    $days_in_month = date('t', $ts);
                    for ($day = 1; $day <= $days_in_month; $day++) {
                        $ts = mktime(0, 0, 0, $month, $day, $year);

                        $cw2 = date('W', $ts);
                        if ($cw != $cw2) {
                            $cw = $cw2;
                    ?>
                            </tr>
                            <tr>
                                <td class="week">
                                    <?= $cw ?>
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
                                    <?= ExamUtil::nice_time($exam['begin']) ?> - <?= ExamUtil::nice_time($exam['end']) ?>
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
                                    <?= htmlReady(empty($exam['alt_room']) ? $exam['room'] : $exam['alt_room']) ?>
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
    <?php endforeach ?>
<?php endforeach ?>

<?php
ExamUtil::create_infobox($infobox, $faculties, $controller->url_for('show/output'), $sem_select, $only_own, $deputies, $sem_tree, $format);
