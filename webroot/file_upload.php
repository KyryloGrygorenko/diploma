
<?php

$file_route_arr=[];
if (!empty($_FILES['document'])) {
    $doc = $_FILES['document'];
    if (!$doc['error']) {
        move_uploaded_file($doc['tmp_name'],'img/' .$doc['name']);
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
<form method="post" enctype="multipart/form-data">
    Загрузить фото<input type="file" name="document" >
    <button>Сохранить</button>
</form>

</body>
</html>



