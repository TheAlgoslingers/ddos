# ddos
Safeguard your website from DDoS attack while running your website smoothly

## Installation
```
  composer require thealgoslithealgoslingers/ddos
```

## Usage

```php
<?php

## require vendor autoload
require_once 'vendor/autoload.php';

use thealgoslingers\ddos;

$ddos = new DDoS();

$ddos->log();
```

## Note
• There are two optional params accepted. The first param is an 'ip address' and the second is 'options'. Both are optional.

```
<?php
$ddos = new DDoS($ip_addresa, $options);
```

### IP Address
• By default, DDoS use the ip address from the user making the request.
• You can manually monitor or log a preferred ip address.

#### Example:
```
<?php

## require vendor autoload
require_once 'vendor/autoload.php';

use thealgoslingers\ddos;

$demo_ip = '0.0.0.0';// provided ip address

$ddos = new DDoS($demo_ip);

$ddos->log();
```

### Options param
The $options is an array of DDoS settings. Here is the list:

`cache`: This is set TRUE or FALSE(FALSE by default). It only has effect on the ip2location whether to cache its dataset (for faster ip lookup) or not.
WARNING: This should be set TRUE once per session. Else, the caching will keep restarting anytime this feature is enabled. Make sure you have enough RAM to enable this feature.

`log_file`: This option is a path to .txt file to log requests. Please make sure you create the file before setting this option(path/to/the/log/file.txt). By default, DDoS create a log file on its own.

`rate` and `timesamp`: These two options work together. They specify how many requests allowed(rate) per day/hours/minutes/etc(timesamp). The timesamp option should be in seconds.
By default, it is 100 requests per 600(10 minutes).

#### Example - All together

```
<?php

## require vendor autoload
require_once 'vendor/autoload.php';

use thealgoslingers\ddos;

$demo_ip = '0.0.0.0';// provided ip address

// set options
$options = array(
 "cache" => FALSE,
 "log_file" => "path/to/your/log/file.txt",
 "rate" => 15 // 15 request,
 "timesamp" => 600 // 10 minutes in seconds
);

$ddos = new DDoS($demo_ip, $options);

$ddos->log();
```

• `log()` in DDoS also accept one optional param which is a function. This function is executed by DDoS when users hit their limit.
• By default, when a user hits its limit, he is automatically redirected to a "429 Too Many Requests". 
• To prevent this from happening, you can tell DDoS to execute your function if a user hit the limit.

#### Example

```
<?php

## require vendor autoload
require_once 'vendor/autoload.php';

use thealgoslingers\ddos;

$demo_ip = '0.0.0.0';// provided ip address

// set options
$options = array(
 "cache" => FALSE,
 "log_file" => "path/to/your/log/file.txt",
 "rate" => 15 // 15 request,
 "timesamp" => 600 // 10 minutes in seconds
);

// Define what DDoS should do if
// users hit the limit
function myFunc(){
  // Do something when user hit the limit
}

$ddos = new DDoS($demo_ip, $options);

$ddos->log('myFunc');

```

• Please note that this function will only be called when the user hits his request limit.


## note
DDoS is highly recommended for applications which have main controller or the MVC architecture; where all requets are managed by a single file controller.