<?php
try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = trim($_GET['id']);
        $sql = "SELECT LPAD(rv_controleContas.pkId,5,0) pkIdF,rv_contasFixa.*, rv_controleContas.*,rv_fornecedores.* FROM rv_controleContas JOIN rv_contasFixa ON (rv_controleContas.fkContaFixa = rv_contasFixa.pkId) LEFT JOIN rv_fornecedores ON (rv_fornecedores.pkId = rv_contasFixa.fkFornecedor) WHERE rv_controleContas.pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        $sql = "SELECT LPAD(rv_controleContas.pkId,5,0) pkIdF,rv_contasFixa.*, rv_controleContas.*,rv_fornecedores.*,rv_controleContas.pkId FROM rv_controleContas JOIN rv_contasFixa ON (rv_controleContas.fkContaFixa = rv_contasFixa.pkId) LEFT JOIN rv_fornecedores ON (rv_fornecedores.pkId = rv_contasFixa.fkFornecedor)";
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
