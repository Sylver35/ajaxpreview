<?php
/**
*
* @package		Breizh Ajax Preview Extension
* @copyright	(c) 2019-2025 Sylver35  https://breizhcode.com
* @license		https://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace sylver35\ajaxpreview\migrations;

use phpbb\db\migration\migration;

class update_1_4_0 extends migration
{
	public function effectively_installed()
	{
		return isset($this->config['ajaxpreview_refresh_pm']);
	}

	static public function depends_on()
	{
		return ['\sylver35\ajaxpreview\migrations\install_data'];
	}

	public function update_data()
	{
		return [
			// Config
			['config.add', ['ajaxpreview_refresh_pm', 3]],
			['config.add', ['ajaxpreview_refresh_sign', 3]],
		];
	}
}
