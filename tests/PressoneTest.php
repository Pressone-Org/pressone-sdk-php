<?php
// src/Test.php

require_once __DIR__ . '/../vendor/autoload.php';

use Pressone\Api\Telephony;

$pressone = new Telephony("pk_43pAHHF2Wp45cQgfp4zD2rl3eHyoT_c4381679ea57c2e8ca0054b0dcd903c93aaa070486eed52b86b33205fe1b218b299d7605539237f28b645b7d5ea896b25bdef0728e60d6bf81b55a6373240df3");
$numbers = $pressone->getNumbers();
assert(isset($numbers["total"]));
assert(isset($numbers["size"]));
assert(isset($numbers["data"]));

// $contacts = $pressone->getContacts();
// print_r($contacts);