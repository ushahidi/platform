<?php

/**
 * Unit tests for Signature Verifier
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Core\Tool;

use Kohana\Validation\Validation;
use Ushahidi\App\Validator\Role\Update;
use Tests\TestCase;
use Mockery as M;
use Ushahidi\Core\Entity\PermissionRepository;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class RoleUpdateTest extends TestCase
{

    public function testRoleDisabled()
    {
        $features = M::mock(\Ushahidi\App\Tools\Features::class);
        $features->shouldReceive('isEnabled')->with('roles')->andReturn(false);
        $this->app->instance('features', $features);

        $validationMock = M::mock(Validation::class);
        $validator = new Update(M::mock(PermissionRepository::class));
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
        $validator = new Update(M::mock(PermissionRepository::class));
        $validationMock->shouldNotReceive('error')->with(
            'name',
            'rolesNotEnabled'
        );
        $validator->checkRolesEnabled($validationMock);
    }
}
