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
        $validationMock = M::mock(Validation::class);
        $validator = new Update(M::mock(PermissionRepository::class), false);
        $validationMock->expects('error')->with(
            'name',
            'rolesNotEnabled'
        );
        $validator->checkRolesEnabled($validationMock);
    }

    public function testRoleEnabled()
    {
        $validationMock = M::mock(Validation::class);
        $validator = new Update(M::mock(PermissionRepository::class), true);
        $validationMock->shouldNotReceive('error')->with(
            'name',
            'rolesNotEnabled'
        );
        $validator->checkRolesEnabled($validationMock);
    }
}
