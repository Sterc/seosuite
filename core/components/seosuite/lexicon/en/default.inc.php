<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                              = 'SEO Suite';
$_lang['seosuite.menu.seosuite']                                = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc']                           = 'Manage 404 URLs and redirects.';

$_lang['area_seosuite']                                         = 'SEO Suite';
$_lang['area_seosuite_tab_meta']                                = 'SEO Suite meta tab';
$_lang['area_seosuite_tab_seo']                                 = 'SEO Suite search engine tab';
$_lang['area_seosuite_tab_social']                              = 'SEO Suite social tab';
$_lang['area_seosuite_sitemap']                                 = 'SEO Suite sitemap';

$_lang['seosuite.urls']                                         = '404 URLs';
$_lang['seosuite.urls_desc']                                    = 'Fixing your 404 URLs is easy. Simply upload a one-column CSV-file.
 SEO Suite will make sure the URLs are redirected to a proper page on your website.
  They will be matched with existing pages on your website, based on the bold part of the example URL: https://example.tld/folder1/folder1/<strong>page-alias</strong>.
   SEO Suite will perform one of these actions:<br /><br />
   1. When there’s exactly one match, it will be automatically converted to a 301 redirect in SEO Tab, or, when SEO Tab is not installed, SEO Suite will handle the redirect;<br />
   2. When there are several matches, you can choose the desired redirect manually;<br />
   3. When there are no matches, you can enter a URL to redirect to yourself.';
$_lang['seosuite.urls_import']                                  = 'Import 404 url(s)';
$_lang['seosuite.urls_remove']                                  = 'Remove 404 URL(s)';
$_lang['seosuite.urls_remove_confirm']                          = 'Are you sure you want to remove the selected 404 URL(s)?';
$_lang['seosuite.url_update']                                   = 'Update 404 URL';
$_lang['seosuite.url_suggestions']                              = '404 URL suggestions';
$_lang['seosuite.url.found_suggestions']                        = 'A suggestion has been found! The suggestion is now connected to this URL.';
$_lang['seosuite.url.found_suggestions_multiple']               = 'More then 1 suggestion has been found. Please add a redirect manually.';
$_lang['seosuite.url.notfound_suggestions']                     = 'No suggestions have been found for this URL.';
$_lang['seosuite.url.found_suggestions.redirect_exists']        = 'A redirect already exists for this 404 page. The 404 URL has been removed.';
$_lang['seosuite.url_remove']                                   = 'Remove URL';
$_lang['seosuite.url_remove_confirm']                           = 'Are you sure you want to delete this URL?';
$_lang['seosuite.url.redirect_to_selected']                     = 'Selected redirect';
$_lang['seosuite.suggestions_found']                            = '[[+suggestions]] page suggestions have been found.';
$_lang['seosuite.suggestion_boost']                             = 'pts';
$_lang['seosuite.label_exclude_words']                          = 'Exclude words';
$_lang['seosuite.label_exclude_words_desc']                     = 'A comma delimited list of words to exclude from 404 URL suggestions.';

$_lang['seosuite.label_url_url']                                = 'Url';
$_lang['seosuite.label_url_url_desc']                           = '';
$_lang['seosuite.label_url_visits']                             = 'Hits';
$_lang['seosuite.label_url_visits_desc']                        = '';
$_lang['seosuite.label_url_last_visit']                         = 'Last hit';
$_lang['seosuite.label_url_last_visit_desc']                    = '';
$_lang['seosuite.label_url_suggestion']                         = 'Suggestion';
$_lang['seosuite.label_url_suggestion_desc']                    = 'The suggestion to redirect the URL to.';
$_lang['seosuite.label_url_suggestions']                        = 'Suggestions';
$_lang['seosuite.label_url_suggestions_desc']                   = '';
$_lang['seosuite.label_url_createdon']                          = 'Created on';
$_lang['seosuite.label_url_createdon_desc']                     = '';
$_lang['seosuite.label_url_match_context']                      = 'Match on domain [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']                 = 'If checked, matching will occur within domain [[+domain]]. This is particularly useful when having multiple websites where multiple contexts contain a lot of the same pages.';
$_lang['seosuite.label_url_match_create_redirect']              = 'Automatically create a URL redirect';
$_lang['seosuite.label_url_match_create_redirect_desc']         = 'Will automatically create a URL redirect if a suggestion is found.';

$_lang['seosuite.label_import_file']                            = 'File';
$_lang['seosuite.label_import_file_desc']                       = 'Select a valid file to import the 404 URL(s).';
$_lang['seosuite.import.instructions']                          = 'Use a .csv, .xls or .xlsx file. Make sure you’ve entered full URLs, including the domain. Example: https://www.seosuite.com instead of seosuite.com. You can also view this <a href="[[+path]]">example import file (XLS)</a>.';

$_lang['seosuite.redirect']                                     = 'URL redirect';
$_lang['seosuite.redirects']                                    = 'URL redirects';
$_lang['seosuite.redirects_desc']                               = 'Manage all your URL redirects here.';
$_lang['seosuite.redirect_create']                              = 'New URL redirect';
$_lang['seosuite.redirect_update']                              = 'Update URL redirect';
$_lang['seosuite.redirect_remove']                              = 'Remove URL redirect';
$_lang['seosuite.redirect_remove_confirm']                      = 'Are you sure you want to remove this URL redirection?';
$_lang['seosuite.redirects_remove']                             = 'Remove URL redirect(s)';
$_lang['seosuite.redirects_remove_confirm']                     = 'Are you sure you want to remove the selected URL redirect(s)?';
$_lang['seosuite.use_redirect_across_domains']                  = 'Use redirect for all domains';

$_lang['seosuite.label_redirect_old_url']                       = 'Old URL';
$_lang['seosuite.label_redirect_old_url_desc']                  = 'The old URL of the URL redirect.';
$_lang['seosuite.label_redirect_new_url']                       = 'New URL';
$_lang['seosuite.label_redirect_new_url_desc']                  = 'The new URL of the URL redirection. This can be with or without a domain, but can also contain the ID of the resource.';
$_lang['seosuite.label_redirect_active']                        = 'Active';
$_lang['seosuite.label_redirect_active_desc']                   = '';
$_lang['seosuite.label_redirect_match_context']                 = 'Context';
$_lang['seosuite.label_redirect_match_context_desc']            = 'Match the redirect to a specific domain or use the redirect as a wildcard for all domains.';
$_lang['seosuite.label_redirect_type']                          = 'Redirect type';
$_lang['seosuite.label_redirect_type_desc']                     = 'The redirect type of the URL redirection.';

$_lang['seosuite.error.emptyfile']                              = 'No file specified.';
$_lang['seosuite.error.extension_notallowed']                   = 'Filetype not allowed. Only .csv, .xls or .xlsx files are allowed.';
$_lang['seosuite.error.ziparchive_notinstalled']                = 'PHP extension ZipArchive is not installed, which is needed to be able to import xls(x) files. Please install the ZipArchive extension or use a .csv file.';

$_lang['seosuite.find_suggestions']                             = 'Find suggestions';
$_lang['seosuite.redirect_error_exists']                        = 'An URL redirect already exists for this URL.';
$_lang['seosuite.resource_no_redirects']                        = 'There are no URL redirects for this page.';
$_lang['seosuite.time_seconds']                                 = 'Less then 1 minute ago';
$_lang['seosuite.time_minute']                                  = '1 minute ago';
$_lang['seosuite.time_minutes']                                 = '[[+minutes]] minutes ago';
$_lang['seosuite.time_hour']                                    = '1 hour ago';
$_lang['seosuite.time_hours']                                   = '[[+hours]] hours ago';
$_lang['seosuite.time_day']                                     = '1 day ago';
$_lang['seosuite.time_days']                                    = '[[+days]] days ago';
$_lang['seosuite.time_week']                                    = '1 week ago';
$_lang['seosuite.time_weeks']                                   = '[[+weeks]] weeks ago';
$_lang['seosuite.time_month']                                   = '1 month ago';
$_lang['seosuite.time_months']                                  = '[[+months]] months ago';
$_lang['seosuite.time_to_long']                                 = 'More then a half year ago';

$_lang['seosuite.widget_desc']                                  = 'Here you can view the 10 most recently added 404 urls. To view and manage all your 404 urls, please visit the <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite manager page.</a>';
