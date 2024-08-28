<?php
// src/Test.php

require_once __DIR__ . '/../vendor/autoload.php';

use Pressone\Api\Telephony;

$secret_key = "pk_43pAHHF2Wp45cQgfp4zD2rl3eHyoT_c4381679ea57c2e8ca0054b0dcd903c93aaa070486eed52b86b33205fe1b218b299d7605539237f28b645b7d5ea896b25bdef0728e60d6bf81b55a6373240df3";

$pressone = new Telephony($secret_key);
$numbers = $pressone->getNumbers();

$members = $pressone->getMembers();
assert(isset($members["total"]));
assert(isset($members["size"]));
assert(isset($members["data"]));

// $contacts = $pressone->getContacts();
print_r($numbers);