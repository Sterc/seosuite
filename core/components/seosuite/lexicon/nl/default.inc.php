<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                              = 'SEO Suite';
$_lang['seosuite.desc']                                         = 'Beheer hier alle zoekmachine gerelateerde functionaliteiten.';

$_lang['area_seosuite']                                         = 'SEO Suite';
$_lang['area_seosuite_tab_seo']                                 = 'SEO Suite: Zoekmachine tab';
$_lang['area_seosuite_tab_social']                              = 'SEO Suite: Social tab';

$_lang['setting_seosuite.branding_url']                         = 'Branding';
$_lang['setting_seosuite.branding_url_desc']                    = 'De URL waar de branding knop heen verwijst, indien leeg wordt de branding knop niet getoond.';
$_lang['setting_seosuite.branding_url_help']                    = 'Branding (help)';
$_lang['setting_seosuite.branding_url_help_desc']               = 'De URL waar de branding help knop heen verwijst, indien leeg wordt de branding help knop niet getoond.';
$_lang['setting_seosuite.exclude_words']                        = 'Woorden uitsluiten';
$_lang['setting_seosuite.exclude_words_desc']                   = 'Een komma gescheiden lijst van woorden die uitgesloten worden van de 404 URL suggesties.';
$_lang['setting_seosuite.tab_seo_default_index_type']           = 'Standaard pagina\'s indexeren';
$_lang['setting_seosuite.tab_seo_default_index_type_desc']      = '';
$_lang['setting_seosuite.tab_seo_default_follow_type']          = 'Standaard links volgen';
$_lang['setting_seosuite.tab_seo_default_follow_type_desc']     = '';
$_lang['setting_seosuite.tab_seo_default_sitemap']              = 'Standaard opnemen in sitemap';
$_lang['setting_seosuite.tab_seo_default_sitemap_desc']         = '';
$_lang['setting_seosuite.tab_social_og_types']                  = 'Facebook type values';
$_lang['setting_seosuite.tab_social_og_types_desc']             = '';
$_lang['setting_seosuite.tab_social_twitter_cards']             = 'Twitter card values';
$_lang['setting_seosuite.tab_social_twitter_cards_desc']        = '';
$_lang['setting_seosuite.default_redirect_type']                = 'Standaard redirect type';
$_lang['setting_seosuite.default_redirect_type_desc']           = '';

$_lang['seosuite.url']                                          = '404 URL';
$_lang['seosuite.urls']                                         = '404 URL\'s';
$_lang['seosuite.urls_desc']                                    = 'Heb je meerdere 404 URL\'s gevonden? Upload hier simpelweg een .csv file en SEO Suite zorgt ervoor dat ze herleid worden op je website. Deze worden gematcht met bestaande pagina\'s op je website, gebaseerd op het laatste deel van de URL (het gedeelte achter de laatste slash). Is er één match, dan wordt deze automatisch geconverteerd naar een 301 redirect in SEO Tab, of, als SEO Tab niet geinstalleerd is, zorgt SEO Suite voor de redirect. Zijn er meerdere matches, dan kun je de gewenste redirect handmatig kiezen. Zijn er geen matches, dan kun je zelf een URL invoeren.';
$_lang['seosuite.url_update']                                   = '404 URL wijzigen';
$_lang['seosuite.url_remove']                                   = '404 URL verwijderen';
$_lang['seosuite.url_remove_confirm']                           = 'Weet je zeker dat je deze 404 URL wilt verwijderen?';
$_lang['seosuite.urls_remove']                                  = '404 URL(s) verwijderen';
$_lang['seosuite.urls_remove_confirm']                          = 'Weet je zeker dat je de geselecteerde 404 URL(s) wilt verwijderen?';
$_lang['seosuite.url_suggesstions']                             = '404 URL suggesties';
$_lang['seosuite.url_import']                                   = 'Importeer 404 url(s)';

$_lang['seosuite.label_url_url']                                = 'Url';
$_lang['seosuite.label_url_url_desc']                           = '';
$_lang['seosuite.label_url_visits']                             = 'Hits';
$_lang['seosuite.label_url_visits_desc']                        = '';
$_lang['seosuite.label_url_last_visit']                         = 'Laatste hit';
$_lang['seosuite.label_url_last_visit_desc']                    = '';
$_lang['seosuite.label_url_suggestion']                         = 'Suggestie';
$_lang['seosuite.label_url_suggestion_desc']                    = 'De suggestie waar de URL verwijzing heen wijst.';
$_lang['seosuite.label_url_suggestions']                        = 'Suggesties';
$_lang['seosuite.label_url_suggestions_desc']                   = '';
$_lang['seosuite.label_url_createdon']                          = 'Aangemaakt op';
$_lang['seosuite.label_url_createdon_desc']                     = '';

$_lang['seosuite.label_url_match_context']                      = 'Matchen op domein [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']                 = 'Indien aangevinkt zal het matchen gebeuren met pagina\'s binnen het domein [[+domain]]. Handig voor wanneer u een meertalige website hebt in meerdere contexten met veel dezelfde pagina\'s.';
$_lang['seosuite.label_url_match_create_redirect']              = 'Automatisch URL verwijzing maken';
$_lang['seosuite.label_url_match_create_redirect_desc']         = 'Indien aangevinkt zal er automatisch een URL verwijzing aangemaakt worden aan de hand van de eerste gevonden match.';

$_lang['seosuite.label_import_file']                            = 'Bestand';
$_lang['seosuite.label_import_file_desc']                       = 'Selecteer een geldig bestand om 404 URL(s) te importeren.';

$_lang['seosuite.redirect']                                     = 'URL verwijzing';
$_lang['seosuite.redirects']                                    = 'URL verwijzingen';
$_lang['seosuite.redirects_desc']                               = 'Beheer hier alle URL verwijzingen.';
$_lang['seosuite.redirect_create']                              = 'Nieuwe URL verwijzing';
$_lang['seosuite.redirect_update']                              = 'URL verwijzing wijzigen';
$_lang['seosuite.redirect_remove']                              = 'URL verwijzing verwijderen';
$_lang['seosuite.redirect_remove_confirm']                      = 'Weet je zeker dat je deze URL verwijzing wilt verwijderen?';
$_lang['seosuite.redirects_remove']                             = 'URL verwijzing(en) verwijderen';
$_lang['seosuite.redirects_remove_confirm']                     = 'Weet je zeker dat je de geselecteerde URL verwijzing(en) wilt verwijderen?';

$_lang['seosuite.label_redirect_old_url']                       = 'Oude URL';
$_lang['seosuite.label_redirect_old_url_desc']                  = 'De oude URL van de URL verwijzing.';
$_lang['seosuite.label_redirect_new_url']                       = 'Nieuwe URL';
$_lang['seosuite.label_redirect_new_url_desc']                  = 'De nieuwe URL van de URL verwijzing. Dit kan met of zonder domein, maar kan ook een ID van een pagina zijn.';
$_lang['seosuite.label_redirect_active']                        = 'Actief';
$_lang['seosuite.label_redirect_active_desc']                   = '';
$_lang['seosuite.label_redirect_type']                          = 'Verwijzingstype';
$_lang['seosuite.label_redirect_type_desc']                     = 'De verwijzingstype van de URL verwijzing.';

$_lang['seosuite.label_exclude_words']                          = 'Woorden uitsluiten';
$_lang['seosuite.label_exclude_words_desc']                     = 'Een komma gescheiden lijst van woorden om uit te sluiten vam de 404 URL suggesties.';

$_lang['seosuite.urls_import']                                  = 'Importeer bestand';
$_lang['seosuite.find_suggestions']                             = 'Vind suggesties';
$_lang['seosuite.exclude_words']                                = 'Woorden uitsluiten';
$_lang['seosuite.suggestions_found']                            = 'Er zijn [[+suggestions]] pagina suggesties gevonden.';
$_lang['seosuite.suggestion_boost']                             = 'pnt';
$_lang['seosuite.redirect_error_exists']                        = 'Er bestaat al een URL verwijzing voor deze URL.';
$_lang['seosuite.resource_no_redirects']                        = 'Er zijn geen URL verwijzingen voor de deze pagina.';
$_lang['seosuite.time_seconds']                                 = 'Minder dan 1 minuut geleden';
$_lang['seosuite.time_minute']                                  = '1 minuut geleden';
$_lang['seosuite.time_minutes']                                 = '[[+minutes]] minuten geleden';
$_lang['seosuite.time_hour']                                    = '1 uur geleden';
$_lang['seosuite.time_hours']                                   = '[[+hours]] uren geleden';
$_lang['seosuite.time_day']                                     = '1 dag geleden';
$_lang['seosuite.time_days']                                    = '[[+days]] dagen geleden';
$_lang['seosuite.time_week']                                    = '1 week geleden';
$_lang['seosuite.time_weeks']                                   = '[[+weeks]] weken geleden';
$_lang['seosuite.time_month']                                   = '1 maand geleden';
$_lang['seosuite.time_months']                                  = '[[+months]] maanden geleden';
$_lang['seosuite.time_to_long']                                 = 'Meer dan een half jaar geleden';









$_lang['seosuite.global.search'] = 'Zoeken';
$_lang['seosuite.url.import'] = 'Importeer bestand';

$_lang['seosuite.import.start'] = 'Starten met importeren van de URL\'s, dit kan even duren afhankelijk van 
de grootte van je bestand.';
$_lang['seosuite.import.instructions'] = 'Gebruik een .csv, .xls of .xlsx bestand. Zorg ervoor dat je de complete URL\'s hebt ingevuld, inclusief de domeinnaam. Voorbeeld: https://www.seosuite.com in plaats van seosuite.com.';

$_lang['seosuite.url.position'] = 'Positie';
$_lang['seosuite.url.found_suggestions'] = 'Er is een suggestie gevonden! De suggestie is nu verbonden met deze URL.';
$_lang['seosuite.url.found_suggestions_multiple'] = 'Er is meer dan 1 suggestie gevonden.
 Voeg a.u.b. handmatig een redirect toe.';
$_lang['seosuite.url.notfound_suggestions'] = 'Er zijn geen suggesties gevonden voor deze URL.';
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
$_lang['setting_seosuite.keywords.fields_desc']             = 'Verander deze gegevens alleen als je weet wat je doet. Standaard waarde:pagetitle,longtitle,content.';
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
