<?php
namespace Doublepoint\Pickit\Model\ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'pickit_post_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Doublepoint\Pickit\Model\Post', 'Doublepoint\Pickit\Model\ResourceModel\Post');
	}

}