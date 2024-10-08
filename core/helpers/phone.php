<?



function unformatphone($phone){
    $phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
    $phone = trim($phone);
    if($phone[0] == '+'){
        unset($phone[0]);
        $phone[1] = 7;
    }
    if($phone[0] == 8){
        $phone[0] = 7;
    }
    return $phone;
}


function formatphone($phone = '', $convert = false, $trim = true){
    // If we have not entered a phone number just return empty
    if (empty($phone)) {
        return '';
    }

    // Strip out any extra characters that we do not need only keep letters and numbers
    $phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);

    // Do we want to convert phone numbers with letters to their number equivalent?
    // Samples are: 1-800-TERMINIX, 1-800-FLOWERS, 1-800-Petmeds
    if ($convert == true) {
        $replace = array('2'=>array('a','b','c'),
            '3'=>array('d','e','f'),
            '4'=>array('g','h','i'),
            '5'=>array('j','k','l'),
            '6'=>array('m','n','o'),
            '7'=>array('p','q','r','s'),
            '8'=>array('t','u','v'), '9'=>array('w','x','y','z'));

        // Replace each letter with a number
        // Notice this is case insensitive with the str_ireplace instead of str_replace
        foreach($replace as $digit=>$letters) {
            $phone = str_ireplace($letters, $digit, $phone);
        }
    }

    // If we have a number longer than 11 digits cut the string down to only 11
    // This is also only ran if we want to limit only to 11 characters
    if ($trim == true && strlen($phone)>11) {
        $phone = substr($phone,  0, 11);
    }

    // Perform phone number formatting here
    if (strlen($phone) == 7) {
        return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1-$2", $phone);
    } elseif (strlen($phone) == 10) {
        $temp = preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "($1)$2-$3-$4", $phone);
        return '+7'.$temp;
    } elseif (strlen($phone) == 11) {
        $temp = preg_replace("/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "$1($2)$3-$4-$5", $phone);
        if($temp[0] == 8){
            $temp[0] = 7;
        }
        return '+'.$temp;

    }

    // Return original phone if not 7, 10 or 11 digits long
    return $phone;
}
