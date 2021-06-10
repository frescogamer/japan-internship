<?php

include_once 'config/database.php';

// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties

    public $firstname;
    public $middlename;
    public $lastname;
    public $nationality;
    public $currently_residing;
    public $field_of_studies;
    public $graduated;
    public $student;
    public $seek_intern;
    public $seek_employment;
    public $visable_till;
    public $email;
    public $password;
    
    public $nativeSpeaking;
    public $secondSpeaking;
    public $socials;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
// create new user record
function create(){

    $sqlSelectMail = "SELECT email FROM users WHERE email = :mail";
    $stmt_email = $this->conn->prepare($sqlSelectMail);
    $stmt_email->bindParam(':mail', $this->email);
    $stmt_email->execute();
    $row = $stmt_email->fetch();
    if (!$row) { // checks email when creating user account.
    // insert query
    $query = "INSERT INTO " . $this->table_name . "
            SET
            firstname = :firstname,
            middlename = :middlename,
            lastname = :lastname,
            nationality = :nationality,
            currently_residing = :currently_residing,
            field_of_studies = :field_of_studies,
            graduated = :graduated,
            student = :student,
            seek_intern = :seek_intern,
            seek_employment = :seek_employment,
            visable_till = :visable_till,
            email = :email,
            password = :password,
            signup_date = :signupDate";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    // $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    // $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    // $this->email=htmlspecialchars(strip_tags($this->email));
    // $this->password=htmlspecialchars(strip_tags($this->password));
    
    // bind the values
    $stmt->bindParam(':firstname', $this->firstname);
    $stmt->bindParam(':middlename', $this->middlename);
    $stmt->bindParam(':lastname', $this->lastname);
    $stmt->bindParam(':nationality', $this->nationality);
    $stmt->bindParam(':currently_residing', $this->currently_residing);
    $stmt->bindParam(':field_of_studies', $this->field_of_studies);
    $stmt->bindParam(':graduated', $this->graduated);
    $stmt->bindParam(':student', $this->student, PDO::PARAM_BOOL);
    $stmt->bindParam(':seek_intern', $this->seek_intern, PDO::PARAM_BOOL);
    $stmt->bindParam(':seek_employment', $this->seek_employment, PDO::PARAM_BOOL);
    $stmt->bindParam(':visable_till', $this->visable_till);
    $stmt->bindParam(':email', $this->email);

    $signupDate = new Datetime('now');
    $timestamp = $signupDate->format('Y-m-d H:i:s');
    $stmt->bindParam(':signupDate', $timestamp);;
    
    // hash the password before saving to database
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password_hash);
    
    // execute the query, also check if query was successful

    // $stmt->debugDumpParams();
    if($stmt->execute()){
        return true;
    }
 
    return false;
    } else {
        return false;
        // code when user excists
    }
}
 
// check if given email exist in the database
function emailExists(){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->table_name . "
            WHERE email = ?
            LIMIT 0,1";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->email=htmlspecialchars(strip_tags($this->email));
 
    // bind given email value
    $stmt->bindParam(1, $this->email);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // assign values to object properties
        $this->id = $row['id'];
        $this->firstname = $row['firstname'];
        $this->middlename = $row['middlename'];
        $this->lastname = $row['lastname'];
        $this->nationality = $row['nationality'];
        $this->currently_residing = $row['currently_residing'];
        $this->field_of_studies = $row['field_of_studies'];
        $this->graduated = $row['graduated'];
        $this->student = $row['student'];
        $this->seek_intern = $row['seek_intern'];
        $this->seek_employment = $row['seek_employment'];
        $this->visable_till = $row['visable_till'];
        $this->password = $row['password'];
 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}

// update a user record
public function update(){

    $password_set=!empty($this->password) ? ", password = :password" : "";

 
    // if no posted password, do not update the password
    $query = "UPDATE " . $this->table_name . "
            SET
            native_speaking = :nativeSpeaking,
            second_third_lang = :secondSpeaking,
            socials = :socials
            {$password_set}
            WHERE id = :id";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
 
    // bind the values from the form
    $stmt->bindParam(':nativeSpeaking', $this->nativeSpeaking);
    $stmt->bindParam(':secondSpeaking', $this->secondSpeaking);
    $stmt->bindParam(':socials', $this->socials);
 
    // hash the password before saving to database
    if(!empty($this->password)){
        $this->password=htmlspecialchars(strip_tags($this->password));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    }
 
    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}

}

