<?php

use Rooles\Permissions;

/**
 * Class PermissionsTest
 *
 * Tests the Permissions Object
 */
class PermissionsTest extends BaseCase
{

    /**
     * @var Permissions
     */
    protected $perms;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->perms = new Permissions();
    }

    /**
     * @test
     */
    public function it_allows_to_grant_role_permissions_string_or_array()
    {
        $this->perms->set([
            'users.own.read',
            'profile',
        ], '*');

        $this->assertTrue($this->perms->evaluate('users.own.read'));
        $this->assertTrue($this->perms->evaluate('profile.read'));
        $this->assertTrue($this->perms->evaluate('profile.delete'));

        $this->assertFalse($this->perms->evaluate('users.read'));
    }

    /**
     * @test
     */
    public function it_allows_to_pass_array_evaluated_as_AND_operator()
    {
        $this->perms->set([
            'users.own.read',
            'profile',
        ], '*');

        $this->assertTrue($this->perms->evaluate(['users.own.read', 'profile.edit']));
        $this->assertFalse($this->perms->evaluate(['users.own.read', 'users.read']));
    }

    /**
     * @test
     */
    public function it_allows_to_deny_role_permissions()
    {
        $this->perms->set([
            'users',
        ], '*')->set([
            'users.*.write'
        ], '!');

        $this->assertTrue($this->perms->evaluate('users.customers.read'));
        $this->assertTrue($this->perms->evaluate('users.customers.all.write.poems'));

        $this->assertFalse($this->perms->evaluate('users.customers.write'));
        $this->assertFalse($this->perms->evaluate('users.admins.write'));
    }

    /**
     * @test
     */
    public function it_allows_for_full_wildcard_permissions()
    {
        $this->perms->set('*', '*');

        $this->assertTrue($this->perms->evaluate('users.read'));
        $this->assertTrue($this->perms->evaluate('*'));
    }

    /**
     * @test
     */
    public function it_allows_for_partials_wildcard_permissions()
    {
        $this->perms->set('users.*', '*'); // Same as users

        $this->assertTrue($this->perms->evaluate('users'));
        $this->assertTrue($this->perms->evaluate('users.*'));

        $this->perms = new Permissions;

        $this->perms->set('*.read', '*');

        $this->assertTrue($this->perms->evaluate('users.read'));
        $this->assertTrue($this->perms->evaluate('news.read'));

        $this->assertFalse($this->perms->evaluate('users.write'));
        $this->assertFalse($this->perms->evaluate('news.write'));

        $this->assertFalse($this->perms->evaluate('*'));
    }

    /**
     * @test
     */
    public function it_allows_for_middle_wildcard_permissions()
    {
        $this->perms->set('users.*.read', '*');

        $this->assertTrue($this->perms->evaluate('users.admin.read'));
        $this->assertTrue($this->perms->evaluate('users.customer.read'));
        $this->assertTrue($this->perms->evaluate('users.*.read.*'));

        $this->assertFalse($this->perms->evaluate('users.customers.delete'));
        $this->assertFalse($this->perms->evaluate('users.*.delete'));
        $this->assertFalse($this->perms->evaluate('users.*'));
        $this->assertFalse($this->perms->evaluate('users.read'));
    }

    /**
     * @test
     */
    public function it_provide_operators_to_check_permissions()
    {
        $this->perms->set([
            'users.read',
            'users.delete',
            'customers.read',
        ], '*');

        // OR
        $this->assertTrue($this->perms->evaluate('users.delete|users.remove'));
        $this->assertTrue($this->perms->evaluate('users.remove|users.delete'));
        $this->assertFalse($this->perms->evaluate('users.remove|users.create'));

        // AND
        $this->assertTrue($this->perms->evaluate('users.read&users.delete'));
        $this->assertFalse($this->perms->evaluate('users.delete&users.create'));

        // OR + AND
        $this->assertTrue($this->perms->evaluate('users.delete|users.remove&users.remove|customers.read'));
        $this->assertFalse($this->perms->evaluate('users.delete|users.remove&users.remove|customers.create'));
    }

    /**
     * @test
     */
    public function it_allows_to_mix_operators_and_array()
    {
        $this->perms->set([
            'users.read',
            'users.delete',
            'customers.read',
        ], '*');

        $this->assertTrue($this->perms->evaluate([
            'users.delete|users.remove',
            'users.read&customers.read'
        ]));
    }

    /**
     * @test
     */
    public function it_should_add_a_wildcard_at_the_end_of_each_permission_set()
    {
        $this->perms->set([
            'users',
        ], '*');

        $this->assertTrue($this->perms->evaluate('users.*'));
        $this->assertTrue($this->perms->evaluate('users.read'));
        $this->assertTrue($this->perms->evaluate('users'));
    }

    /**
     * @test
     */
    public function it_should_follow_positive_specificity_rule()
    {
        $this->perms->set([
            'customers.books', // more specific
            '*.drafts.read' // more specific
        ], '*')->set([
            'customers.*.read',
            '*.drafts'
        ], '!');

        $this->assertTrue($this->perms->evaluate('customers.books.read'));
        $this->assertTrue($this->perms->evaluate('customers.books'));
        $this->assertTrue($this->perms->evaluate('comments.drafts.read'));

        $this->assertFalse($this->perms->evaluate('customers.magazines.read'));
        $this->assertFalse($this->perms->evaluate('comments.drafts'));
        $this->assertFalse($this->perms->evaluate('comments.drafts.write'));
    }

    /**
     * @test
     */
    public function it_should_follow_negative_specificity_rule()
    {
        $this->perms->set([
            'customers.*.read',
            '*.drafts'
        ], '*')->set([
            'customers.books', // more specific
            '*.drafts.read' // more specific
        ], '!');

        $this->assertTrue($this->perms->evaluate('customers.magazines.read'));
        $this->assertTrue($this->perms->evaluate('comments.drafts.write'));

        $this->assertFalse($this->perms->evaluate('customers'));
        $this->assertFalse($this->perms->evaluate('customers.books.read'));
        $this->assertFalse($this->perms->evaluate('customers.books'));
        $this->assertFalse($this->perms->evaluate('comments.drafts.read'));
    }

    /**
     * @test
     */
    public function it_should_follow_positive_specificity_rule_2()
    {
        $this->perms->set([
            '*',
            'customers.read'
        ], '*')->set([
            'customers'
        ], '!');

        $this->assertTrue($this->perms->evaluate('customers.read'));
        $this->assertTrue($this->perms->evaluate('something'));
        $this->assertFalse($this->perms->evaluate('customers.write'));
    }

    /**
     * @test
     */
    public function it_should_allow_to_nest_wildcards_or_single_permissions()
    {
        $this->perms->set([
            'users',
            'customers.*.read'
        ], '*')->set([
            'users.delete',
            'customers.*.write'
        ], '!');

        $this->assertTrue($this->perms->evaluate('users.read.lot'));
        $this->assertTrue($this->perms->evaluate('users.read.*.*'));
        $this->assertTrue($this->perms->evaluate('users.read.*.*.read'));

        $this->assertFalse($this->perms->evaluate('users.delete.lot'));
        $this->assertFalse($this->perms->evaluate('users.delete.*.*'));
        $this->assertFalse($this->perms->evaluate('users.delete.*.*.read'));
        $this->assertFalse($this->perms->evaluate('customers.*.*.read'));
    }

    /**
     * @test
     */
    public function it_should_pile_up_positive_permissions_with_higher_specifity()
    {
        $this->perms->set([
            'users',
            'users.customers.write'
        ], '*')->set([
            'users.customers.test.prova'
        ], '!');

        $this->assertFalse($this->perms->evaluate('users'));
        $this->assertFalse($this->perms->evaluate('users.customers'));

        $this->perms = new Permissions();

        $this->perms->set([
            'users',
            'users.customers.write'
        ], '*')->set('users.customers', '!');

        $this->assertTrue($this->perms->evaluate('users.write'));
        $this->assertTrue($this->perms->evaluate('users.customers.write'));
        $this->assertFalse($this->perms->evaluate('users.customers.read'));
    }

    /**
     * @test
     */
    public function it_should_pile_up_positive_permissions()
    {
        $this->perms->set([
            'users',
            'users.write'
        ], '*');

        $permissions = $this->getProtectedProperty($this->perms, 'permissions');

        $this->assertEquals([
            'users' => [
                '*'     => '*',
                'write' => [
                    '*' => '*'
                ]
            ]
        ], $permissions);
    }

    /**
     * @test
     */
    public function it_doensnt_allow_to_skip_a_level_of_permission()
    {

        $this->perms->set([
            'company.read'
        ], '*');

        $this->assertFalse($this->perms->evaluate('company.disabled.read'));

    }

    /**
     * @param        $object
     * @param string $propery
     *
     * @return mixed
     */
    protected function getProtectedProperty($object, $propery)
    {
        $reflection = new ReflectionClass($object);
        $property   = $reflection->getProperty($propery);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

}
