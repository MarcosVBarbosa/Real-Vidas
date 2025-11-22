<?php
function getResult($stmt)
{
    if ($stmt->rowCount() < 1) {
        http_response_code(204);
        return null;
    } elseif ($stmt->rowCount() == 1) {
        return $stmt->fetch(PDO::FETCH_OBJ);
    } else {
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}