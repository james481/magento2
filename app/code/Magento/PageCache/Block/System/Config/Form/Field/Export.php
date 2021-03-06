<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\PageCache\Block\System\Config\Form\Field;

use Magento\App\ConfigInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class Export
 */
class Export extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(Context $context, ConfigInterface $config, array $data = array())
    {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = array('website' => $buttonBlock->getRequest()->getParam('website'));

        $url = $this->getUrl("*/PageCache/exportVarnishConfig", $params);
        $data = array(
            'id' => 'system_full_page_cache_varnish_export_button',
            'label' => __('Export VCL'),
            'onclick' => "setLocation('" . $url . "')"
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }

    /**
     * Return PageCache TTL value from config
     * to avoid saving empty field
     *
     * @return string
     */
    public function getTtlValue()
    {
        return $this->config->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL);
    }
}
