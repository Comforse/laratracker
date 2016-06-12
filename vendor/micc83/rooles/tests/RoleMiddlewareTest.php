<?php

/**
 * Class RoleMiddlewareTest
 */
class RoleMiddlewareTest extends BaseCase
{

    /**
     * Setup tests
     */
    public function setUp()
    {

        parent::setUp();

        get('restricted', [
            'middleware' => 'role:Admin|root',
            function () {
                return 'Hello World';
            }
        ]);

        $roleRepo = $this->app->make(Rooles\Contracts\RoleRepository::class);

        $roleRepo->create('Admin');
        $roleRepo->create('root');
        $roleRepo->create('operator');

    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_not_logged_in()
    {
        $this->visitAndCatchException('restricted', 'Rooles\ForbiddenHttpException')
             ->dontSee('Hello World')
             ->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_hasnt_the_needed_role()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'operator'
        ]));

        $this->visitAndCatchException('restricted', 'Rooles\ForbiddenHttpException')
             ->dontSee('Hello World')
             ->seeStatusCode(403);

    }

    /**
     * @test
     */
    public function it_passes_if_user_has_the_needed_role()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'Admin'
        ]));

        $this->get('restricted')->see('Hello World');

        $this->be(new UserMock([
            'name' => 'The Pope',
            'role' => 'root'
        ]));

        $this->get('restricted')->see('Hello World');

    }

    /**
     * @test
     */
    public function it_respond_with_forbidden_to_ajax_calls()
    {
        $this->get('restricted', ['X-Requested-With' => 'XMLHttpRequest'])
             ->see('"message":"Forbidden"')
             ->seeStatusCode(403);
    }

}
