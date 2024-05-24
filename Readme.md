
title: Docs to share with ARM for their server-side
created at: Wed Apr 24 2024 15:59:10 GMT+0000 (Coordinated Universal Time)
updated at: Fri May 24 2024 16:38:03 GMT+0000 (Coordinated Universal Time)
---

# Docs to share with ARM for their server-side

# PressOne PHP SDK

If you are using any php library, you can install PressOne as part of your dependencies using composer

Run:

    composer install pressone/api

Then use it in your project as thus

    // .env
    // PRESSONE_SECRET_KEY = "your_pressone_secret"

    <?php
    use Pressone\Api\Telephony;

    class {
      private $telephone;

      function __construct() {
        $this->telephone = new Telephony(PRESSONE_SECRET_KEY);
      }
    }

Now you have access to call our methods.

    // returns all the numbers you have with PressOne
    $telephone->getNumbers();

    // assign a customer to a number
    $data = [
      "email"         => "user@pressone.co",
      "phone_number"  => "08123456789",
      // optional params,
      "first_name"    => "John",
      "last_name"     => "Doe",
      "role"          => "owner",
    ];

    $telephone->assignNumber($data);

    // get your call credential
    $telephone->getCallCredentials($public_key, $number_id);
    // get $public_key from frontend client
    // get $number_id from the response in $telephone->getNumbers()

    // returns call records
    $telephone->getCallRecords();

    // returns contacts
    $telephone->getContacts();

          