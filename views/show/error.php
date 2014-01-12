<?php
$formats = ExamUtil::get_formats();
$onlyOwnPreset = $only_own ? ' checked="checked"' : '';
$dozentPerms = $GLOBALS['perm']->have_perm('dozent');
$deputiesPreset = $deputies ? ' checked="checked"' : '';
?>

<?php if ($format_error): ?>
    <?= MessageBox::error(_('Das ausgewählte Ausgabeformat ist ungültig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(_('Es wurden keine Prüfungen im %s gefunden, die den ausgewählten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<?php
$infobox = ExamUtil::create_infobox($faculties, $controller->url_for('show/output'), $sem_select, $only_own, $deputies, $sem_tree, $format);
