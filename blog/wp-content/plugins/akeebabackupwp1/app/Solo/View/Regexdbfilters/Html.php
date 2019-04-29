<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Regexdbfilters;


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
		// Get a JSON representation of the directories data
		/** @var \Solo\Model\Regexdbfilters $model */
		$model = $this->getModel();

		$root_info = $model->get_roots();
		$roots = array();
		if (!empty($root_info))
		{
			// Loop all dir definitions
			foreach ($root_info as $def)
			{
				$roots[] = $def->value;
				$options[] = Select::option($def->value, $def->text);
			}
		}
		$site_root = '[SITEDB]';
		$attributes = 'onchange="Solo.Regexdbfilters.activeRootChanged();"';
		$this->root_select = Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root');
		$this->roots = $roots;

		$json = json_encode($model->get_regex_filters($site_root));
		$this->json =  $json ;

		// Get profile ID
		$profileid = \AEPlatform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$this->profilename = \AEPlatform::getInstance()->get_profile_name($profileid);

		// Load additional Javascript
		Template::addJs('media://js/solo/regexdbfilters.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);

		return true;
	}
} 