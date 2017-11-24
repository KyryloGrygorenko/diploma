<?php
$status = 'Success';

try {
    $dbh = new PDO('mysql: host=localhost; dbname=my_db', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sth = $dbh->prepare('INSERT INTO tbl_message (name, email, phone, message) VALUES (:name, :email, :phone, :message)');
    $sth->execute($_POST);
} catch (PDOException $e) {
    $status = 'Fail: ' . $e->getMessage();
}

echo $status;
