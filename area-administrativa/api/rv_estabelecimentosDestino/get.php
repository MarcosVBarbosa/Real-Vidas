<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT * FROM rv_estabelecimentosDestino WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else if (
        isset($_GET['fkEstabelecimento'], $_GET['fkDestino'], $_GET['fkServico']) &&
        is_numeric($_GET['fkEstabelecimento']) && is_numeric($_GET['fkDestino']) && is_numeric($_GET['fkServico'])
    ) {
        $fkEstabelecimento = trim($_GET['fkEstabelecimento']);
        $fkDestino = trim($_GET['fkDestino']);
        $fkServico = trim($_GET['fkServico']);
        $sql = "SELECT * FROM rv_estabelecimentosDestino WHERE fkEstabelecimento = :fkEstabelecimento AND fkDestino = :fkDestino AND fkServico = :fkServico";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fkDestino', $fkDestino, PDO::PARAM_INT);
        $stmt->bindParam(':fkEstabelecimento', $fkEstabelecimento, PDO::PARAM_INT);
        $stmt->bindParam(':fkServico', $fkServico, PDO::PARAM_INT);
    } else if (isset($_GET['fkEstabelecimento']) && is_numeric($_GET['fkEstabelecimento'])) {
        $fkEstabelecimento = trim($_GET['fkEstabelecimento']);
        $sql = "SELECT * FROM rv_estabelecimentosDestino WHERE fkEstabelecimento = :fkEstabelecimento";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fkEstabelecimento', $fkEstabelecimento, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM rv_estabelecimentosDestino";
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
