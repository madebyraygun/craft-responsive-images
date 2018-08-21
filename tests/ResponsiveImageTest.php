<?php

class ResponsiveImageTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $image = new \gentsagency\responsiveimages\models\ResponsiveImage();
        $image->addSource(128, 'small');
        $image->addSource(512, 'medium');
        $image->addSource(1024, 'large');

        $this->image = $image;
    }

    protected function _after()
    {
    }

    public function testDefaultSrc()
    {
        // When no argument is passed, `src()` should return the smallest one
        $this->assertEquals($this->image->src(), 'small');
    }

    public function testSpecificSrc()
    {
        $this->assertEquals($this->image->src(512), 'medium');
    }

    public function testRoundUpSrc()
    {
        $this->assertEquals($this->image->src(256), 'medium');
    }

    public function testOutOfRangeSrc()
    {
        $this->assertEquals($this->image->src(3000), 'large');
    }

    public function testSrcset()
    {
        $this->assertEquals($this->image->srcset(), 'small 128w,medium 512w,large 1024w');
    }

    public function testMagicMethods()
    {
        $this->assertEquals($this->image->src, $this->image->src());
        $this->assertEquals($this->image->srcset, $this->image->srcset());
    }
}
