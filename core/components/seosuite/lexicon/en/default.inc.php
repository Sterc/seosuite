<?php
/**
 * Default English Lexicon Entries for SEO Suite
 *
 * @package seosuite
 * @subpackage lexicon
 */

$_lang['seosuite'] = 'SEO Suite';

$_lang['seosuite.menu.seosuite'] = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc'] = 'Manage 404 URLs';

$_lang['seosuite.global.search'] = 'Search';

$_lang['seosuite.url.urls'] = '404 URLs';
$_lang['seosuite.url.intro_msg'] = 'Found a 404 URL? Manage them here. Simply upload a .csv file and the 
SEO Suite will make sure the URLs are redirected on your website. It will be matched looking at the part 
after the last / in the URL. When there’s one match, it will be an automatic 301. If there’s several matches, 
you can choose the desired redirect. If there’s none, you can enter a URL yourself.';
$_lang['seosuite.url.import'] = 'Import file';
$_lang['seosuite.url.file'] = 'File';
$_lang['seosuite.import.start'] = 'Starting with importing the URLs, this could take some time 
depending on the size of your file.';
$_lang['seosuite.import.instructions'] = 'Use a .csv, .xls or .xlsx file. Make sure you’ve used the full URLs, 
including the domain.';

$_lang['seosuite.url.url'] = '404 URL';
$_lang['seosuite.url.solved'] = 'Solved';
$_lang['seosuite.url.position'] = 'Position';
$_lang['seosuite.url.redirect_to'] = 'Redirect to';
$_lang['seosuite.url.suggestions'] = 'Redirect suggestions';
$_lang['seosuite.url.find_suggestions'] = 'Find suggestions';
$_lang['seosuite.url.found_suggestions'] = 'We found a suggestion and this one is now linked to this URL.';
$_lang['seosuite.url.notfound_suggestions'] = 'We could not find any suggestion for this URL.';
$_lang['seosuite.url.update'] = 'Update URL';
$_lang['seosuite.url.remove'] = 'Remove URL';
$_lang['seosuite.url.remove_confirm'] = 'Are you sure you want to remove this URL?';

$_lang['seosuite.error.url_alreadyexists'] = 'That URL already exists.';
$_lang['seosuite.error.url_notfound'] = 'Item not found.';
$_lang['seosuite.err.item_name_ae'] = 'Item not found.';
$_lang['seosuite.error.url_notspecified'] = 'URL is not specified.';
$_lang['seosuite.err.item_name_ns'] = 'Value is not specified.';
$_lang['seosuite.error.url_remove'] = 'An error occurred while trying to remove the URL.';
$_lang['seosuite.error.url_save'] = 'An error occurred while trying to save the URL.';
$_lang['seosuite.error.emptyfile'] = 'No file specified';
$_lang['seosuite.error.extension_notallowed'] = 'Filetype not allowed. Only csv files are allowed.';
$_lang['seosuite.error.ziparchive_notinstalled'] = 'PHP extension ZipArchive is not installed, 
which is needed to be able to import xls(x) files. Please install the ZipArchive extension, or use a .csv file.';

$_lang['seosuite.import.seoUrl.error'] = 'The found suggestion could not be automatically saved as redirect. 
Please add it manually.';
$_lang['seosuite.import.seoUrl.error'] = 'We could not connect the suggestion to the Seo Tab URL, 
please add it manually.';
$_lang['seosuite.seotab.notfound'] = 'Seo Tab is not installed or invalid version. 
To be able to (automatically) add redirects for 404 URLs you should install Seo Tab (version 2+).';
$_lang['seosuite.seotab.versioninvalid'] = 'Your version of Seo Tab is outdated. 
Please install Seo Tab version 2.0 (or higher) for the redirects to work properly.';
