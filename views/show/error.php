<?php
$formats = ExamUtil::get_formats();
$onlyOwnPreset = $only_own ? ' checked="checked"' : '';
$dozentPerms = $GLOBALS['perm']->have_perm('dozent');
$deputiesPreset = $deputies ? ' checked="checked"' : '';
?>

<?php if ($format_error): ?>
    <?= MessageBox::error(_('Das ausgew�hlte Ausgabeformat ist ung�ltig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(_('Es wurden keine Pr�fungen im %s gefunden, die den ausgew�hlten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
$infobox = ExamUtil::create_infobox($faculties, $controller->url_for('show/output'), $sem_select, $only_own, $deputies, $sem_tree, $format);
