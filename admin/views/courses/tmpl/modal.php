<?php
/**
 * @package     Courses
 * @subpackage  com_courses
 *
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @copyright   Copyright (C) 2014 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Get the application.
$app = JFactory::getApplication();

if ($app->isSite())
{
	// Check for request forgeries.
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

// Load dependent classes.
require_once JPATH_SITE . '/components/com_courses/helpers/route.php';

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip bootstrap script.
JHtml::_('bootstrap.tooltip');

// Load the modal behavior script.
JHtml::_('behavior.framework', true);

// Initialiase variables.
$function  = $app->input->getCmd('function', 'jSelectCourse');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_courses&view=courses&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label>
			</div>
			<div class="btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_COURSES_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
					<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';document.id('filter_access').value='';document.id('filter_state').value='';document.id('filter_category_id').value='';document.id('filter_language').value='';this.form.submit();">
					<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters pull-left">
			<select name="filter_state" id="filter_state" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true); ?>
			</select>
			<select name="filter_access" id="filter_access" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS'); ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')); ?>
			</select>
			<?php if ($this->state->get('filter.forcedLanguage')): ?>
				<select name="filter_category_id" id="filter_category_id" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_courses', array('filter.language' => array('*', $this->state->get('filter.forcedLanguage')))), 'value', 'text', $this->state->get('filter.category_id')); ?>
				</select>
				<input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
				<input type="hidden" name="filter_language" value="<?php echo $this->escape($this->state->get('filter.language')); ?>" />
			<?php else: ?>
				<select name="filter_category_id" id="filter_category_id" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_courses'), 'value', 'text', $this->state->get('filter.category_id')); ?>
				</select>
				<select name="filter_language" id="filter_language" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
				</select>
			<?php endif; ?>
		</div>
	</fieldset>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_COURSES_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item):
				if ($item->language && JLanguageMultilang::isEnabled())
				{
					$tag = strlen($item->language);

					if ($tag == 5)
					{
						$lang = substr($item->language, 0, 2);
					}
					elseif ($tag == 6)
					{
						$lang = substr($item->language, 0, 3);
					}
					else {
						$lang = "";
					}
				}
				elseif (!JLanguageMultilang::isEnabled())
				{
					$lang = "";
				}
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', null, '<?php echo $this->escape(CoursesHelperRoute::getCourseRoute($item->id, $item->catid, $item->language)); ?>', '<?php echo $this->escape($lang); ?>', null);"><?php echo $this->escape($item->title); ?></a>
					</td>
					<td class="center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="center">
						<?php if ($item->language == '*'): ?>
							<?php echo JText::alt('JALL', 'language'); ?>
						<?php else: ?>
							<?php echo $item->language_title ? $this->escape($item->language_title): JText::_('JUNDEFINED'); ?>
						<?php endif; ?>
					</td>
					<td class="center nowrap">
						<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>