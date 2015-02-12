<?php
// Update-Action leitet hierher um, daher funktioniert AutoNavigation nicht
Navigation::activateItem('/calendar/examcalendar/settings');
?>

<?php if ($update_success): ?>
    <?= MessageBox::success(dgettext('examcalendar', 'Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form id="settings" action="<?= $controller->url_for('show/update') ?>" method="post" class="studip_form">
    <h2>
        <?= dgettext('examcalendar', 'Globale Einstellungen f�r den Pr�fungskalender') ?>
    </h2>

    <fieldset>
        <legend>
            <?= dgettext('examcalendar', 'Folgende Termin-Typen sollen im Pr�fungskalender dargestellt werden:') ?>
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
            <?= dgettext('examcalendar', 'Ordnen Sie hier den Fakult�ten einen Farbwert zu:') ?>
        </legend>

        <?= dgettext('examcalendar', 'Farbwerte werden Hexadezimal angegeben, ohne das f�hrende Raute-Zeichen.') ?><br />
        <?= dgettext('examcalendar', 'Wenn JavaScript aktiviert ist, steht ein Farbauswahl-Popup zur Verf�gung.') ?><br />
        <br />

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
                        <input class="color {pickerPosition:'top'}" type="text" name="color[]" value="<?= empty($f['color']) ? '000000' : $f['color'] ?>" maxlength="6" size="8"/>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </fieldset>

    <?= Studip\Button::createAccept() ?>
    <button type="reset" class="cancel button" name="resetButton" id="resetButton"><?= dgettext('examcalendar', 'zur�cksetzen') ?></button>
</form>

<?php
if (version_compare($GLOBALS['SOFTWARE_VERSION'], "3.1") < 0) {
    $infobox_content = array(
        array ('kategorie' => dgettext('examcalendar', 'Information') . ':',
               'eintrag'   => array (
                    array ('icon' => 'icons/16/black/info.png',
                           'text' => dgettext('examcalendar', 'W�hlen Sie die Termin-Typen aus, die bei der Erstellung eines Pr�fungskalenders ber�cksichtigt werden sollen.')
                    ),
                    array ('icon' => 'icons/16/black/info.png',
                           'text' => dgettext('examcalendar', 'Die Farben, die Sie den Fakult�ten zuordnen, werden in den Listen- und Kalenderausgaben verwendet, um darzustellen, welche Pr�fungen zu welchen Fakult�ten geh�ren.') . '<br />' .
                                     dgettext('examcalendar', 'Jede Ausgabe enth�lt eine Legende, die die Farbzuordnung der Fakult�ten enth�lt.')
                    )
              )
        )
    );
    $infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
}
