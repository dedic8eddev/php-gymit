<?php

// Return mongo objectId
if( ! function_exists('mId') ){
    function mId($id){
        return new \MongoDB\BSON\ObjectID($id);
    }
}

if(!function_exists("closeBrowserTab")){
    function closeBrowserTab() {
        echo "<script>window.close();</script>";
    }
}

if( ! function_exists('mongoDateToLocal') ){
    function mongoDateToLocal($date) {
        return mongoDateToDatetime($date)->format('Y-m-d H:i:s');
    }
}

if( ! function_exists('mongoDateToDatetime') ){
    function mongoDateToDatetime($date) {
        $datetime = new DateTime($date);
        $timezone = new DateTimeZone('Europe/Prague');

        $datetime->setTimezone($timezone);
        return $datetime;
    }
}

if( ! function_exists('localDateToMongo') ){
    function localDateToMongo($date) {
        return gmdate('Y-m-d\TH:i:s\Z',strtotime($date));
    }
}

if( ! function_exists('slugify') ){
    function slugify($text){
        if (empty($text)) return false;
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text;
    }
}

if (! function_exists('blogArticleLink')){
    function blogArticleLink($article){
        if(is_array($article)) return base_url() . 'blog/article/' . slugify($article['title']) . '-' . $article['id'];
        else return base_url() . 'blog/article/' . slugify($article->title) . '-' . $article->id;
    }
}

if( ! function_exists('humanDate') ){
    function humanDate($d, $time=true, $year=true) {
        $format=$year?'j.n.Y':'j.n.'; // without year?
        if(empty($d)) return "---";
           $date=date("j.n.Y",strtotime($d));
           $time=$time?' '.date("H:i",strtotime($d)):''; // without time?
        if($date==date("j.n.Y")) return "Dnes$time";
        else if($date==date("j.n.Y", strtotime("yesterday"))) return "Včera$time";
        else if($date==date("j.n.Y", strtotime("tomorrow"))) return "Zítra$time";
        else return date($format,strtotime($d)).$time;
    }
}

if( ! function_exists('humanTime')){
    function humanTime($time){
        $time = explode(":", $time);
        if( ! function_exists('s')){
            function s($num, $text_1, $text_2, $text_3){
                return "$num " . (abs($num) == 1 ? $text_1 : ($num == 0 || abs($num) >= 5 ? $text_3 : $text_2));
            }
        }
        if ($time[0] > 24) echo s(floor($time[0] / 24), "den", "den", "den");
        elseif (intval($time[0])) echo s(intval($time[0]), "hodina", "hodin", "hodin");
        else echo s(intval($time[1]), "minuta", "minut", "minut");
    }
}

if( ! function_exists('substrwords')){
    function substrwords($text, $maxchar, $end='...') {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);      
            $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } 
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } 
        else {
            $output = $text;
        }
        return $output;
    }
}

// Checks if the current process is being run from the CLI (Cron / command line / etc.)
if ( ! function_exists('isCLI')){
	function isCLI()
	{
		if ( defined('STDIN') )
		{
			return true;
		}
	
		if ( php_sapi_name() === 'cli' )
		{
			return true;
		}
	
		if ( array_key_exists('SHELL', $_ENV) ) {
			return true;
		}
	
		if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) 
		{
			return true;
		} 
	
		if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
		{
			return true;
		}
	
		return false;
	}

    if (! function_exists('echoPriceWithCurrency')) {
        function echoPriceWithCurrency($price, string $currencyCode = 'CZK', string $posfix = ',- '): void
        {
            switch ($currencyCode) {
                case 'CZK':
                    $currency = 'Kč';
                case 'EUR':
                    $currency = '€';
                case 'USD':
                    $currency = '$';
                default:
                    $currency = 'Kč';
            }

            echo number_format($price, 2, ',', ' ') . $posfix . $currency;
        }
    }

	if (! function_exists('escapeHtml')) {
        function escapeHtml($string): string
        {
            return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
        }
    }

    if (! function_exists('echoEscapedHtml')) {
        function echoEscapedHtml($string): void
        {
            echo escapeHtml($string);
        }
    }

    if (! function_exists('dateFromString')) {
        function dateFromString($datetimeString, string $format = 'j.n.Y'): string
        {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeString);

            return $date->format($format);
        }
    }

    if (! function_exists('timeFromString')) {
        function timeFromString($datetimeString, string $format = 'H:i'): string
        {
            return dateFromString($datetimeString, $format);
        }
    }

    if (! function_exists('url')) {
        /**
         * @param string $section
         * @param string|null $slug
         * @param int|null $id
         * @param string $delimiter
         * @return string
         */
        function url(string $section, ?string $slug = null, ?int $id = null, string $delimiter = '-'): string
        {
            $url = $section . '/';

            if ($slug !== null) {
                $url .= slugify($slug);
            }

            if ($id !== null) {
                $url .= $delimiter . $id;
            }

            return base_url($url);
        }
    }

    if (! function_exists('paymentPeriodToHuman')) {
        function paymentPeriodToHuman(string $period): string
        {
            switch ($period) {
                case 'day':
                    return 'Denně';
                case 'week':
                    return 'Týdně';
                case 'month':
                    return 'Měsíčně';
                case 'year':
                    return 'Ročně';
            }
        }
    }

    if (! function_exists('isTrainer')) {
        function isTrainer(int $groupId): bool
        {
            return in_array($groupId, [PERSONAL_TRAINER, MASTER_TRAINER]);
        }
    }

}