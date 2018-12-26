<?php
namespace Doublepoint\Pickit\Ui\Component\Listing;

class PickitDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
	protected function _initSelect()
	{
		parent::_initSelect();
		$this->getSelect()->joinLeft(
			['pe' => $this->getTable('pickit_estados')],
			'main_table.estado = pe.post_id',
			['descripcion']
		);
		return $this;
	}
}