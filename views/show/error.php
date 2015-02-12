<?php if ($format_error): ?>
    <?= MessageBox::error(_('Das ausgewählte Ausgabeformat ist ungültig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(_('Es wurden keine Prüfungen im %s gefunden, die den ausgewählten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
ExamUtil::create_infobox($infobox, $faculties, $controller, $sem_select, $only_own, $deputies, $sem_tree, $format);
