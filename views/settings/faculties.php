<?php if ($update_success): ?>
    <?= MessageBox::success(dgettext('examcalendar', 'Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form id="settings" method="post" class="studip_form">
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
                    <input class="color {pickerPosition:'top'}" type="text" name="color[]" value="<?= empty($f['color']) ? '000000' : $f['color'] ?>" maxlength="6"/>
                </td>
            </tr>
        <?php endforeach ?>
    </table>

    <?= Studip\Button::createAccept(dgettext('examcalendar', 'speichern'), 'save') ?>
    <button type="reset" class="cancel button" name="resetButton" id="resetButton"><?= dgettext('examcalendar', 'zurücksetzen') ?></button>
</form>

<?php
ExamUtil::create_settings_sidebar($controller, 'faculties');

$helpbar = Helpbar::Get();
$helpbar->addPlainText('', array(
    dgettext('examcalendar', 'Die Farben, die Sie den Fakultäten zuordnen, werden in den Listen- und Kalenderausgaben verwendet, um darzustellen, welche Prüfungen zu welchen Fakultäten gehören.'),
    dgettext('examcalendar', 'Jede Ausgabe enthält eine Legende, die die Farbzuordnung der Fakultäten enthält.'),
    dgettext('examcalendar', 'Farbwerte werden Hexadezimal angegeben, ohne das führende Raute-Zeichen.'),
    dgettext('examcalendar', 'Wenn JavaScript aktiviert ist, steht ein Farbauswahl-Popup zur Verfügung.')
));
