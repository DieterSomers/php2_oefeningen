<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once "lib/autoload.php";

PrintHead();
PrintJumbo( $title = "Leuke plekken in Europa" ,
                        $subtitle = "Tips voor citytrips voor vrolijke vakantiegangers!" );
PrintNavbar();
?>

<div class="container">
    <div class="row">


<?php
    //toon messages als er zijn
    $container->getMessageService()->ShowErrors();
    $container->getMessageService()->ShowInfos();

    //export button
    $output ="";
    $output .= "<a style='margin-left: 10px' class='btn btn-info' role='button' href='export/export_images.php'>Export CSV</a>";
    $output .= "<div><br></div>";

    //get data
    $data = $container->getDBManager()->GetData( "select * from images" );

    //add weather data
    $restClient = new RESTClient( $authentication = null );

    foreach ($data as $key => $value){
        $url = 'http://api.openweathermap.org/data/2.5/weather?q=' . $value['img_weather_location'] . '&units=metric&lang=nl&APPID=b90d5d379fbdbdb8665094835b52be2a';

        $restClient->CurlInit($url);
        $response = json_decode( $restClient->CurlExec() );

        $data[$key]['weather_description'] = $response->weather[0]->description;
        $data[$key]['weather_temp'] = round($response->main->temp);
        $data[$key]['weather_humidity'] = $response->main->humidity;

    }


    //get template
    $template = file_get_contents("templates/column.html");

    //merge
    $output .= MergeViewWithData( $template, $data );
    print $output;
?>

    </div>
</div>

</body>
</html>