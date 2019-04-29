<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\S3import;


use Awf\Uri\Uri;

class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		$document = $this->container->application->getDocument();

		$model = $this->getModel();
		$model->getS3Credentials();
		$contents = $model->getContents();
		$buckets = $model->getBuckets();
		$bucketSelect = $model->getBucketsDropdown();
		$root = $model->getState('folder', '', 'raw');

		// Assign variables
		$this->s3access = $model->getState('s3access');
		$this->s3secret = $model->getState('s3secret');
		$this->buckets = $buckets;
		$this->bucketSelect = $bucketSelect;
		$this->contents = $contents;
		$this->root = $root;
		$this->crumbs = $model->getCrumbs();

		return true;
	}

	public function onBeforeDownloadToServer()
	{
		$this->setLayout('downloading');
		$model = $this->getModel();

		$total = $model->getState('totalsize', 0, 'int');
		$done = $model->getState('donesize', 0, 'int');
		$part = $model->getState('part', 0, 'int') + 1;
		$parts = $model->getState('totalparts', 0, 'int');

		if ($total <= 0)
		{
			$percent = 0;
		}
		else
		{
			$percent = (int)(100 * ($done / $total));

			if ($percent < 0)
			{
				$percent = 0;
			}

			if ($percent > 100)
			{
				$percent = 100;
			}
		}

		$this->total = $total;
		$this->done = $done;
		$this->percent = $percent;
		$this->total_parts = $parts;
		$this->current_part = $part;

		// Render the progress bar
		$document = $this->container->application->getDocument();
		$this->step = $model->getState('step', 1, 'int') + 1;

		return true;
	}
} 