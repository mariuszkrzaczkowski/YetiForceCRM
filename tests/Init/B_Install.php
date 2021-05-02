<?php

/**
 * Install test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\Init;

class B_Install extends \Tests\Base
{
	/**
	 * Testing database installation from SQL file.
	 */
	public function testInstall()
	{
		$db = \App\Db::getInstance();
		$schema = $db->getSchema();

		$this->assertNotNull($schema->getTableSchema('a_yf_adv_permission'));
		$this->assertNotNull($schema->getTableSchema('yetiforce_updates'));
		$this->assertTrue(((new \App\Db\Query())->from('vtiger_ws_fieldtype')->count()) > 0);
	}
}
