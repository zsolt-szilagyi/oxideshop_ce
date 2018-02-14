<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

/**
 * Class AccountReviewController
 *
 * @package OxidEsales\EshopCommunity\Application\Controller
 */
class AccountReviewController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/productreviewlist.tpl';

    /**
     * Get a list of a range of product reviews for the active user.
     * The range to retrieve is determined by the offset and rowCount parameters
     * which behave like in the MySQL LIMIT clause
     *
     * @param integer $offset   The offset to start with
     * @param integer $rowCount The number of items to retrieve
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel|null
     */
    public function getProductReviewList($offset, $rowCount)
    {
        $productReviewList = null;

        if ($user = $this->getUser()) {
            $userId = $user->getId();

            $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
            $productReviewList = $review->getProductReviewsByUserId($userId, $start, $limit);
        }

        return $productReviewList;
    }

    /**
     * Delete a product review, which belongs to the active user
     *
     * @param string $reviewId ID of the record to be deleted.
     *
     * @return bool True, if the review is gone, False, if the review cannot be deleted, because the validation failed
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    public function deleteProductReview($reviewId)
    {
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);

        /** There must be an active user */
        if (!$user = $this->getUser()) {
            return false;
        }

        /** The review must exist */
        if (!$review->load($reviewId)) {
            return false;
        }

        /** It must be a product review */
        if ('oxarticle' !== $review->getObjectType()) {
            return false;
        }

        /** It must belong to the active user */
        $reviewUserId = $review->getUser()->getId();
        $userId = $user->getId();
        if ($reviewUserId != $userId) {
            return false;
        };

        /**
         * If no exception is thrown, the review is gone: Or it has been deleted by the method call below or it has
         * never been there.
         */
        $review->deleteReview($reviewId);

        return true;
    }
}
