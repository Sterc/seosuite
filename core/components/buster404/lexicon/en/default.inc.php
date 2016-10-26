<?php
/**
 * Default English Lexicon Entries for Buster404
 *
 * @package buster404
 * @subpackage lexicon
 */

$_lang['buster404'] = '404Buster';

$_lang['buster404.menu.buster404'] = '404Buster';
$_lang['buster404.menu.buster404_desc'] = 'Manage 404 URLs';

$_lang['buster404.global.search'] = 'Search';

$_lang['buster404.url.urls'] = '404 URLs';
$_lang['buster404.url.intro_msg'] = 'Found a 404 URL? Manage them here. Simply upload a .csv file and the 
404Buster will make sure the URLs are redirected on your website. It will be matched looking at the part 
after the last / in the URL. When there’s one match, it will be an automatic 301. If there’s several matches, 
you can choose the desired redirect. If there’s none, you can enter a URL yourself.';
$_lang['buster404.url.import'] = 'Import file';
$_lang['buster404.url.file'] = 'File';
$_lang['buster404.import.start'] = 'Starting with importing the URLs, this could take some time 
depending on the size of your file.';
$_lang['buster404.import.instructions'] = 'Use a .csv file. Make sure you’ve used the full URLs, including 
the domain.';

$_lang['buster404.url.url'] = '404 URL';
$_lang['buster404.url.solved'] = 'Solved';
$_lang['buster404.url.position'] = 'Position';
$_lang['buster404.url.redirect_to'] = 'Redirect to';
$_lang['buster404.url.suggestions'] = 'Redirect suggestions';
$_lang['buster404.url.find_suggestions'] = 'Find suggestions';
$_lang['buster404.url.found_suggestions'] = 'We found a suggestion and this one is now linked to this URL.';
$_lang['buster404.url.notfound_suggestions'] = 'We could not find any suggestion for this URL.';
$_lang['buster404.url.update'] = 'Update URL';
$_lang['buster404.url.remove'] = 'Remove URL';
$_lang['buster404.url.remove_confirm'] = 'Are you sure you want to remove this URL?';

$_lang['buster404.error.url_alreadyexists'] = 'That URL already exists.';
$_lang['buster404.error.url_notfound'] = 'Item not found.';
$_lang['buster404.err.item_name_ae'] = 'Item not found.';
$_lang['buster404.error.url_notspecified'] = 'URL is not specified.';
$_lang['buster404.err.item_name_ns'] = 'Value is not specified.';
$_lang['buster404.error.url_remove'] = 'An error occurred while trying to remove the URL.';
$_lang['buster404.error.url_save'] = 'An error occurred while trying to save the URL.';
$_lang['buster404.error.emptyfile'] = 'No file specified';
$_lang['buster404.error.extension_notallowed'] = 'Filetype not allowed. Only csv files are allowed.';

$_lang['buster404.import.seoUrl.error'] = 'We could not connect the suggestion to the SeoTab URL, 
please add this manually.';
$_lang['buster404.seotab.notfound'] = 'The package StercSEO is not installed. To automatically connect 
the suggestion page to a URL you should install this package.';
