<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Router\Router;
use Awf\Text\Text;

class Upload extends ControllerDefault
{
	/**
	 * This controller does not have a default task
	 *
	 * @return  void
	 *
	 * @throws \RuntimeException
	 */
	public function main()
	{
		throw new \RuntimeException('Invalid task', 500);
	}

	/**
	 * This task starts the upload of the archive to the remote server
	 *
	 * @return  void
	 */
	public function start()
	{
		$id = $this->getAndCheckId();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=upload&tmpl=component&task=cancelled&id=' . $id);

		// Check the backup stat ID
		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		$view = $this->getView();

		$view->done = 0;
		$view->error = 0;

		$view->id = $id;
		$view->setLayout('default');

		$this->display();
	}

	/**
	 * This task steps the upload and displays the results
	 *
	 * @return  void
	 */
	public function upload()
	{
		// Get the parameters
		$id = $this->getAndCheckId();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=upload&tmpl=component&task=cancelled&id=' . $id);

		$part = $this->input->get('part', 0, 'int');
		$frag = $this->input->get('frag', 0, 'int');

		// Check the backup stat ID
		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		$model = $this->getModel();

		$model->setState('id', $id);
		$model->setState('part', $part);
		$model->setState('frag', $frag);

		$result = $model->upload();

		$view = $this->getView();

		$id = $model->getState('id');
		$part = $model->getState('part');
		$frag = $model->getState('frag');
		$stat = $model->getState('stat');
		$remote_filename = $model->getState('remotename');

		if ($part >= 0 && ($result !== false))
		{
			if ($part < $stat['multipart'])
			{
				$view->setLayout('uploading');
				$view->parts = $stat['multipart'];
				$view->part = $part;
				$view->frag = $frag;
				$view->id = $id;
				$view->done = 0;
				$view->error = 0;
			}
			else
			{
				$view->setLayout('done');
				$view->done = 1;
				$view->error = 0;
			}
		}
		else
		{
			$view->done = 0;
			$view->error = 1;
			$view->errorMessage = $model->getState('errorMessage', '');
			$view->setLayout('error');
		}

		$this->display();
	}

	/**
	 * This task shows the error page when the upload fails for any reason
	 *
	 * @return  void
	 */
	public function cancelled()
	{
		$view = $this->getView();

		$view->setLayout('error');

		$this->display();
	}

	/**
	 * Gets the stats record ID from the request and checks that it does exist
	 *
	 * @return  boolean|integer  False if an invalid ID is found, the numeric ID if it's valid
	 */
	private function getAndCheckId()
	{
		$id = $this->input->get('id', 0, 'int');

		if ($id <= 0)
		{
			return false;
		}

		$statObject = \AEPlatform::getInstance()->get_statistics($id);

		if (empty($statObject) || !is_array($statObject))
		{
			return false;
		}

		return $id;
	}
} 