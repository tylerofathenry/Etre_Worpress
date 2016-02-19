<?php

class Etre_WordPress_Model_Observer
{
    /**
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return boolean
     */

    public function wpValidateFormKey(Varien_Event_Observer $observer)
    {
        if ($this->_isWordPressRequestAndLoggedIn()):
            $this->_byPassFormKey();
        endif;
        if ($this->_isGrantedAccessBySession() == true):
            $this->_byPassFormKey();
        endif;
    }

    protected function _isGrantedAccessBySession()
    {
        $currentAction = Mage::app()->getRequest()->getActionName();
        $currentController = Mage::app()->getRequest()->getControllerName();
        $actionsAllowedToByPass = ["loadOptions", "chooser"];
        $controllersAllowedToByPass = ["cms_hierarchy_widget"];
        $isAllowedAccessFromWordPress = Mage::getSingleton("admin/session")->getAllowedWidgetAccessFromWordPress();
        if ($isAllowedAccessFromWordPress == true && (in_array($currentAction, $actionsAllowedToByPass, true) || in_array($currentController, $controllersAllowedToByPass, true))):
            return true;
        endif;
        return false;
    }

    protected function _byPassFormKey()
    {
        return Mage::app()->getRequest()->setParam("form_key", Mage::getSingleton("core/session")->getFormKey());
    }

    protected function _isWordPressRequestAndLoggedIn()
    {
        /* We're note verifying _wpnonce with WordPress, so we can't be sure that
          the request is actually coming from WordPress. But it is a start.
          Also, _isAllowedWpReferrer() is not the most secure method of
          guaranteeing this request is coming from our WordPress instance */
        if (($this->_isAllowedWpReferrer() == true || Mage::getSingleton("admin/session")->getAllowedWidgetAccessFromWordPress() == true) && Mage::app()->getRequest()->getParam('isAjax') == true && Mage::app()->getRequest()->getParam('_wpnonce') !== null && Mage::getSingleton('admin/session')->isLoggedIn() == true):
            Mage::getSingleton("admin/session")->setAllowedWidgetAccessFromWordPress(true);
            return true;
        elseif (Mage::app()->getRequest()->getParam('isAjax', true) && Mage::app()->getRequest()->getParam('_wpnonce', true)):
            /*The request is likely coming from wordpress but the admin is not logged in */
            Mage::getSingleton("admin/session")->addError(Mage::helper("etre_wordpress")->__("You must be logged into Magento to perform this action."));
            return false;
        endif;
    }

    protected function _isAllowedWpReferrer()
    {
        return $this->pathDoesContainWpBaseUrl();
    }

    /**
     * @return bool
     */
    protected function pathDoesContainWpBaseUrl()
    {
        $wpBaseUrl = Mage::helper("wordpress")->getBaseUrl();
        if (0 === strpos($_SERVER['HTTP_REFERER'], $wpBaseUrl)) {
            return true;
        }

        return false;
    }

}
