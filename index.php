<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php
    $today = new DateTime();
    $weeks = 0;
    $missed = 0;

    while($weeks < 4){
        if ($today->format('N') < 6){
            $url = 'https://www.sodexo.fi/ruokalistat/output/daily_json/27793/'.$today->format('Y/m/d').'/fi';
            $result = file_get_contents($url);
            $res = json_decode($result, true);
            if (empty($res[courses])){
                $missed++;
            }else {
                echo $today->format('Y/m/d').' '.$today->format( 'N' ).'<br>';
                foreach($res[courses] as $r) {
                    echo $r[category] .' '. $r[title_fi] .' '. $r[properties], '<br>';
                }
            }
            if ($today->format('N') == 5){
                $weeks++;
            }
            if ($missed >= 3){
                break;
            }
        }
        $today->modify( '+1 days' );
    }

    ?>
    <pre>
</pre>
</body>
</html>