<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set user property values
$user->firstname = $data->firstname;
$user->middlename = $data->middlename;
$user->lastname = $data->lastname;
$user->nationality = $data->nationality;
$user->currently_residing = $data->currently_residing;
$user->field_of_studies = $data->field_of_studies;
$user->graduated = $data->graduated;
$user->student = $data->student;
$user->seek_intern = $data->seek_intern;
$user->seek_employment = $data->seek_employment;

$user->visable_till = $data->visable_till;

$user->email = $data->email;
$user->password = $data->password;

// var_dump($user->firstname);
// var_dump($user->middlename);
// var_dump($user->lastname);
// var_dump($user->nationality);
// var_dump($user->currently_residing);
// var_dump($user->field_of_studies);
// var_dump($user->graduated);
// var_dump($user->student);
// var_dump($user->seek_intern);
// var_dump($user->seek_employment);
// var_dump($user->visable_till);
// var_dump($user->email);
// var_dump($user->password);

if(
    !empty($user->firstname) &&
    !empty($user->middlename) &&
    !empty($user->lastname) &&
    !empty($user->nationality) &&
    !empty($user->currently_residing) &&
    !empty($user->field_of_studies) &&
    !empty($user->graduated) &&
    !empty($user->student) &&
    !empty($user->seek_intern) &&
    !empty($user->seek_employment) &&
    !empty($user->visable_till) &&
    !empty($user->email) &&
    !empty($user->password)
    ){

    if($user->create()){
         // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
    } else {
    // set response code
    http_response_code(303);

    echo json_encode(array("message" => "Email excist."));
    }
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>