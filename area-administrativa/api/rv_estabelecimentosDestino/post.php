<?php
try {
    if (isset($json['fkEstabelecimento']) && isset($json['fkDestino'])) {

        $sql = "INSERT INTO rv_estabelecimentosDestino (fkEstabelecimento,fkServico, fkDestino, ida,idaVolta) VALUES (:fkEstabelecimento,:fkServico, :fkDestino, :ida,:idaVolta)";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':fkEstabelecimento', trim($json['fkEstabelecimento']) === '' ? null : trim($json['fkEstabelecimento']));
        $stmt->bindValue(':fkDestino', trim($json['fkDestino']) === '' ? null : trim($json['fkDestino']));
        $stmt->bindValue(':fkServico', trim($json['fkServico']) === '' ? null : trim($json['fkServico']));
        $stmt->bindValue(':ida', trim($json['ida']) === '' ? null : trim($json['ida']));
        $stmt->bindValue(':idaVolta', trim($json['idaVolta']) === '' ? null : trim($json['idaVolta']));

        $stmt->execute();

        http_response_code(201);
        $result = [
            'status' => 'success',
            'result' => 'Registro inserido com sucesso!'
        ];
    } else {
        http_response_code(400);
        $result = [
            'status' => 'fail',
            'result' => 'Campos obrigatórios ausentes!'
        ];
    }
} catch (Throwable $th) {
    http_response_code(500);
    $result = [
        'status' => 'fail',
        'result' => $th->getMessage()
    ];
} finally {
    $conn = null;
    echo json_encode($result);
}
