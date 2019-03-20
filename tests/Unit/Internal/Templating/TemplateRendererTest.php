<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Templating;

use OxidEsales\EshopCommunity\Internal\Templating\TemplateRenderer;

class TemplateRendererTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $response = 'rendered template';
        /** @var \OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineInterface $engine */
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('render')
            ->with('template')
            ->will($this->returnValue($response));

        $delegatingEngine = new TemplateRenderer($engine);

        $this->assertSame($response, $delegatingEngine->renderTemplate('template', []));
    }

    public function testGetExistingEngine()
    {
        /** @var \OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineInterface $engine */
        $engine = $this->getEngineMock();

        $delegatingEngine = new TemplateRenderer($engine);

        $this->assertSame($engine, $delegatingEngine->getEngine());
    }

    private function getEngineMock()
    {
        $engine = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineInterface')
            ->getMock();

        $engine->expects($this->any())
            ->method('getDefaultFileExtension')
            ->will($this->returnValue('tpl'));

        return $engine;
    }
}
