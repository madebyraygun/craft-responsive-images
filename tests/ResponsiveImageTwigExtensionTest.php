<?php

class ResponsiveImageTwigExtensionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->twigExtension = new \gentsagency\responsiveimages\twigextensions\ResponsiveImagesTwigExtension();
    }

    protected function _after()
    {
    }

    // tests
    public function testConfiguresFilter()
    {
        $filters = $this->twigExtension->getFilters();

        $this->assertNotEmpty($filters);
        $this->assertEquals($filters[0]->getName(), 'responsiveImages');
    }

    public function testConfiguresFunction()
    {
        $functions = $this->twigExtension->getFunctions();

        $this->assertNotEmpty($functions);
        $this->assertEquals($functions[0]->getName(), 'responsiveImage');
    }
}
