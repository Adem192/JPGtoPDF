<?php
  require('fpdf/fpdf.php');

  if(isset($_POST["submit"]) && isset($_FILES['imagefile'])) {
    $photo = array();
    $pdf = new FPDF();
    foreach ($_FILES['imagefile']['name'] as $f => $name) {
      $image = scaleImageFileToBlob($_FILES['imagefile']['tmp_name'][$f]);

      if ($image == '') {
          echo 'Image type not supported';
      } else {
        $uploadpath = "C:/wamp64/www/adems_projects/JPGtoPDF/tmp/";
        $image1 = $uploadpath.basename($_FILES['imagefile']['name'][$f]);
        move_uploaded_file($_FILES['imagefile']['tmp_name'][$f], $image1);
        $pdf-> AddPage();
        $pdf-> Image($image1,10,10,190,280);
        unlink($image1);
        // array_push($photo,"<img src='data:image/jpeg;base64," . base64_encode( $image )."' style='display: block; width: 100%; height: auto;'>");
        // $image_type = $_FILES['imagefile']['type'];
        // $image = addslashes($image);
      }
    }
    $pdf-> Output();
  }

  function scaleImageFileToBlob($file) {

    $source_pic = $file;
    $max_width = 200;
    $max_height = 200;

    list($width, $height, $image_type) = getimagesize($file);

    switch ($image_type)
    {
        case 1: $src = imagecreatefromgif($file); break;
        case 2: $src = imagecreatefromjpeg($file);  break;
        case 3: $src = imagecreatefrompng($file); break;
        default: return '';  break;
    }

    $x_ratio = $max_width / $width;
    $y_ratio = $max_height / $height;

    if( ($width <= $max_width) && ($height <= $max_height) ){
        $tn_width = $width;
        $tn_height = $height;
        }elseif (($x_ratio * $height) < $max_height){
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_width;
        }else{
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_height;
    }

    $tmp = imagecreatetruecolor($tn_width,$tn_height);

    imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);

    ob_start();

    switch ($image_type)
    {
        case 1: imagegif($tmp); break;
        case 2: imagejpeg($tmp, NULL, 100);  break; // best quality
        case 3: imagepng($tmp, NULL, 0); break; // no compression
        default: echo ''; break;
    }

    $final_image = ob_get_contents();

    ob_end_clean();

    return $final_image;
  }

?>

<html>
  <head>
    <link rel="stylesheet" href="styles.css">
  </head>
  <title>JPG to PDF Converter</title>
  <div class="banner">
    <h1>JPG to PDF Converter</h1>
  </div>
  <div class="main_content">
    <div class="intro_container">
      <h4 class="intro">
        This is an image to PDF converter using the FPDF library. It supports JPEG, PNG and GIF image formats.<br/>
        <br/>You can select multiple images to upload; they will appear as one image per page.<br/>
        <br/>To use this: Click "Upload", choose your images (remember to hold Ctrl to select multiple), click "Open" on the dialog box to finish uploading, then click "Submit".<br/>
        A new tab will open in your browser, displaying the PDF document.<br/>
      </h4>
    </div>
    <form class="upload_container" action="JPGtoPDF.php" method="post" enctype="multipart/form-data" target="_blank">
      <input type="hidden" name="upload" value="1"/>
      <label class="upload">
          <input class="upload" type="file" name="imagefile[]" multiple/>
          Upload
      </label>
      <input class="upload" type="submit" name="submit" value="Submit"/>
    </form>
  </div>
</html>
