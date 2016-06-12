<?php

use Rooles\RoleManager;

/**
 * Class RoolesServiceProviderTest
 *
 * Tests the Role Object
 */
class RoolesServiceProviderTest extends BaseCase
{

    /**
     * @test
     */
    public function it_register_a_singleton_for_roleManager()
    {
        $this->assertEquals('Rooles\RoleManager', get_class($this->roleManager()));
    }

    /**
     * @return RoleManager
     */
    public function roleManager()
    {
        return App::make(Rooles\Contracts\RoleRepository::class);
    }

    /**
     * @test
     */
    public function it_register_roles_specified_in_config_file()
    {
        Config::set('rooles.roles', [
            'Admin' => [
                'name'  => 'Administrator',
                'grant' => [
                    '*'
                ],
                'deny'  => [
                    'admins.delete'
                ]
            ]
        ]);

        $admin = $this->roleManager()->getOrCreate('Admin');

        $this->assertTrue($admin->can('users.add'));
        $this->assertFalse($admin->can('admins.delete'));
        $this->assertEquals('Administrator', $admin);
    }

}
