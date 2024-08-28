# PressOne PHP SDK

If you are using any Php library, you can install PressOne as part of your dependencies using composer

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
        // for test mode pass "true" as a second parameter to the constructor
        // $this->telephone = new Telephony(PRESSONE_SECRET_KEY, true);
      }
    }

Now you have access to call the follow methods.

    // returns all available numbers you can purchase on PressOne
    $numbers = $telephone->availableNumbers();
    // $numbers: {
    //  data: [
    //    {
    //      phone_number: "02012345678",
    //      country_code: "NG",
    //    },
    //    ...
    //  ],
    //  total: 432,
    //  size: 10
    // }

    // returns all the numbers you have with PressOne
    $numbers = $telephone->getNumbers();
    // $numbers: [
    //   {
    //     phone_number: "02012345678",
    //     status: "verified",
    //     label: "My Number"
    //     number_id: 2693,
    //   },
    //   ...
    // ]

    // You can create a user and assign the user to a number using this method.
    $data = [
      "email"         => "user@pressone.co", // customer's email
      "phone_number"  => "+23408123456789", // customer's phone number
      "number_ids"    => [234, 567], // number_id from $telephone->getNumbers() result
      // optional params,
      "first_name"    => "John",
      "last_name"     => "Doe",
      "role"          => "owner",
    ];
    $telephone->assignNumber($data);

    // returns the list of assigned customers to number
    $members = $telephone->getMembers();
    // $members: [
    //   {
    //     phone_number: "+23408123456789",
    //     full_name: "John Doe",
    //     receiver_id: 234
    //     receiver_code: 100,
    //     user_id: 2
    //   },
    //   ...
    // ]

    // get your call credential
    $token = $telephone->getCredentials($user_id);
    // $user_id is from the response in $telephone->assignNumber();
    // 
    // $token: {
    //    refresh: "eyJ283w...",
    //    access: "eyJ283w...",
    //    expiry: 7200
    // }

    // returns call records
    $telephone->getCallRecords();

    // returns contacts
    $telephone->getContacts();

          