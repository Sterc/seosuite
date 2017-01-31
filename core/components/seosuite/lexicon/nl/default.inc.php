<?php
/**
 * Default Dutch Lexicon Entries for SEO Suite
 *
 * @package seosuite
 * @subpackage lexicon
 */

$_lang['seosuite'] = 'SEO Suite';

$_lang['seosuite.menu.seosuite'] = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc'] = 'Beheer je 404 URL\'s';

$_lang['seosuite.global.search'] = 'Zoeken';

$_lang['seosuite.url.urls'] = '404 URL\'s';
$_lang['seosuite.url.intro_msg'] = 'Heb je een 404 URL gevonden? Upload hier simpelweg een .csv file en 
de SEO Suite zorgt ervoor dat ze herleid worden. De match vindt plaats door te kijken naar het laatste deel 
van de URL, achter de laatste /. Is er één match, dan wordt deze automatisch in de 301 redirect gezet. Zijn 
er meer, dan kan je kiezen en is er geen, dan kan je deze zelf invullen.';
$_lang['seosuite.url.import'] = 'Importeer bestand';
$_lang['seosuite.url.file'] = 'Bestand';
$_lang['seosuite.import.start'] = 'Starten met importeren van de URL\'s, dit kan even duren afhankelijk van 
de grootte van je bestand.';
$_lang['seosuite.import.instructions'] = 'Gebruik een .csv, .xls of .xlsx bestand. Zorg ervoor dat je de complete URL’s 
hebt ingevuld, inclusief de domeinnaam.';

$_lang['seosuite.url.url'] = '404 URL';
$_lang['seosuite.url.solved'] = 'Opgelost';
$_lang['seosuite.url.position'] = 'Positie';
$_lang['seosuite.url.redirect_to'] = 'Redirect naar';
$_lang['seosuite.url.suggestions'] = 'Redirect suggesties';
$_lang['seosuite.url.find_suggestions'] = 'Vind suggesties';
$_lang['seosuite.url.found_suggestions'] = 'Een suggestie voor deze URL is gevonden en gekoppeld.';
$_lang['seosuite.url.notfound_suggestions'] = 'Er zijn geen suggesties gevonden voor deze  URL.';
$_lang['seosuite.url.update'] = 'Update URL';
$_lang['seosuite.url.remove'] = 'Verwijder URL';
$_lang['seosuite.url.remove_confirm'] = 'Weet u zeker dat u deze URL wil verwijderen?';

$_lang['seosuite.error.url_alreadyexists'] = 'Deze URL bestaat al.';
$_lang['seosuite.error.url_notfound'] = 'Item niet gevonden.';
$_lang['seosuite.err.item_name_ae'] = 'Item niet gevonden.';
$_lang['seosuite.error.url_notspecified'] = 'URL is niet gedefinieerd.';
$_lang['seosuite.err.item_name_ns'] = 'Waarde is niet gedefinieerd.';
$_lang['seosuite.error.url_remove'] = 'Er is een fout opgetreden tijdens het verwijderen van de URL.';
$_lang['seosuite.error.url_save'] = 'Er is een fout opgetreden tijdens het opslaan van de URL.';
$_lang['seosuite.error.emptyfile'] = 'Geen bestand opgegeven';
$_lang['seosuite.error.extension_notallowed'] = 'Bestandstype niet toegestaan. Alleen csv bestanden zijn toegestaan.';
$_lang['seosuite.error.ziparchive_notinstalled'] = 'PHP extension ZipArchive is niet geinstalleerd, 
welke nodig is om xls(x) bestanden te importeren. Installeer de ZipArchive extension, of gebruik een .csv bestand.';

$_lang['seosuite.import.seoUrl.error'] = 'De gevonden suggestie kon niet automatisch als redirect toegevoegd worden. 
Graag deze handmatig aanmaken.';
$_lang['seosuite.import.seoUrl.error'] = 'We konden niet het voorstel koppelen aan de URL in Seo Tab, 
graag deze handmatig koppelen.';
$_lang['seosuite.seotab.notfound'] = 'Seo Tab is niet geinstalleerd of is niet de juiste versie. 
Om redirects (automatisch) toe te voegen voor een 404 URL dient Seo Tab (minimaal versie 2) geinstalleerd te zijn.';
$_lang['seosuite.seotab.versioninvalid'] = 'De geinstalleerde versie van Seo Tab is verouderd. 
Installeer Seo Tab versie 2 (of hoger) om een redirect te kunnen toevoegen.';
