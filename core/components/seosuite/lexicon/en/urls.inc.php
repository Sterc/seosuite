<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite.urls']                                  = '404 URLs';
$_lang['seosuite.urls_desc']                             = 'Manage 404 URLs and create redirects.';
$_lang['seosuite.urls_import']                           = 'Import URLs';
$_lang['seosuite.urls_remove']                           = 'Remove URL';
$_lang['seosuite.urls_remove_confirm']                   = 'Are you sure you want to remove this URL?';
$_lang['seosuite.urls_cleaned']                          = 'Cleaned %s invalid URLs out of %s total URLs.';
$_lang['seosuite.urls_clean_invalid']                    = 'Clean Invalid URLs';
$_lang['seosuite.urls_clean_invalid_confirm']            = 'Are you sure you want to clean invalid URLs? This will remove all URLs that match common hack/break attempt patterns.';
$_lang['seosuite.urls_generate_suggestions']             = 'Generate AI Suggestions';
$_lang['seosuite.urls_generate_suggestions_desc']        = 'Generate redirect suggestions for 404 URLs using AI.';
$_lang['seosuite.urls_generate_suggestions_confirm']     = 'Are you sure you want to generate AI suggestions for the selected URLs?';
$_lang['seosuite.urls_generate_suggestions_all']         = 'Generate AI Suggestions for All URLs';
$_lang['seosuite.urls_generate_suggestions_all_confirm'] = 'Are you sure you want to generate AI suggestions for all URLs? This may take some time.';
$_lang['seosuite.urls_create_redirects']                 = 'Create Redirects from Suggestions';
$_lang['seosuite.urls_create_redirects_confirm']         = 'Are you sure you want to create redirects from the suggestions? This will create a redirect for each URL using the best suggestion.';

$_lang['seosuite.label_url_url']                         = 'URL';
$_lang['seosuite.label_url_suggestions']                 = 'Suggestions';
$_lang['seosuite.label_url_visits']                      = 'Visits';
$_lang['seosuite.label_url_last_visit']                  = 'Last visit';
$_lang['seosuite.label_url_createdon']                   = 'Created on';
$_lang['seosuite.label_url_suggestion']                  = 'Suggestion';
$_lang['seosuite.label_url_suggestion_desc']             = 'Select a suggestion to use for the redirect.';
$_lang['seosuite.label_url_match_context']               = 'Match context for [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']          = 'Only find suggestions in the same context as the URL.';
$_lang['seosuite.label_url_match_create_redirect']       = 'Create redirect';
$_lang['seosuite.label_url_match_create_redirect_desc']  = 'Create a redirect for the URL if a suggestion is found.';
$_lang['seosuite.label_url_redirect_type']               = 'Redirect type';
$_lang['seosuite.label_url_redirect_type_desc']          = 'Select the type of redirect to create.';
$_lang['seosuite.label_import_file']                     = 'File';
$_lang['seosuite.label_import_file_desc']                = 'Select a file to import URLs from.';

$_lang['seosuite.url_remove']                            = 'Remove URL';
$_lang['seosuite.url_suggesstions']                      = 'URL suggestions';
$_lang['seosuite.url_suggestions']                       = 'URL suggestions';
$_lang['seosuite.find_suggestions']                      = 'Find suggestions';

$_lang['seosuite.exclude_words']                         = 'Exclude words';
$_lang['seosuite.label_exclude_words']                   = 'Exclude words';
$_lang['seosuite.label_exclude_words_desc']              = 'Enter words to exclude from suggestions, separated by commas.';

$_lang['seosuite.blocked_words']                         = 'Blocked words';
$_lang['seosuite.label_blocked_words']                   = 'Blocked words';
$_lang['seosuite.label_blocked_words_desc']              = 'Enter words to block from URLs, separated by commas. URLs containing these words will be automatically removed when cleaning invalid URLs.';

$_lang['seosuite.import.instructions']                   = 'Upload a .csv, .xlsx or .xls file with the URLs. <a href="[[+path]]">Download an example file</a>.';

$_lang['seosuite.ai_error_no_urls']                      = 'No URLs found to process.';
$_lang['seosuite.ai_suggestions_generated']              = 'Generated suggestions for [[+total]] URLs: [[+success]] successful, [[+existing]] already had suggestions, [[+no_match]] had no matches. Created [[+redirects]] redirects.';

$_lang['seosuite.time_seconds']                          = 'less than a minute ago';
$_lang['seosuite.time_minute']                           = 'about a minute ago';
$_lang['seosuite.time_minutes']                          = '[[+minutes]] minutes ago';
$_lang['seosuite.time_hour']                             = 'about an hour ago';
$_lang['seosuite.time_hours']                            = '[[+hours]] hours ago';
$_lang['seosuite.time_day']                              = 'a day ago';
$_lang['seosuite.time_days']                             = '[[+days]] days ago';
$_lang['seosuite.time_week']                             = 'a week ago';
$_lang['seosuite.time_weeks']                            = '[[+weeks]] weeks ago';
$_lang['seosuite.time_month']                            = 'a month ago';
$_lang['seosuite.time_months']                           = '[[+months]] months ago';
$_lang['seosuite.time_to_long']                          = 'too long ago';
