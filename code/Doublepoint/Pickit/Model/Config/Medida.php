<?php
namespace  Doublepoint\Pickit\Model\Config;
class Medida
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'gramos' => 'Gramos',
            'kilos' => 'Kg'
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'gramos' => 'Gramos',
            'kilos' => 'Kg'
        );
    }

}
