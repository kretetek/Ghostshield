<?php
/**
 * @package        solo
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Extradirs\Html $this */

$router = $this->container->router;

?>
<div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="dialogLabel">
					<?php echo Text::_('CONFIG_UI_BROWSER_TITLE'); ?>
				</h4>
			</div>
			<div class="modal-body" id="dialogBody">
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="errorDialog" tabindex="-1" role="dialog" aria-labelledby="errorDialogLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="errorDialogLabel">
					<?php echo Text::_('CONFIG_UI_AJAXERRORDLG_TITLE'); ?>
				</h4>
			</div>
			<div class="modal-body" id="errorDialogBody">
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
				<th>&nbsp;</th>
				<!-- Edit -->
				<th>&nbsp;</th>
				<!-- Directory path -->
				<th rel="popover" data-original-title="<?php echo Text::_('EFF_LABEL_DIRECTORY') ?>"
					data-content="<?php echo Text::_('EFF_LABEL_DIRECTORY_HELP') ?>">
					<?php echo Text::_('EFF_LABEL_DIRECTORY') ?>
				</th>
				<!-- Directory path -->
				<th rel="popover" data-original-title="<?php echo Text::_('EFF_LABEL_VINCLUDEDIR') ?>"
					data-content="<?php echo Text::_('EFF_LABEL_VINCLUDEDIR_HELP') ?>">
					<?php echo Text::_('EFF_LABEL_VINCLUDEDIR') ?>
				</th>
			</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>


<script type="text/javascript">
Solo.loadScripts[Solo.loadScripts.length] = function () {
	(function($){
		Solo.System.params.AjaxURL = '<?php echo Escape::escapeJS($router->route('index.php?view=extradirs&task=ajax&format=raw')) ?>';

		/**
		 * AJAX error callback
		 *
		 * @param   msg  The error message to show
		 */
		Solo.System.params.errorCallback = function(msg)
		{
			$('#errorDialogLabel').html('<?php echo Escape::escapeJS(Text::_('CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
			$('#errorDialogBody').html('');
			var alertBox = $(document.createElement('div')).addClass('alert alert-danger');
			alertBox.html('<?php echo Escape::escapeJS(Text::_('CONFIG_UI_AJAXERRORDLG_TEXT')) ?><br><pre>' + msg + '</pre>');
			alertBox.appendTo($('#errorDialogBody'));
			$('#errorDialog').modal({backdrop: 'static', keyboard: false});
		}

		// Push translations
		Solo.Extradirs.translations['UI-ROOT'] = '<?php echo Escape::escapeJS(Text::_('FILTERS_LABEL_UIROOT')) ?>';
		Solo.Extradirs.translations['UI-ERROR-FILTER'] = '<?php echo Escape::escapeJS(Text::_('FILTERS_LABEL_UIERRORFILTER')) ?>';

		// Push some custom URLs
		Solo.Configuration.URLs['browser'] = '<?php echo Escape::escapeJS($router->route('index.php?view=browser&tmpl=component&processfolder=1&folder=')) ?>';

		// Bootstrap the page display
		var data = eval(<?php echo Escape::escapeJS($this->json,"'"); ?>);

		Solo.Extradirs.render(data);
	}(akeeba.jQuery));
};
</script>