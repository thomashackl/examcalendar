<?php if ($format_error): ?>
    <?= MessageBox::error(_('Das ausgew�hlte Ausgabeformat ist ung�ltig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(_('Es wurden keine Pr�fungen im %s gefunden, die den ausgew�hlten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
ExamUtil::create_infobox($infobox, $faculties, $controller, $sem_select, $only_own, $deputies, $sem_tree, $format);
