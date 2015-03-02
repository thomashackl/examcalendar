<?php
// Update-Action leitet hierher um, daher funktioniert AutoNavigation nicht
Navigation::activateItem('/calendar/examcalendar/settings');
?>

<?php if ($update_success): ?>
    <?= MessageBox::success(dgettext('examcalendar', 'Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form id="settings" action="<?= $controller->url_for('show/update') ?>" method="post" class="studip_form">
    <h2>
        <?= dgettext('examcalendar', 'Globale Einstellungen für den Prüfungskalender') ?>
    </h2>

    <fieldset>
        <legend>
            <?= dgettext('examcalendar', 'Folgende Termin-Typen sollen im Prüfungskalender dargestellt werden:') ?>
        </legend>
        <?php for ($i = 1; $i <= count($GLOBALS['TERMIN_TYP']); $i++): ?>
            <label>
                <input class="studip_checkbox checkbox" type="checkbox" name="exam_types[]" value="<?= $i ?>"<?= in_array($i, $exam_types) ? ' checked="checked"' : '' ?>/>
                <?= htmlReady($GLOBALS['TERMIN_TYP'][$i]['name']) ?>
            </label>
        <?php endfor ?>
    </fieldset>

    <fieldset>
        <legend>
            <?= dgettext('examcalendar', 'Ordnen Sie hier den Fakultäten einen Farbwert zu:') ?>
        </legend>

        <?= dgettext('examcalendar', 'Farbwerte werden Hexadezimal angegeben, ohne das führende Raute-Zeichen.') ?><br />
        <?= dgettext('examcalendar', 'Wenn JavaScript aktiviert ist, steht ein Farbauswahl-Popup zur Verfügung.') ?><br />
        <br />

        <table class="default">
            <tr>
                <th>
                    <?= dgettext('examcalendar', 'Fakultät') ?>
                </th>
                <th>
                    <?= dgettext('examcalendar', 'Farbe') ?>
                </th>
            </tr>
            <?php foreach ($faculties as $f): ?>
                <tr>
                    <td>
                        <?= htmlReady($f['faculty']) ?>
                    </td>
                    <td>
                        <input type="hidden" name="fac_id[]" value="<?= $f['fac_id'] ?>"/>
                        <input class="color {pickerPosition:'top'}" type="text" name="color[]" value="<?= empty($f['color']) ? '000000' : $f['color'] ?>" maxlength="6" size="8"/>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </fieldset>

    <?= Studip\Button::createAccept() ?>
    <button type="reset" class="cancel button" name="resetButton" id="resetButton"><?= dgettext('examcalendar', 'zurücksetzen') ?></button>
</form>

<?php
$helpbar = Helpbar::Get();
$helpbar->addPlainText('', array(
    dgettext('examcalendar', 'Wählen Sie die Termin-Typen aus, die bei der Erstellung eines Prüfungskalenders berücksichtigt werden sollen.'),
    dgettext('examcalendar', 'Die Farben, die Sie den Fakultäten zuordnen, werden in den Listen- und Kalenderausgaben verwendet, um darzustellen, welche Prüfungen zu welchen Fakultäten gehören.'),
    dgettext('examcalendar', 'Jede Ausgabe enthält eine Legende, die die Farbzuordnung der Fakultäten enthält.')
));
