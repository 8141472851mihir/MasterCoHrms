<?php
include_once dirname(dirname(__DIR__)) . '/EnvLoader.php';

class model
{
    private $ary = [];
    private static $envLoaded = false;

    public function __construct()
    {
        if (!self::$envLoaded) {
            EnvLoader::load();
            self::$envLoaded = true;
        }
    }

    function set_data($name, $value)
    {
        $this->ary[$name] = $value;
    }
    function get_data($name)
    {
        return $this->ary[$name] ?? null;
    }

    function base_url() {
        return EnvLoader::get('APP_URL');
    }
    function company_url() {
        return EnvLoader::get('COMPANY_URL');
    }
    function api_key() {
        return EnvLoader::get('API_KEY');
    }
}
//for filter data
 function test_input($data)
{
    if(is_array($data)) {
        return array_map('test_input', $data);
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
function custom_echo($x, $length)
{
  if(strlen($x)<=$length)
  {
    echo $x;
  }
  else
  {
    $y=substr($x,0,$length) . '...';
    echo $y;
  }
}

// count time 
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = (int) floor($diff->d / 7);
    $remainingDays = $diff->d - ($weeks * 7);

    $labels = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    $values = array(
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $weeks,
        'd' => $remainingDays,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    );

    foreach ($labels as $key => &$label) {
        if (!empty($values[$key])) {
            $label = $values[$key] . ' ' . $label . ($values[$key] > 1 ? 's' : '');
        } else {
            unset($labels[$key]);
        }
    }

    if (!$full) {
        $labels = array_slice($labels, 0, 1);
    }
    return $labels ? implode(', ', $labels) . ' ago' : 'just now';
}


function imageResize($imageSrc,$imageWidth,$imageHeight,$newImageWidth,$newImageHeight) {

    $newImageLayer=imagecreatetruecolor($newImageWidth,$newImageHeight);
    imagecopyresampled($newImageLayer,$imageSrc,0,0,0,0,$newImageWidth,$newImageHeight,$imageWidth,$imageHeight);

    return $newImageLayer;
    // echo $newImageLayer;
}