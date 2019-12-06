<?php

class SeoSuiteResourceDelete extends SeoSuitePlugin
{
    /*
     * @param $event
     * @param $properties
     */
    public function onEmptyTrash($event, $properties)
    {
        if (is_array($properties['resources'])) {
            foreach ($properties['resources'] as $resource) {
                $this->deleteSeoSuiteResource($resource);
            }
        }
    }

    /**
     * Delete SeoSuiteResource record for a specific resource.
     *
     * @param $resource
     */
    protected function deleteSeoSuiteResource($resource)
    {
        if ($resource && $ssResource = $this->modx->getObject('SeoSuiteResource', ['resource_id' => $resource->get('id')])) {
            $ssResource->remove();
        }
    }
}