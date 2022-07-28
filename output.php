<!DOCTYPE html>
<html>
<head>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  <script src="task.js"></script>
  <link rel="stylesheet" href="test.css">
</head>
<body>

<form id = "formId" action="upload.php" method="post" enctype="multipart/form-data">
<div class='parent flex-parent'>
    <div class='child flex-child'>
        <label class="form-label" for="customFile">Select File to upload:</label>
        <input type="file" class="form-control" name="fileToUpload"  id="fileToUpload" />  
    </div>
</div>

<div class='parent flex-parent'>
    <div class='child flex-child'> 
        <label class="form-label" for="customFile">Enter Stock Name:</label>
        <input  id = "input_box" value="" placeholder = "Enter stock name" name="input_box">
    </div>
    <div class='child flex-child'>
        <label class="form-label" for="customFile">Start_Date:</label>
        <input name = "datepicker" type="text" id="datepicker" value ="">
    </div>
    <div class='child flex-child'>
        <label class="form-label" for="customFile">End_Date:</label>
        <input  name = "datepicker2" type="text" id="datepicker2" value="">
    </div>
</div>

<div class='parent flex-parent'>
    <input disabled type ="submit" id = "submit"  value="Upload Stock File" name="submit">
</div>

</form>

</div>

</body>
</html>