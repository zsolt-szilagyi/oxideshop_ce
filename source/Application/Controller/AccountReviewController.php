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
    protected $itemsPerPage = 2;

    /**
     * @return \stdClass|void
     */
    public function getPageNavigation()
    {
        $this->_iCntPages = ceil($this->getProductReviewItemsCnt() / $this->itemsPerPage);
        $this->_oPageNavigation = $this->generatePageNavigation();

        return $this->_oPageNavigation;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();
        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_PRODUCT_REVIEWS', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Show the Reviews list only, if the feature has been enabled in eShop Admin
     * -> Master Settings -> Core Settings -> Settings -> Account settings -> "Allow users to manage their product reviews"
     *
     * @return string
     */
    public function render()
    {
        if ($this->getShowProductReviewList()) {
            $this->_sThisTemplate = 'page/account/productreviews.tpl';
        }

        return parent::render();
    }

    /**
     * Get a list of a range of product reviews for the active user.
     * The range to retrieve is determined by the offset and rowCount parameters
     * which behave like in the MySQL LIMIT clause
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel|null
     */
    public function getProductReviewList()
    {
        $productReviewList = null;

        if ($user = $this->getUser()) {
            $currentPage = $this->getActPage();
            $offset = $currentPage * $this->itemsPerPage;
            $rowCount = $this->itemsPerPage;

            $userId = $user->getId();

            $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
            $productReviewList = $review->getProductReviewsByUserId($userId, $offset, $rowCount);
        }

        return $productReviewList;
    }

    /**
     * Delete a product review and rating, which belongs to the active user.
     * Keep in mind, that this method may return only false or void. Any other return value will cause malfunction in
     * higher layers
     *
     * @return bool False, if the review cannot be deleted, because the validation failed
     *
     * @throws \Exception
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function deleteProductReviewAndRating()
    {
        /**
         * Do some validation and gather the needed data
         */

        /** The CSFR token must be valid */
        if (!$this->getSession()->checkSessionChallenge()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('ERROR_PRODUCT_REVIEW_AND_RATING_NOT_DELETED');

            return false;
        }

        /** There must be an active user */
        if (!$user = $this->getUser()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('ERROR_PRODUCT_REVIEW_AND_RATING_NOT_DELETED');

            return false;
        }
        if (!$userId = $user->getId()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('ERROR_PRODUCT_REVIEW_AND_RATING_NOT_DELETED');

            return false;
        }

        /**
         * Perform the deletion.
         * If the rating cannot be deleted, the review will also not be deleted: It is possible to create a review without
         * rating, but an existing rating always assumes an existing a review. This logic will be maintained on deletion.
         */

        $db = \OxidEsales\EshopCommunity\Core\DatabaseProvider::getDb();
        $db->startTransaction();
        try {
            $ratingDeleted = true;
            /** The article id must be given to be able to delete the rating */
            $articleId = $this->getArticleIdFromRequest();
            if (!$articleId ||
                !$this->deleteProductRating($userId, $articleId)
            ) {
                $ratingDeleted = false;
            }

            /** The review id must be given to be able to delete a single review */
            $reviewId = $this->getReviewIdFromRequest();
            if (!$ratingDeleted ||
                !$reviewId ||
                !$this->deleteProductReview($userId, $reviewId)
            ) {
                $reviewDeleted = false;
            } else {
                $reviewDeleted = true;
            }

            if ($ratingDeleted && $reviewDeleted) {
                $db->commitTransaction();
            } else {
                $db->rollbackTransaction();
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('ERROR_PRODUCT_REVIEW_AND_RATING_NOT_DELETED');
            }
        } catch (\Exception $exception) {
            $db->rollbackTransaction();

            throw $exception;
        }

        if (!(($ratingDeleted && $reviewDeleted))) {
            return false;
        }
    }

    /**
     * Delete a given review for a given user
     *
     * @param string $userId    Id of the user the rating belongs to
     * @param string $articleId Id of the rating to delete
     *
     * @return bool True, if the rating has been deleted, False if the validation failed
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    protected function deleteProductRating($userId, $articleId)
    {
        if (!$shopId = \OxidEsales\EshopCommunity\Core\Registry::getConfig()->getShopId()) {
            return false;
        }

        $rating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);
        /**
         * There might be the case that there has been no product rating introducted during the product review.
         * This case will be treated here as if it has been already deleted.
         */
        if (!$ratingId = $rating->getProductRatingByUserId($articleId, $userId, $shopId)) {
            return true;
        }

        $rating->delete($ratingId);

        return true;
    }

    /**
     * Delete a given review for a given user
     *
     * @param string $userId   Id of the user the review belongs to
     * @param string $reviewId Id of the review to delete
     *
     * @return bool True, if the review has been deleted, False if the validation failed
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    protected function deleteProductReview($userId, $reviewId)
    {
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
        if ($reviewUserId != $userId) {
            return false;
        };

        $review->delete($reviewId);

        return true;
    }

    /**
     * @return mixed
     */
    protected function getArticleIdFromRequest()
    {
        $articleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aId');

        return $articleId;
    }

    /**
     * @return mixed
     */
    protected function getReviewIdFromRequest()
    {
        $reviewId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('reviewId');

        return $reviewId;
    }
}
