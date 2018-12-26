<?php
namespace  Doublepoint\Pickit\Model\Config;
class TestMode
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => 'Habilitado',
            '0' => 'Deshabilitado'
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
            '1' => 'Habilitado',
            '0' => 'Deshabilitado'
        );
    }

}
