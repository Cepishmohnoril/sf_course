<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\AppExtension;
use Generator;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $input, string $output): void
    {
        $slugger = new AppExtension();
        $this->assertSame($output, $slugger->slugify($input));
    }

    public function getSlugs(): Generator
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield [' Lorem Ipsum', 'lorem-ipsum'];
        yield [' lOrEm  iPsUm  ', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', 'lorem-ipsum'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['Children\'s books', 'childrens-books'];
        yield ['Five star movies', 'five-star-movies'];
        yield ['Adults 60+', 'adults-60'];
    }
}
