<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT * FROM rv_categoriasFornecedores WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        $sql = "SELECT (SELECT MAX(pkId) + 1 FROM rv_categoriasFornecedores) AS proximoId, rv_categoriasFornecedores.* FROM rv_categoriasFornecedores";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = getResult($stmt);

    echo json_encode(['status' => 'success', 'result' => $result]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'result' => $th->getMessage()]);
} finally {
    $conn = null;
}
