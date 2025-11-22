<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT * FROM rv_colaboradores WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        $sql = "SELECT rv_colaboradores.*,rv_tipoColaboradores.nome AS especialidade FROM rv_colaboradores LEFT JOIN rv_tipoColaboradores ON (rv_tipoColaboradores.pkId = rv_colaboradores.fkTipoColaborador)";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = getResult($stmt);

    echo json_encode(['status' => 'success', 'result' => $result]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'result' => $th->getMessage()]);
} finally {
    $conn = null;
}
