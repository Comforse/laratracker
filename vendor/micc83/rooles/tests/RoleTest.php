<?php

use Rooles\Permissions;
use Rooles\Role;
use Rooles\RoleManager;

/**
 * Class RoleTest
 *
 * Tests the Role Object
 */
class RoleTest extends BaseCase
{

    /**
     * @var RoleManager
     */
    public $roles;

    /**
     * @test
     */
    public function it_allows_to_assign_role_name()
    {
        $customer = new Role('Admin', new Permissions);

        $customer->assignName('Administrator');

        $this->assertEquals('Administrator', $customer);
        $this->assertEquals('Administrator', $customer->name());
    }

    /**
     * @test
     */
    public function if_empty_name_is_provided_id_will_be_returned_instead()
    {
        $customer = new Role('Admin', new Permissions);
        $customer->assignName('');
        $this->assertEquals('Admin', $customer);
    }

    /**
     * @test
     */
    public function it_allows_to_check_role_id()
    {
        $customer = new Role('customer', new Permissions);

        $this->assertTrue($customer->is('customer'));
        $this->assertTrue($customer->isIn(['customer', 'users']));
        $this->assertEquals('customer', $customer->name());
        $this->assertFalse($customer->is('Admin'));
    }

    /**
     * @test
     */
    public function it_allows_to_grant_role_permissions()
    {
        $areaManager = new Role('area manager', new Permissions);

        $areaManager->grant([
            'profile',
        ]);

        $this->assertTrue($areaManager->can('profile.read'));
        $this->assertFalse($areaManager->can('users.read'));
    }

    /**
     * @test
     */
    public function it_allows_to_deny_role_permissions()
    {
        $areaManager = new Role('area manager', new Permissions);

        $areaManager->grant([
            'users',
        ]);

        $areaManager->deny([
            'users.*.write'
        ]);

        $this->assertTrue($areaManager->can('users.customers.read'));
        $this->assertFalse($areaManager->can('users.admins.write'));
    }

    /**
     * @test
     */
    public function provide_a_cannot_method_which_inverts_the_result_of_can()
    {
        $role = new Role('role', new Permissions);

        $role->grant('users.write');

        $this->assertTrue($role->can('users.write'));
        $this->assertFalse($role->cannot('users.write'));
    }

    /**
     * @test
     */
    public function method_is_and_isIn_are_case_insensitive()
    {
        $role = new Role('Role', new Permissions);

        $this->assertTrue($role->is('role'));
        $this->assertTrue($role->isIn(['ROLE', 'test']));
    }

}
