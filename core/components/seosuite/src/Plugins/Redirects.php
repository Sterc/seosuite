<?php
namespace Sterc\SeoSuite\Plugins;

use Sterc\SeoSuite\Plugins\Base;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;
use Sterc\SeoSuite\Model\SeoSuiteUrl;

class Redirects extends Base
{
    /**
     * @access public.
     */
    public function onPageNotFound()
    {
        $fullUrl = $this->getFullUrl();
        $alias = $this->getPageAlias();
        if (!empty($fullUrl)) {
            $this->redirect($fullUrl, $alias);
        }
    }

    /**
     * Redirect or log the 404 request.
     *
     * @access protected.
     * @param String $request.
     */
    protected function redirect(string $request, $alias = '')
    {
        $criteria = $this->modx->newQuery(SeoSuiteRedirect::class, [
            'active' => 1,
            [
                'old_url'      => $request,
                'OR:old_url:=' => $alias,
            ],
        ]);

        $criteria->sortby('context_key', 'DESC');

        foreach ($this->modx->getIterator(SeoSuiteRedirect::class, $criteria) as $redirect) {
            $redirect->set('visits', (int) $redirect->get('visits') + 1);
            $redirect->set('last_visit', date('Y-m-d H:i:s'));
            $redirect->set('old_url', $request);

            if ($redirect->save()) {
                if (is_numeric($redirect->get('new_url'))) {
                    $url = $this->modx->makeUrl($redirect->get('new_url'), '', '', 'full');
                } else {
                    $url = $redirect->get('new_url');
                }

                if (str_starts_with($url, 'www')) {
                    $url = 'https://' . $url;
                }

                if (!empty($url)) {
                    $this->modx->sendRedirect($url, [
                        'responseCode' => $redirect->get('redirect_type') ?: 'HTTP/1.1 301 Moved Permanently'
                    ]);
                }
            }
        }

        if ($this->shouldLog404($request)) {
            $url = $this->modx->getObject(SeoSuiteUrl::class, [
                'url' => $request
            ]);

            if (!$url) {
                $url = $this->modx->newObject(SeoSuiteUrl::class);
            }

            if ($url) {
                $url->fromArray([
                    'url'         => $request,
                    'visits'      => (int) $url->get('visits') + 1,
                    'last_visit'  => date('Y-m-d H:i:s')
                ]);

                $url->save();
            }
        }
    }

    /**
     * Check if the 404 request should be logged.
     *
     * Checks for SQL injection via regex.
     * Checks if url contains words which are set in system_setting.
     *
     * @access protected.
     * @param String $request.
     * @return Bool.
     */
    protected function shouldLog404($request)
    {
        $url = $this->seosuite->serverProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (!preg_match('/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+$/', $url)) {
            return false;
        }

        $blockedWords = $this->seosuite->config['blocked_words'];

        if (count($blockedWords) > 0) {
            foreach ($blockedWords as $word) {
                if (strpos($request, $word) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function getFullUrl(): string
    {
        $protocol = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://';
        $host     = $_SERVER['HTTP_HOST'];
        $resource  = $_SERVER['REQUEST_URI'];

        return $protocol . $host . $resource;
    }

    private function getPageAlias(): string
    {
        $aliasParam = $this->modx->getOption('request_param_alias', null, 'q');
        return urldecode(trim($_REQUEST[$aliasParam], '/'));
    }
}
