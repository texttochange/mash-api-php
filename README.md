#MASH REST API SDK for PHP

> This is currently under development. Drastic changes may occur.  
This can only be used by approved applications allowed to use MASH.


## Requirements
- curl 


## Quick start

Create a new api instance using the token given by MASH:
```php
 $api = new MashApi($token, $url);
```
A url can be specified if it doesn't match the default one.

To test if is working:
```php
print_r($api->test());
```
Should return information about the application making the request.

Get some participants:
```php
$participants = $api->getParticipants(array(
  'country' => 'prt',
  'num' => 10,
));
```


## Endpoints

*__$payload__ should always be a key=>value array using the arguments specified in the documentation.*

Get a list of participants:
```php
$api->getParticipants($payload);
```

Create a participant:
```php
$api->createParticipant($payload);
```

Get a specific participant:
```php
$api->getParticipant($uuid, $payload);
```

Add usage to a participant:
```php
$api->addUsage($uuid, $payload);
```

Remove usage from a participant:
```php
$api->removeUsage($uuid, $payload);
```