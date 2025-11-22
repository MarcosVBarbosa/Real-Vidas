<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT * FROM rv_colaboradoresServico WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else if (isset($_GET['fkColaborador'])) {
        $fkColaborador = trim($_GET['fkColaborador']);
        $sql = "SELECT * FROM rv_colaboradoresServico WHERE fkColaborador = :fkColaborador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fkColaborador', $fkColaborador, PDO::PARAM_INT);
    } else {
        // $sql = "SELECT * FROM rv_colaboradoresServico";
        // $stmt = $conn->prepare($sql);
        echo json_encode(['status' => 'success', 'result' => 'else']);
        exit;
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
