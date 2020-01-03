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
$_lang['seosuite.url.intro_msg'] = 'Fixing your 404 URLs is easy. Simply upload a one-column CSV-file.
 SEO Suite will make sure the URLs are redirected to a proper page on your website.
  They will be matched with existing pages on your website, based on the bold part of the example URL: https://example.tld/folder1/folder1/<strong>page-alias</strong>.
   SEO Suite will perform one of these actions:<br /><br />
   1. When there’s exactly one match, it will be automatically converted to a 301 redirect in SEO Tab, or, when SEO Tab is not installed, SEO Suite will handle the redirect;<br />
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

/* Keywords. */
$_lang['seosuite.characters']                      = 'Characters';


$_lang['seosuite.emptymetadescription']            = '<i>Please enter a description</i>';
$_lang['seosuite.branding_text']                   = 'This site is optimized with the Sterc SeoSuite plugin - https://github.com/Sterc/seosuite.';

/* Areas. */
$_lang['area_general']  = 'General';
$_lang['area_meta']     = 'Meta';
$_lang['area_sitemap']  = 'Sitemap';

/* Settings. */
$_lang['setting_seosuite.meta.counter_fields']                    = 'Fields where the keywords tool will work on';
$_lang['setting_seosuite.meta.counter_fields_desc']               = 'Only change these if you know what you\'re doing. Default value: longtitle:30|70,description:70|155,content.';
$_lang['setting_seosuite.meta.searchengine']                      = 'Search engine used for the preview.';
$_lang['setting_seosuite.meta.searchengine_desc']                 = 'Possible values: google, yandex. Defaults to google.';
$_lang['setting_seosuite.meta.disabled_templates']                = 'Disabled Templates';
$_lang['setting_seosuite.meta.disabled_templates_desc']           = 'Comma separated list of disabled template IDs.';
$_lang['setting_seosuite.meta.default_meta_title']                = 'Format used for the title.';
$_lang['setting_seosuite.meta.default_meta_title_desc']           = 'Here you can specify the format for the title used in Google/Yandex preview.';
$_lang['setting_seosuite.meta.default_meta_description']          = 'Format used for the META description.';
$_lang['setting_seosuite.meta.default_meta_description_desc']     = 'Here you can specify the format for the META description used in Google/Yandex preview.';
$_lang['setting_seosuite.meta.max_keywords_title']                = 'Max amount of focus keywords allowed in title.';
$_lang['setting_seosuite.meta.max_keywords_title_desc']           ='Use this setting to control the maximum amount of focus keywords that are allowed in the title to get a thumbs-up. It is advised to use no more than 4 keywords in your title.';
$_lang['setting_seosuite.meta.max_keywords_description']          = 'Max amount of focus keywords allowed in description.';
$_lang['setting_seosuite.meta.max_keywords_description_desc']     = 'Use this setting to control the maximum amount of focus keywords that are allowed in the description to get a thumbs-up. It is advised to use no more than 8 keywords in your description.';
$_lang['setting_seosuite.sitemap.default_changefreq']             = 'Default resource setting: update frequency';
$_lang['setting_seosuite.sitemap.default_changefreq_desc']        = 'Default frequency (daily, weekly, monthly).';
$_lang['setting_seosuite.sitemap.default_priority']               = 'Default resource setting: priority';
$_lang['setting_seosuite.sitemap.default_priority_desc']          = 'Priority of page in sitemap.xml (0.25 or 0.5 or 1).';
$_lang['setting_seosuite.sitemap.dependent_ultimateparent']       = 'Resources depend on properties of parent/ultimate parent';
$_lang['setting_seosuite.sitemap.dependent_ultimateparent_desc']  = 'If turned on, resources will be hidden from the XML sitemap if their parent or ultimate parent resource is deleted or unpublished.';
$_lang['setting_seosuite.sitemap.babel.add_alternate_links']      = 'Add alternate links to XML Sitemap';
$_lang['setting_seosuite.sitemap.babel.add_alternate_links_desc'] = 'Adds alternate links to XML Sitemap URLs based on Babel translations.';
