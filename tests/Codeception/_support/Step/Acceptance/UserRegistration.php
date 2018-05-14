<?php
namespace Step\Acceptance;
use OxidEsales\Codeception\Module\Translator;

class UserRegistration extends \AcceptanceTester
{
    public function registerUser($userLoginDataToFill, $userDataToFill, $addressDataToFill)
    {
        $I = $this;
        $breadCrumbName = Translator::translate("YOU_ARE_HERE") . ":" . Translator::translate("PAGE_TITLE_REGISTER");
        $registrationPage = new \OxidEsales\Codeception\Page\UserRegistration($I);
        $registrationPage->enterUserLoginData($userLoginDataToFill)
            ->enterUserData($userDataToFill)
            ->enterAddressData($addressDataToFill)
            ->registerUser();

        $I->see($breadCrumbName, $registrationPage::$breadCrumb);
        $I->see(Translator::translate('MESSAGE_WELCOME_REGISTERED_USER'));
    }
}