<?php
namespace  Doublepoint\Pickit\Model\Config;
class Imposicion
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'manual' => 'Manual al realizar envio.',
            'automatica' => 'Automatica segun estado definido.',
            'noimponer' => 'No realizar imposicion.'
        );
    }
}
