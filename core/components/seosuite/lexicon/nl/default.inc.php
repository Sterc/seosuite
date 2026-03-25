<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                              = 'SEO Suite';
$_lang['seosuite.menu.seosuite']                                = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc']                           = 'Beheer 404 URL\'s en redirects.';

$_lang['seosuite.urls']                                         = '404 URLs';
$_lang['seosuite.urls_desc']                                    = 'Het corrigeren van uw 404-URL’s is eenvoudig. U hoeft alleen maar een CSV-bestand met één kolom te uploaden.
SEO Suite zorgt ervoor dat de URL’s worden omgeleid naar de juiste pagina’s van uw website.
Ze worden vergeleken met bestaande pagina’s op uw website aan de hand van het vetgedrukte gedeelte van de voorbeeld-URL: https://example.tld/ordner1/ordner1/<strong>ressourcen-alias</strong>.
   SEO Suite voert een van de volgende acties uit:<br /><br />
   1. Als er precies één overeenkomst is, wordt deze automatisch omgezet in een 301-omleiding in SEO Tab, of, als SEO Tab niet is geïnstalleerd, neemt SEO Suite de omleiding over. <br />
   2. Als er meerdere overeenkomsten zijn, kunt u handmatig de gewenste omleiding selecteren.<br />
   3. Als er geen overeenkomsten zijn, kunt u een aangepaste URL invoeren.';
$_lang['seosuite.urls_import']                                  = 'Importeer 404 url(s)';
$_lang['seosuite.urls_remove']                                  = '404 URL(s) verwijderen';
$_lang['seosuite.urls_remove_confirm']                          = 'Weet je zeker dat je de geselecteerde 404 URL(s) wilt verwijderen?';
$_lang['seosuite.url_suggestions']                              = '404 URL suggesties';
$_lang['seosuite.url.found_suggestions']                        = 'Er is een suggestie gevonden! De suggestie is nu verbonden met deze URL.';
$_lang['seosuite.url.found_suggestions.redirect_exists']        = 'Er bestaat al een redirect voor deze 404 pagina, we hebben de 404 URL verwijderd.';
$_lang['seosuite.url_remove']                                   = 'Verwijder URL';
$_lang['seosuite.url_remove_confirm']                           = 'Weet je zeker dat je de geselecteerde 404 URL(s) wilt verwijderen?';
$_lang['seosuite.suggestions_found']                            = 'Er zijn [[+suggestions]] pagina suggesties gevonden.';
$_lang['seosuite.suggestion_boost']                             = 'pnt';
$_lang['seosuite.exclude_words']                                = 'Suggestions uitsluiten';
$_lang['seosuite.label_exclude_words']                          = 'Woorden uitsluiten van URL suggesties';
$_lang['seosuite.label_exclude_words_desc']                     = 'Een komma gescheiden lijst van woorden om uit te sluiten van de 404 URL suggesties.';
$_lang['seosuite.blocked_words']                                = 'Logging uitsluiten';
$_lang['seosuite.label_blocked_words']                          = 'Exclude words from 404 page logging';
$_lang['seosuite.label_blocked_words_desc']                     = 'Een komma gescheiden lijst van woorden om uit te sluiten van de 404 pagina logging.';
$_lang['seosuite.label_url_url']                                = 'Url';
$_lang['seosuite.label_url_visits']                             = 'Hits';
$_lang['seosuite.label_url_last_visit']                         = 'Laatste hit';
$_lang['seosuite.label_url_suggestion']                         = 'Suggestie';
$_lang['seosuite.label_url_suggestion_desc']                    = 'De suggestie waar de URL verwijzing heen wijst.';
$_lang['seosuite.label_url_suggestions']                        = 'Suggesties';
$_lang['seosuite.label_url_createdon']                          = 'Aangemaakt op';
$_lang['seosuite.label_url_match_context']                      = 'Matchen op domein [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']                 = 'Indien aangevinkt zal het matchen gebeuren met pagina\'s binnen het domein [[+domain]] . Handig voor wanneer u een meertalige website hebt in meerdere contexten met veel dezelfde pagina\'s.';
$_lang['seosuite.label_url_match_create_redirect']              = 'Automatisch URL verwijzing maken';
$_lang['seosuite.label_url_match_create_redirect_desc']         = 'Indien aangevinkt zal er automatisch een URL verwijzing aangemaakt worden aan de hand van de eerste gevonden match.';

$_lang['seosuite.label_import_file']                            = 'Bestand';
$_lang['seosuite.label_import_file_desc']                       = 'Selecteer een geldig bestand om 404 URL(s) te importeren.';
$_lang['seosuite.import.instructions']                          = 'Gebruik een .csv, .xls of .xlsx bestand. Zorg ervoor dat je de complete URL\'s hebt ingevuld, inclusief de domeinnaam. Voorbeeld: https://www.seosuite.com in plaats van seosuite.com. Zie ook dit <a href="[[+path]]">voorbeeld import bestand (XLS)</a>.';
$_lang['seosuite.import.start']                                 = 'Import gestart!';

$_lang['seosuite.redirects']                                    = 'URL verwijzingen';
$_lang['seosuite.redirects_desc']                               = 'Beheer hier alle URL verwijzingen. Een URL verwijzining is bedoelt om een niet (meer) bestaande pagina door te verwijzen naar de nieuwe pagina. Een URL verwijziging kun je maken voor een specifieke context of voor alle contexten tegelijk en word hieronder aangeduid als "*/". URL verwijzigen met een specifieke context heeft voorrang op een URL verwijzing voor alle contexten.';
$_lang['seosuite.redirect_create']                              = 'Nieuwe URL verwijzing';
$_lang['seosuite.redirect_update']                              = 'URL verwijzing wijzigen';
$_lang['seosuite.redirect_remove']                              = 'URL verwijzing verwijderen';
$_lang['seosuite.redirect_remove_confirm']                      = 'Weet je zeker dat je deze URL verwijzing wilt verwijderen?';
$_lang['seosuite.redirects_remove']                             = 'URL verwijzing(en) verwijderen';
$_lang['seosuite.redirects_remove_confirm']                     = 'Weet je zeker dat je de geselecteerde URL verwijzing(en) wilt verwijderen?';
$_lang['seosuite.use_redirect_across_domains']                  = 'Gebruik redirect voor alle domeinen';
$_lang['seosuite.redirect_test']                                = 'Redirect testen';

$_lang['seosuite.label_redirect_old_url']                       = 'Oude URL';
$_lang['seosuite.label_redirect_old_url_desc']                  = 'De oude URL van de URL verwijzing zonder domain naam. Bijvoorbeeld: "/voorbeeld".';
$_lang['seosuite.label_redirect_new_url']                       = 'Nieuwe URL / ID';
$_lang['seosuite.label_redirect_new_url_desc']                  = 'De nieuwe URL van de URL verwijzing. Dit kan met of zonder domein, maar kan ook een ID van een pagina zijn.';
$_lang['seosuite.label_redirect_active']                        = 'Actief';
$_lang['seosuite.label_redirect_active_desc']                   = '';
$_lang['seosuite.label_redirect_match_context']                 = 'Context';
$_lang['seosuite.label_redirect_match_context_desc']            = 'Match de redirect aan een specifieke context of gebruik de redirect als een wildcard voor alle websites.';
$_lang['seosuite.label_redirect_type']                          = 'Verwijzingstype';
$_lang['seosuite.label_redirect_type_desc']                     = 'De verwijzingstype van de URL verwijzing.';

$_lang['seosuite.error.emptyfile']                              = 'Geen bestand opgegeven.';
$_lang['seosuite.error.extension_notallowed']                   = 'Bestandstype niet toegestaan. Alleen .csv bestanden zijn toegestaan.';
$_lang['seosuite.error.ziparchive_notinstalled']                = 'PHP extensie ZipArchive is niet geïnstalleerd, deze is nodig om xls(x) bestanden te importeren. Installeer de ZipArchive extensie of gebruik een .csv bestand.';

$_lang['seosuite.friendly_urls_disabled']                       = 'Zoekmachine vriendelijke URLs staat momenteel niet ingeschakeld in deze MODx installatie, om SEO Suite goed te laten werken dient zoekmachine vriendelijke URLs ingeschakeld te zijn.';
$_lang['seosuite.find_suggestions']                             = 'Vind suggesties';
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

$_lang['seosuite.migration']                                    = 'Migratie';
$_lang['seosuite.migration_desc']                               = 'Voor een upgrade van SEO Suite V1, SEO Pro of SEO Tab is een gegevensmigratie vereist. Hier kunt u de migraties voor deze extra’s uitvoeren.';
$_lang['seosuite.migration.seosuitev1.results']                 = '<b>[[+redirects]]</b> omleidingen en <b>[[+urls]]</b> URL’s gevonden voor SEO Suite V1.';
$_lang['seosuite.migration.seosuitev1.empty']                   = '<i>Geen gegevens gevonden voor de migratie van SEO Suite V1.</i>';
$_lang['seosuite.migration.seopro.results']                     = '<b>[[+count]]</b> SEO Pro-zoekwoordbronnen gevonden.';
$_lang['seosuite.migration.seopro.empty']                       = '<i>Geen gegevens gevonden voor de SEO Pro-migratie.</i>';
$_lang['seosuite.migration.seotab.results']                     = '<b>[[+urls]]</b> omleidingen en <b>[[+resources]]</b> bronnen gevonden voor SEO-tab.';
$_lang['seosuite.migration.seotab.empty']                       = '<i>Geen gegevens gevonden voor de migratie van het tabblad ‘SEO’.</i>';
$_lang['seosuite.migration.migrate']                            = 'Migreren';

$_lang['seosuite.widget']                                       = 'SEO Suite';
$_lang['seosuite.widget_desc']                                  = 'SEO Suite-dashboardwidget';
$_lang['seosuite.widget_intro']                                  = 'Hier zie je de meest recent toegevoegde 404 URL\'s . Om alle 404 URL\'s te bekijken en beheren, ga je naar de <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite manager pagina.</a>';
