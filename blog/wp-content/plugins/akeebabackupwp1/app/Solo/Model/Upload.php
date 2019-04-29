<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Mvc\Model;

class Upload extends Model
{
	/**
	 * Uploads a fragment of the backup archive to the remote server
	 *
	 * @return  boolean|integer  False on failure, true on success, 1 if more work is required
	 */
	public function upload()
	{
		$id = $this->getState('id', -1);
		$part = $this->getState('part', -1);
		$frag = $this->getState('frag', -1);

		// Calculate the filenames
		$stat = \AEPlatform::getInstance()->get_statistics($id);
		$local_filename = $stat['absolute_path'];
		$basename = basename($local_filename);
		$extension = strtolower(str_replace(".", "", strrchr($basename, ".")));

		if ($part > 0)
		{
			$new_extension = substr($extension, 0, 1) . sprintf('%02u', $part);
		}
		else
		{
			$new_extension = $extension;
		}

		$filename = $basename . '.' . $new_extension;
		$local_filename = substr($local_filename, 0, -strlen($extension)) . $new_extension;

		// Load the post-processing engine
		\AEPlatform::getInstance()->load_configuration($stat['profile_id']);
		$config = \AEFactory::getConfiguration();

		$session = $this->container->segment;
		$engine = null;

		if (!empty($savedEngine) && ($frag != -1))
		{
			// If it's not the first fragment, try to revive the saved engine
			$savedEngine = $session->get('postproc_engine', null, 'akeeba');
			$engine = unserialize($savedEngine);
		}

		if (empty($engine))
		{
			$engine_name = $config->get('akeeba.advanced.proc_engine');
			$engine = \AEFactory::getPostprocEngine($engine_name);
		}

		// Start uploading
		$result = $engine->processPart($local_filename);

		switch ($result)
		{
			case true:
				$part++;
				break;

			case 1:
				$frag++;
				$savedEngine = serialize($engine);
				$session->set('postproc_engine', null, 'akeeba');
				break;

			case false;
				$warning = $engine->getWarning();
				$error = $engine->getError();
				$this->setState('errorMessage', empty($warning) ? $error : $warning);
				$part = -1;

				return false;
				break;
		}

		$remote_filename = $config->get('akeeba.advanced.proc_engine', '') . '://';
		$remote_filename .= $engine->remote_path;

		if ($part >= 0)
		{
			if ($part >= $stat['multipart'])
			{
				// Update stats with remote filename
				$data = array(
					'remote_filename' => $remote_filename
				);

				\AEPlatform::getInstance()->set_or_update_statistics($id, $data, $engine);
			}
		}

		$this->setState('id', $id);
		$this->setState('part', $part);
		$this->setState('frag', $frag);
		$this->setState('stat', $stat);
		$this->setState('remotename', $remote_filename);

		return $result;
	}
} 