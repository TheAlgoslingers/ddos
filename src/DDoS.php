<?php
/***
 * thealgoslingers
 * Twitter @thealgoslingers
 * Aug 16th, 2024
 */
namespace thealgoslingers;

class DDoS
{
  public function __construct($ip='',
  // @Class options
  $options = array(
   /** Cache option
   * WARNING: Please make sure your
   * system have sufficient RAM to enable
   * this feature
   * 
   * CAUTION: If enabling the cache feature,
   * Please set TRUE once in a while. Example
   * once per session
  */
    "cache" => FALSE,// It should be boolean(TRUE or FALSE)
    
    // Optional path to log or
    // to be lpgged-in file
    "log_file" => '',
    
    // Set the rate limit
    // (e.g., 100 requests per 10 minutes)
    // @key rate - requests
    "rate" => 100,
    
    // @key timesamp - years/days/hours/minutes in seconds
    "timesamp" => 600)
    ) {
      
    if(! is_bool($options['cache'])){
      throw new Exception(__CLASS__ . ': Cache option must be a boolean (TRUE or FALSE)');
    }
    
    // set @Class cache option
    $this->cache_option = $options['cache'];
    
    // path to ip2location database
    // (Usually .BIN file)
    $file = dirname(dirname(dirname(__DIR__))) . '/ip2location/ip2location-php/data/IP2LOCATION-LITE-DB1.IPV6.BIN';
    
    if(! file_exists($file) || !is_file($file)){
      // the ip2location dataset not found
      throw new Exception(__CLASS__ . ': No IP2Location LITE database found');
    }
    
    // The log file 
    $log_file = $options['log_file'];
    
    ## if log file not provided
    if(empty($log_file)){
      ## create log file
      ## if not.provided
      $log_file = dirname(__FILE__) . 'ddos_logs.txt';
    }
    
    // set @Class database file
    $this->database_file = $file;
    
    // set @Class log file
    $this->log_file = $log_file;
    
    // set @Class rate limit
    $this->rate = $options["rate"];
    
    // set @Class timestamp
    $this->timestamp = $options["timestamp"];
    
    // user ip
    $this->ip = $ip;
    
    if(empty($ip)){
      
      if(!empty($_SERVER['HTTP_CLIENT_IP'])){  
                $this->ip = $_SERVER['HTTP_CLIENT_IP'];  }  
                
      //whether ip is from the proxy  
      elseif (empty($ip) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}  
                
      //whether ip is from the remote address  
      elseif(empty($ip)){  
             $this->ip = $_SERVER['REMOTE_ADDR'];  
     }
   }
   
  // Run cache
    $this->__cache($this->cache_option);
    
  }
  
  protected function __cache($option){
    if($option){
      /***
       * Cache the database into memory to
       * accelerate lookup speed
       * WARNING: Please make sure your
       * system have sufficient RAM to enable
       * this feature
       */
       $this->db = new \IP2Location\Database($this->database_file, \IP2Location\Database::MEMORY_CACHE);
    }
  }
  
  public function log(collable $function = null){
    // Get the visitor's IP address
    $visitor_ip       = $this->ip ?? 'UNKNOWN';
    $visitor_country  = $this->ip_country() ?? 'UNKNOWN';
    $rate_limit       = $this->rate;
    $time_window      = $this->timestamp;
    
    // Read the log file
    // if the file exists
    if(file_exists($this->log_file)){
    
    $log_data = file($this->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Filter logs for this IP within the time window
    $recent_requests = array_filter($log_data, function ($line) use ($visitor_ip, $time_window) {
    list($log_timestamp, $log_ip, $log_country) = explode(" - ", $line);
    
    $log_time = strtotime($log_timestamp);
    
    //return
    return $log_ip === $visitor_ip && (time() - $log_time) <= $time_window;
   });
   
   // Check if the rate limit has been exceeded
  if (count($recent_requests) >= $rate_limit) {
    
    // if caller want to execute own function
    if(function_exists($function)){
     return $function();
    }
    // else
    // Block or limit the request
    header('HTTP/1.1 429 Too Many Requests');
    echo "Too many requests. Please try again later.";
    exit;
  }
}
    // Get the current timestamp
    $today = date("Y-m-d H:i:s");
    
    $log_entry = "$today - $visitor_ip - $visitor_country\n";
    
    // Log the IP address and timestamp
    file_put_contents($this->log_file, $log_entry, FILE_APPEND);
  }
  
  public function ip(){
    return $this->ip;
  }
  
  public function ip_country(){
    $this->db = new \IP2Location\Database($this->database_file, \IP2Location\Database::FILE_IO);
    $country = $this->db->lookup($this->ip, \IP2Location\Database::COUNTRY_NAME);
    return $country;
  }
  
}