<?php
namespace Doublepoint\Pickit\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		$this->insertarPickitOrder($installer);
		//$this->insertarPickitEstados($installer);
		//$this->insertarArgentina($installer);
		$installer->endSetup();
	}

	public function insertarPickitOrder($installer) 
	{
		if (!$installer->tableExists('pickit_order')) {	
			$table = $installer->getConnection()->newTable(
				$installer->getTable('pickit_order')
			)
				->addColumn(
					'post_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['identity' => true, 'nullable' => false, 'primary'  => true, 'unsigned' => true],
					null
				)
				->addColumn(
					'id_orden',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'direccion',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'localidad',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'provincia',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'cp_destino',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'nombre',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'apellido',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'email',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'telefono',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'precio',
					\Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'valor_declarado',
					\Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'volumen',
					\Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'peso',
					\Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'cod_tracking',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'estado',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'tracking',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					null
				)
				->addColumn(
					'constancia',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					600,
					['nullable' => false],
					null
				)
				->addColumn(
					'order_increment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => false],
					null
				)
				->addColumn(
					'id_cotizacion',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'dni',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					20,
					['nullable' => false],
					null
				)
				->addColumn(
					'datos_sucursal',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'id_transaccion',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['nullable' => false],
					null
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					null
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					null
				)
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('pickit_order'),
				$installer->getIdxName(
					$installer->getTable('pickit_order'),
					['direccion','localidad','provincia','cp_destino','nombre','apellido','email','telefono','cod_tracking','estado','tracking',
						'constancia','order_increment_id','dni','datos_sucursal'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['direccion','localidad','provincia','cp_destino','nombre','apellido','email','telefono','cod_tracking','estado','tracking',
					'constancia','order_increment_id','dni','datos_sucursal'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
	}

	public function insertarPickitEstados($installer) 
	{
		if (!$installer->tableExists('pickit_estados')) {	
			$table = $installer->getConnection()->newTable(
				$installer->getTable('pickit_estados')
			)
				->addColumn(
					'post_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['identity' => true, 'nullable' => false, 'primary'  => true, 'unsigned' => true],
					null
				)
				->addColumn(
					'descripcion',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					null
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					null
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					null
				)
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('pickit_estados'),
				$installer->getIdxName(
					$installer->getTable('pickit_estados'),
					['descripcion'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['descripcion'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
		$this->insertarEstados($installer);
	}

	public function insertarEstados($installer) 
	{
		$data = ['En retailer', 'Disponible para Colecta'];
        foreach ($data as $row) {
            $bind = ['descripcion' => $row];
            $installer->getConnection()->insert($installer->getTable('pickit_estados'), $bind);
        }
	}
	
	public function insertarArgentina($installer) 
	{
		$data = [
            ['AR', 'BA', 	'Buenos Aires'],
            ['AR', 'CABA', 	'Ciudad Autónoma de Buenos Aires'],
            ['AR', 'CT', 	'Catamarca'],
            ['AR', 'CC', 	'Chaco'],
            ['AR', 'CH', 	'Chubut'],
            ['AR', 'CD', 	'Córdoba'],
            ['AR', 'CR', 	'Corrientes'],
            ['AR', 'ER', 	'Entre Ríos'],
            ['AR', 'FO', 	'Formosa'],
            ['AR', 'JY', 	'Jujuy'],
            ['AR', 'LP', 	'La Pampa'],
            ['AR', 'LR', 	'La Rioja'],
            ['AR', 'MZ', 	'Mendoza'],
            ['AR', 'MN', 	'Misiones'],
            ['AR', 'NQ', 	'Neuquén'],
            ['AR', 'RN', 	'Río Negro'],
            ['AR', 'SA', 	'Salta'],
            ['AR', 'SJ', 	'San Juan'],
            ['AR', 'SL', 	'San Luis'],
            ['AR', 'SC', 	'Santa Cruz'],
            ['AR', 'SF', 	'Santa Fe'],
            ['AR', 'SE', 	'Santiago del Estero'],
            ['AR', 'TF', 	'Tierra del Fuego, Antártida e Islas del Atlántico Sur'],
            ['AR', 'TM', 	'Tucumán']
        ];
        foreach ($data as $row) {
            $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
            $installer->getConnection()->insert($installer->getTable('directory_country_region'), $bind);
            $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory_country_region'));
            $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
            $installer->getConnection()->insert($installer->getTable('directory_country_region_name'), $bind);
        }
	}
}