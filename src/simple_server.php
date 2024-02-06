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
  //$course["name"] = $name;
  
  $course[$name]["students"] = array();
  $course[$name]["books"] = array();

  foreach ($books as $book) {
    $course[$name]["books"] += $book;
  }
  

  return $course;
}


// Function to check for duplicate names in the array
function hasDuplicateName($dataArray, $name) {
  $hasKey = array_key_exists($name, $dataArray);
}

// Function to read JSON data from a file
function readJSONFromFile($filename) {
  if (file_exists($filename)) {
      $jsonData = file_get_contents($filename);
      return $jsonData !== false ? json_decode($jsonData, true) : array();
  } else {
      return array(); // Return an empty array if file doesn't exist
  }
}


function searchJSONFile($filename) {
  $existingData = readJSONFromFile($filename);

  if ($existingData) {
    foreach ($existingData as $ed) {
      if ($key == array_key_first($ed)) {
        // if in here then we found a matching course

      } 
    }
  } else {
    echo "File is empty.";
  }
}


function writeJson($data) {
  $existingData = readJSONFromFile("array.json");

  $isPresent = false;


  $key = array_key_first($data);

  if($existingData)  {
    foreach ($existingData as $ed) {
      if ($key == array_key_first($ed)) {
        $isPresent = true;
      } 
    }
  }


  array_push($existingData, $data);

  if ($isPresent) {
    echo "<p>Not adding key, already in there</p>";
  } else {
      
    $jsonData = json_encode($existingData);
    file_put_contents("array.json", $jsonData);
  }

}

print "<p>Hi, ! This is new important data for your web page.</p> ";

// get the q parameter from URL
if (array_key_exists('instructor', $_GET)) {
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
  
    $courses = readJSONFromFile("array.json");

    echo "<div class=\"container\"><table class=\"pure-table pure-table-bordered\"> 
      <thead><tr> 
        <th> Name </th> 
        <th> Title </th>  
        <th> Publisher </th> 
        <th> Edition </th> 
        <th> Publish Date </th> 
        <th> Title </th>  
        <th> Publisher </th> 
        <th> Edition </th> 
        <th> Publish Date </th> 
      </tr></thead>";
    foreach ($courses as $course) {
      $name = array_key_first($course);
      
      echo "<tr>  <td> $name </td>";
      echo "</tr>";
    }
  
  
    echo "</table></div>";
  


}
?>