# Important! Currently we are working on SEOSuite V2 #
The repo can be found here: https://github.com/Sterc/seosuite

## SEO Suite
[SEO Suite][1] was introduced by [Sterc][4] as a premium MODX Extra that automatically redirects your 404 URLs to relevant pages on your website. 404 errors are a fairly common issue for anyone who’s transitioning from an old website to a new website. SEO Suite makes sure your visitors are redirected to a relevant page when they’re looking for an old URL.

Through simply uploading a single column .csv file containing your 404 URLs, SEO Suite will look for similar pages on your website and redirect them automatically. This matching process is based on the URL information after the last slash (example.com/**this-information**).

## Workflow
1. Gather your 404 URLs in a single column .csv file by exporting them or adding them manually. Make sure you’ve entered full URLs, including the domain. Example: https://modx.org instead of modx.org.
2. Import the .csv file into SEO Suite.
3. SEO Suite will look for similarities between your 404 URLs and the pages on your website (make sure the pages are published):
   *  When there is one match, it will be automatically converted into a 301 redirect and stored in SEO Tab.
   *  When there are several matches, you can choose the desired redirect manually (by choosing from suggestions).
   *  When there are no matches, you can pick a redirect yourself (SEO Suite offers a search function so you can find a relevant redirect easily).

## Requirements
To make sure that SEO Suite functions properly, the following requirements should be met:
-  [SEO Tab][2] (version 2.0 or newer) has to be installed. This is where the redirects of your 404 URLs will be stored.
-  [MODX version 2.5.0 or newer][3] has to be installed. 

## Cleanup cronjob
Inside the `core/components/seosuite/elements/cronjobs/` directory you can find the SeoSuite cronjob. It removes unresolved redirects which are older then 1 month and have been triggered just once.
   
Example usage:  

```php redirect-cleanup.php --till=2018-11-23 --triggered=2```

File: redirect-cleanup.php

| Property  | Description                                                                 | Default value          |
|-----------|-----------------------------------------------------------------------------|------------------------|
| till      | Till date for unresolved redirects to remove.                               | Current date - 1 month |
| triggered | Maximum amount of triggers for the unresolved redirects you want to remove. | 1                      |

## Features
* When someone visits a non-existent page (404) on your website, the URL will be automatically added to SEO Suite so you can redirect it to an existing page.
* SEO Suite now comes with a Dashboard widget, showing the 10 newest SEO Suite URL's
* It is possible to import a .csv file containing 404 URL's and then search for redirects inside only one (related) context. This comes in handy for multilingual websites.
* To get more specific redirect suggestions, you can exclude words from the matching system. **Pay attention:** when you add words to exclude **after** a .csv import, you might need to refresh the suggestions by clicking your right mouse button on the relevant 404 URL and choose 'Find suggestions'. After doing this, it will be refreshed.
* Block certain redirects from being saved by adding blocking key words to system settings.

## Future features
* 301 redirect statistics: SEO Suite will feature a custom manager page containing 301 redirects statistics.
A dashboard widget will be provided which shows the 10 redirects with the most hits.
* Automatically import 404's from Google Search Console.

## Bugs and feature requests
We greatly value your feedback, feature requests and bug reports. Please email them to modx@sterc.com.

[1]: https://www.sterc.com/modx/extras/seosuite
[2]: https://www.sterc.com/modx/extras/seotab
[3]: https://modx.com/download
[4]: https://www.sterc.com
