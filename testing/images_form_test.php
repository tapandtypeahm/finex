<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="images_form_test_process.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file[0][]" id="file"><br>
<label for="file">Filename 2:</label>
<input type="file" name="file[0][]" id="file"><br>
<label for="file">Filename 3:</label>
<input type="file" name="file[1][]" id="file"><br>
<label for="file">Filename 4:</label>
<input type="file" name="file[1][]" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>
