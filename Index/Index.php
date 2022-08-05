<?php
/**
 * 
 * @category: Magepow
 * @Copyright (c) 2014 Magepow  (<https://www.magepow.com>)
 * @authors: Magepow (<magepow<support@magepow.com>>)
 * @date:    2021-04-27 08:40:44
 * @license: <http://www.magepow.com/license-agreement>
 * @github: <https://github.com/magepow> 
 */
namespace Magiccart\Lookbook\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {
    
    protected $_pageFactory;
    protected $helperData;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magiccart\Lookbook\Helper\Data $helperData
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function execute()
    {
        $helperTitle = $this->helperData->getConfigModule('general/title');
        $resultPage = $this->_pageFactory->create();  
        $resultPage->getConfig()->getTitle()->set((__($helperTitle)));
        return $resultPage; 
    }
}