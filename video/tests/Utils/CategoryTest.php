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

            $this->$name->urlgenerator = $kernel->getContainer()->get('router');
        }
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFrontPage(string $categoriesString, array $arrayFromDb, int $categoryId): void
    {
        $this->mockCategoryTreeFrontPage->categories = $arrayFromDb;
        $this->mockCategoryTreeFrontPage->slugger = new AppExtension();
        $mainParentId = $this->mockCategoryTreeFrontPage->getMainParent($categoryId)['id'];
        $categoriesTree = $this->mockCategoryTreeFrontPage->buildTree($mainParentId);

        $this->assertSame($categoriesString, $this->mockCategoryTreeFrontPage->getCategoryList($categoriesTree));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminList
     */
    public function testCategoryTreeAdminList(string $compare, array $arrayFromDb): void
    {
        $this->mockCategoryTreeAdminList->categories = $arrayFromDb;
        $categoriesTree = $this->mockCategoryTreeAdminList->buildTree();

        $this->assertSame($compare, $this->mockCategoryTreeAdminList->getCategoryList($categoriesTree));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     */
    public function testCategoryTreeAdminOptionList(array $arrayToCompare, array $arrayFromDb): void
    {
        $this->mockCategoryTreeAdminOptionList->categories = $arrayFromDb;
        $categoriesTree = $this->mockCategoryTreeAdminOptionList->buildTree();

        $this->assertSame($arrayToCompare, $this->mockCategoryTreeAdminOptionList->getCategoryList($categoriesTree));
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
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li></ul>',
            [ ['id'=>2,'parent_id'=>null,'name'=>'Toys'] ]
         ];

         yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Movies<a href="/admin/su/edit-category/3"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/3">Delete</a></li></ul>',
            [
                ['id'=>2,'parent_id'=>null,'name'=>'Toys'],
                ['id'=>3,'parent_id'=>null,'name'=>'Movies']
            ]
         ];

         yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Toys<a href="/admin/su/edit-category/2"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/2">Delete</a></li><li><i class="fa-li fa fa-arrow-right"></i>  Movies<a href="/admin/su/edit-category/3"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/3">Delete</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Horrors<a href="/admin/su/edit-category/4"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/4">Delete</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>  Not so scary<a href="/admin/su/edit-category/5"> Edit</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/su/delete-category/5">Delete</a></li></ul></li></ul></li></ul>',

            [
                ['id'=>2,'parent_id'=>null,'name'=>'Toys'],
                ['id'=>3,'parent_id'=>null,'name'=>'Movies'],
                ['id'=>4,'parent_id'=>3,'name'=>'Horrors'],
                ['id'=>5,'parent_id'=>4,'name'=>'Not so scary']
            ]
         ];
    }

    public function dataForCategoryTreeAdminOptionList(): Generator
    {
        yield [
            [
                ['name'=>'Electronics','id'=>1],
                ['name'=>'--Computers','id'=>6],
                ['name'=>'----Laptops','id'=>8],
                ['name'=>'------HP','id'=>14]
            ],
            [
                ['name'=>'Electronics','id'=>1, 'parent_id'=>null],
                ['name'=>'Computers','id'=>6, 'parent_id'=>1],
                ['name'=>'Laptops','id'=>8, 'parent_id'=>6],
                ['name'=>'HP','id'=>14, 'parent_id'=>8]
            ]
        ];
    }

}
