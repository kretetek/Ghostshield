<?php
/**
 * @package		solo
 * @copyright	2014 Nicholas K. Dionysopoulos / Akeeba Ltd 
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Regexfsfilters;


use Awf\Html\Select;
use Awf\Uri\Uri;
use Awf\Utils\Template;

class Html extends \Solo\View\Html
{
	/**
	 * Execute before displaying the main and only page of the off-site files inclusion page
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		// Get a JSON representation of the available roots
		$filters = \AEFactory::getFilters();
		$root_info = $filters->getInclusions('dir');
		$roots = array();
		$options = array();

		if(!empty($root_info))
		{
			// Loop all dir definitions
			foreach($root_info as $dir_definition)
			{
				if(is_null($dir_definition[1]))
				{
					// Site root definition has a null element 1. It is always pushed on top of the stack.
					array_unshift($roots, $dir_definition[0]);
				}
				else
				{
					$roots[] = $dir_definition[0];
				}

				$options[] = Select::option($dir_definition[0], $dir_definition[0]);
			}
		}

		$site_root = $roots[0];
		$attributes = 'onchange="Solo.Regexfsfilters.activeRootChanged();"';
		$this->root_select =  Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root' );
		$this->roots =  $roots;

		// Get a JSON representation of the directories data
		/** @var \Solo\Model\Regexfsfilters $model */
		$model = $this->getModel();
		$directories = $model->get_regex_filters($site_root);
		$json = json_encode($directories);
		$this->json =  $json;

		// Get profile ID
		$profileid = \AEPlatform::getInstance()->get_active_profile();
		$this->profileid =  $profileid;

		// Get profile name
		$this->profilename = \AEPlatform::getInstance()->get_profile_name($profileid);

		// Load additional Javascript
		Template::addJs('media://js/solo/regexfsfilters.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);

		return true;
	}
} 