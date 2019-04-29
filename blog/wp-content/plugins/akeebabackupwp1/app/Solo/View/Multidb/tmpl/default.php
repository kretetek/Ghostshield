<?php
/**
 * @package     Solo
 * @copyright   2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use \Awf\Text\Text;
use \Solo\Helper\Escape;

// Used for type hinting
/** @var \Solo\View\Multidb\Html $this */

$router = $this->container->router;

$token = $this->container->session->getCsrfToken()->getValue();
?>

<div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="dialogLabel">
				</h4>
			</div>
			<div class="modal-body" id="dialogBody">
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="akEditorDialog" tabindex="-1" role="dialog" aria-labelledby="akEditorDialogLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="akEditorDialogLabel">
					<?php echo Text::_('FILTER_EDITOR_TITLE') ?>
				</h4>
			</div>
			<div class="modal-body" id="akEditorDialogBody">
				<div class="form-horizontal" id="ak_editor_table">
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_driver">
							<?php echo Text::_('UI-MULTIDB-DRIVER')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<?php echo \Awf\Html\Select::genericList($this->dbDriversOptions, 'ake_driver', array('class' => 'form-control'), 'value', 'text', 'mysqli', 'ake_driver'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_host">
							<?php echo Text::_('UI-MULTIDB-HOST')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="ake_host" id="ake_host" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_port">
							<?php echo Text::_('UI-MULTIDB-PORT')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="number" name="ake_port" id="ake_port" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_username">
							<?php echo Text::_('UI-MULTIDB-USERNAME')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="ake_username" id="ake_username" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_password">
							<?php echo Text::_('UI-MULTIDB-PASSWORD')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="password" name="ake_password" id="ake_password" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_database">
							<?php echo Text::_('UI-MULTIDB-DATABASE')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="ake_database" id="ake_database" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3 col-xs-12" for="ake_prefix">
							<?php echo Text::_('UI-MULTIDB-PREFIX')?>
						</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="ake_prefix" id="ake_prefix" class="form-control">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="akEditorBtnDefault"><?php echo Text::_('UI-MULTIDB-TEST'); ?></button>
				<button type="button" class="btn btn-primary" id="akEditorBtnSave"><?php echo Text::_('UI-MULTIDB-SAVE'); ?></button>
				<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo Text::_('UI-MULTIDB-CANCEL'); ?></button>
			</div>
		</div>
	</div>
</div>

<div class="alert alert-info">
	<strong><?php echo Text::_('CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<fieldset>
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
			<tr>
				<!-- Delete -->
				<th width="2em">&nbsp;</th>
				<!-- Edit -->
				<th width="2em">&nbsp;</th>
				<!-- Database host -->
				<th><?php echo Text::_('MULTIDB_LABEL_HOST') ?></th>
				<!-- Database -->
				<th><?php echo Text::_('MULTIDB_LABEL_DATABASE') ?></th>
			</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">
Solo.loadScripts[Solo.loadScripts.length] = function () {
	(function($){
		// Set the AJAX proxy URL
		Solo.System.params.AjaxURL = '<?php echo Escape::escapeJS($router->route('index.php?view=multidb&task=ajax')) ?>';

		/**
		 * AJAX error callback
		 *
		 * @param   msg  The error message to show
		 */
		Solo.System.params.errorCallback = function(msg)
		{
			$('#dialogLabel').html('<?php echo Escape::escapeJS(Text::_('CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
			$('#dialogBody').html('');
			var alertBox = $(document.createElement('div')).addClass('alert alert-danger');
			alertBox.html('<?php echo Escape::escapeJS(Text::_('CONFIG_UI_AJAXERRORDLG_TEXT')) ?><br><pre>' + msg + '</pre>');
			alertBox.appendTo($('#dialogBody'));
			$('#dialog').modal({backdrop: 'static', keyboard: false});
		}

		// Push translations
		Solo.Multidb.translations['UI-ROOT'] = '<?php echo Escape::escapeJS(Text::_('FILTERS_LABEL_UIROOT')) ?>';
		Solo.Multidb.translations['UI-ERROR-FILTER'] = '<?php echo Escape::escapeJS(Text::_('FILTERS_LABEL_UIERRORFILTER')) ?>';
		<?php
			$keys = array(
				'UI-MULTIDB-HOST', 'UI-MULTIDB-PORT', 'UI-MULTIDB-USERNAME', 'UI-MULTIDB-PASSWORD',
				'UI-MULTIDB-DATABASE', 'UI-MULTIDB-PREFIX', 'UI-MULTIDB-TEST', 'UI-MULTIDB-SAVE',
				'UI-MULTIDB-CANCEL', 'UI-MULTIDB-LOADING', 'UI-MULTIDB-CONNECTOK',
				'UI-MULTIDB-CONNECTFAIL', 'UI-MULTIDB-SAVEFAIL', 'UI-MULTIDB-DRIVER'
			);
			foreach($keys as $key)
			{
				echo "\tSolo.Multidb.translations['".$key."'] = '" . Escape::escapeJS(Text::_($key))."';\n";
			}
		?>

		// Push the location to the loading image file
		Solo.Multidb.loadingGif = '<?php echo \Awf\Uri\Uri::base(false, $this->container) . 'media/loading.gif' ?>';

		// Bootstrap the page display
		var data = JSON.parse('<?php echo Escape::escapeJS($this->json,"'"); ?>');

		Solo.Multidb.render(data);
	}(akeeba.jQuery));
};
</script>