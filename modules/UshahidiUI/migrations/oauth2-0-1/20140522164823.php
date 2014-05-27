<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth2_0_1_20140522164823 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$config = Kohana::$config->load('ushahidiui.oauth');

		$results = DB::select('client_id')
			->from('oauth_clients')
			->where('client_id', '=', $config['client'])
			->execute($db);

		if ($results->count())
		{
			DB::update('oauth_clients')
				->set('client_secret', $config['client_secret'])
				->execute($db);
		}
		else
		{
			DB::insert('oauth_clients')
				->columns(array('client_id', 'client_secret'))
				->values(array($config['client'], $config['client_secret']))
				->execute($db);
		}
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$config = Kohana::$config->load('ushahidiui.oauth');
		DB::delete('oauth_clients')
			->where('client_id', '=', $config['client'])
			->execute($db);
	}

}
