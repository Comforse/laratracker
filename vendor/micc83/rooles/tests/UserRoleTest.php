<?php

use UserMock as User;

/**
 * Class UserRoleTest
 */
class UserRoleTest extends BaseCase {

    /**
     * @test
     */
    public function it_adds_roles_capability_to_eloquent_models () {

        $user = new User([
            'name' => 'Joshua',
            'role' => 'customer'
        ]);

        App::make(Rooles\Contracts\RoleRepository::class)->create('customer')->grant([
            'products.read',
            'products.write'
        ]);

        $this->assertTrue($user->can('products.read'));
        $this->assertFalse($user->can('products.edit'));

    }

}