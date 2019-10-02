<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

/**
 * @stable
 */
interface ProductRatingBridgeInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
