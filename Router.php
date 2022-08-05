<?php

namespace Magiccart\Lookbook\Controller;


class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
    protected $_brand;
    protected $helper;
    protected $_response;
    protected $collectionFactory;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magiccart\Lookbook\Model\ResourceModel\Lookbook\CollectionFactory $collectionFactory,
        \Magiccart\Lookbook\Helper\Data $helper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if(!$this->helper->getConfigModule('general/enabled')) return;
        $identifier = trim($request->getPathInfo(), '/');
        $router     = $this->helper->getConfigModule('general/router');
        $urlSuffix  = $this->helper->getConfigModule('general/url_suffix');
        if ($length = strlen($urlSuffix)) {
            if (substr($identifier, -$length) == $urlSuffix) {
                $identifier = substr($identifier, 0, strlen($identifier) - $length);
            }
        }

        $routePath = explode('/', $identifier);
        $routeSize = sizeof($routePath); //den count //

        if ($identifier == $router) {
            $request->setModuleName('lookbook')
                    ->setControllerName('index')
                    ->setActionName('index')
                    ->setPathInfo('/lookbook/index/index');
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');

        } elseif ($routeSize == 2 && $routePath[0] == $router) {
            $url_key = $routePath[1];
            $model = $this->collectionFactory->create();
            $model->load($url_key, 'urlkey');


            if (!empty($model->load($url_key, 'urlkey'))) {
                $id = $model->load($url_key, 'urlkey')->getData('lookbook_id');
                $request->setModuleName('lookbook')
                        ->setControllerName('index')
                        ->setActionName('index')
                        ->setParam('id', $id)
                        ->setPathInfo('/lookbook/index/index');
                return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
            }
        } else {
            return;
        }
    }
}