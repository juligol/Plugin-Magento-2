<?php
namespace  Doublepoint\Pickit\Model\Config;
class DisponibleRetiro
{
   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'imprimir' => 'Al imprimir etiqueta.',
            'imponer' => 'Al imponer.'
        );
    }
}
