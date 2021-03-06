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
 * @package     Magento_ProductAlert
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\ProductAlert\Block\Product\View;

/**
 * Test class for \Magento\ProductAlert\Block\Product\View\Stock
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Helper\Data
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Registry
     */
    protected $_registry;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Block\Product\View\Stock
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $this->getMock(
            'Magento\ProductAlert\Helper\Data',
            array('isStockAlertAllowed', 'getSaveUrl'),
            array(),
            '',
            false
        );
        $this->_product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('isAvailable', 'getId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_registry = $this->getMockBuilder(
            'Magento\Registry'
        )->disableOriginalConstructor()->setMethods(
            array('registry')
        )->getMock();
        $this->_block = $objectManager->getObject(
            'Magento\ProductAlert\Block\Product\View\Stock',
            array('helper' => $this->_helper, 'registry' => $this->_registry)
        );
        $this->_layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
    }

    public function testSetTemplateStockUrlAllowed()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects(
            $this->once()
        )->method(
            'getSaveUrl'
        )->with(
            'stock'
        )->will(
            $this->returnValue('http://url')
        );

        $this->_product->expects($this->once())->method('isAvailable')->will($this->returnValue(false));

        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($this->_product)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('path/to/template.phtml', $this->_block->getTemplate());
        $this->assertEquals('http://url', $this->_block->getSignupUrl());
    }

    /**
     * @param bool $stockAlertAllowed
     * @param bool $productAvailable
     * @dataProvider setTemplateStockUrlNotAllowedDataProvider
     */
    public function testSetTemplateStockUrlNotAllowed($stockAlertAllowed, $productAvailable)
    {
        $this->_helper->expects(
            $this->once()
        )->method(
            'isStockAlertAllowed'
        )->will(
            $this->returnValue($stockAlertAllowed)
        );
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_product->expects($this->any())->method('isAvailable')->will($this->returnValue($productAvailable));

        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($this->_product)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }

    public function setTemplateStockUrlNotAllowedDataProvider()
    {
        return array(
            'stock alert not allowed' => array(false, false),
            'product is available (no alert)' => array(true, true),
            'stock alert not allowed and product is available' => array(false, true)
        );
    }

    public function testSetTemplateNoProduct()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue(null)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }
}
