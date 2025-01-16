<?php
/**
*
* @package		Breizh Ajax Preview Extension
* @copyright	(c) 2019-2025 Sylver35  https://breizhcode.com
* @license		https://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace sylver35\ajaxpreview\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\template\template;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\config\config;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\config\config */
	protected $config;

	/**
	 * Constructor
	 *
	 */
	public function __construct(template $template, helper $helper, language $language, config $config)
	{
		$this->template = $template;
		$this->helper = $helper;
		$this->language = $language;
		$this->config = $config;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.posting_modify_template_vars'	=> 'posting_modify_template_vars',
			'core.ucp_pm_compose_template'		=> 'ucp_pm_compose_template',
			'core.ucp_profile_modify_signature'	=> 'ucp_profile_modify_signature',
			'core.acp_board_config_edit_add'	=> 'acp_board_config_edit_add'
		];
	}

	/**
	 * @param array $event
	 */
	public function posting_modify_template_vars($event)
	{
		$event['page_data'] = array_merge($event['page_data'], [
			'S_DISPLAY_PREVIEW'		=> true,
			'S_SHOW_AJAX_PREVIEW'	=> true,
			'S_IN_SIGNATURE'		=> false,
			'PREVIEW_DATA'			=> 'message',
			'REFRESH_TIME'			=> $this->config['ajaxpreview_refresh'] * 1000,
			'U_PREVIEW_AJAX'		=> $this->helper->route('sylver35_ajaxpreview_message_ajax', ['action' => 'message', 'forum_id' => $event['forum_id']]),
		]);
	}

	/**
	 * @param array $event
	 */
	public function ucp_pm_compose_template($event)
	{
		$event['template_ary'] = array_merge($event['template_ary'], [
			'S_DISPLAY_PREVIEW'		=> true,
			'S_SHOW_AJAX_PREVIEW'	=> true,
			'S_IN_SIGNATURE'		=> false,
			'PREVIEW_DATA'			=> 'message',
			'REFRESH_TIME'			=> $this->config['ajaxpreview_refresh_pm'] * 1000,
			'U_PREVIEW_AJAX'		=> $this->helper->route('sylver35_ajaxpreview_pm_ajax', ['action' => 'message']),
		]);
	}

	/**
	 * @param array $event
	 */
	public function ucp_profile_modify_signature($event)
	{
		$event['preview'] = true;
		$event['signature'] = ($event['signature'] === '') ? ' ' : $event['signature'];

		$this->template->assign_vars([
			'S_SHOW_AJAX_PREVIEW'	=> true,
			'S_IN_SIGNATURE'		=> true,
			'PREVIEW_DATA'			=> 'signature',
			'REFRESH_TIME'			=> $this->config['ajaxpreview_refresh_sign'] * 1000,
			'U_PREVIEW_AJAX'		=> $this->helper->route('sylver35_ajaxpreview_sign_ajax', ['action' => 'signature']),
		]);
	}

	/**
	 * @param array $event
	 */
	public function acp_board_config_edit_add($event)
	{
		$mode = $event['mode'];
		if (in_array($mode, ['post', 'message', 'signature']))
		{
			$this->language->add_lang('ajaxpreview', 'sylver35/ajaxpreview');
			$add_vars = [];
			$data = [
				'post'		=> [
					'id'		=> 'ajaxpreview_refresh',
					'before'	=> 'edit_time',
					'lang'		=> 'AJAX_PREVIEW',
				],
				'message'	=> [
					'id'		=> 'ajaxpreview_refresh_pm',
					'before'	=> 'pm_edit_time',
					'lang'		=> 'AJAX_PREVIEW',
				],
				'signature'	=> [
					'id'		=> 'ajaxpreview_refresh_sign',
					'before'	=> 'max_sig_chars',
					'lang'		=> 'AJAX_PREVIEW_SIGN',
				],
			];

			$add_vars[$mode] = [
				$data[$mode]['id'] => [
					'lang'		=> $data[$mode]['lang'],
					'explain'	=> $data[$mode]['lang'] . '_EXPLAIN',
					'validate'	=> 'int:1:99',
					'type'		=> 'number:1:99',
					'append'	=> ' ' . $this->language->lang('SECONDS'),
				],
			];

			$display_vars = $event['display_vars'];
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $add_vars[$mode], ['before' => $data[$mode]['before']]);
			$event['display_vars'] = $display_vars;
		}
	}
}
