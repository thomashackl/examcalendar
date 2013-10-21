<?php
Navigation::activateItem('/calendar/pruefungskalender/index');

$semesterData = SemesterData::getInstance();
$currentSemester = $semesterData->getCurrentSemesterData();

$onlyOwnPreset = $GLOBALS['perm']->have_perm('admin') ? '' : ' checked="checked"';
$dozentPerms = $GLOBALS['perm']->have_perm('dozent');
?>

<?php if ($format_error): ?>
    <?= MessageBox::error(_('Das ausgewählte Ausgabeformat ist ungültig.')) ?>
<?php endif ?>

<?php if ($no_results): ?>
    <?= MessageBox::info(sprintf(_('Es wurden keine Prüfungen im %s gefunden, die den ausgewählten Kriterien entsprechen!'), htmlReady($semester))) ?>
<?php endif ?>

<form action="<?= $controller->url_for('show/output') ?>" method="post">
    <h2>
        <?= _('Konfigurieren Sie hier Ihren Prüfungskalender') ?>
    </h2>

    <div>
        <?= _('Semester wählen:') ?><br />
        <?= $semesterData->GetSemesterSelector(null, $currentSemester['semester_id'], 'semester_id', false) ?>
    </div>
    <br />

    <div>
        <input class="checkbox" type="checkbox" name="only_own"<?= $onlyOwnPreset ?>/>
        <?= _('nur meine eigenen Veranstaltungen') ?>
        <?php if ($dozentPerms): ?>
            <br />

            <input class="checkbox" type="checkbox" name="deputies"/>
            <?= _('Veranstaltungen, in denen ich Dozierendenvertretung bin') ?>
        <?php endif ?>
    </div>
    <br />

    <div>
        <?= _('auf Fakultät oder Studiengang eingrenzen:') ?><br />
        <select name="sem_tree" size="10">
            <option value="all" selected="selected">-- <?= _("alle") ?> --</option>
            <?php foreach ($entries as $id => $name): ?>
                <option value="<?= $id; ?>"><?= htmlReady($name) ?></option>
                <?php if ($children[$id]): ?>
                    <?php foreach ($children[$id] as $cid => $cname): ?>
                        <option value="<?= $cid ?>">&nbsp;&nbsp;&nbsp;<?= htmlReady($cname) ?></option>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </div>
    <br />

    <div>
        <?= _('Ausgabeformat wählen:') ?><br />
        <select name="format" size="1">
            <option value="html_list">HTML (<?= _('Liste') ?>)</option>
            <option value="html_calendar">HTML (<?= _('Kalender') ?>)</option>
            <!-- <option value="pdf_list">PDF (<?= _('Liste') ?>)</option> -->
            <!-- <option value="pdf_calendar">PDF (<?= _('Kalender') ?>)</option> -->
            <option value="ical">iCal</option>
        </select>
    </div>
    <br />

    <input class="button" type="image" name="submit" <?= makeButton('ausgeben', 'src') ?>/>
</form>

<?php
$infobox_content = array(
    array ('kategorie' => 'Information:',
           'eintrag'   => array (
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Hier können Sie sich einen Prüfungskalender für ein bestimmtes Semester generieren lassen.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Sie können entscheiden, ob Sie alle Prüfungen des gewählten Semesters angezeigt bekommen möchten, oder nur Prüfungen in Veranstaltungen, in denen Sie angemeldet sind.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Sie haben die Möglichkeit, nur Prüfungen einer bestimmten Fakultät oder eines einzelnen Studiengangs anzeigen zu lassen.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Die Ausgabe kann in verschiedenen Formaten erfolgen, z.B. als Liste oder Kalender, jeweils in HTML oder als PDF.')
                )
          )
    )
);
$infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
