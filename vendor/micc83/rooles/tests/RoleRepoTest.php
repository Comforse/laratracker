<?php

use Rooles\Permissions;
use Rooles\Role;
use Rooles\RoleManager;

/**
 * Class RoleRepoTest
 *
 * Tests the Roles Repository
 */
class RoleRepoTest extends BaseCase
{

    /**
     * @var RoleManager
     */
    var $roleRepo;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->roleRepo = new RoleManager();
    }

    /**
     * @test
     */
    public function it_allows_to_create_and_store_roles()
    {

        $this->roleRepo->create('sales agent')->grant([
            'users.own.read'
        ]);

        $salesAgent = $this->roleRepo->get('sales agent');

        $this->assertTrue($salesAgent->can('users.own.read'));
        $this->assertFalse($salesAgent->can('users.read'));

    }

    /**
     * @test
     */
    public function it_allows_to_get_existing_role_or_create_a_new_one()
    {

        $this->roleRepo->getOrCreate('Admin')->grant('*');

        $this->assertTrue($this->roleRepo->getOrCreate('Admin')->can('*'));

    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Role not found
     */
    public function it_throws_exception_if_role_doesnt_exists()
    {
        $this->roleRepo->get('detractor');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Duplicated role!
     */
    public function it_throws_exception_if_role_with_same_name_already_exists()
    {
        $this->roleRepo->create('test')->grant('*');
        $this->roleRepo->add(new Role('test', new Permissions));
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Duplicated role!
     */
    public function role_keys_are_case_insensitive () {
        $this->roleRepo->create('Test');
        $this->roleRepo->get('test')->grant('*');
        $this->roleRepo->add(new Role('test', new Permissions));
    }

    /**
     * @test
     */
    public function if_empty_rolename_is_requested_return_a_default_empty_role()
    {
        $this->assertEquals('Default', $this->roleRepo->get('')->name());
    }



}
