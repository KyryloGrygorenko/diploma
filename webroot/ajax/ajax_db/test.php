<?php
$msg_info = array();
$status = 'Success';
try {
    $dbh = new PDO('mysql: host=localhost; dbname=my_db', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sth = $dbh->query('SELECT * FROM tbl_message');
    $msg_info = $sth->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $status = 'fail: ' . $e->getMessage();
}
$data = array(
    'msg_info' => $msg_info,
    'status' => $status
);
echo json_encode($data);