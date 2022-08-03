<?php

/**
 * Unit tests for Signature Verifier
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Core\Tool;

use Kohana\Validation\Validation;
use Mockery as M;
use Tests\TestCase;
use Ushahidi\Contracts\Repository\Entity\PermissionRepository;
use Ushahidi\App\V3\Validator\Role\Update;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class UpdateTest extends TestCase
{
    protected $permissonRepoMock;

    public function testRoleDisabled()
    {
        $features = M::mock(\Ushahidi\App\Tools\Features::class);
        $features->shouldReceive('isEnabled')->with('roles')->andReturn(false);
        $this->app->instance('features', $features);

        $validationMock = M::mock(Validation::class);
        /** @var PermissionRepository */
        $permissonRepoMock = M::mock(PermissionRepository::class);
        $validator = new Update($permissonRepoMock);
        $validationMock->expects('error')->with(
            'name',
            'rolesNotEnabled'
        );
        $validator->checkRolesEnabled($validationMock);
    }

    public function testRoleEnabled()
    {
        $features = M::mock(\Ushahidi\App\Tools\Features::class);
        $features->shouldReceive('isEnabled')->with('roles')->andReturn(true);
        $this->app->instance('features', $features);

        $validationMock = M::mock(Validation::class);
        /** @var PermissionRepository */
        $permissonRepoMock = M::mock(PermissionRepository::class);
        $validator = new Update($permissonRepoMock);
        $validationMock->shouldNotReceive('error')->with(
            'name',
            'rolesNotEnabled'
        );
        $validator->checkRolesEnabled($validationMock);
    }
}
