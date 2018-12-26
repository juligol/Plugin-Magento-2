<?php
namespace  Doublepoint\Pickit\Model\Config;
class PesoMax
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '30000' => '30 Kg',
            '50000' => '50 Kg',
            '100000' => '100 Kg'
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
            '30000' => '30 Kg',
            '50000' => '50 Kg',
            '100000' => '100 Kg'
        );
    }

}
