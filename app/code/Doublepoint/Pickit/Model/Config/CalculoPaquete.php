<?php
namespace  Doublepoint\Pickit\Model\Config;
class CalculoPaquete
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => 'Un paquete por pedido',
            '2' => 'Un paquete para cada producto'
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
            '1' => 'Un paquete por pedido',
            '2' => 'Un paquete para cada producto'
        );
    }

}
