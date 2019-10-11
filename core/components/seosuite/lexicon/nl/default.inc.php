<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                          = 'SEO Suite';
$_lang['area_seosuite_tab']                                 = 'SEO tab';

$_lang['setting_seosuite.tab_default_index_type']           = 'Standaard pagina\'s indexen';
$_lang['setting_seosuite.tab_default_index_type_desc']      = '';
$_lang['setting_seosuite.tab_default_follow_type']          = 'Standaard links volgen';
$_lang['setting_seosuite.tab_default_follow_type_desc']     = '';


$_lang['seosuite'] = 'SEO Suite';

$_lang['seosuite.menu.seosuite'] = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc'] = 'Beheer je 404 URL\'s.';

$_lang['seosuite.global.search'] = 'Zoeken';

$_lang['seosuite.url.urls'] = '404 URL\'s';
$_lang['seosuite.url.intro_msg'] = 'Heb je meerdere 404 URL\'s gevonden? Upload hier simpelweg een .csv file en 
SEO Suite zorgt ervoor dat ze herleid worden op je website. Deze worden gematcht met bestaande pagina\'s op je website, gebaseerd op het laatste deel van de URL (het gedeelte achter de laatste slash).
 Is er één match, dan wordt deze automatisch geconverteerd naar een 301 redirect in SEO Tab, of, als SEO Tab niet geinstalleerd is, zorgt SEO Suite voor de redirect.
  Zijn er meerdere matches, dan kun je de gewenste redirect handmatig kiezen. Zijn er geen matches, dan kun je zelf een URL invoeren.';
$_lang['seosuite.url.import'] = 'Importeer bestand';
$_lang['seosuite.url.file'] = 'Bestand';
$_lang['seosuite.import.start'] = 'Starten met importeren van de URL\'s, dit kan even duren afhankelijk van 
de grootte van je bestand.';
$_lang['seosuite.import.instructions'] = 'Gebruik een .csv, .xls of .xlsx bestand. Zorg ervoor dat je de complete URL\'s hebt ingevuld, inclusief de domeinnaam. Voorbeeld: https://www.seosuite.com in plaats van seosuite.com.';

$_lang['seosuite.url.url'] = '404 URL';
$_lang['seosuite.url.solved'] = 'Opgelost';
$_lang['seosuite.url.position'] = 'Positie';
$_lang['seosuite.url.redirect_to'] = 'Redirect naar';
$_lang['seosuite.url.suggestions'] = 'Redirect suggesties';
$_lang['seosuite.url.triggered'] = '404 Aanroepen';
$_lang['seosuite.url.createdon'] = '404 Aanmaakdatum';
$_lang['seosuite.url.find_suggestions'] = 'Vind suggesties';
$_lang['seosuite.url.found_suggestions'] = 'Er is een suggestie gevonden! De suggestie is nu verbonden met deze URL.';
$_lang['seosuite.url.found_suggestions_multiple'] = 'Er is meer dan 1 suggestie gevonden.
 Voeg a.u.b. handmatig een redirect toe.';
$_lang['seosuite.url.notfound_suggestions'] = 'Er zijn geen suggesties gevonden voor deze URL.';
$_lang['seosuite.url.update'] = 'Update URL';
$_lang['seosuite.url.remove'] = 'Verwijder URL';
$_lang['seosuite.url.remove_confirm'] = 'Weet je zeker dat je deze URL wil verwijderen?';
$_lang['seosuite.url.choose_suggestion'] = 'Kies uit suggesties';
$_lang['seosuite.url.choose_manually'] = 'Kies handmatig een pagina';
$_lang['seosuite.url.redirect_to_selected'] = 'Geselecteerde redirect';

$_lang['seosuite.error.url_alreadyexists'] = 'Deze URL bestaat al.';
$_lang['seosuite.error.url_notfound'] = 'Item niet gevonden.';
$_lang['seosuite.err.item_name_ae'] = 'Item niet gevonden.';
$_lang['seosuite.error.url_notspecified'] = 'URL is niet gedefinieerd.';
$_lang['seosuite.err.item_name_ns'] = 'Waarde is niet gedefinieerd.';
$_lang['seosuite.error.url_remove'] = 'Er is een fout opgetreden tijdens het verwijderen van de URL.';
$_lang['seosuite.error.url_save'] = 'Er is een fout opgetreden tijdens het opslaan van de URL.';
$_lang['seosuite.error.emptyfile'] = 'Geen bestand opgegeve.';
$_lang['seosuite.error.extension_notallowed'] = 'Bestandstype niet toegestaan. Alleen .csv bestanden zijn toegestaan.';
$_lang['seosuite.error.ziparchive_notinstalled'] = 'PHP extensie ZipArchive is niet geïnstalleerd, 
deze is nodig om xls(x) bestanden te importeren. Installeer de ZipArchive extensie of gebruik een .csv bestand.';

$_lang['seosuite.import.seoUrl.error'] = 'De gevonden suggestie kon niet automatisch worden toegevoegd als redirect.
 Voeg deze a.u.b. handmatig toe.';
$_lang['seosuite.import.seoUrl.error'] = 'De suggestie kon niet worden verbonden met de SEO Tab URL.
 Verbind deze a.u.b. handmatig.';
$_lang['seosuite.seotab.notfound'] = 'SEO Tab is niet geïnstalleerd of de versie is ongeldig. 
Installeer a.u.b. SEO Tab (versie 2.0 of hoger) om SEO Suite in staat te stellen
 om redirects automatisch te koppelen aan 404 URL\'s.';
$_lang['seosuite.seotab.versioninvalid'] = 'De geinstalleerde versie van Seo Tab is verouderd. 
Installeer Seo Tab versie 2 (of hoger) om een redirect te kunnen toevoegen.';

$_lang['seosuite.widget_desc'] = 'Hier zie je de meest recent toegevoegde 404 urls.
 Om alle 404 urls te bekijken en beheren, ga naar de <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite manager pagina.</a>';

$_lang['seosuite.match_site_url'] = 'Match context site url';
$_lang['seosuite.match_site_url_desc'] = 'Should the matching system only match pages within the same context.
 Useful for when you have a multi-language website in multiple contexts with a lot of the same pages.';

/* Keywords. */
$_lang['seosuite.keywords']                 = 'Keywords';
$_lang['seosuite.characters']               = 'Karakters';
$_lang['seosuite.characters.allowed']       = 'Toegestane karakters';
$_lang['seosuite.focuskeywords']            = 'Focus keywords';
$_lang['seosuite.focuskeywords_desc']       = 'Vul je keywords komma-gescheiden in.';
$_lang['seosuite.prevbox']                  = 'Google Preview';
$_lang['seosuite.prevbox_yandex']           = 'Yandex Preview';
$_lang['seosuite.emptymetadescription']     = '<i>Vul een <span class="seosuite-google-description--field">omschrijving</span> in</i>';
$_lang['seosuite.branding_text']            = 'Deze website is geoptimaliseerd met de Sterc SeoSuite plugin - https://github.com/Sterc/seosuite.';

/* Settings. */
$_lang['setting_seosuite.preview.delimiter']            = 'Scheidingsteken in Google/Yandex preview.';
$_lang['setting_seosuite.preview.delimiter_desc']       = 'Scheidingsteken tussen titel en sitenaam';
$_lang['setting_seosuite.preview.searchengine']         = 'Welke zoekmachine gebruiken in de preview';
$_lang['setting_seosuite.preview.searchengine_desc']    = 'Mogelijke waarden: google, yandex.';
$_lang['setting_seosuite.preview.usesitename']          = 'Site naam tonen in Google/Yandex Preview en in de SeoSuite title placeholder?';
$_lang['setting_seosuite.preview.usesitename_desc']     = 'Als deze op \'nee\' staat wordt het scheidingsteken en de sitenaam niet getoond.';
$_lang['setting_seosuite.preview.title_format']         = 'Format used for the meta title.';
$_lang['setting_seosuite.preview.title_format_desc']    = 'Here you can specify the format used for the meta title used
 in Google/Yandex preview.';

$_lang['setting_seosuite.keywords.fields']                  = 'Velden waarop de keyword tool zal werken ';
$_lang['setting_seosuite.keywords.fields_desc']             = 'Verander deze gegevens alleen als je weet wat je doet. Standaard waarde:pagetitle,longtitle,description,alias,menutitle. SEO Pro werkt niet op het content veld';
$_lang['setting_seosuite.keywords.version']                 = 'Versie nummer';
$_lang['setting_seosuite.keywords.version_desc']            = 'Huidige '.$_lang['setting_seosuite.version'];
$_lang['setting_seosuite.keywords.disabledtemplates']       = 'Niet-gebruikte templates';
$_lang['setting_seosuite.keywords.disabledtemplates_desc']  = 'Komma gescheiden lijst met template ID\'s waar SEO Pro niet getoond wordt.';
$_lang['setting_seosuite.keywords.max_keywords_title']      = 'Maximaal aantal focus keywords toegestaan in titel.';
$_lang['setting_seosuite.keywords.max_keywords_title_desc'] = 'Vul hier het maximaal aantal toegestane focus keywords voor de titel om een thumbs-up te krijgen.
 We adviseren om niet meer dan 4 keywords in de titel te gebruiken.';

$_lang['setting_seosuite.keywords.max_keywords_description']      = 'Maximaal aantal focus keywords toegestaan in omschrijving.';
$_lang['setting_seosuite.keywords.max_keywords_description_desc'] = 'Vul hier het maximaal aantal toegestane focus keywords voor de omschrijving om een thumbs-up te krijgen.
 We adviseren om niet meer dan 8 keywords in de omschrijving te gebruiken.';
