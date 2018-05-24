# Le Frecce API

A simple class to interact with LeFrecce.it mobile site APIs

## Requirements

- PHP 5.6+
- guzzlehttp/guzzle 6.2+
- nesbot/carbon ^1.22+

## Installing

Use Composer to install it:

```
composer require filippo-toso/viaggia-treno
```

## Using It

```
use FilippoToso\LeFrecceAPI\Client as LeFrecce;

// Create the client
$client = new LeFrecce();

// Autocomplete a station name
$locations = $client->locations('Milano');
print_r($locations);
```

## Thanks  

The creation of this class is based on SimoDax documentation:

https://github.com/SimoDax/Trenitalia-API/wiki/API-Trenitalia---lefrecce.it
