<?php

class Etre_WordPress_Block_Adminhtml_Widgets_Wordpress_Author
    extends Fishpig_Wordpress_Block_Sidebar_Widget_Posts
{
    protected function _toHtml()
    {
        $author = Mage::getModel('wordpress/user')->load($this->getAuthorId());
        $html = Mage::helper('wordpress')->getUrl('author/' . urlencode($author->getUserNicename())) . '/';
        return $html;
    }

}

;