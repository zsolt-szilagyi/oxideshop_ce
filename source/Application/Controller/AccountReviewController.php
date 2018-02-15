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
     * TODO Shit! If a user does not rate a product, it is posible to write more than one review.
     * ratings and reviews are both to be deleted. Additionally a  user can write/have more than one review per
     * product.
     *
     * Delete a product review, which belongs to the active user
     *
     * @return bool True, if the review is gone, False, if the review cannot be deleted, because the validation failed
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    public function deleteProductReview()
    {
        if (!\OxidEsales\Eshop\Core\Registry::getSession()->checkSessionChallenge()) {
            return false;
        }

        /** There must be an active user */
        if (!$user = $this->getUser()) {
            return false;
        }
        if (!$userId = $user->getId()) {
            return false;
        }

        /** The article id must be given to be able to delete the rating */
        $articleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aId');
        if (!$articleId) {
            return false;
        }

        /** The review id must be given to be able to delete a single review */
        $reviewId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('reviewId');
        if (!$reviewId) {
            return false;
        }

        /** The review must exist */
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
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

        try {
            $review->delete($reviewId);
        } catch (\Exception $exception) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('PRODUCT_REVIEW_NOT_DELETED');
        }

        /**
         * TODO
         * Delete ratings as well
         */
    }
}
