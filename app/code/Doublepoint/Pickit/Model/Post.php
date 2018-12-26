<?php
namespace Doublepoint\Pickit\Model;

class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'pickit_order';

	protected $_cacheTag = 'pickit_order';

	protected $_eventPrefix = 'pickit_order';

	protected function _construct()
	{
		$this->_init('Doublepoint\Pickit\Model\ResourceModel\Post');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}