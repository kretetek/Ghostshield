<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Mvc\Model;

class Regexfsfilters extends Model
{
	/**
	 * Returns an array containing a list of regex filters and their respective type for a given root
	 *
	 * @param   string  $root  Which root directory to return the filters for
	 *
	 * @return  array  Array of filter definitions
	 */
	public function get_regex_filters($root)
	{
		// These are the regex filters I know of
		$known_filters = array(
			'regexfiles',
			'regexdirectories',
			'regexskipdirs',
			'regexskipfiles'
		);

		// Filters already set
		$set_filters = array();

		// Loop all filter types
		foreach ($known_filters as $filter_name)
		{
			// Get this filter type's set filters
			$filter = \AEFactory::getFilterObject($filter_name);
			$temp_filters = $filter->getFilters($root);

			// Merge this filter type's regular expressions to the list
			if (count($temp_filters))
			{
				foreach ($temp_filters as $new_regex)
				{
					$set_filters[] = array(
						'type' => $filter_name,
						'item' => $new_regex
					);
				}
			}

		}

		return $set_filters;
	}

	/**
	 * Delete a regex filter
	 *
	 * @param   string  $type    Filter type
	 * @param   string  $root    The filter's root
	 * @param   string  $string  The filter string to remove
	 *
	 * @return  boolean True on success
	 */
	public function remove($type, $root, $string)
	{
		$filter = \AEFactory::getFilterObject($type);
		$success = $filter->remove($root, $string);

		if ($success)
		{
			$filters = \AEFactory::getFilters();
			$filters->save();
		}

		return $success;
	}

	/**
	 * Creates a new regex filter
	 *
	 * @param   string  $type    Filter type
	 * @param   string  $root    The filter's root
	 * @param   string  $string  The filter string to remove
	 *
	 * @return  boolean  True on success
	 */
	public function setFilter($type, $root, $string)
	{
		$knownFilters = $this->get_regex_filters($root);
		$item = array('type' => $type, 'item' => $string);
		if (in_array($item, $knownFilters))
		{
			$success = true;
		}
		else
		{
			$filter = \AEFactory::getFilterObject($type);
			$success = $filter->set($root, $string);

			if ($success)
			{
				$filters = \AEFactory::getFilters();
				$filters->save();
			}
		}

		return $success;
	}

	/**
	 * AJAX request proxy
	 *
	 * @return  array  A return array, depending on the method used
	 */
	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch ($verb)
		{
			// Produce a list of regex filters
			case 'list':
				$ret_array = $this->get_regex_filters($action['root']);
				break;

			// Set a filter (used by the editor)
			case 'set':
				$ret_array = array('success' => $this->setFilter($action['type'], $action['root'], $action['node']));
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = array('success' => $this->remove($action['type'], $action['root'], $action['node']));
				break;
		}

		return $ret_array;
	}
} 