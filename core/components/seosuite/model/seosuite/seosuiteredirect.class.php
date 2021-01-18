<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteRedirect extends xPDOSimpleObject
{
    /**
     * @access public.
     * @return String.
     */
    public function getRedirectUrl()
    {
        if (is_numeric($this->get('new_url'))) {
            $object = $this->xpdo->getObject('modResource', [
                'id' => $this->get('resource_id')
            ]);

            if ($object) {
                $this->xpdo->switchContext($object->get('context_key'));

                return $this->xpdo->makeUrl($this->get('new_url'));
            }
        }

        return $this->get('new_url');
    }

    /**
     * @access public.
     * @return String.
     */
    public function getOldSiteUrl()
    {
        if (!empty($this->get('resource_id'))) {
            $object = $this->xpdo->getObject('modResource', [
                'id' => $this->get('resource_id')
            ]);

            if ($object) {
                return $this->getSiteUrl($object->get('context_key'));
            }
        }

        return $this->getSiteUrl($this->get('context_key'));
    }

    /**
     * @access public.
     * @return String.
     */
    public function getNewSiteUrl()
    {
        if (is_numeric($this->get('new_url'))) {
            $object = $this->xpdo->getObject('modResource', [
                'id' => $this->get('new_url')
            ]);

            if ($object) {
                return $this->getSiteUrl($object->get('context_key'));
            }
        }

        return $this->getOldSiteUrl();
    }

    /**
     * @access private.
     * @param String $context.
     * @return String.
     */
    private function getSiteUrl($context)
    {
        if (!empty($context)) {
            $object = $this->xpdo->getContext($context);

            if ($object) {
                return $object->getOption('site_url');
            }
        }

        return '';
    }
}
