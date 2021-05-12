<?php

require_once './core/helpers.php';
require_once './core/jwt_helper.php';

$user;

try {
    $payloadData = $jwtHelper->validate();
    // the payload data will contain user name & id
    // that we set in payload when generating token
    // it will be returned as a stdClass object, so if required convert to associative array

    $userId = $payloadData->userId;
    $userName = $payloadData->userName;

    // Query database to fetch User by ID
    // if User not present in DB return an error response
    // you can also modify the validate function to include the logic of
    // checking if the user is present in db
    $userExistsInDb = true;
    if (!$userExistsInDb) {
        throw new AuthException('Unauthorized', 401, ['info' => 'Sorry! Invalid user.']);
    }
} catch (Exception $e) {
    errorResponse($e);
}

// if all the above is valid then we can return the workorder list for that user
$workorders = [
    ['id' => 1, 'order_no' => 'ABC123'],
    ['id' => 2, 'order_no' => 'ABC124'],
    ['id' => 3, 'order_no' => 'ABC125'],
];

jsonResponse($workorders);
