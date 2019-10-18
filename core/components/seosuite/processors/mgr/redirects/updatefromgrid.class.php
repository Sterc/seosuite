<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

require_once __DIR__ . '/update.class.php';

class SeoSuiteRedirectUpdateFromGridProcessor extends SeoSuiteRedirectUpdateProcessor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $data = $this->getProperty('data');

        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data = $this->modx->fromJSON($data);

        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $this->setProperties($data);

        $this->unsetProperty('data');

        return parent::initialize();
    }
}

return 'SeoSuiteRedirectUpdateFromGridProcessor';
