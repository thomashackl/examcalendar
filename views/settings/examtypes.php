<?php if ($update_success): ?>
    <?= MessageBox::success(dgettext('examcalendar', 'Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form id="settings" method="post" class="studip_form">
    <fieldset>
        <legend>
            <?= dgettext('examcalendar', 'Anzuzeigende Termintypen') ?>
        </legend>
        <?php for ($i = 1; $i <= count($GLOBALS['TERMIN_TYP']); $i++): ?>
            <label>
                <input class="checkbox" type="checkbox" name="exam_types[]" value="<?= $i ?>"<?= in_array($i, $exam_types) ? ' checked="checked"' : '' ?>/>
                <?= htmlReady($GLOBALS['TERMIN_TYP'][$i]['name']) ?>
            </label>
        <?php endfor ?>
    </fieldset>

    <?= Studip\Button::createAccept(dgettext('examcalendar', 'speichern'), 'save') ?>
</form>

<?php
ExamUtil::create_settings_sidebar($controller, 'examtypes');

$helpbar = Helpbar::Get();
$helpbar->addPlainText('', dgettext('examcalendar', 'Wählen Sie hier die Termintypen aus, die bei der Erstellung eines Prüfungskalenders berücksichtigt werden sollen.'));
