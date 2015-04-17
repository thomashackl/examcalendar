<?php if ($format_error): ?>
    <?= MessageBox::error(dgettext('examcalendar', 'Das ausgew�hlte Ausgabeformat ist ung�ltig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(dgettext('examcalendar', 'Es wurden keine Pr�fungen im %s gefunden, die den ausgew�hlten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
ExamUtil::create_show_sidebar($controller, $sem_select, $only_own, $deputies, $previous, $filter, $filters, $format, $faculties);
