# SEO Suite
SEO Suite is a MODX premium Extra that helps you automate the redirection of 404 URLs to matching pages on your website by simply uploading a .csv file!

## Documentation
Fixing your 404 URLs is easy. Simply upload a one-column CSV-file. SEO Suite will make sure the URLs are redirected to a proper page on your website. They will be matched with existing pages on your website, based on the bold part of the example URL: https://example.tld/folder1/folder1/page-alias. SEO Suite will perform one of these actions:

1. When there’s one match, it will be automatically converted to a 301 redirect in SEO Tab;
2. When there are several matches, you can choose the desired redirect manually;
3. When there are no matches, you can enter a URL to redirect to yourself.

## Roadmap
1.0.0 - CSV import and automatic 404 solving
1.1.0 - Automatically track 404 pages on your MODX website and add them to SEO Suite to solve them. A dashboard widget will be provided which shows the 10 most recent generated 404 pages.
1.2.0 - 301 redirect stats. SEO Suite will feature a custom manager page containing 301-redirects stats. A dashboard widget will be provided which shows the 10 redirects with the most hits.
1.3.0 - Automatically import 404's from Google Search Console
