<?php

use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Page\UserOrderHistory;
use OxidEsales\Codeception\Module\Translator;

class MainCest
{
    public function frontPageWorks(AcceptanceTester $I)
    {
        $I->amOnPage(Home::$URL);
        $I->see(Translator::translate("HOME"));
    }

    public function shopBrowsing(AcceptanceTester $I)
    {
        // open start page
        $I->amOnPage(Home::$URL);

        $I->see(Translator::translate("HOME"));
        $I->see(Translator::translate('START_BARGAIN_HEADER'));

        // open category
        $I->click('Test category 0 [EN] šÄßüл', '#navigation');
        $I->see('Test category 0 [EN] šÄßüл', 'h1');

        // check if subcategory exists
        $I->see('Test category 1 [EN] šÄßüл', '#moreSubCat_1');

        //open Details page
        $I->click('#productList_1');

        // login to shop
        $I->amOnPage(UserOrderHistory::$URL);
        $I->see(Translator::translate('LOGIN'), 'h1');

        $I->fillField(UserOrderHistory::$loginUserNameField,'example_test@oxid-esales.dev');
        $I->fillField(UserOrderHistory::$loginUserPasswordField,'useruser');
        $I->click(UserOrderHistory::$loginButton);

        $I->see(Translator::translate('ORDER_HISTORY'), 'h1');
    }

}
