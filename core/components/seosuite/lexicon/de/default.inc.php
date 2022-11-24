<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                              = 'SEO Suite';
$_lang['seosuite.menu.seosuite']                                = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc']                           = 'Verwalte 404 URLs und Weiterleitungen.';

$_lang['area_seosuite']                                         = 'SEO Suite';
$_lang['area_seosuite_tab_meta']                                = 'SEO Suite - Meta';
$_lang['area_seosuite_tab_seo']                                 = 'SEO Suite - SEO';
$_lang['area_seosuite_tab_social']                              = 'SEO Suite - Soziale Netzwerke';
$_lang['area_seosuite_sitemap']                                 = 'SEO Suite - Sitemap';

$_lang['seosuite.urls']                                         = '404 URLs';
$_lang['seosuite.urls_desc']                                    = 'Das Korrigieren Ihrer 404-URLs ist einfach. Laden Sie einfach eine einspaltige CSV-Datei hoch.
 SEO Suite sorgt dafür, dass die URLs auf die richtigne Seiten Ihrer Website umgeleitet werden.
  Sie werden anhand des fettgedruckten Teils der Beispiel-URL mit bestehenden Seiten auf Ihrer Website abgeglichen: https://example.tld/ordner1/ordner1/<strong>ressourcen-alias</strong>.
   SEO Suite wird eine der folgenden Aktionen durchführen:<br /><br />
   1. Wenn es genau eine Übereinstimmung gibt, wird sie automatisch in eine 301-Weiterleitung in SEO Tab umgewandelt, oder, wenn SEO Tab nicht installiert ist, wird SEO Suite die Weiterleitung übernehmen.<br />
   2. Wenn es mehrere Übereinstimmungen gibt, können Sie die gewünschte Umleitung manuell auswählen.<br />
   3. Wenn es keine Übereinstimmungen gibt, können Sie eine benutzerdefinierte URL eingeben.';
$_lang['seosuite.urls_import']                                  = '404 URL(s) importieren';
$_lang['seosuite.urls_remove']                                  = '404 URL(s) löschen';
$_lang['seosuite.urls_remove_confirm']                          = 'Sind Sie sicher, dass Sie die ausgewählte(n) 404-URL(s) entfernen möchten?';
$_lang['seosuite.url_update']                                   = '404 URL aktualisieren';
$_lang['seosuite.url_suggestions']                              = '404 URL Vorschläge';
$_lang['seosuite.url.found_suggestions']                        = 'Ein Vorschlag wurde gefunden! Der Vorschlag ist jetzt mit dieser URL verbunden.';
$_lang['seosuite.url.found_suggestions_multiple']               = 'Es wurde mehr als ein Vorschlag gefunden. Bitte fügen Sie die Weiterleitung manuell hinzu.';
$_lang['seosuite.url.notfound_suggestions']                     = 'Es wurden keine Vorschläge für diese URL gefunden.';
$_lang['seosuite.url.found_suggestions.redirect_exists']        = 'Für diese 404-Seite existiert bereits eine Weiterleitung. Die 404-URL wurde entfernt.';
$_lang['seosuite.url_remove']                                   = 'URL entfernen';
$_lang['seosuite.url_remove_confirm']                           = 'Sind Sie sicher, dass Sie diese URL löschen wollen?';
$_lang['seosuite.url.redirect_to_selected']                     = 'Ausgewählte Umleitung';
$_lang['seosuite.suggestions_found']                            = '[[+suggestions]] Seitenvorschläge wurden gefunden.';
$_lang['seosuite.suggestion_boost']                             = 'pts';
$_lang['seosuite.exclude_words']                                = 'Vorschläge ausschließen';
$_lang['seosuite.label_exclude_words']                          = 'Wörter von URL-Vorschlägen ausschließen';
$_lang['seosuite.label_exclude_words_desc']                     = 'Eine durch Komma getrennte Liste von Wörtern, die von 404-URL-Vorschlägen ausgeschlossen werden sollen.';
$_lang['seosuite.blocked_words']                                = 'Aus Protokollierung ausschließen';
$_lang['seosuite.label_blocked_words']                          = 'Wörter aus der 404-Seitenprotokollierung ausschließen';
$_lang['seosuite.label_blocked_words_desc']                     = 'Eine durch Komma getrennte Liste von Wörtern, die von der 404-Seiten-Protokollierung ausgeschlossen werden sollen.';
$_lang['seosuite.label_url_url']                                = 'URL';
$_lang['seosuite.label_url_url_desc']                           = '';
$_lang['seosuite.label_url_visits']                             = 'Aufrufe';
$_lang['seosuite.label_url_visits_desc']                        = '';
$_lang['seosuite.label_url_last_visit']                         = 'Letzter Aufruf';
$_lang['seosuite.label_url_last_visit_desc']                    = '';
$_lang['seosuite.label_url_suggestion']                         = 'Vorschlag';
$_lang['seosuite.label_url_suggestion_desc']                    = 'Der Vorschlag, die URL umzuleiten';
$_lang['seosuite.label_url_suggestions']                        = 'Vorschläge';
$_lang['seosuite.label_url_suggestions_desc']                   = '';
$_lang['seosuite.label_url_createdon']                          = 'Erstellt am';
$_lang['seosuite.label_url_createdon_desc']                     = '';
$_lang['seosuite.label_url_match_context']                      = 'Übereinstimmung mit der Domain [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']                 = 'Wenn diese Option aktiviert ist, erfolgt der Abgleich innerhalb der Domain [[+domain]]. Dies ist besonders nützlich, wenn Sie mehrere Websites betreiben, bei denen mehrere Kontexte viele gleiche Seiten enthalten.';
$_lang['seosuite.label_url_match_create_redirect']              = 'Automatisch eine URL-Umleitung erstellen';
$_lang['seosuite.label_url_match_create_redirect_desc']         = 'Erstellt automatisch eine URL-Umleitung, wenn ein Vorschlag gefunden wird.';

$_lang['seosuite.label_import_file']                            = 'Datei';
$_lang['seosuite.label_import_file_desc']                       = 'Wählen Sie eine gültige Datei aus, um die 404-URL(s) zu importieren.';
$_lang['seosuite.import.instructions']                          = 'Verwenden Sie eine .csv, .xls, oder .xlsx-Datei. Vergewissern Sie sich, dass Sie die vollständigen URLs einschließlich der Domain eingegeben haben. Beispiel: https://www.seosuite.com anstelle von seosuite.com. Sie können auch diese <a href="[[+path]]">Beispielimportdatei (XLS)</a> ansehen.';

$_lang['seosuite.redirect']                                     = 'URL Weiterleitung';
$_lang['seosuite.redirects']                                    = 'URL Weiterleitungen';
$_lang['seosuite.redirects_desc']                               = 'Verwalten Sie hier alle Ihre URL-Weiterleitungen. Eine URL-Weiterleitung wird verwendet, um eine nicht vorhandene Seite auf eine neue Seite weiterzuleiten. Eine URL-Weiterleitung kann für einen bestimmten Kontext oder für alle Kontexte auf einmal erfolgen und wird unten als "* /" angezeigt. URL-Weiterleitungen mit einem bestimmten Kontext haben Vorrang vor einer URL-Weiterleitung für alle Kontexte.';
$_lang['seosuite.redirect_create']                              = 'Neue URL Weiterleitung';
$_lang['seosuite.redirect_update']                              = 'URL Weiterleitung bearbeiten';
$_lang['seosuite.redirect_remove']                              = 'URL Weiterleitung löschen';
$_lang['seosuite.redirect_remove_confirm']                      = 'Sind Sie sicher, dass Sie diese URL-Umleitung entfernen möchten?';
$_lang['seosuite.redirects_remove']                             = 'URL-Weiterleitung(en) entfernen';
$_lang['seosuite.redirects_remove_confirm']                     = 'Sind Sie sicher, dass Sie die ausgewählte(n) URL-Umleitung(en) entfernen möchten?
';
$_lang['seosuite.use_redirect_across_domains']                  = 'Weiterleitung für alle Domains verwenden';

$_lang['seosuite.label_redirect_old_url']                       = 'Alte URL';
$_lang['seosuite.label_redirect_old_url_desc']                  = 'Die alte URL der Weiterleitung ohne den Domainnamen. Zum Beispiel: "/example".';
$_lang['seosuite.label_redirect_new_url']                       = 'Neue URL';
$_lang['seosuite.label_redirect_new_url_desc']                  = 'Die neue URL der Weiterleitung. Sie kann mit oder ohne Domain angegeben werden, kann aber auch die Ressourcen-ID enthalten.';
$_lang['seosuite.label_redirect_active']                        = 'Aktiv';
$_lang['seosuite.label_redirect_active_desc']                   = '';
$_lang['seosuite.label_redirect_match_context']                 = 'Kontext';
$_lang['seosuite.label_redirect_match_context_desc']            = 'Passen Sie die Weiterleitung an eine bestimmte Domain an oder verwenden Sie die Weiterleitung als Platzhalter für alle Domains.';
$_lang['seosuite.label_redirect_type']                          = 'Art der Umleitung';
$_lang['seosuite.label_redirect_type_desc']                     = 'Der Typ der Weiterleitung.';

$_lang['seosuite.error.emptyfile']                              = 'Keine Datei ausgewählt.';
$_lang['seosuite.error.extension_notallowed']                   = 'Der Dateityp ist nicht erlaubt. Nur .csv, .xls oder .xlsx Dateien sind erlaubt.';
$_lang['seosuite.error.ziparchive_notinstalled']                = 'Die PHP-Erweiterung ZipArchive ist nicht installiert, die für den Import von xls(x)-Dateien erforderlich ist. Bitte installieren Sie die ZipArchive-Erweiterung oder verwenden Sie eine .csv-Datei.';

$_lang['seosuite.friendly_urls_disabled']                       = 'Suchmaschinenfreundliche URLs sind derzeit nicht in dieser MODx-Installation aktiviert. Damit SEO Suite einwandfrei funktioniert, müssen die Suchmaschinenfreundliche URLs aktiviert werden.';
$_lang['seosuite.find_suggestions']                             = 'Vorschläge finden';
$_lang['seosuite.redirect_error_exists']                        = 'Für diese URL existiert bereits eine URL-Umleitung.';
$_lang['seosuite.resource_no_redirects']                        = 'Für diese Seite gibt es keine URL-Weiterleitungen.';
$_lang['seosuite.time_seconds']                                 = 'Vor weniger als 1 Minute';
$_lang['seosuite.time_minute']                                  = 'vor einer Minute';
$_lang['seosuite.time_minutes']                                 = 'vor [[+minutes]] Minuten';
$_lang['seosuite.time_hour']                                    = 'vor einer Stunde';
$_lang['seosuite.time_hours']                                   = 'vor [[+hours]] Stunden';
$_lang['seosuite.time_day']                                     = 'Vor einem Tag';
$_lang['seosuite.time_days']                                    = 'Vor [[+days]] Tagen';
$_lang['seosuite.time_week']                                    = 'Vor einer Woche';
$_lang['seosuite.time_weeks']                                   = 'Vor [[+weeks]] Wochen';
$_lang['seosuite.time_month']                                   = 'Vor einem Monat';
$_lang['seosuite.time_months']                                  = 'Vor [[+months]] Monaten';
$_lang['seosuite.time_to_long']                                 = 'Vor mehr als einem halben Jahr';

$_lang['seosuite.widget_desc']                                  = 'Hier können Sie die 10 zuletzt hinzugefügten 404-URLs sehen. Um alle Ihre 404-URLs anzuzeigen und zu verwalten, gehen Sie bitte auf folgende Seite: <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite</a>.';
