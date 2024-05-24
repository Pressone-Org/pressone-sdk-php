<?php
// src/Test.php

require_once __DIR__ . '/../vendor/autoload.php';

use Pressone\Api\Telephony;

$secret_key = "";

$pressone = new Telephony($secret_key);
$numbers = $pressone->getNumbers();

$members = $pressone->getMembers();
assert(isset($members["total"]));
assert(isset($members["size"]));
assert(isset($members["data"]));

// $contacts = $pressone->getContacts();
print_r($numbers);