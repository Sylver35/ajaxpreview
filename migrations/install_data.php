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

class install_data extends migration
{
	public function effectively_installed()
	{
		return isset($this->config['ajaxpreview_refresh']);
	}

	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v33x\v331'];
	}

	public function update_data()
	{
		return [
			// Config
			['config.add', ['ajaxpreview_refresh', 4]],
		];
	}
}
