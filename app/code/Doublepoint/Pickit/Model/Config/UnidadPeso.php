<?php
namespace  Doublepoint\Pickit\Model\Config;
class Unidadpeso
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'kg' => 'Kilogramos',
            'l' => 'Litros',
            'pv' => 'Peso Volumétrico'
        );
    }

}
