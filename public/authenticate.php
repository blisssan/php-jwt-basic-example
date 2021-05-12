<?php

declare(strict_types=1);

require_once './helpers.php';
require_once './jwt_helper.php';

/*
* Dummy user object you can user your own model or associative array
* if using associative array, modify the JWTHelper class to access user's name & id
* from the associateive array instead of stdClass object or User object
*/
class User
{
    public $name;
    public $id;
    public $email;
}

$user = new User();
$user->name = 'Santhosh';
$user->id = 1;
$user->email = 'sample@email.com';

// Query & validate user here

/*
 * Query database using the credentials sent & retrieve an user object
 * either a custom object like above or stdClass object is also fine
*/

$response = $jwtHelper->generateTokenForUser($user);

jsonResponse($response);
