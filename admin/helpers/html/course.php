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

/**
 * Utility class working with course.
 *
 * @package     Courses
 * @subpackage  com_courses
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @since       3.2
 */
abstract class JHtmlCourse
{
	/**
	 * Render the list of associated items.
	 *
	 * @param   int  $courseid  The course item id.
	 *
	 * @return  string  The language HTML.
	 */
	public static function association($courseid)
	{
		// Defaults.
		$html = '';

		// Get the associations.
		if ($associations = JLanguageAssociations::getAssociations('com_courses', '#__courses', 'com_courses.item', $courseid))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Create the base select statement.
			$query->select('c.*')
				->select('l.sef as lang_sef')
				->from('#__courses as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id = c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language = l.lang_code')
				->select('l.image')
				->select('l.title as language_title');

			// Set the query and load the result.
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$url = JRoute::_('index.php?option=com_courses&task=course.edit&id=' . (int) $item->id);
					$tooltipParts = array(
						JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
							$item->language_title,
							array('title' => $item->language_title),
							true
						),
						$item->title,
						'(' . $item->category_title . ')'
					);

					$item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}

	/**
	 * Show the feature/unfeature links.
	 *
	 * @param   int      $value      The state value.
	 * @param   int      $i          Row number.
	 * @param   boolean  $canChange  Is user allowed to change?
	 *
	 * @return  string  HTML code.
	 */
	public static function featured($value = 0, $i = 0, $canChange = true)
	{
		// Load the tooltip bootstrap script.
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action.
		$states = array(
			0 => array('unfeatured', 'courses.featured', 'COM_COURSES_UNFEATURED', 'COM_COURSES_TOGGLE_TO_FEATURE'),
			1 => array('featured', 'courses.unfeatured', 'COM_COURSES_FEATURED', 'COM_COURSES_TOGGLE_TO_UNFEATURE'),
		);
		$state  = JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon   = $state[0];

		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-' . $icon . '"></i></a>';
		}
		else
		{
			$html = '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-' . $icon . '"></i></a>';
		}

		return $html;
	}
}
