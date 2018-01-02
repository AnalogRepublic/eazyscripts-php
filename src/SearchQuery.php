<?php

namespace EazyScripts;

/**
 * This object represents the query we'll include in an
 * API request which allows us to search.
 */
class SearchQuery
{
    /**
     * The term we're searching.
     *
     * @var string
     */
    public $search;

    /**
     * How many we want to get in the request's response.
     *
     * @var integer
     */
    public $take;

    /**
     * How many we want to skip.
     *
     * @var integer
     */
    public $skip;

    /**
     * Create a new search query objtec
     *
     * @param string  $search
     * @param integer $take
     * @param integer $skip
     */
    public function __construct($search = "", $take = 24, $skip = 0)
    {
        $this->search = $search;
        $this->take = $take;
        $this->skip = $skip;
    }

    /**
     * Get the array formatted for the search query.
     *
     * @return array
     */
    public function getRequestQuery()
    {
        $params = [
            "Search" => trim($this->search),
            "Take"   => max(0, min(24, $this->take)),
            "Skip"   => max(0, $this->skip),
        ];

        // If we've not provided a search term, then just remove it from the params.
        if (empty($params["Search"])) {
            unset($params["Search"]);
        }

        return $params;
    }
}
