<?php

namespace App\Data;

use App\Entity\Category;

use function PHPSTORM_META\type;

class SearchData
{
    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * @var null|integer
     */
    public $noteMax;

    /**
     * @var null|integer
     */
    public $noteMin;

    public function addCategory($category)
    {
        if ($category instanceof Category) {
            array_push($this->categories, $category);
        }
    }
}