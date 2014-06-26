<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth_league_20140626193509 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		try
		{
			DB::insert('oauth_scopes')
				->columns(array('scope', 'name'))
				->values(array('stats', 'stats'))
				->execute($db);
		}
		catch (Kohana_Database_Exception $e)
		{
			// ignore duplicate key errors
		}
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		DB::delete('oauth_scopes')
			->where('scope', '=', 'stats')
			->execute($db);
	}

}
