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
      }
    }

Now you have access to call the follow methods.

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

    // assign a customer to a number
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
    //   },
    //   ...
    // ]

    // get your call credential
    $telephone->getCallCredentials($public_key, $number_id);
    // $public_key would be sent from frontend client
    // $number_id is from the response in $telephone->getNumbers()

    // returns call records
    $telephone->getCallRecords();

    // returns contacts
    $telephone->getContacts();

          