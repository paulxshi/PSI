<?php
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// verify status
if ($data['status'] == 'PAID') {

    $external_id = $data['external_id'];

    // TODO: Update database
    // mark order as PAID
}
