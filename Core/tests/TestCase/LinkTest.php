<?php

namespace Croogo\Core\Test\TestCase;

use Cake\TestSuite\TestCase;
use Croogo\Core\Link;

class LinkTest extends TestCase
{
    public function testCreateFromLinkString()
    {
        $link = Link::createFromLinkString('plugin:Croogo%2FNodes/controller:Nodes/action:promoted');

        $this->assertEquals('Croogo/Nodes', $link['plugin']);
        $this->assertEquals('Nodes', $link['controller']);
        $this->assertEquals('promoted', $link['action']);
    }

    public function testToLinkString()
    {
        $link = new Link([
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted'
        ]);

        $this->assertEquals('plugin:Croogo%2FNodes/controller:Nodes/action:promoted', $link->toLinkString());
    }

    public function testGetUrl()
    {
        $link = new Link([
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted'
        ]);

        $this->assertEquals([
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted'
        ], $link->getUrl());

        $linkExample = new Link('http://example.com');

        $this->assertEquals('http://example.com', $linkExample->getUrl());
    }

    public function testToString()
    {
        $link = new Link([
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted'
        ]);

        $this->assertEquals('plugin:Croogo%2FNodes/controller:Nodes/action:promoted', (string)$link);

        $linkExample = new Link('http://example.com');

        $this->assertEquals('http://example.com', (string)$linkExample);
    }

    public function testObjectProperties()
    {
        $link = new Link([
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted'
        ]);

        $this->assertNull($link->prefix);
        $this->assertEquals('Croogo/Nodes', $link->plugin);
        $this->assertEquals('Nodes', $link->controller);
        $this->assertEquals('promoted', $link->action);
    }
}
