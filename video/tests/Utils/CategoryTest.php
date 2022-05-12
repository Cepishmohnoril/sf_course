<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use App\Twig\AppExtension;
use Generator;

class CategoryTest extends KernelTestCase
{

    /**
     * @var CategoryTreeFrontPage
     */
    protected $mockCategoryTreeFrontPage;

    /**
     * @var CategoryTreeAdminList
     */
    protected $mockCategoryTreeAdminList;

    /**
     * @var CategoryTreeAdminOptionList
     */
    protected $mockCategoryTreeAdminOptionList;


    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $testedClasses = [
            'CategoryTreeFrontPage',
            'CategoryTreeAdminList',
            'CategoryTreeAdminOptionList',
        ];

        foreach($testedClasses as $class) {
            $name = 'mock' . $class;

            $this->$name = $this
            ->getMockBuilder('App\Utils\\' . $class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

            $this->mockCategoryTreeFrontPage->urlgenerator = $kernel->getContainer()->get('router');
        }
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFrontPage(string $categoriesString, array $categoriesDbArray, int $categoryId): void
    {
        $this->mockCategoryTreeFrontPage->categories = $categoriesDbArray;
        $this->mockCategoryTreeFrontPage->slugger = new AppExtension();
        $mainParentId = $this->mockCategoryTreeFrontPage->getMainParent($categoryId)['id'];
        $categoriesTree = $this->mockCategoryTreeFrontPage->buildTree($mainParentId);

        $this->assertSame($categoriesString, $this->mockCategoryTreeFrontPage->getCategoryList($categoriesTree));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminList
     */
    public function testCategoryTreeAdminList(): void
    {

    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     */
    public function testCategoryTreeAdminOptionList(array $arrayTocompare, array $arrayFromDb): void
    {

    }

    public function dataForCategoryTreeFrontPage(): Generator
    {
        yield [
            '<ul><li><a href="/video-list/computers,6">Computers</a><ul><li><a href="/video-list/laptops,8">Laptops</a><ul><li><a href="/video-list/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ],
            1
        ];

        yield [
            '<ul><li><a href="/video-list/computers,6">Computers</a><ul><li><a href="/video-list/laptops,8">Laptops</a><ul><li><a href="/video-list/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ],
            6
         ];

        yield [
            '<ul><li><a href="/video-list/computers,6">Computers</a><ul><li><a href="/video-list/laptops,8">Laptops</a><ul><li><a href="/video-list/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ],
            8
         ];

        yield [
            '<ul><li><a href="/video-list/computers,6">Computers</a><ul><li><a href="/video-list/laptops,8">Laptops</a><ul><li><a href="/video-list/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ],
            14

        ];
    }

    public function dataForCategoryTreeAdminList(): Generator
    {

    }

    public function dataForCategoryTreeAdminOptionList(): Generator
    {

    }

}
