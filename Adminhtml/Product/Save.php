<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-05-16 10:40:51
 * @@Modify Date: 2019-05-24 15:18:42
 * @@Function:
 */

namespace Magiccart\Lookbook\Controller\Adminhtml\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magiccart\Lookbook\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_lookbookFactory->create();
            $storeViewId = $this->getRequest()->getParam('store');

            if ($id = $this->getRequest()->getParam('lookbook_id')) {
                $model->load($id);
            }

            if (isset($_FILES['image']) && isset($_FILES['image']['name']) && strlen($_FILES['image']['name'])) {
                /*
                 * Save image upload
                 */
                try {
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                    $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

                    $uploader->addValidateCallback('lookbook_image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath('magiccart/lookbook/')
                    );
                    $data['image'] = 'magiccart/lookbook'.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    } else {
                        $data['image'] = null;
                    }
                }
            }

            if (isset($_FILES['marker']) && isset($_FILES['marker']['name']) && strlen($_FILES['marker']['name'])) {
                /*
                 * Save image upload
                 */
                try {
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'marker']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                    $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

                    $uploader->addValidateCallback('lookbook_marker', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath('magiccart/lookbook/marker/')
                    );
                    $data['marker'] = 'magiccart/lookbook/marker'.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['marker']) && isset($data['marker']['value'])) {
                    if (isset($data['marker']['delete'])) {
                        $data['marker'] = null;
                        $data['delete_marker'] = true;
                    } elseif (isset($data['marker']['value'])) {
                        $data['marker'] = $data['marker']['value'];
                    } else {
                        $data['marker'] = null;
                    }
                }
            }

            $unsetData = [
                'form_key',
                'lookbook_id',
                'title',
                'identifier',
                'link',
                'image',
                'content',
                'product_id',
                'classes',
                'stores',
                'options',
                'order',
                'description',
                'customer_group_id',
                'priority',
                'status',
            ];
            $config = $data;
            foreach ($unsetData as $key) {
                unset($config[$key]);
            }

            $data['config'] = $this->json->serialize($config);

            if(isset($data['stores'])) $data['stores'] = implode(',', $data['stores']);
            $model->setData($data)
                ->setStoreViewId($storeViewId);

            try {
                if( isset($data['options']) && $data['options'] == '{' ){
                    $this->messageManager->addError(__('Something went wrong while saving the lookbook.'));
                } else {
                    $model->save();
                    $this->messageManager->addSuccess(__('The Lookbook has been saved.'));                    
                }
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'lookbook_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_lookbook_id' => $this->getRequest()->getParam('current_lookbook_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => TRUE]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the lookbook.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['lookbook_id' => $this->getRequest()->getParam('lookbook_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
