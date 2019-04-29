<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Joomla!-specific Filter: Skip Directories
 *
 * Exclude files of special directories
 */
class AEFilterPlatformJskipfiles extends AEAbstractFilter
{	
	function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'content';
		$this->method	= 'direct';

		$configuration = AEFactory::getConfiguration();
		
		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'joomla')
		{
			$this->enabled = false;
			return;
		}

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array (
			// Output & temp directory of the application
			self::treatDirectory($configuration->get('akeeba.basic.output_directory')),
			// default temp directory
			'tmp',
			// cache directories
			'cache',
			'administrator/cache',
			// This is not needed except on sites running SVN or beta releases
			'installation',
			// Default backup output for Akeeba Backup
			'administrator/components/com_akeeba/backup',
			// MyBlog's cache
			'components/libraries/cmslib/cache',
			// The logs directory
			'logs',
			'log'
		);
	}

	private static function treatDirectory($directory)
	{
		// Get the site's root
		$configuration = AEFactory::getConfiguration();
		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		if(stristr($root, '['))
		{
			$root = AEUtilFilesystem::translateStockDirs($root);
		}

		$site_root = AEUtilFilesystem::TrimTrailingSlash(AEUtilFilesystem::TranslateWinPath($root));

		$directory = AEUtilFilesystem::TrimTrailingSlash(AEUtilFilesystem::TranslateWinPath($directory));

		// Trim site root from beginning of directory
		if( substr($directory, 0, strlen($site_root)) == $site_root )
		{
			$directory = substr($directory, strlen($site_root));
			if( substr($directory,0,1) == '/' ) $directory = substr($directory,1);
		}

		return $directory;
	}

}