<?php

class Webplanet_Dailydeal_Adminhtml_DailydealController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        try {
            $this->loadLayout()
                    ->_setActiveMenu('promo/webplanet_dailydeal')
                    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Daily Deals Management'), Mage::helper('adminhtml')->__('Daily Deals Management'));
        } catch (Exception $ex) {
            
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $block = $this->getLayout()->createBlock('dailydeal/adminhtml_deals');
        $content_block = $this->getLayout()->getBlock('content');
        $content_block->append($block);
        $this->renderLayout();
    }

    public function editAction()
    {

        $id = $this->getRequest()->getParam('id');

        $deal = Mage::getModel('dailydeal/deal')->load($id);

        if ($deal->getId() || !$id) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $deal->setData($data);
            }

            Mage::register('dailydeal', $deal);

            $this->loadLayout();
            $this->_setActiveMenu('promo/webplanet_dailydeal');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent(
                    $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit'))->_addLeft(
                    $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit_tabs'));


            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        try {
            if (($this->getRequest() == null) || ($this->getRequest()->getPost() == null)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find item to save'));
                $this->_redirect('*/*/');
                return;
            }

            $data = $this->getRequest()->getPost();

            $model = Mage::getModel('dailydeal/deal');
            // Fill model with POST data
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));


            // Update creation/update date
            $gmtNow = Mage::getModel('core/date')->gmtDate(time());
            if (!$model->getCreatedTime())
                $model->setCreatedTime($gmtNow);
            $model->setUpdateTime($gmtNow);

            // Save model to db
            $model->save();

            // Add notifications
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Item was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);


            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        try {
            if ($this->getRequest()->getParam('id') > 0) {
                $deal = Mage::getModel('dailydeal/deal')->load($this->getRequest()->getParam('id'));
                $productId = $deal->getProductId();

                $deal->delete();

                // Clear product cache (to ensure the observer removes watermark and special price etc.):
                $product = Mage::getModel('catalog/product')->load($productId);
                $product->cleanCache();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            }
            $this->_redirect('*/*/');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }

    public function massDeleteAction()
    {

        $dealIds = $this->getRequest()->getParam('deal_id');
        if (!is_array($dealIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($dealIds as $dealId) {
                    $deal = Mage::getModel('dailydeal/deal')->load($dealIds);
                    $deal->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($dealIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function gridProductAction()
    {
        // for ajax call
        $response = $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit_product_grid')->getHtml();
        $this->getResponse()->setBody($response);
    }

}
