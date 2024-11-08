<?php
namespace Sterc\SeoSuite\Model;

use xPDO\xPDO;
use modResource;
use xPDO\Om\xPDOQuery;

/**
 * Class SeoSuiteUrl
 *
 * @property string $context_key
 * @property string $url
 * @property array $suggestions
 * @property int $visits
 * @property string $last_visit
 * @property string $createdon
 *
 * @package Sterc\SeoSuite\Model
 */
class SeoSuiteUrl extends \xPDO\Om\xPDOSimpleObject
{
    /**
     * @deprecated 4.0.0 No longer used by internal code and not recommended. The url field should contain full url.
     * @var string $context_key
     */
    private string $context_key;

    /**
     * @access public.
     * @return String.
     */
    public function getTimeAgo()
    {
        $timestamp = $this->get('last_visit');

        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $days    = floor((time() - $timestamp) / 86400);
        $minutes = floor((time() - $timestamp) / 60);

        $output = [
            'minutes'   => $minutes,
            'hours'     => ceil($minutes / 60),
            'days'      => $days,
            'weeks'     => ceil($days / 7),
            'months'    => ceil($days / 30)
        ];

        if ($days < 1) {
            if ($minutes < 1) {
                return $this->xpdo->lexicon('seosuite.time_seconds', $output);
            }

            if ($minutes === 1) {
                return $this->xpdo->lexicon('seosuite.time_minute', $output);
            }

            if ($minutes <= 59) {
                return $this->xpdo->lexicon('seosuite.time_minutes', $output);
            }

            if ($minutes === 60) {
                return $this->xpdo->lexicon('seosuite.time_hour', $output);
            }

            if ($minutes <= 1380) {
                return $this->xpdo->lexicon('seosuite.time_hours', $output);
            }

            return $this->xpdo->lexicon('seosuite.time_day', $output);
        }

        if ($days === 1) {
            return $this->xpdo->lexicon('seosuite.time_day', $output);
        }

        if ($days <= 6) {
            return $this->xpdo->lexicon('seosuite.time_days', $output);
        }

        if ($days <= 7) {
            return $this->xpdo->lexicon('seosuite.time_week', $output);
        }

        if ($days <= 29) {
            return $this->xpdo->lexicon('seosuite.time_weeks', $output);
        }

        if ($days <= 30) {
            return $this->xpdo->lexicon('seosuite.time_month', $output);
        }

        if ($days <= 180) {
            return $this->xpdo->lexicon('seosuite.time_months', $output);
        }

        return $this->xpdo->lexicon('seosuite.time_to_long', $output);
    }

    /**
     * Get resources that has the best alias and pagetitle match with the last url segment.
     * To boost the resources we count how many times the word is found
     * in the alias or pagetitle. The resources will be sorted by the boost DESC.
     *
     * @access public.
     * @param Boolean $context.
     * @param Array $excludeWords.
     * @return Array.
     */
    public function getRedirectSuggestions($context = false, array $excludeWords = [])
    {
        $suggestions = [];
        $words       = array_diff($this->getUrlSegmentWords($this->get('url')), $excludeWords);

        asort($words);

        if (count($words) === 0) {
            return [];
        }

        $criteria = $this->xpdo->newQuery(modResource::class, [
            'published' => 1,
            'deleted'   => 0
        ]);

        if ($context) {
            $criteria->where([
                'context_key' => $this->get('context_key')
            ]);
        }

        if (count($words) >= 1) {
            $where = [];

            foreach ($words as $word) {
                $where[] = [
                    'alias:LIKE'        => '%' . $word . '%',
                    'OR:pagetitle:LIKE' => '%' . $word . '%'
                ];
            }

            $criteria->where($where, xPDOQuery::SQL_OR);
        }

        foreach ($this->xpdo->getCollection(modResource::class, $criteria) as $suggestion) {
            $boost = 0;

            /**
             * Check if the last url segment and the alias has the same words,
             * If then it is the best result so boost the resource to the max.
             */
            $aliasWords = array_diff($this->getUrlSegmentWords($suggestion->get('alias')), $excludeWords);

            if (array_values($words) === array_values($aliasWords)) {
                $boost = 100;
            } else {
                /**
                 * Check how many times the last url segment words exists in the alias and pagetitle
                 * to boost the resource.
                 */
                foreach ($words as $word) {
                    $boost += substr_count(strtolower($suggestion->get('alias')), strtolower($word));
                    $boost += substr_count(strtolower($suggestion->get('pagetitle')), strtolower($word));
                }
            }

            $suggestions[] = [
                'id'    => $suggestion->get('id'),
                'boost' => $boost
            ];
        }

        $sort = array_column($suggestions, 'boost');

        array_multisort($sort, SORT_DESC, $suggestions);

        $output = [];
        foreach ($suggestions as $suggestion) {
            $output[$suggestion['id']] = $suggestion['boost'];
        }

        return $output;
    }

    /**
     * @access public.
     * @param String $segment.
     * @return Array.
     */
    public function getUrlSegmentWords($segment)
    {
        $words = [];

        foreach (str_word_count(str_replace('-', '_', $segment), 1, '1234567890') as $word) {
            $words[] = trim(trim($word), '123456789');
        }

        return array_filter($words);
    }
}
