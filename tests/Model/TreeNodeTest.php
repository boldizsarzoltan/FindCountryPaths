<?php

namespace App\Tests\Model;


use App\Entity\Countries;
use App\Entity\Country;
use App\Model\TreeNode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TreeNodeTest extends KernelTestCase
{
    public function test_worksAsIntended()
    {
        $treeNode = $this->createMock(TreeNode::class);
        $country = $this->createMock(Country::class);
        $treeNodeToTest = new TreeNode($country, $treeNode);
        $this->assertEquals(
            $country,
            $treeNodeToTest->country
        );
        $this->assertEquals(
            $treeNode,
            $treeNodeToTest->parentNode
        );
    }
}