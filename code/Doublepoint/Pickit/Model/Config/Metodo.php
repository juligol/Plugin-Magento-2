<?php
namespace  Doublepoint\Pickit\Model\Config;
class Metodo
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'basico' => 'Básico',
            'medio' => 'Medio',
            'completo' => 'Completo'
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
            'basico' => 'Básico',
            'medio' => 'Medio',
            'completo' => 'Completo'
        );
    }

}
