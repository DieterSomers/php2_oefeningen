<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

$public_access = true;
require_once "../lib/autoload.php";

header("Access-Control-Allow-Origin: 'https://gf.dev'");
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

$parts = explode("/", $request_uri);

//var_dump($parts);
//zoek "rest" in de uri
for ( $i=0; $i<count($parts) ;$i++)
{
    if ( $parts[$i] == "oef2.2" )
    {
        break;
    }
}

$request_part = $parts[$i+2];
if ( count($parts) > $i + 3 ) $id = $parts[$i + 3];

//Fouten
if($request_part != "btwcodes" OR $id != null)
{
    if($request_part == "btwcode" AND !is_numeric($id))
    {
        print json_encode( [ "msg" => "Het opgegeven ID is ongeldig"]);
        die;
    }
    if($request_part != "btwcode")
    {
        print json_encode( [ "msg" => "Deze combinatie van Resource en Method is niet toegelaten"]);
        die;
    }
}


//GET btwcodes
if ( $method == "GET" AND $request_part == "btwcodes" )
{
    $sql = "select * from eu_btw_codes";

    //get data
    $data = $container->getDBManager()->GetData( $sql, 'assoc' );

    print json_encode( [ 'msg' => 'OK', 'data' => $data ] ) ;
}

//GET btwcode id
if ( $method == "GET" AND $request_part == "btwcode" )
{
    $sql = "select * from eu_btw_codes where eub_id=$id";

    //get data
    $data = $container->getDBManager()->GetData( $sql, 'assoc' );

    print json_encode( [ 'msg' => 'OK', 'data' => $data ] ) ;
}

//POST btwcodes
if ( $method == "POST" AND $request_part == "btwcodes"  )
{
    $code = $_POST["code"];
    $land = $_POST["land"];
    $sql = "INSERT INTO eu_btw_codes SET eub_land = '$land', eub_code = '$code'";

    //post data
    $container->getDBManager()->ExecuteSQL( $sql );

    //get id
    $data = $container->getDBManager()->GetData( "select max(eub_id) from eu_btw_codes", 'assoc' );

    http_response_code(201);
    print json_encode( [ "msg" => "BTW code $code - $land aangemaakt", "eub_id" =>  $data[0]["max(eub_id)"]] ) ; //normaal zou je hier een OK teruggeven
}

//PUT btwcode id
if ( $method == "PUT" AND $request_part == "btwcode" )
{
    $contents = json_decode( file_get_contents("php://input") );
    $code = $contents->code;
    $land = $contents->land;

    $sql = "UPDATE eu_btw_codes SET eub_land = '$land', eub_code = '$code' WHERE eub_id=$id";

    //put data
    $container->getDBManager()->ExecuteSQL( $sql );

    print json_encode( [ "msg" => "OK", "info" => "BTW code $code - $land gewijzigd" ] ) ; //normaal zou je hier een OK teruggeven
}

//DELETE btwcode id
if ( $method == "DELETE" AND $request_part == "btwcode" )
{
    $sql = "DELETE FROM eu_btw_codes WHERE eub_id=$id";

    //delete data
    $container->getDBManager()->ExecuteSQL( $sql );

    print json_encode( [ "msg" => "OK", "info" => "BTW code $id verwijderd" ] ) ; //normaal zou je hier een OK teruggeven
}

?>

