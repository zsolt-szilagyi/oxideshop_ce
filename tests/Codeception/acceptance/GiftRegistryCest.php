<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;
use OxidEsales\Codeception\Module\Translator;

class GiftRegistryCest
{
    /**
     * @group myAccount
     * @group giftRegistry
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function enabledGiftRegistry(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('if product gift registry functionality is enabled');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $userData = $this->getExistingUserData();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $detailsPage = $detailsPage->openAccountMenu()
            ->checkGiftRegistryItemCount(0)
            ->closeAccountMenu()
            ->addProductToGiftRegistryList()
            ->openAccountMenu()
            ->checkGiftRegistryItemCount(1)
            ->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see(Translator::translate('MY_GIFT_REGISTRY'), $userAccountPage::$dashboardGiftRegistryPanelHeader);
        $I->see(Translator::translate('PRODUCT').' 1', $userAccountPage::$dashboardGiftRegistryPanelContent);

        $userAccountPage = $userAccountPage->logoutUser()
            ->login($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MY_GIFT_REGISTRY'), $userAccountPage::$dashboardGiftRegistryPanelHeader);
        $I->see(Translator::translate('PRODUCT').' 1', $userAccountPage::$dashboardGiftRegistryPanelContent);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->removeProductFromGiftRegistryList()
            ->openAccountMenu()
            ->checkGiftRegistryItemCount(0)
            ->closeAccountMenu();
    }

    /**
     * @group myAccount
     * @group giftRegistry
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function userGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user gift registry functionality');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $userData = $this->getExistingUserData();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        //add to gift registry and open gift registry page
        $giftRegistryPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->addProductToGiftRegistryList()
            ->openAccountPage()
            ->openGiftRegistryPage()
            ->seeProductData($productData);

        //open product details page
        $detailsPage = $giftRegistryPage->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage::$productTitle);

        $giftRegistryPage = $detailsPage->openUserGiftRegistryPage()
            ->addProductToBasket(1, 2);
        $I->see(2, $giftRegistryPage::$miniBasketMenuElement);

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see(Translator::translate('GIFT_REGISTRY_EMPTY'));

        $I->deleteFromDatabase('oxuserbaskets', ['oxuserid' => 'testuser']);
        $I->clearShopCache();
    }

    /**
     * @group myAccount
     * @group giftRegistry
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function makingPublicUserGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user gift registry functionality setting it as searchable and public');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $userData = $this->getExistingUserData();
        $adminUserData = $this->getAdminUserData();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        //add to gift registry and open the page of it
        $giftRegistryPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->addProductToGiftRegistryList()
            ->openUserGiftRegistryPage();

        //making gift registry searchable
        $giftRegistryPage = $giftRegistryPage->makeListSearchable()
            ->logoutUser()
            ->loginUser($adminUserData['userLoginName'], $adminUserData['userPassword'])
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see(Translator::translate('GIFT_REGISTRY_SEARCH_RESULTS'));
        $I->see(Translator::translate('GIFT_REGISTRY_OF') .' '. $userData['userName'] .' '. $userData['userLastName']);
        $giftRegListPage = $giftRegistryPage->openFoundGiftRegistryList();
        $title = Translator::translate('GIFT_REGISTRY_OF') .' '. $userData['userName'] .' '. $userData['userLastName'];
        $I->see($title, $giftRegListPage::$headerTitle);
        $I->see(sprintf(Translator::translate('WISHLIST_PRODUCTS'), $userData['userName'] .' '. $userData['userLastName']));
        $giftRegListPage->seeProductData($productData, 1);

        //making gift registry not searchable
        $giftRegistryPage = $giftRegListPage->openUserGiftRegistryPage()
            ->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MESSAGE_MAKE_GIFT_REGISTRY_PUBLISH'));
        $giftRegistryPage = $giftRegistryPage->makeListNotSearchable()
            ->logoutUser()
            ->loginUser($adminUserData['userLoginName'], $adminUserData['userPassword'])
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see(Translator::translate('MESSAGE_SORRY_NO_GIFT_REGISTRY'));

        $giftRegistryPage = $giftRegistryPage->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->sendGiftRegistryEmail(
                'example@oxid-esales.dev',
                'recipient',
                'Hi, I created a Gift Registry at OXID.'
            );
        $I->see(sprintf(Translator::translate('GIFT_REGISTRY_SENT_SUCCESSFULLY'), 'example@oxid-esales.dev'));

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see(Translator::translate('GIFT_REGISTRY_EMPTY'));
    }

    /**
     * @group myAccount
     * @group giftRegistry
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function disabledUserGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('disabled user gift registry via performance options');

        //(Use gift registry) is disabled
        $I->updateConfigInDatabase('bl_showWishlist', false);

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        //TODO: does it work with shared sessions
        $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->dontSeeElement($detailsPage::$addToGiftRegistryLink);
        $detailsPage->openAccountMenu();
        $I->dontSee(Translator::translate('MY_GIFT_REGISTRY'));
        $detailsPage->closeAccountMenu();

        $accountPage = $detailsPage->openAccountPage();
        $I->dontSee(Translator::translate('MY_GIFT_REGISTRY'), $accountPage::$giftRegistryLink);

        //(Use gift registry) is enabled again
        $I->cleanUp();

    }

    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

    private function getAdminUserData()
    {
        return \Codeception\Util\Fixtures::get('adminUser');
    }
}
