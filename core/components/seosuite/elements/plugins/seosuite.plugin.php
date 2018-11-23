<?php
/**
 * Plugin for SEO Suite for handling the redirects
 */

$corePath = $modx->getOption(
    'seosuite.core_path',
    null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/seosuite/'
);
$seoSuite = $modx->getService(
    'seosuite',
    'SeoSuite',
    $corePath . 'model/seosuite/',
    array(
        'core_path' => $corePath
    )
);
if (!($seoSuite instanceof SeoSuite)) {
    return;
}

switch ($modx->event->name) {
    case 'OnPageNotFound':
        $redirectUrl = false;
        $url = $modx->getOption('server_protocol').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $redirectObject = $modx->getObject('SeoSuiteUrl', array('url' => $url));
        if ($redirectObject) {
            /* Only create redirectUrl when handler is 1 (SeoSuite) */
            if ($redirectObject->get('redirect_to') !== 0 && $redirectObject->get('redirect_handler') === 1) {
                $count = (int) $redirectObject->get('triggered_count');
                
                $redirectObject->set('last_triggered', time());
                $redirectObject->set('triggered_count', ++$count);
                $redirectObject->save();
                
                $redirectUrl = $modx->makeUrl($redirectObject->get('redirect_to'), '', '', 'full');
            }
        } else {
            /* Create new SeoSuiteUrl object, and try to find matches */
            /* When one redirect match is found, redirect to that page */
            $suggestions      = '';
            $redirect_to      = 0;
            $solved           = 0;
            $redirect_handler = 0;
            $findSuggestions  = $seoSuite->findRedirectSuggestions($url);
            if (count($findSuggestions)) {
                if (count($findSuggestions) === 1) {
                    $redirect_to = $findSuggestions[0];
                    $solved = 1;

                    if (!$seoSuite->checkSeoTab()) {
                        $redirect_handler = 1;
                    } else {
                        $seoSuite->addSeoTabRedirect($url, $findSuggestions[0]);
                    }
                }
                $suggestions = json_encode(array_values($findSuggestions));
            }

            $modx->exec(
                "INSERT INTO {$modx->getTableName('SeoSuiteUrl')}
                SET {$modx->escape('url')} = {$modx->quote($url)},
                    {$modx->escape('suggestions')} = {$modx->quote($suggestions)},
                    {$modx->escape('redirect_to')} = {$modx->quote($redirect_to)},
                    {$modx->escape('redirect_handler')} = {$modx->quote($redirect_handler)},
                    {$modx->escape('solved')} = {$modx->quote($solved)}"
            );
            
            if ($redirect_to) {
                $redirectUrl = $url;
            }
        }

        if ($redirectUrl) {
            $modx->sendRedirect($redirectUrl, 0, 'REDIRECT_HEADER', 'HTTP/1.1 301 Moved Permanently');
        }
        break;
}