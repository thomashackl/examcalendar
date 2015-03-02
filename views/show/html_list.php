<table class="default">
    <caption>
        <?= sprintf(dgettext('examcalendar', 'Prüfungskalender für das %s'), htmlReady($semester)) ?>
    </caption>
    <thead>
        <tr>
            <th>

            </th>
            <th>
                <?= dgettext('examcalendar', 'Datum') ?>
            </th>
            <th>
                <?= dgettext('examcalendar', 'Veranstaltung') ?>
            </th>
            <?php if ($selected > 1): ?>
                <th>
                    <?= dgettext('examcalendar', 'Art') ?>
                </th>
            <?php endif ?>
            <th>
                <?= dgettext('examcalendar', 'Raum') ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td class="colorbox_exams" style="background: #<?= $faculties[$exam['fac_id']]['color'] ?>">

                </td>
                <td>
                    <?= ExamUtil::nice_date($exam['begin']) ?><br />
                    <?= ExamUtil::nice_time($exam['begin']) ?> - <?= ExamUtil::nice_time($exam['end']) ?>
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
                    <?= htmlReady(empty($exam['alt_room']) ? $exam['room'] : $exam['alt_room']) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php
ExamUtil::create_infobox($controller, $sem_select, $only_own, $deputies, $sem_tree, $format, $faculties);
