<?php
use Sterc\SeoSuite\Snippets\Sitemap;

$sitemap = new Sitemap($modx);

return $sitemap->process($scriptProperties);
