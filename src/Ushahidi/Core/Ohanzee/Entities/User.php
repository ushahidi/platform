<?php

/**
 * Ushahidi Platform User Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\Entity\User as EntityUser;
use Ushahidi\Core\StaticEntity;
use Ushahidi\Core\Tool\Hasher\Password as PasswordHash;

class User extends StaticEntity implements EntityUser
{

    const DEFAULT_LOGINS = 0;
    const DEFAULT_LAST_LOGIN = null;
    const DEFAULT_FAILED_ATTEMPTS = 0;
    const DEFAULT_LANGUAGE = null;

    protected $id;
    protected $email;
    protected $realname;
    protected $password;
    protected $logins = 0;
    protected $failed_attempts = 0;
    protected $last_login;
    protected $last_attempt;
    protected $created;
    protected $updated;
    protected $role;
    protected $language;
    protected $contacts;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'              => 'int',
            'email'           => '*email',
            'realname'        => 'string',
            'password'        => 'string',
            'logins'          => 'int',
            'failed_attempts' => 'int',
            'last_login'      => 'int',
            'last_attempt'    => 'int',
            'created'         => 'int',
            'updated'         => 'int',
            'role'            => 'string',
            'language'        => 'string',
            'contacts'        => 'array'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'users';
    }

    public function getDefaultData()
    {
        return [
            'logins' => 0,
            'failed_attempts' => 0
        ];
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): User
    {
        if ($action === "update") {
            $new_password = isset($input['password'])?(new PasswordHash())->hash($input["password"]):null;
            return new User([
                "id" => $old_Values['id'],
                "email" => isset($input["email"]) ? $input["email"] : $old_Values['email'],
                "password" => $new_password?$new_password:$old_Values['password'],
                "realname" => isset($input["realname"]) ? $input["realname"] : $old_Values['realname'],
                "role" => isset($input["role"]) ? $input["role"] : $old_Values['role'],
                "logins" => isset($input["logins"]) ? $input["logins"] : $old_Values['logins'],
                "failed_attempts" =>
                isset($input["failed_attempts"]) ? $input["failed_attempts"] : $old_Values['failed_attempts'],
                "last_login" => isset($input["last_login"]) ? $input["last_login"] : $old_Values['last_login'],
                "language" => isset($input["language"]) ? $input["language"] : $old_Values['language'],
                "created" => $old_Values['created'] ?? time(),
                "updated" => time()
            ]);
        }
        return new User([
            "email" => $input["email"],
            "password" => (new PasswordHash())->hash($input["password"]),
            "realname" => isset($input["realname"])?$input["realname"]:null,
            "role" => $input["role"],
            "logins" => self::DEFAULT_LOGINS,
            "failed_attempts" => self::DEFAULT_FAILED_ATTEMPTS,
            "last_login" => self::DEFAULT_LAST_LOGIN,
            "language" => isset($input["realname"])?$input["realname"]:self::DEFAULT_LANGUAGE,
            "created" => time()
        ]);
    }
}
