<?php
/**
 * @package		solo
 * @copyright	2014 Nicholas K. Dionysopoulos / Akeeba Ltd 
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Extradirs;


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
		/** @var \Solo\Model\Extradirs $model */
		$model = $this->getModel();
		$directories = $model->get_directories();
		$json = json_encode($directories);
		$this->json =  $json;

		// Get profile ID
		$profileid = \AEPlatform::getInstance()->get_active_profile();
		$this->profileid =  $profileid;

		// Get profile name
		$this->profilename = \AEPlatform::getInstance()->get_profile_name($profileid);

		// Load additional Javascript
		Template::addJs('media://js/solo/extradirs.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);
		Template::addJs('media://js/solo/configuration.js', $this->container->application);

		return true;
	}
} 