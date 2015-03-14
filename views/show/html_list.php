<?php PageLayout::setTitle(sprintf(dgettext('examcalendar', 'Prüfungskalender für das %s'), htmlReady($semester))) ?>

<table class="default">
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
                    <a href="<?= URLHelper::getLink('dispatch.php/course/' . ($only_own ? 'overview?cid' : 'details?sem_id') . '=' . $exam['sem_id']) ?>"><?= htmlReady($exam['num']) ?> <?= htmlReady($exam['title']) ?></a>
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
ExamUtil::create_show_sidebar($controller, $sem_select, $only_own, $deputies, $previous, $sem_tree, $sem_tree_data, $format, $faculties);
