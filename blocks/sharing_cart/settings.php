<?php  //$Id: settings.php 775 2012-09-04 09:52:19Z malu $

$settings->add(
	new admin_setting_configmulticheckbox(
		'block_sharing_cart/userdata_copyable_modtypes',
		get_string('conf:userdata_copyable_modtypes', 'block_sharing_cart'),
		get_string('conf:userdata_copyable_modtypes_desc', 'block_sharing_cart'),
		array('wiki' => 1, 'forum' => 1, 'data' => 1),
		array_reduce(
			$DB->get_records('modules', array(), 'name ASC'),
			function ($list, $module) use ($CFG, $OUTPUT)
			{
				$icon = '<img src="' . $OUTPUT->pix_url('icon', $module->name) . '" class="icon" alt="" />';
				$list[$module->name] = ' ' . $icon . ' ' . get_string('modulename', $module->name);
				return $list;
			},
			array()
			)
	)
);
