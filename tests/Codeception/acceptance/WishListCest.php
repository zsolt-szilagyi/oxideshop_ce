<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;
use OxidEsales\Codeception\Module\Translator;

class WishListCest
{
    /**
     * @group myAccount
     * @group wishList
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function enabledWishList(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('if product compare functionality is enabled');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->openAccountMenu()
            ->checkWishListItemCount(0)
            ->closeAccountMenu()
            ->addToWishList()
            ->openAccountMenu()
            ->checkWishListItemCount(1)
            ->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see(Translator::translate('MY_WISH_LIST'), $userAccountPage::$dashboardWishListPanelHeader);
        $I->see(Translator::translate('PRODUCT').' 1', $userAccountPage::$dashboardWishListPanelContent);

        $userAccountPage->logoutUser()->login($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MY_WISH_LIST'), $userAccountPage::$dashboardWishListPanelHeader);
        $I->see(Translator::translate('PRODUCT').' 1', $userAccountPage::$dashboardWishListPanelContent);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->removeFromWishList()
            ->openAccountMenu()
            ->checkWishListItemCount(0)
            ->closeAccountMenu();
    }

    /**
     * @group myAccount
     * @group wishList
     *
     * @param Start $I
     */
    public function userWishList(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user wish list functionality');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 €'
        ];

        $userData = $this->getExistingUserData();

        $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $detailsPage = $detailsPage->addToWishList()
            ->openAccountMenu()
            ->checkWishListItemCount(1)
            ->closeAccountMenu()
            ->openUserWishListPage()
            ->seeProductData($productData)
            ->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage::$productTitle);

        $wishListPage = $detailsPage->openAccountPage()
            ->openWishListPage()
            ->addProductToBasket(1, 2);
        $I->see(2, $wishListPage::$miniBasketMenuElement);
        $wishListPage = $wishListPage->removeProductFromList(1);

        $I->see(Translator::translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage::$headerTitle);
        $I->see(Translator::translate('WISH_LIST_EMPTY'));
    }

    /**
     * @group myAccount
     * @group wishList
     *
     * @param Start $I
     */
    public function userWishListAddingVariant(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user wish list functionality, if a variant of product was added');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $userData = $this->getExistingUserData();

        $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see('14 EN product šÄßüл');
        //add parent to wish list
        $wishListPage = $detailsPage->addToWishList()
            ->selectVariant(1, 'S')
            ->selectVariant(2, 'black')
            ->selectVariant(3, 'lether')
            ->addToWishList()
            ->openAccountMenu()
            ->checkWishListItemCount(2)
            ->closeAccountMenu()
            ->openUserWishListPage()
            ->seeProductData($productData);

        //assert variant
        $productData = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'desc' => '',
            'price' => '25,00 €'
        ];
        $wishListPage->seeProductData($productData, 2);

        $wishListPage->removeProductFromList(2)
            ->removeProductFromList(1);

        $I->see(Translator::translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage::$headerTitle);
        $I->see(Translator::translate('WISH_LIST_EMPTY'));
    }

    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

}
