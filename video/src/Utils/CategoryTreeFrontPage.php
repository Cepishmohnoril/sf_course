<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories)
    {
        //TODOOT
        $this->categoryList .= '<ul>';

        foreach($categories as $value) {
            $catName = $value['name'];
            $url = $this->urlGen->generate('video_list', ['categoryName' => $catName, 'id' => $value['id']]);

            $this->categoryList .= "<li><a href=\"#\">$catName</a>";

            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }
        }

        $this->categoryList .= '</ul>';

        return $this->categoryList;
    }
}