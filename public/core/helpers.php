<?php

function errorResponse($exception)
{
    $message = $exception->getMessage();
    $data = [
        'message' => $message,
    ];
    $code = 400;
    if ($exception instanceof AuthException) {
        $data = $exception->data;
        $code = $exception->code;
    }
    header('HTTP/1.0 '.$code.' '.$message);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
