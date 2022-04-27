<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlgenerator;

    /**
     * @var Array
     */
    protected static $categoriesCache;

    /**
     * @var Array
     */
    public $categories;

    /**
     * @var String
     */
    public $categoryList;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlgenerator)
    {
        $this->em = $em;
        $this->urlgenerator = $urlgenerator;
        $this->categories = $this->getCategories();
    }

    /**
     * @param Array $categories
     */
    abstract public function getCategoryList(array $categories);

    /**
     * @return Array
     */
    private function getCategories(): array
    {
        if (self::$categoriesCache) {
            return self::$categoriesCache;
        }

        $conn = $this->em->getConnection();
        $query = "SELECT * FROM categories";
        $stmt = $conn->prepare($query);
        self::$categoriesCache = $stmt->executeQuery()->fetchAllAssociative();

        return self::$categoriesCache;
    }

    /**
     * @param Int $parantId
     * @return Array
     */
    public function buildTree(int $parentId = null): array
    {
        $subcategory = [];

        foreach($this->categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($category['id']);

                if ($children) {
                    $category['children'] = $children;
                }
                $subcategory[] = $category;
            }
        }

        return $subcategory;
    }
}
