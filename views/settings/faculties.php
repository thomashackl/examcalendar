<?php if ($update_success): ?>
    <?= MessageBox::success(dgettext('examcalendar', 'Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form id="settings" method="post" class="studip_form">
    <table class="default">
        <tr>
            <th>
                <?= dgettext('examcalendar', 'Fakult�t') ?>
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
    <button type="reset" class="cancel button" name="resetButton" id="resetButton"><?= dgettext('examcalendar', 'zur�cksetzen') ?></button>
</form>

<?php
ExamUtil::create_settings_sidebar($controller, 'faculties');

$helpbar = Helpbar::Get();
$helpbar->addPlainText('', array(
    dgettext('examcalendar', 'Die Farben, die Sie den Fakult�ten zuordnen, werden in den Listen- und Kalenderausgaben verwendet, um darzustellen, welche Pr�fungen zu welchen Fakult�ten geh�ren.'),
    dgettext('examcalendar', 'Jede Ausgabe enth�lt eine Legende, die die Farbzuordnung der Fakult�ten enth�lt.'),
    dgettext('examcalendar', 'Farbwerte werden Hexadezimal angegeben, ohne das f�hrende Raute-Zeichen.'),
    dgettext('examcalendar', 'Wenn JavaScript aktiviert ist, steht ein Farbauswahl-Popup zur Verf�gung.')
));
