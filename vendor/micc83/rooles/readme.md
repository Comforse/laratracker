Laravel Rooles [![Build Status](https://travis-ci.org/micc83/rooles.svg?branch=master)](https://travis-ci.org/micc83/rooles) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/micc83/rooles/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/micc83/rooles/?branch=master)
-------------
#### Simple roles and permissions manager for Laravel 5.1

### Why another Laravel RBAC (Role based access control) ?!?

Well, good point! Lately even *Taylor Otwell* is working on a custom ACL system to be shipped with (I guess as a separated package) **Laravel 5.2** so what's the point on creating a new one?
Well it's all about complexity. Most of the ACL systems out here such as [romanbican/roles](https://github.com/romanbican/roles), [kodeine/laravel-acl](https://github.com/kodeine/laravel-acl) or [Sentinel](https://cartalyst.com/manual/sentinel/) are packed with tons of amazing features... which most of the time I'm not using! :D

That's why I thought to build a minimal **Laravel roles and permissions manager** that provides a very simple RBAC implementation on top of the **default Laravel Auth System**.
Each user can be assigned a single Role, while permissions for each Role are stored in a single config file. With the package are provided a very intuitive and well documented API, a Trait to check permissions directly on the Eloquent User Model and two Middlewares to easily protect routes and Controllers.

However, as your application grown, you might need a more complex ACL system, that's why the package comes with a couple of Contracts that you can leverage to improve or replace functionalities at need. You can see **Rooles** not only as a fully working RBAC but also as a *starting point to develop your own custom roles and permission manager*.

### Setup

Run the following from your terminal from within the path containing the Laravel `composer.json` file:

```sh
$ composer require micc83/rooles
```

Open `config/app.php` and add the following line at the end of the providers array:

```php
Rooles\RoolesServiceProvider::class
```

Run the following command from your terminal to publish the migration file (it will simply add a `role` column to the default Users table), the config file and a default blade template for the *403-Forbidden* view (It will not be published if one has already been created):

```sh
$ php artisan vendor:publish
```

In order to be able to use route and Controllers middlewares (so to be able to filter who's able to access a given route or Controller method) open `App/Http/Kernel.php` and add the following lines at the end of the `$routeMiddleware` array:

```php
'perms' => \Rooles\PermsMiddleware::class,
'role'  => \Rooles\RoleMiddleware::class,
```

As **Rooles** works on top of the default *Auth* system of Laravel and with the *Eloquent* User Model you must add the `Rooles\Traits\UserRole` trait to the User Class located in `App/User.php` as follow:

```php
use \Rooles\Traits\UserRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword, UserRole;

    // ...
}
```

> **Important note on Laravel version >= 1.1.11** From this version on Laravel implements its own permission manager trough the `Authorizable` trait/contract so, in order to have the User model work with Rooles, you must remove any reference to both the `Authorizable` trait and interface from the Eloquent user model.

### Setting up users role

Only a single Role can be assigned to each User. You can hardcode the role inside the User Eloquent model adding the role attribute as follow:

```php
protected $attributes = [
    'role' => 'admin'
];
```

Or run the provided migration to add the `role` column to the Users Table so to be able to change Users role at runtime:

```php
$user = User::find(1);
$user->role = 'admin';
$user->save();
```

### Setting up permissions

All the permissions for any given role are set in the `config/rooles.php` file as follow:

```php
<?php return [
    'roles' => [
        'default' => []
        'admin' => [
            'name' => 'Administrator',
            'grant' => '*'
        ],
        'editor' => [
            'grant' => [
                'posts.*',
                'users.*.read',
                'users.*.ban',
                'comments.*',
                'profile.*'
            ],
            'deny' => [
                'users.admin.ban',
                'posts.delete',
                'comments.delete'
            ]
        ]
    ]
];
```

As you can see the format used is:

```php
[
    'roles' => [
        'role_id' => [
            'name' => 'role_name',
            'grant' => 'string_or_array_of_granted_permissions',
            'deny' => 'string_or_array_of_denied_permissions',
        ]
    ]
]
```

The `default` role is applied to any user which has no role applied and provides no permissions unless differently stated in the config file.

The `name` property is optional and allows to set a name differing from the provided Role ID.

You can also create roles and handle permissions manually. Here's an example:

```php
app()->make(\Rooles\Contracts\RoleRepository::class)
     ->getOrCreate('customer')
     ->assignName('Client')
     ->grant(['cart.*', 'products.buy'])
     ->deny('cart.discount');
```

### Permissions strategy

There are four main concept to remember when creating a permissions strategy for **Rooles**:

1. Every role always start with **no permissions**;
2. The **wildcard character** \* is used to define a whole subset of available permissions. For example if we take in consideration the grant `users.*.ban`, that means that editors can ban any group of users ( `users.reader`, `users.author` etc... ) but not `users.admin` as the permission has been denied in the deny array.
3. When you grant or deny a permission, if not already set, a *wildcard will be automatically appended* so `customers` is the same as `customers.*`. That also means that any child permission of the given one will be granted or denied, for example:
    ```php
        $role->grant('comments'); // Same as writing comments.*

        $role->can('comments.write'); // true
        $role->can('comments.pingbacks.write') // true
    ```
4. When you apply both grants and denies in order to figure out which rule will 'win' you'll have to think in terms of **specificity**. The more specific rule will always win. Let's see an example:
    ```php
        $role->grant('comments.write.*') // Same as writing comments.write
             ->deny('*.write');

        $role->can('comments.write'); // true
        $role->can('users.write') // false
    ```
As you probably guessed from the example specificity is calculated on the position of the wildcards and length of the permission. As you move the wildcard to the right you gain in specificity.

### Checking for User permissions

From within your Controller methods or wherever you feel comfortable you can check for a given user permissions as follow:

```php
$user = User::find(1);
if ($user->can('comments.post')){
    // Do something...
}
```

The same to check the logged in user permissions:

```php
public function index(Illuminate\Contracts\Auth\Guard $auth) {

    if ( $auth->user->can('users.list') ){
        // Do something...
    }

}
```

The API exposes a convenient method to negate a permissions assertion:

```php
if ( $user->cannot('users.list') ) redirect()->to('dashboard');
```

You can evaluate multiple assertions passing an array through:

```php
if ( $user->can(['users.list', 'users.read']) ) // Do something when the user has both the permissions (AND)
```

There are also two convenient operator to use with the can/cannot assertions:

```php
if ( $user->can('users.list&users.read') ) // Do something when the user has both the permissions (& > AND)
if ( $user->can('users.list|users.read') ) // Do something when the user has one of the requested permissions (| > OR)
```

Multiple operators can ben be joined together but mind that AND operators have always priority over OR operators.

### Checking for User role

You can make a more general assertion checking for the user role ID (case insensitive):

```php
if ( $user->role->is('admin') ) echo 'Hello Boss';
```

Or check if the user role ID is in a given range (still case insensitive):

```php
if ( $user->role->isIn(['lamer', 'trool']) ) echo 'Hello Looser';
```

You can also get the User role name (the ID will be returned if no name is provided), using one of the following syntax:

```php
// If in a string context:
echo $user->role;
// Otherwise:
if ($user->role->name() === 'Admin') // Do something
```

If you need to make some comparisons, like for example in a Select input field you better use the ID instead of the name. Example:

```php
{!! Form::select('role', ['editor' => 'Editor', 'admin' => 'Administrator'], $user->role->id()) !!}
```

> Remember that role ID is automatically converted to lowercase with UTF8 support.

### Protect routes and Controllers through Middlewares

**Rooles** provides two Middlewares to protect both routes and Controllers.

To protect routes by User Role you can use the **role Middleware**:

```php
Route::get('admin/users/', [
    'middleware' => [
        'auth',
        'role:admin|editor', // Give access to both admins and editors
    ],
    function () {
        return view('admin.users.index');
    }
]);
```

In order to check for user permissions on a route you can use the **perms Middleware** as follow:

```php
Route::get('admin/users/', [
    'middleware' => [
        'auth',
        'perms:users.list|users.edit', // Give access to users with users.list OR users.edit permissions
    ]
    function () {
        return view('admin.users.index');
    }
]);
```

In both case you'll have probably noticed that I'm calling the **Auth middleware** as the user must be logged in in order to check its Role and permissions.

Most of the times you'll be probably being dealing with routes groups, in that case you can simply:

```php
// Route Group
Route::group([
    'middleware' => [
        'auth',
        'role:admin|editor' // Give access to both admins and editors
    ]
], function () {
    Route::resource('users', 'UserController');
    Route::resource('posts', 'PostController');
});
```

Middlewares can also be used in Controllers as follow:

```php
class UserController extends Controller
{

    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @param UserRepo $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->middleware('perms:users', ['except' => 'show']);
    }
```

Here we are saying that in order to access any controller method we must have a role that provides all the `users` permissions but we don't need any permission to show user profiles. You can find a better documentation on Controller Middlewares on the official [Laravel website](http://laravel.com/docs/5.0/controllers#controller-middleware).

#### Handling middlewares HTTP error responses

**Rooles** middlewares handles error responses differently depending on the nature of the request. For Ajax requests they will respond with a JSON Object and a `403` status code as follow:

```json
{
    "error" : {
        "code" : 403,
        "message" : "Forbidden"
    }
}
```

So that you can intercept it in JavaScript as follow:

```js
if ('error' in response) console.log(response.error.message);
```

For normal requests in case of missing authorizations a `Rooles\ForbiddenHttpException` is thrown, which by default (when debug is disabled) will result in the previously published 403 error page with a `403` status code. The page itself can be customized editing the `resources/views/errors/403.blade.php` template.

Otherwise if you'd rather not to show a view but instead implement some custom behaviour you can play with the render method in `app/Exceptions/Handler.php` as follow:

```php
public function render($request, Exception $e)
{
    if ($e instanceof \Rooles\ForbiddenHttpException) {
        return redirect('/')->withErrors(['You don\'t have the needed permissions to perform this action!']);
    }
    return parent::render($request, $e);
}
```

This way when an Forbiddeng error is thrown you'll be redirected to the given page with an error flash message. To show the message you can add the following to your blade template:

```php
@if ($errors->has())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif
```

### Documentation

I firmly believe that even the best coded application in the world is bound to failure when missing a good documentation. That's why I humbly ask to open an issue whenever you'll think something is missing or could be improved.

### Contributions

I'd be glad if you'd like to contribute to the project however I'd ask not to implement new features but to improve the few existing ones (improve patterns, algorythms etc). Each PR must follow [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards and pass all the existing tests (or add furthers when needed).
