<?php
/**
 * Plugin for SEO Suite for handling the redirects
 */

switch ($modx->event->name) {
    case 'OnPageNotFound':
        $url = $modx->getOption('server_protocol').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $redirectObject = $modx->getObject(
            'SeoSuiteUrl',
            array(
                'url' => $url,
                'redirect_to:!=' => 0,
                'redirect_handler' => 1
            )
        );
        if ($redirectObject) {
            $redirectUrl = $modx->makeUrl($redirectObject->get('redirect_to'), '', '', 'full');
            $modx->sendRedirect($redirectUrl, 0, 'REDIRECT_HEADER', 'HTTP/1.1 301 Moved Permanently');
        }
        break;
}