<?php
namespace  Doublepoint\Pickit\Model\Config;
class FranjaHoraria
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => 'de 8:00 a 17:00 hs',
            '2' => 'de 8:00 a 12:00 hs',
            '3' => 'de 14:00 a 17:00 hs'
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
            '1' => 'de 8:00 a 17:00 hs',
            '2' => 'de 8:00 a 12:00 hs',
            '3' => 'de 14:00 a 17:00 hs'
        );
    }

}
