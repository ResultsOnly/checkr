# Checkr
PHP wrapper for checkr.io API (https://checkr.io/docs)

## Usage
**Set Authentication keys**
  ```PHP
putenv("CHECKR_TEST_KEY=f208a926e6191a66418bdfeaba8a61fec83df402");
putenv("CHECKR_PRODUCTION_KEY=893968950d556d8f7d88138128a4adf51a8788a2");
  ```

**Create a person**
```PHP
$person = [
	'first_name' => 'Sheldon',
	'last_name' => 'Cooper',
	'dob' => '10/08/1983',
	'email' => 'scoop@example.com',
	'phone' => '5555555555',
	'address' => '2311 Los Robles',
	'city' => 'Pasedena',
	'state' => 'CA',
	'zipcode' => '90041',
	'ssn' => '111-11-2001',
	'driver_license_number' => 'F1112002',
	'driver_license_state' => 'CA'
];
```

**Create candidate**
```PHP
$checkr = new Checkr();
$candidate = $checkr->createCandidate($person);
```

**List candidates**
```PHP
$checkr->listCandidates(); //returns array of candidate objects
```

**Get candidate**
```PHP
$checkr->getCandidate('630441bcc3d4cf786bc2ae62'); //returns single candidate object
```

**Get Report**
```PHP
$checkr->getReport('edc337e641fb170ce4263393'); //return singlel report object
```
## Contributing
Fork it and submit pull request
