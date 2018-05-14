<?php
namespace Step\Acceptance;

use OxidEsales\Codeception\Page\UserCheckout;
use OxidEsales\Codeception\Module\Translator;

class UserRegistrationInCheckout extends \AcceptanceTester
{
    public function createRegisteredUserInCheckout(
        $userLoginData,
        $userData,
        $addressData,
        $shippingAddressData = null)
    {
        $I = $this;
        $userCheckout = $this->enterRegisteredUserData($userLoginData, $userData, $addressData);

        if ($shippingAddressData) {
            $userCheckout->openShippingAddressForm()->enterShippingAddressData($shippingAddressData);
        }

        $paymentPage = $userCheckout->goToNextStep();
        $breadCrumbName = Translator::translate("YOU_ARE_HERE").':'.Translator::translate("PAY");
        $I->see($breadCrumbName, $paymentPage::$breadCrumb);
        return $paymentPage;
    }

    public function createNotRegisteredUserInCheckout(
        $userLogin,
        $userData,
        $addressData,
        $shippingAddressData = null)
    {
        $I = $this;
        $userCheckout = $this->enterNotRegisteredUserData($userLogin, $userData, $addressData);

        if ($shippingAddressData) {
            $userCheckout->openShippingAddressForm()->enterShippingAddressData($shippingAddressData);
        }

        $paymentPage = $userCheckout->goToNextStep();
        $breadCrumbName = Translator::translate("YOU_ARE_HERE").':'.Translator::translate("PAY");
        $I->see($breadCrumbName, $paymentPage::$breadCrumb);
        return $paymentPage;
    }

    public function createNotValidRegisteredUserInCheckout(
        $userLoginData,
        $userData,
        $addressData,
        $shippingAddressData = null)
    {
        $I = $this;
        $userCheckout = $this->enterRegisteredUserData($userLoginData, $userData, $addressData);

        if ($shippingAddressData) {
            $userCheckout->openShippingAddressForm()->enterShippingAddressData($shippingAddressData);
        }

        $userCheckout = $userCheckout->tryToRegisterUser();
        $breadCrumbName = Translator::translate("YOU_ARE_HERE").':'.Translator::translate("ADDRESS");
        $I->see($breadCrumbName, $userCheckout::$breadCrumb);

        return $userCheckout;
    }

    private function enterRegisteredUserData($userLoginData, $userData, $addressData)
    {
        $userCheckout = new UserCheckout($this);
        $userCheckout = $userCheckout->selectOptionRegisterNewAccount();

        $userCheckout->enterUserLoginData($userLoginData)
            ->enterUserData($userData)
            ->enterAddressData($addressData);
        return $userCheckout;
    }

    private function enterNotRegisteredUserData($userLogin, $userData, $addressData)
    {
        $userCheckout = new UserCheckout($this);
        $userCheckout = $userCheckout->selectOptionNoRegistration();

        $userCheckout->enterUserLoginName($userLogin)
            ->enterUserData($userData)
            ->enterAddressData($addressData);
        return $userCheckout;
    }
}