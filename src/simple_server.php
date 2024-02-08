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
      $differences[] = 'Title';
  }
  if ($book1['bpub'] !== $book2['bpub']) {
      $differences[] = 'Book Publisher';
  }
  if ($book1['bed'] !== $book2['bed']) {
      $differences[] = 'Book Edition';
  }
  if ($book1['dop'] !== $book2['dop']) {
      $differences[] = 'Date of Printing';
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
  } else {
    $existingData = array();
  }


  array_push($existingData, $data);

  if ($isPresent) {
    echo "<p>Not adding key, already in there</p>";
  } else {
      
    $jsonData = json_encode($existingData, JSON_PRETTY_PRINT);
    file_put_contents("array.json", $jsonData);
  }

}

function updateJson($courseKey, $data) {
  $existingData = readJSONFromFile("array.json");

  $isPresent = false;
  if($existingData)  {
    foreach ($existingData as &$ed) {
      if ($courseKey == array_key_first($ed)) {
        // temp
        $students = $ed[$courseKey]["students"];
        $ed = $data;
        $ed[array_key_first($ed)]["students"] = $students;

        $jsonData = json_encode($existingData, JSON_PRETTY_PRINT);
        file_put_contents("array.json", $jsonData);
      } 
    }
  } else {
    return;
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
    echo "Created new course <b>$course</b>";

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
              array_push($student[$name]["flags"], @compareBooks($course[$key]["books"][1],
                $book2));
            } else {
              $student = createStudent($name, $book);
              array_push($student[$name]["flags"], compareBooks($course[$key]["books"][0],
                $book));
                array_push($student[$name]["flags"], array());
                
            }

         
            // Initialize the students array if it's not already initialized
            if (!isset($course[$key]['students'])) {
                $course[$key]['students'] = array();
            }
  
            echo "<div class='container'>";
            $count_books = 0;          
            foreach ($student[$name]["flags"] as $flag) {
              // do not print anything if they have no flags
              if (count($flag) === 0) {
                continue;
              }
              
              $pl = $course[$key]['books'][$count_books]["title"];

              if (count($course[$key]['books']) == 1) {
                  echo "<p>Student has incorrect book data for: </br><b><pre>  $pl</pre></b></p><ul>";
                foreach ($flag as $incorrectData) {
                  echo "<li>The $incorrectData is incorrect.</li>";
                }
                $count_books += 1;
                echo "</ul>";
                echo "<p>Student has incorrect book data for: </br><b><pre>  $pl</pre></b></p><ul>";
                foreach ($student[$name]["flags"][1] as $incorrectData) {
                  echo "<li>The $incorrectData is incorrect.</li>";
                }
                $count_books += 1;
                echo "</ul>";
                break;
              } 

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

  // update functionality
    if (array_key_exists('update', $_GET)) {      
      $isInstructorUpdate = $_GET["update"];
      if ($isInstructorUpdate === "course") {
        

          $oldcourse = $_GET['oldcourse'];

          $course = $_GET['course4'];
          $title = $_GET['title4'];
          $bpub = $_GET['pub4'];
          $bed = $_GET['ed4'];
          $dop = $_GET['dop4'];
      
          
          echo $course;



          $books = array();
      
          $book = createBook($title, $bpub, $bed, $dop);
      
          // making assumption if title2 exists the rest does
          // should actually validate in prod
          if (array_key_exists('title5', $_GET)) {
      
            $title2 = $_GET['title5'];
            $bpub2 = $_GET['pub5'];
            $bed2 = $_GET['ed5'];
            $dop2 = $_GET['dop5'];
            $book2 = createBook($title2, $bpub2, $bed2, $dop2);
      
            //array_push($books, $book, $book2);
            $write = createCourse($course, $book, $book2);
      
          } else {
            //array_push($books, $book);
            $write = createCourse($course, $book);
      
          }
      
          // update course
          updateJson($oldcourse, $write);
          echo "<p>Updated Course <b>$oldcourse</b> to <b>$course</b> </p>";
      


      } elseif ($isInstructorUpdate === "student") {
        // student can do what
        $courses = readJSONFromFile("array.json");
        // See which course the student is being added into
        $key = $_GET['course6'];
        if ($courses) {
          foreach ($courses as &$course) {
            if (isset($course[$key])) {

              $name = $_GET['name6'];
              $newname6 = $_GET['newname6'];
              // Check to see if name is alr in the array.
              $isFound = false;
              foreach ($course[$key]['students'] as $student_check) {
                if ($name == key($student_check)) {
                  $isFound = true;
                }
              }
              // if name is in the array update the array
              if ($isFound) {
                $title = $_GET['title6'];
                $bpub = $_GET['pub6'];
                $bed = $_GET['ed6'];
                $dop = $_GET['dop6'];

                $book = createBook($title, $bpub, $bed, $dop);

                // making assumption if title2 exists the rest does
                // should actually validate in prod
                if (array_key_exists('title7', $_GET)) {
                  $title3 = $_GET['title7'];
                  $bpub3 = $_GET['pub7'];
                  $bed3 = $_GET['ed7'];
                  $dop3 = $_GET['dop7'];
                  $book2 = createBook($title3, $bpub3, $bed3, $dop3);

                  $student = createStudent($newname6, $book, $book2);

                  $student[$newname6]["flags"][0] = compareBooks($course[$key]["books"][0],
                    $book);
                  $student[$newname6]["flags"][1] = compareBooks($course[$key]["books"][1],
                    $book2);
                } else {
                  $student = createStudent($newname6, $book);
                  $student[$newname6]["flags"][0] = compareBooks($course[$key]["books"][0],
                    $book);
                    array_push($student[$newname6]["flags"], array());
                    //$student[$newname6]["flags"][1] = array();
                }

              
                // Initialize the students array if it's not already initialized
                if (!isset($course[$key]['students'])) {
                    $course[$key]['students'] = array();
                }

                echo "<div class='container'>";
                $count_books = 0;          
                foreach ($student[$newname6]["flags"] as $flag) {
                  // do not print anything if they have no flags
                  if (count($flag) === 0) {
                    continue;
                  }
                  $pl = $course[$key]['books'][$count_books]["title"];
                  echo "<p>Student has incorrect book data for: </br><b><pre>  $pl</pre></b></p><ul>";
                  foreach ($flag as $incorrectData) {
                    echo "<li>The $incorrectData is incorrect.</li>";
                  }
                  $count_books += 1;
                  echo "</ul>";
                }
                echo "</div>";


////////////////////////////////////
                //$course[$key]['students'], $student;

                //$course[$key]['students']= $student;

                foreach ($course[$key]['students'] as &$astudent) {
                  if ($name == array_key_first($astudent)) {
                    // temp
                    $astudent = $student;
                                  
                    $jsonData = json_encode($courses, JSON_PRETTY_PRINT);
                    file_put_contents("array.json", $jsonData);
                    date_default_timezone_set("America/Chicago");
    
                    echo "Updated student at " . date("h:i:sa");
    
                  } 
                }


                

              }
            } 
          }
        }
        



      }
    
    
    } else {
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

          $student[key($student)]["flags"][0] = compareBooks($student[key($student)]["books"][0],
          $course[$name]["books"][0]);
          $student[key($student)]["flags"][1] = compareBooks($student[key($student)]["books"][1],
          $course[$name]["books"][1]);

          $flag_ = $student[key($student)]["flags"];
          //echo count($flag_[0]);
          //echo count($flag_[1]);
          if (count($flag_[0]) != 0 || count($flag_[1]) != 0) {
            echo "<span style='color: #f00000'> (X) ";
          } else {
            echo "<span style='color: #000000'> (✓) ";
          }
          echo (key($student));
          echo "</span>, ";
        }


        echo "</td></tr>";

        
      }


      echo "</table></div>";



    }
  }
  
?>