---------------------------------------
SEO Suite
---------------------------------------
Author: Sterc <modx@sterc.com>
---------------------------------------

## What is SEO Suite?
SEO Suite was introduced by Sterc as a premium MODX Extra that automatically redirects your 404 URLs to relevant pages on your website. 404 errors are a fairly common issue for anyone who’s transitioning from an old website to a new website. SEO Suite makes sure your visitors are redirected to a relevant page when they’re looking for an old URL.

Through simply uploading a single column .csv file containing your 404 URLs, SEO Suite will look for similar pages on your website and redirect them automatically. This matching process is based on the URL information after the last slash (example.com/**this-information**).

## Features
* When someone visits a non-existent page (404) on your website, the URL will be automatically added to SEO Suite so you can redirect it to an existing page.
* SEO Suite now comes with a Dashboard widget, showing the 10 newest SEO Suite URL's
* It is possible to import a .csv file containing 404 URL's and then search for redirects inside only one (related) context. This comes in handy for multilingual websites.
* To get more specific redirect suggestions, you can exclude words from the matching system. **Pay attention:** when you add words to exclude **after** a .csv import, you might need to refresh the suggestions by clicking your right mouse button on the relevant 404 URL and choose 'Find suggestions'. After doing this, it will be refreshed.
* Block certain redirects from being saved by adding blocking key words to system settings.

## Workflow
1. Gather your 404 URLs in a single column .csv file by exporting them or adding them manually. Make sure you’ve entered full URLs, including the domain. Example: https://modx.org instead of modx.org.
2. Import the .csv file into SEO Suite.
3. SEO Suite will look for similarities between your 404 URLs and the pages on your website (make sure the pages are published):
   *  When there is one match, it will be automatically converted into a 301 redirect and stored in SEO Tab.
   *  When there are several matches, you can choose the desired redirect manually (by choosing from suggestions).
   *  When there are no matches, you can pick a redirect yourself (SEO Suite offers a search function so you can find a relevant redirect easily).

## Cleaning cronjob
Inside the `core/components/seosuite/elements/cronjobs/` directory you can find the SeoSuite cronjobs. It removes unresolved redirects which are older then 1 month and have been triggered just once.

Example usage:
```php redirect-cleanup.php --till=2018-11-23 --triggered=2```

Properties:
- till: Till date for unresolved redirects to remove (Default: Current date - 1 month)
- triggered: Maximum amount of triggers for the unresolved redirects you want to remove (Default: 1)

## Blocked words
Use the Exclude list within the Component to manage what keywords aren't allowed to be inside URL's. In other words: if one of the keywords in the 'Exclude list' are inside an 404 URL, SEO Suite will not add it. Useful keywords are things like 'wp-admin', 'administrator', 'plesk', etc.