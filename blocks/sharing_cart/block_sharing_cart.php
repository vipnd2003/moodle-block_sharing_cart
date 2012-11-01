<?php
/**
 *  Sharing Cart - Block
 *  
 *  @author  VERSION2, Inc.
 *  @version $Id: block_sharing_cart.php 882 2012-11-01 05:06:21Z malu $
 */

require_once __DIR__.'/classes/controller.php';

class block_sharing_cart extends block_base
{
	public function init()
	{
		$this->title   = get_string('pluginname', __CLASS__);
		$this->version = 2012110100;
	}
	
	public function applicable_formats()
	{
		return array('course' => true);
	}
	
	/**
	 *  Get the block content
	 *  
	 *  @global object $CFG
	 *  @global object $USER
	 *  @global moodle_page $PAGE
	 *  @return object|string
	 */
	public function get_content()
	{
		global $CFG, $USER, $PAGE;
		
		if ($this->content !== null)
			return $this->content;
		
		if (!$this->page->user_is_editing())
			return $this->content = '';
		
		$context = context_course::instance($this->page->course->id);
		if (!has_capability('moodle/backup:backupactivity', $context))
			return $this->content = '';
		
		$controller = new sharing_cart\controller();
		$html = $controller->render_tree($USER->id);
		
		if (empty($CFG->enableajax)) {
			$html = $this->get_content_noajax();
		}
		
		$PAGE->requires->js('/blocks/sharing_cart/block_sharing_cart.js');
		$PAGE->requires->yui_module('block_sharing_cart', 'M.block_sharing_cart.init', array(), null, true);
		
		$footer = '<div style="display:none;">'
		        . '<div class="header-commands">' . $this->get_header() . '</div>'
		        . '<form name="strings">' . $this->get_hidden_strings() . '</form>'
		        . '</div>';
		return $this->content = (object)array('text' => $html, 'footer' => $footer);
	}
	
	/**
	 *  Get the block header
	 *  
	 *  @global core_renderer $OUTPUT
	 *  @return string
	 */
	private function get_header()
	{
		global $OUTPUT;
		
		// link to bulkdelete
		$alt = get_string('bulkdelete', __CLASS__);
		$src = new moodle_url('/blocks/sharing_cart/pix/bulkdelete.gif');
		$url = new moodle_url('/blocks/sharing_cart/bulkdelete.php', array('course' => $this->page->course->id));
		$bulkdelete = '<a class="icon editing_bulkdelete" title="' . $alt . '" href="' . $url . '">'
		            . '<img src="' . $src . '" alt="' . $alt . '" />'
		            . '</a>';
		
		// help for Sharing Cart
		$helpicon = $OUTPUT->help_icon('sharing_cart', __CLASS__);
		
		return $bulkdelete . $helpicon;
	}
	
	/**
	 *  Get the block content for no-AJAX
	 *  
	 *  @global core_renderer $OUTPUT
	 *  @return string
	 */
	private function get_content_noajax()
	{
		global $OUTPUT;
		
		$html = '<div class="error">' . get_string('err:requireajax', __CLASS__) . '</div>';
		if (has_capability('moodle/site:config', context_system::instance())) {
			$url = new moodle_url('/admin/settings.php?section=ajax');
			$link = '<a href="' . $url . '">' . get_string('ajaxuse') . '</a>';
			$html .= '<div>' . $OUTPUT->rarrow() . ' ' . $link . '</div>';
		}
		return $html;
	}
	
	private function get_hidden_strings()
	{
		$params = array(
			'yes'              => get_string('yes'),
			'no'               => get_string('no'),
			'ok'               => get_string('ok'),
			'cancel'           => get_string('cancel'),
			'error'            => get_string('error'),
			'edit'             => get_string('edit'),
			'move'             => get_string('move'),
			'delete'           => get_string('delete'),
			'movehere'         => get_string('movehere'),
			'copyhere'         => get_string('copyhere', __CLASS__),
			'notarget'         => get_string('notarget', __CLASS__),
			'backup'           => get_string('backup', __CLASS__),
			'restore'          => get_string('restore', __CLASS__),
			'movedir'          => get_string('movedir', __CLASS__),
			'clipboard'        => get_string('clipboard', __CLASS__),
			'confirm_backup'   => get_string('confirm_backup', __CLASS__),
			'confirm_userdata' => get_string('confirm_userdata', __CLASS__),
			'confirm_delete'   => get_string('confirm_delete', __CLASS__),
			);
		return implode('', array_map(function ($name, $value)
		{
			return '<input type="hidden" name="' . $name . '"'
			     . ' value="' . htmlspecialchars($value) . '" />';
		},
		array_keys($params), array_values($params)));
	}
}
