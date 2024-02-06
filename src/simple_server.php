<?php


function addStudentToCourse($name, $title) {
  // find the course in file
  $student = array(
    // Name
    "books" => array (

    )
  );

  $student["name"] = $name;
  $student["books"][0]["title"] = $title;
  
  return $student;
}

function createBook($title, $bpub, $bed, $dop) {
  $book = array();
  $book["title"] = $title;
  $book["bpub"] = $bpub;
  $book["bed"] = $bed;
  $book["dop"] = $dop;

  
  return $book;
}

function createCourse($name, ...$books) {
  $course = array();
  $course["course"] = $name;
  $course["students"] = array();
  $course["books"] = array();

  foreach ($books as $book) {
    $course["books"] += $book;
  }
  

  return $course;
}




function writeJson($data) {
 // $fp = fopen("data.json", 'w+');

  $inp = file_get_contents("array.json");
  $tempArray = json_decode($inp);
  //$idk = array($tempArray);

  array_push($tempArray, $data);
  $jsonData = json_encode($tempArray);
  file_put_contents("array.json", $jsonData);
//  fclose($fp);


}

print "<p>Hi, ! This is new important data for your web page.</p> ";

// get the q parameter from URL
if (($_GET['instructor'] ?? true)) {
  $isInstructor = $_GET['instructor'];
  echo $isInstructor;
  // handle instructor
  if ($isInstructor === "true") {
    // an instructor can do what?
    $course = $_GET['course'];
    $title = $_GET['title'];
    $bpub = $_GET['pub'];
    $bed = $_GET['ed'];
    $dop = $_GET['dop'];

    $book = createBook($title, $bpub, $bed, $dop);

    // search course
    // if found course write to it

    $write = createCourse($course, $book);
    // if no course found make new one
    writeJson($write);

  } else {
    // a student can do what
    echo "124rdajkfjka";
    echo "pls";
  }


} else {



}
?>