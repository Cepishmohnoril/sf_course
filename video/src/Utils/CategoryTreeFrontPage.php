<?php

namespace App\Utils;
use App\Twig\AppExtension;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

//Some bullshit, but it is in the lesson
class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public function getCategoryListAndParent(int $id): string
    {
        $this->slugger = new AppExtension; // Twig extension to slugify url's for categories
        $parentData = $this->getMainParent($id); // main parent of subcategory
        $this->mainParentName = $parentData['name']; // for accesing in view
        $this->mainParentId = $parentData['id']; // for accesing in view
        $key = array_search($id, array_column($this->categories,'id'));
        $this->currentCategoryName = $this->categories[$key]['name']; // for accesing in view
        $categories_array = $this->buildTree($parentData['id']); // builds array for generating nested html list
        return $this->getCategoryList($categories_array);
    }

    public function getCategoryList(array $categories)
    {
        $this->categoryList .= '<ul>';

        foreach($categories as $value) {
            $catName = $this->slugger->slugify($value['name']);
            $url = $this->urlgenerator->generate('video_list', ['categoryName' => $catName, 'id' => $value['id']]);

            $this->categoryList .= "<li><a href=\"$url\">" . $value['name'] . "</a>";

            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }

            $this->categoryList .= "</li>";
        }

        $this->categoryList .= '</ul>';

        return $this->categoryList;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categories, 'id'));
        if($this->categories[$key]['parent_id'] != null)
        {
            return $this->getMainParent($this->categories[$key]['parent_id']);
        }
        else
        {
            return [
                'id'=>$this->categories[$key]['id'],
                'name'=>$this->categories[$key]['name']
                ];
        }
    }
}