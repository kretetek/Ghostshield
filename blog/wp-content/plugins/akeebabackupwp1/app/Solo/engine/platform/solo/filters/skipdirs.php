<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Subdirectories exclusion filter
 */
class AEFilterPlatformSkipdirs extends AEAbstractFilter
{
	function __construct()
	{
		$this->object = 'dir';
		$this->subtype = 'children';
		$this->method = 'direct';
		$this->filter_name = 'PlatformSkipdirs';

		if (AEFactory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = AEFactory::getConfiguration();

		// Get the site's root
		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array(
			// Output & temp directory of the application
			self::treatDirectory($configuration->get('akeeba.basic.output_directory')),
			// Default backup output directory
			self::treatDirectory(APATH_BASE . '/backups'),
		);

		if (!$configuration->get('akeeba.platform.addsolo', 0))
		{
			$this->filter_data[$root][] = self::treatDirectory(APATH_BASE);
		}
		else
		{
			$soloRoot = APATH_BASE;
			$this->filter_data[$soloRoot] = array(
				self::treatDirectory($configuration->get('akeeba.basic.output_directory'), $soloRoot),
				'backups',
				self::treatDirectory(APATH_BASE . '/backups', $soloRoot),
			);
		}

		parent::__construct();
	}

	private static function treatDirectory($directory, $root = null)
	{
		// Get the site's root
		$configuration = AEFactory::getConfiguration();

		if (empty($root))
		{
			$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');
			if (stristr($root, '['))
			{
				$root = AEUtilFilesystem::translateStockDirs($root);
			}
		}
		$site_root = AEUtilFilesystem::TrimTrailingSlash(AEUtilFilesystem::TranslateWinPath($root));

		$directory = AEUtilFilesystem::TrimTrailingSlash(AEUtilFilesystem::TranslateWinPath($directory));

		// Trim site root from beginning of directory
		if (substr($directory, 0, strlen($site_root)) == $site_root)
		{
			$directory = substr($directory, strlen($site_root));
			if (substr($directory, 0, 1) == '/')
			{
				$directory = substr($directory, 1);
			}
		}

		return $directory;
	}
}