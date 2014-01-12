<?php
// Update-Action leitet hierher um, daher funktioniert AutoNavigation nicht
Navigation::activateItem('/calendar/pruefungskalender/settings');
?>

<?php if ($update_success): ?>
    <?= MessageBox::success(_('Die Einstellungen wurden gespeichert.')) ?>
<?php endif ?>

<form action="<?= $controller->url_for('show/update') ?>" method="post">
    <h2>
        <?= _('Globale Einstellungen f�r den Pr�fungskalender') ?>
    </h2>

    <div>
        <h4>
            <?= _('Folgende Termin-Typen sollen im Pr�fungskalender dargestellt werden:') ?>
        </h4>
        <?php for ($i = 1; $i <= count($GLOBALS['TERMIN_TYP']); $i++): ?>
            <label>
                <input class="checkbox" type="checkbox" name="exam_types[]" value="<?= $i ?>"<?= in_array($i, $exam_types) ? ' checked="checked"' : '' ?>/>
                <?= htmlReady($GLOBALS['TERMIN_TYP'][$i]['name']) ?>
            </label>
            <br />
        <?php endfor ?>
    </div>
    <br />

    <div>
        <h4>
            <?= _('Ordnen Sie hier den Fakult�ten einen Farbwert zu:') ?>
        </h4>

        <div>
            <?= _('Farbwerte werden Hexadezimal angegeben, ohne das f�hrende Raute-Zeichen.') ?><br />
            <?= _('Wenn JavaScript aktiviert ist, steht ein Farbauswahl-Popup zur Verf�gung.') ?>
        </div>
        <br />

        <table class="default">
            <tr>
                <th>
                    <?= _('Fakult�t') ?>
                </th>
                <th>
                    <?= _('Farbe') ?>
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
    </div>
    <br />

    <?= Studip\Button::createAccept(_('speichern')) ?>
</form>

<?php
$infobox_content = array(
    array ('kategorie' => 'Information:',
           'eintrag'   => array (
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('W�hlen Sie die Termin-Typen aus, die bei der Erstellung eines Pr�fungskalenders ber�cksichtigt werden sollen.')
                ),
                array ('icon' => 'icons/16/black/info.png',
                       'text' => _('Die Farben, die Sie den Fakult�ten zuordnen, werden in den Listen- und Kalenderausgaben verwendet, um darzustellen, welche Pr�fungen zu welchen Fakult�ten geh�ren.') . '<br />' .
                                 _('Jede Ausgabe enth�lt eine Legende, die die Farbzuordnung der Fakult�ten enth�lt.')
                )
          )
    )
);
$infobox = array('picture' => 'infobox/board2.jpg', 'content' => $infobox_content); // TODO Bild
