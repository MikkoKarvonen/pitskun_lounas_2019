<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pitskun lounas 2019</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light center">
        <span class="navbar-brand" href="">
            🍴 Pitskun lounas 2019 🍴
        </span>
    </nav>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col"></th>
                <th scope="col">Lounas</th>
                <th scope="col">Kasvis</th>
                <th scope="col">Deli</th>
                <th scope="col">Jälkiruoka</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $today = new DateTime();
            $weeks = 0;
            $missed = 0;
            $days = ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe'];

            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';

            while($weeks < 4){
                if ($today->format('N') < 6){
                    $url = 'https://www.sodexo.fi/ruokalistat/output/daily_json/27793/'.$today->format('Y/m/d').'/fi';
                    $result = file_get_contents($url);
                    $res = json_decode($result, true);
                    if (empty($res[courses])){
                        $missed++;
                    }else {
                        if ($today->format('N') == 1 && $weeks > 0){
                            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';
                        }
                        $dayContent = '<th scope="row">'.$days[$today->format('w')].'<br>'.$today->format('j.n.').'</th>';
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
    <div class="footer-copyright text-center py-3">© 2019: 
        <a href="https://github.com/MikkoKarvonen/pitskun_lounas_2019" target="_blank">Mikko Karvonen</a>
    </div>
</body>
</html>