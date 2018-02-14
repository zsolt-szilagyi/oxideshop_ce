<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

/**
 * Class AccountControllerReviewTests
 *
 * Test the correct behavior of the recommendation management feature in the account controller
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Application\Controller
 */
class AccountControllerReviewTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * If there is no active user the number of items returned by
     * \OxidEsales\EshopCommunity\Application\Controller\AccountController::getProductReviewItemsCnt
     * should be 0
     *
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::getProductReviewItemsCnt
     */
    public function testGetProductReviewItemsCntReturnZeroForNoUser()
    {
        $expectedReviewsCount = 0;

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $actualReviewsCount = $accountControllerMock->getProductReviewItemsCnt();

        $this->assertSame( $expectedReviewsCount, $actualReviewsCount);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::getProductReviewItemsCnt
     */
    public function testGetProductReviewItemsCntReturnsExpectedCountForActiveUser()
    {
        $expectedReviewsCount = 100;

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getProductReviewItemsCntByUserId']);
        $reviewsMock->expects($this->any())->method('getProductReviewItemsCntByUserId')->will($this->returnValue($expectedReviewsCount));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $actualReviewsCount = $accountControllerMock->getProductReviewItemsCnt();

        $this->assertSame( $expectedReviewsCount, $actualReviewsCount);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::getProductReviewList
     */
    public function testGetProductReviewListReturnsNullForNoUser()
    {
        $expectedProductReviewList = null;

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $actualProductReviewList = $accountControllerMock->getProductReviewList(0, 10);

        $this->assertSame( $expectedProductReviewList, $actualProductReviewList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::getProductReviewList
     */
    public function testGetProductReviewListReturnsExpectedListForActiveUser()
    {
        $expectedProductReviewsList = new \OxidEsales\Eshop\Core\Model\ListModel();

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getProductReviewsByUserId']);
        $reviewsMock->expects($this->any())->method('getProductReviewsByUserId')->will($this->returnValue($expectedProductReviewsList));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $actualProductReviewsList = $accountControllerMock->getProductReviewList(0,10);

        $this->assertSame( $expectedProductReviewsList, $actualProductReviewsList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteProductReview
     */
    public function testDeleteProductReviewReturnsFalseOnNoUser()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $result = $accountControllerMock->deleteProductReview('reviewId');

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteProductReview
     */
    public function testDeleteProductReviewReturnsFalseOnNoReview()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $result = $accountControllerMock->deleteProductReview('reviewId');

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteProductReview
     */
    public function testDeleteProductReviewReturnsFalseOnWrongReviewType()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxrecommlist'));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountControllerMock->deleteProductReview('reviewId');

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteProductReview
     */
    public function testDeleteProductReviewReturnsFalseOnWrongReviewUser()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("someId"));

        $reviewUserMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $reviewUserMock->expects($this->any())->method('getId')->will($this->returnValue("otherId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load','getUser']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxarticle'));
        $reviewsMock->expects($this->any())->method('getUser')->will($this->returnValue($reviewUserMock));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountControllerMock->deleteProductReview('reviewId');

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteProductReview
     */
    public function testDeleteProductReviewReturnsTrue()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountController::class, ['getUser']);
        $accountControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load','getUser']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxarticle'));
        $reviewsMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountControllerMock->deleteProductReview('reviewId');

        $this->assertTrue($result);
    }
}
