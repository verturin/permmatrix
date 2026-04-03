<?php
/**
 * Forum Permission Matrix — Main Event Listener
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\event;

if (!defined('IN_PHPBB'))
{
	exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $config_table;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$config_table,
		$root_path,
		$php_ext
	)
	{
		$this->auth         = $auth;
		$this->config       = $config;
		$this->helper       = $helper;
		$this->db           = $db;
		$this->request      = $request;
		$this->template     = $template;
		$this->user         = $user;
		$this->config_table = $config_table;
		$this->root_path    = $root_path;
		$this->php_ext      = $php_ext;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'                      => 'load_language',
			'core.page_header'                     => 'add_page_header_link',
			'core.adm_page_header'                 => 'load_acp_language',
		];
	}

	/**
	 * Load extension language files on user setup.
	 */
	public function load_language($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'verturin/permmatrix',
			'lang_set' => 'permmatrix',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Load ACP language file so module menu titles are translated.
	 */
	public function load_acp_language($event)
	{
		$this->user->add_lang_ext('verturin/permmatrix', 'permmatrix_acp');
	}

	/**
	 * Add permission matrix link to the page header navigation (only for logged-in users).
	 */
	public function add_page_header_link($event)
	{
		// Only show to logged-in users and if extension is enabled
		if ($this->user->data['user_id'] == ANONYMOUS || !$this->config['verturin_permmatrix_enabled'])
		{
			return;
		}

		$this->template->assign_vars([
			'U_PERMMATRIX' => $this->helper->route('verturin_permmatrix'),
		]);
	}
}
