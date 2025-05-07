<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Processor;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;

/**
 * Processor to remove multiple suggestions.
 */
class RemoveMultiple extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $ids = $this->getProperty('ids', '');
        
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seosuite.error_no_suggestions_selected'));
        }
        
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        $count = 0;
        
        foreach ($ids as $id) {
            $suggestion = $this->modx->getObject(SeoSuiteSuggestion::class, $id);
            
            if ($suggestion) {
                if ($suggestion->remove()) {
                    $count++;
                }
            }
        }
        
        return $this->success($this->modx->lexicon('seosuite.suggestions_deleted', [
            'count' => $count
        ]));
    }
}
