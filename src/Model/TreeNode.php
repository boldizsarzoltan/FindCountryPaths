<?php

namespace App\Model;

use App\Entity\Countries;
use App\Entity\Country;

class TreeNode
{
    public function __construct(
        public readonly Country $country,
        public readonly ?TreeNode $parentNode = null
    ) {
    }

    /**
     * @return TreeNode|null
     */
    public function getParentNode(): ?TreeNode
    {
        return $this->parentNode;
    }
}