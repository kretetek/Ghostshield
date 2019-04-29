<?php
/**
 * @package		solo
 * @copyright	2014 Nicholas K. Dionysopoulos / Akeeba Ltd 
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Multidb;


use Awf\Database\Driver;
use Awf\Html\Select;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Model\Multidb;

class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		// Get a JSON representation of the database connection data
		/** @var Multidb $model */
		$model = $this->getModel();
		$databases = $model->get_databases();
		$json = json_encode($databases);
		$this->json =  $json;

		// Get profile ID
		$profileid = \AEPlatform::getInstance()->get_active_profile();
		$this->profileid =  $profileid;

		// Get profile name
		$this->profilename =  \AEPlatform::getInstance()->get_profile_name($this->profileid);

		// Get the possible database drivers
		$this->dbDriversOptions = $model->getDatabaseDriverOptions();

		// Load additional Javascript
		Template::addJs('media://js/solo/multidb.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);

		return true;
	}
} 