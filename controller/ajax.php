<?php
/**
*
* @package		Breizh Ajax Preview Extension
* @copyright	(c) 2019-2025 Sylver35  https://breizhcode.com
* @license		https://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace sylver35\ajaxpreview\controller;

use phpbb\request\request;

class ajax
{
	/** @var \phpbb\request\request */
	protected $request;

	/**
	 * Constructor
	 *
	 */
	public function __construct(request $request)
	{
		$this->request = $request;
	}

	/**
	 * Function construct_ajax
	 *
	 * @return void
	 */
	public function construct_ajax($action, $forum_id = 0)
	{
		$message = $this->request->variable('content', '', true);
		$uid = $bitfield = $options = '';
		$update_count = [];

		generate_text_for_storage($message, $uid, $bitfield, $options, true, false, true);
		$message = generate_text_for_display($message, $uid, $bitfield, 1 | 2, false);

		if ($action === 'message')
		{
			$total = $this->request->variable('total', 0);
			$attachements = $plupload = [];
			for ($i = 0; $i < $total; $i++)
			{
				$plupload[$i] = $this->request->variable('plupload-' . $i, '', true);
				list($attach_id, $is_orphan, $attach_comment, $real_filename, $filesize) = explode(',', $plupload[$i]);
				$attachements[$i] = [
					'attach_id'			=> (int) $attach_id,
					'is_orphan'			=> (int) $is_orphan,
					'attach_comment'	=> (string) $attach_comment,
					'real_filename'		=> (string) $real_filename,
					'filesize'			=> (int) $filesize,
				];
			}
			parse_attachments($forum_id, $message, $attachements, $update_count, true);
		}

		// Always display images/smilies with correct url
		$message = str_replace(['src="./../../', 'src="./../', 'src="./'], 'src="' . generate_board_url() . '/', $message);

		// A litle magic for simple mentions ext
		$message = str_replace(['[mention]', '[/mention]'], ['<span class="mention">@', '</span>'], $message);

		$json_response = new \phpbb\json_response;
		$json_response->send([
			'content'		=> $message,
		]);
	}
}
