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
            include 'credentials.php';
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $today = new DateTime();
            $weeks = 0;
            $missed = 0;
            $missedMonday = false;
            $days = ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe'];

            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';

            while($weeks < 4){
                if ($today->format('N') < 6){
                    $sql = "SELECT * FROM courses WHERE day='".$today->format('Y-m-d')."';";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 0) {
                        $url = 'https://www.sodexo.fi/ruokalistat/output/daily_json/27793/'.$today->format('Y/m/d').'/fi';
                        $result = file_get_contents($url);
                        $res = json_decode($result, true);
                        foreach($res['courses'] as $r) {
                            $sql = "INSERT INTO courses (day, category, name, properties)
                            VALUES ('".$today->format('Y-m-d')."', '".$r[category]."', '".$r[title_fi]."', '".$r[properties]."')";
                            if ($conn->query($sql) === false) {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }
                        $sql = "SELECT * FROM courses WHERE day='".$today->format('Y-m-d')."';";
                        $result = $conn->query($sql);
                    }

                    if ($result->num_rows > 0) {
                        if ($missed > 0){
                            $missed = 0;
                        }
                        if (($today->format('N') == 1 || $missedMonday) && $weeks > 0){
                            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';
                            if ($missedMonday){
                                $missedMonday = false;
                            }
                        }       
                        $dayContent = '<th scope="row">'.$days[$today->format('w')].'<br>'.$today->format('j.n.').'</th>';
                        while($row = $result->fetch_assoc()) {
                            $dayContent .= '<td>'.$row["name"] .'<br>'. $row["properties"].'</td>';
                        }
                        echo '<tr>'.$dayContent.'</tr>';        
                    } else {
                        $missed++;
                        if ($today->format('N') == 1){
                            $missedMonday = true;
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
            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="footer-copyright text-center py-3">© 2019: 
        <a href="https://github.com/MikkoKarvonen/pitskun_lounas_2019" target="_blank">Mikko Karvonen</a>
    </div>
</body>
</html>