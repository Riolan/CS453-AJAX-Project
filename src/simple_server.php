<?php


function createStudent($name, ...$books) {
  // find the course in file
  $student = array();

  $student[$name]["books"] = array();
  $student[$name]["flags"] = array();
  
  foreach ($books as $book) {
    array_push($student[$name]["books"], $book); 
  }
  

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
    array_push($course[$name]["books"], $book);    
  }
  

  return $course;
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


function compareBooks($book1, $book2) {
  $differences = array();

  // Compare book properties
  if ($book1['title'] !== $book2['title']) {
      $differences[] = 'title';
  }
  if ($book1['bpub'] !== $book2['bpub']) {
      $differences[] = 'bpub';
  }
  if ($book1['bed'] !== $book2['bed']) {
      $differences[] = 'bed';
  }
  if ($book1['dop'] !== $book2['dop']) {
      $differences[] = 'dop';
  }

  return $differences;
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
      
    $jsonData = json_encode($existingData, JSON_PRETTY_PRINT);
    file_put_contents("array.json", $jsonData);
  }

}


// get the instructor parameter from URL
if (array_key_exists('instructor', $_GET)) {
  $isInstructor = $_GET['instructor'];
  // handle instructor
  if ($isInstructor === "true") {
    // an instructor can do what?
    $course = $_GET['course'];
    $title = $_GET['title'];
    $bpub = $_GET['pub'];
    $bed = $_GET['ed'];
    $dop = $_GET['dop'];

    $books = array();

    $book = createBook($title, $bpub, $bed, $dop);

    // making assumption if title2 exists the rest does
    // should actually validate in prod
    if (array_key_exists('title2', $_GET)) {

      echo "tesing";
      $title2 = $_GET['title2'];
      $bpub2 = $_GET['pub2'];
      $bed2 = $_GET['ed2'];
      $dop2 = $_GET['dop2'];
      $book2 = createBook($title2, $bpub2, $bed2, $dop2);

      //array_push($books, $book, $book2);
      $write = createCourse($course, $book, $book2);

    } else {
      //array_push($books, $book);
      $write = createCourse($course, $book);

    }


    // search course
    // if found course write to it
    //$write = createCourse($course, $books);

    // if no course found make new one
    writeJson($write);

  } else {
    // student can do what
    $courses = readJSONFromFile("array.json");
    // See which course the student is being added into
    $key = $_GET['course1'];
    if ($courses) {
      foreach ($courses as &$course) {
        if (isset($course[$key])) {

          $name = $_GET['name1'];

          // Check to see if name is alr in the array.
          $isFound = false;
          foreach ($course[$key]['students'] as $student_check) {
            if ($name == key($student_check)) {
              echo "<div class='container'>";
              echo "Student's name is already present.";
              echo "</div>";
              $isFound = true;
            }
          }
          // if name is not in the array add the student
          if (!$isFound) {
            $title = $_GET['title1'];
            $bpub = $_GET['pub1'];
            $bed = $_GET['ed1'];
            $dop = $_GET['dop1'];
  
            $book = createBook($title, $bpub, $bed, $dop);

            // making assumption if title2 exists the rest does
            // should actually validate in prod
            if (array_key_exists('title3', $_GET)) {
              $title3 = $_GET['title3'];
              $bpub3 = $_GET['pub3'];
              $bed3 = $_GET['ed3'];
              $dop3 = $_GET['dop3'];
              $book2 = createBook($title3, $bpub3, $bed3, $dop3);

              $student = createStudent($name, $book, $book2);

              array_push($student[$name]["flags"], compareBooks($course[$key]["books"][0],
                $book));
              array_push($student[$name]["flags"], compareBooks($course[$key]["books"][1],
                $book2));
            } else {
              $student = createStudent($name, $book);
              array_push($student[$name]["flags"], compareBooks($course[$key]["books"][0],
                $book));
            }

         
            // Initialize the students array if it's not already initialized
            if (!isset($course[$key]['students'])) {
                $course[$key]['students'] = array();
            }
  
            echo "<div class='container'>";
            $count_books = 0;          
            foreach ($student[$name]["flags"] as $flag) {
              $pl = $course[$key]['books'][$count_books]["title"];
              echo "<p>Student has incorrect book data for: </br><b><pre>  $pl</pre></b></p><ul>";
              foreach ($flag as $incorrectData) {
                echo "<li>The $incorrectData is incorrect.</li>";
              }
              $count_books += 1;
              echo "</ul>";
            }
            echo "</div>";



            array_push($course[$key]['students'], $student);
                  
            $jsonData = json_encode($courses, JSON_PRETTY_PRINT);
            file_put_contents("array.json", $jsonData);
            date_default_timezone_set("America/Chicago");

            echo "Added student at " . date("h:i:sa");

            

          }
        } 
      }
    }
    
  }


} else { // instructor variable not found

    if (array_key_exists('instructor', $_GET)) {
      
    }
    // Used for displaying explixitly from the file
    $courses = readJSONFromFile("array.json");

    echo "<div class=\"container\"><table class=\"pure-table pure-table-bordered\"> 
      <thead><tr> 
        <th> Course Name </th> 
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
      $books = $course[$name]["books"];

      echo "<tr>  <td> $name </td>";

      foreach ($books as $book) {
        
        $title = $book['title'];
        echo "<td> $title </td>";
        
        $pub = $book['bpub'];
        echo "<td> $pub </td>";

        $bed = $book['bed'];
        echo "<td> $bed </td>";

        $dop = $book['dop'];
        echo "<td> $dop </td>";

      }

      echo "</tr>";

      echo "<tr><td colspan=9 style='background-color:#DCDCDC'>Students: ";

      foreach ($course[$name]['students'] as $student) {

        echo (key($student));
        echo ", ";
      }


      echo "</td></tr>";

      
    }
  
  
    echo "</table></div>";
  


}
?>