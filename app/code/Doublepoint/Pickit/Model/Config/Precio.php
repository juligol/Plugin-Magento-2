<?php
namespace  Doublepoint\Pickit\Model\Config;
class Precio
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'automatico' => 'Automático',
            'fijo' => 'Fijo',
            'porcentaje' => 'Porcentaje Personalizado'
        );
    }


}
