<?php if ($format_error): ?>
    <?= MessageBox::error(dgettext('examcalendar', 'Das ausgewählte Ausgabeformat ist ungültig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(dgettext('examcalendar', 'Es wurden keine Prüfungen im %s gefunden, die den ausgewählten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
ExamUtil::create_show_sidebar($controller, $sem_select, $only_own, $deputies, $previous, $filter, $filters, $format, $faculties);
