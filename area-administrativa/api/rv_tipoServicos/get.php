<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT * FROM rv_tipoServicos WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else if (isset($_GET['fkServico'])) {
        $fkServico = trim($_GET['fkServico']);
        $sql = "SELECT * FROM rv_tipoServicos WHERE fkServico = :fkServico";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fkServico', $fkServico, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM rv_tipoServicos";
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
