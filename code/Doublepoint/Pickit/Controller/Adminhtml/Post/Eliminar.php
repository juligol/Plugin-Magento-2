<?php

namespace Doublepoint\Pickit\Controller\Adminhtml\Post;

use Magento\Framework\Controller\ResultFactory;
 
class Eliminar extends \Magento\Backend\App\Action
{
    public function __construct(
		\Magento\Backend\App\Action\Context $context, 
		\Magento\Ui\Component\MassAction\Filter $filter, 
		\Doublepoint\Pickit\Model\ResourceModel\CollectionFactory $collectionFactory
	)
	{
		$this->filter = $filter;
		$this->collectionFactory = $collectionFactory;
		parent::__construct($context, $filter, $collectionFactory);
	}

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $item->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}