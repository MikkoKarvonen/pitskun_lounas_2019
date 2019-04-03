<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Lounas</th>
                <th>Kasvis</th>
                <th>Deli</th>
                <th>Jälkiruoka</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $today = new DateTime();
            $weeks = 0;
            $missed = 0;
            $days = ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe'];

            while($weeks < 4){
                if ($today->format('N') < 6){
                    $url = 'https://www.sodexo.fi/ruokalistat/output/daily_json/27793/'.$today->format('Y/m/d').'/fi';
                    $result = file_get_contents($url);
                    $res = json_decode($result, true);
                    if (empty($res[courses])){
                        $missed++;
                    }else {
                        $dayContent = '<td>'.$days[$today->format('w')].'<br>'.$today->format('j.n.').'</td>';
                        foreach($res[courses] as $r) {
                            $dayContent .= '<td>'.$r[title_fi] .'<br>'. $r[properties].'</td>';
                        }
                        echo '<tr>'.$dayContent.'</tr>';
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
        </tbody>
    </table>
</body>
</html>