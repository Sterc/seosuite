<?php
/**
 * Default English Lexicon Entries for SEO Suite
 *
 * @package seosuite
 * @subpackage lexicon
 */

$_lang['seosuite'] = 'SEO Suite';

$_lang['seosuite.menu.seosuite'] = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc'] = 'Manage bulk 404 URLs.';

$_lang['seosuite.global.search'] = 'Search';

$_lang['seosuite.url.urls'] = '404 URLs';
$_lang['seosuite.url.intro_msg'] = '404 errors are bothersome, for both the online user and Google, and can keep valuable traffic from your website. The MODX Extra \'SEO Suite\' quickly resolves these 404 errors. 
 All you have to do is upload a file containing all the errors, and SEO Suite will automatically search for matches within existing pages:<br/><br/>
   1. When there’s exactly one match, SEO Suite will automatically handle the redirect;<br />
   2. When there are several matches, you can choose the desired redirect manually;<br />
   3. When there are no matches, you can enter a URL to redirect to yourself.';
$_lang['seosuite.url.import'] = 'Import file';
$_lang['seosuite.url.file'] = 'File';
$_lang['seosuite.import.start'] = 'Starting with importing the URLs, this could take some time 
depending on the size of your file.';
$_lang['seosuite.import.instructions'] = 'Use a .csv, .xls or .xlsx file. Make sure you’ve entered full URLs, 
including the domain. Example: https://modx.org instead of modx.org.';

$_lang['seosuite.url.url'] = '404 URL';
$_lang['seosuite.url.solved'] = 'Solved';
$_lang['seosuite.url.position'] = 'Position';
$_lang['seosuite.url.redirect_to'] = 'Redirect to';
$_lang['seosuite.url.suggestions'] = 'Redirect suggestions';
$_lang['seosuite.url.find_suggestions'] = 'Find suggestions';
$_lang['seosuite.url.triggered'] = '404 Fired';
$_lang['seosuite.url.createdon'] = '404 Created';
$_lang['seosuite.url.found_suggestions'] = 'We found a match! It is now linked to this URL.';
$_lang['seosuite.url.found_suggestions_multiple'] = 'Found more than one redirect match.
 Please add a redirect manually';
$_lang['seosuite.url.notfound_suggestions'] = 'We could not find any matches for this URL.';
$_lang['seosuite.url.update'] = 'Update URL';
$_lang['seosuite.url.remove'] = 'Remove URL';
$_lang['seosuite.url.remove_confirm'] = 'Are you sure you want to remove this URL?';
$_lang['seosuite.url.choose_suggestion'] = 'Choose from suggestions';
$_lang['seosuite.url.choose_manually'] = 'Choose resource manually';
$_lang['seosuite.url.redirect_to_selected'] = 'Your selected redirect';

$_lang['seosuite.error.url_alreadyexists'] = 'That URL already exists.';
$_lang['seosuite.error.url_notfound'] = 'Item not found.';
$_lang['seosuite.err.item_name_ae'] = 'Item not found.';
$_lang['seosuite.error.url_notspecified'] = 'URL is not specified.';
$_lang['seosuite.err.item_name_ns'] = 'Value is not specified.';
$_lang['seosuite.error.url_remove'] = 'An error occurred while trying to remove the URL.';
$_lang['seosuite.error.url_save'] = 'An error occurred while trying to save the URL.';
$_lang['seosuite.error.emptyfile'] = 'No file specified.';
$_lang['seosuite.error.extension_notallowed'] = 'Filetype not allowed. Only .csv, .xls or .xlsx files are allowed.';
$_lang['seosuite.error.ziparchive_notinstalled'] = 'PHP extension ZipArchive is not installed, 
which is needed to be able to import xls(x) files. Please install the ZipArchive extension or use a .csv file.';

$_lang['seosuite.import.seoUrl.error'] = 'The URL suggestion could not be automatically saved as redirect. 
Please add it manually.';
$_lang['seosuite.import.seoUrl.error'] = 'We could not connect the URL suggestion to the SEO Tab URL, 
please add it manually.';
$_lang['seosuite.seotab.notfound'] = 'SEO Tab is not installed or the version is invalid. 
To be able to (automatically) add redirects for 404 URLs, please install SEO Tab (version 2.0 or newer).';
$_lang['seosuite.seotab.versioninvalid'] = 'Your version of SEO Tab is outdated. 
Please install SEO Tab version 2.0 (or newer) for the redirects to work properly.';

$_lang['seosuite.widget_desc'] = 'Here you can view the 10 most recently added 404 urls.
 To view and manage all your 404 urls, please visit the <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite manager page.</a>';

$_lang['seosuite.match_site_url'] = 'Match context site url';
$_lang['seosuite.match_site_url_desc'] = 'Should the matching system only match pages within the same context.
 Useful for when you have a multi-language website in multiple contexts with a lot of the same pages.';

$_lang['setting_seosuite.exclude_words'] = 'Exclude words';
$_lang['setting_seosuite.exclude_words_desc'] = 'Comma separated list of words which will be excluded from the suggestions matching system.';
