<?php class Etre_WordPress_Model_Rewrite_Widget extends Mage_Widget_Model_Widget{
    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     */
    public function getWidgetDeclaration($type, $params = array(), $asIs = true)
    {
        $directive = '{{widget type="' . $type . '"';

        foreach ($params as $name => $value) {
            // Retrieve default option value if pre-configured
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif (trim($value) == '') {
                $widget = $this->getConfigAsObject($type);
                $parameters = $widget->getParameters();
                if (isset($parameters[$name]) && is_object($parameters[$name])) {
                    $value = $parameters[$name]->getValue();
                }
            }
            if ($value) {
                $directive .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $directive .= '}}';

        if ($asIs) {
            return $directive;
        }

        $config = Mage::getSingleton('widget/widget_config');
        $imageName = str_replace('/', '__', $type) . '.gif';
        if (is_file($config->getPlaceholderImagesBaseDir() . DS . $imageName)) {
            $image = $config->getPlaceholderImagesBaseUrl() . $imageName;
        } else {
            $image = $config->getPlaceholderImagesBaseUrl() . 'default.gif';
        }
        /* $html = sprintf('<img id="%s" src="%s" title="%s">',
            $this->_idEncode($directive),
            $image,
            Mage::helper('core')->urlEscape($directive)
        ); */
        // Our WordPress plugin will insert the TinyMCE widget into the wordpress post as an image (the same as magento) but for debugging we are outputting text for now.
        $html = Mage::helper('core')->urlEscape($directive);
        return $html;
    }

}