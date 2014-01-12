<?php
// Output-Action kann hierher umleiten
Navigation::getItem('/calendar/pruefungskalender/output')->setActive(false);
Navigation::activateItem('/calendar/pruefungskalender/index');

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

<form action="<?= $controller->url_for('show/output') ?>" method="post">
    <h2>
        <?= _('Konfigurieren Sie hier Ihren Pr�fungskalender') ?>
    </h2>

    <div>
        <?= _('Semester w�hlen:') ?><br />
        <?= SemesterData::getInstance()->GetSemesterSelector(null, $sem_select, 'semester_id', false) ?>
    </div>
    <br />

    <div>
        <label>
            <input class="checkbox" type="checkbox" name="only_own"<?= $onlyOwnPreset ?>/>
            <?= _('nur meine eigenen Veranstaltungen') ?>
        </label>
        <?php if ($dozentPerms): ?>
            <br />

            <label>
                <input class="checkbox" type="checkbox" name="deputies"<?= $deputiesPreset ?>/>
                <?= _('Veranstaltungen, in denen ich Dozierendenvertretung bin') ?>
            </label>
        <?php endif ?>
    </div>
    <br />

    <div>
        <?= _('auf Fakult�t oder Studiengang eingrenzen:') ?><br />
        <select name="sem_tree" size="10">
            <option value="all" selected="selected"<?= $sem_tree == 'all' ? ' selected="selected"' : ''?>>-- <?= _("alle") ?> --</option>
            <?php foreach ($entries as $id => $name): ?>
                <option value="<?= $id; ?>"><?= htmlReady($name) ?></option>
                <?php if ($children[$id]): ?>
                    <?php foreach ($children[$id] as $cid => $cname): ?>
                        <option value="<?= $cid ?>"<?= $sem_tree == $cid ? ' selected="selected"' : ''?>>&nbsp;&nbsp;&nbsp;<?= htmlReady($cname) ?></option>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </div>
    <br />

    <div>
        <?= _('Ausgabeformat w�hlen:') ?><br />
        <select name="format" size="1">
            <?php foreach ($formats as $id => $name): ?>
                <option value="<?= $id ?>"<?= $format == $id ? ' selected="selected"' : ''?>><?= $name ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <br />

    <?= Studip\Button::create(_('ausgeben')) ?>
</form>

<?php
$infobox_content = array(
    array ('kategorie' => 'Information:',
           'eintrag'   => array (
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Hier k�nnen Sie sich einen Pr�fungskalender f�r ein bestimmtes Semester generieren lassen.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Sie k�nnen entscheiden, ob Sie alle Pr�fungen des gew�hlten Semesters angezeigt bekommen m�chten, oder nur Pr�fungen in Veranstaltungen, in denen Sie angemeldet sind.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Sie haben die M�glichkeit, nur Pr�fungen einer bestimmten Fakult�t oder eines einzelnen Studiengangs anzeigen zu lassen.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Die Ausgabe kann in verschiedenen Formaten erfolgen, z.B. als Liste oder Kalender, jeweils in HTML oder als PDF.')
                )
          )
    )
);
$infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
