<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Database\Driver;
use Awf\Html\Select;
use Awf\Mvc\Model;
use Awf\Text\Text;

class Multidb extends Model
{
	/**
	 * Returns an array containing a list of database definitions
	 *
	 * @return  array  Array of definitions; The key contains the internal root name, the data is the database
	 *                 configuration data.
	 */
	public function get_databases()
	{
		// Get database inclusion filters
		$filter = \AEFactory::getFilterObject('multidb');
		$database_list = $filter->getInclusions('db');

		return $database_list;
	}

	/**
	 * Delete a database definition
	 *
	 * @param   string  $root  The name of the database root key to remove
	 *
	 * @return  boolean  True on success
	 */
	public function remove($root)
	{
		$filter = \AEFactory::getFilterObject('multidb');
		$success = $filter->remove($root, null);
		$filters = \AEFactory::getFilters();

		if ($success)
		{
			$filters->save();
		}

		return $success;
	}

	/**
	 * Creates a new database definition
	 *
	 * @param   string  $root
	 * @param   array   $data
	 *
	 * @return  boolean
	 */
	public function setFilter($root, $data)
	{
		$filter = \AEFactory::getFilterObject('multidb');
		$success = $filter->set($root, $data);
		$filters = \AEFactory::getFilters();

		if ($success)
		{
			$filters->save();
		}

		return $success;
	}

	/**
	 * Tests the connectivity to a database
	 *
	 * @param   array  $data
	 *
	 * @return  array  Status array: 'status' is true on success, 'message' contains any error message while connecting
	 *                 to the database
	 */
	public function test($data)
	{
		$error = '';

		try
		{
			$db = \AEFactory::getDatabase($data);
			if ($db->getErrorNum() > 0)
			{
				$error = $db->getErrorMsg();
			}
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}


		return array(
			'status'  => empty($error),
			'message' => $error
		);
	}

	/**
	 * AJAX request proxy
	 *
	 * @return   array|boolean
	 */
	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch ($verb)
		{
			// Set a filter (used by the editor)
			case 'set':
				$ret_array = $this->setFilter($action['root'], $action['data']);
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = array('success' => $this->remove($action['root']));
				break;

			// Test connection (used by the editor)
			case 'test':
				$ret_array = $this->test($action['data']);
				break;
		}

		return $ret_array;
	}

	public function getDatabaseDriverOptions()
	{
		$connectors = Driver::getConnectors();
		$options = array();

		foreach ($connectors as $connector)
		{
			$options[] = Select::option(strtolower($connector), Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_' . $connector));
		}

		return $options;
	}
} 