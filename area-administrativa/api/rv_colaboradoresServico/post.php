    <?php
    try {
        if (isset($json['fkColaborador']) && isset($json['fkServico']) && isset($json['valorHora'])) {

            $sql = "INSERT INTO rv_colaboradoresServico (fkColaborador, fkServico, valorHora,fkTipoServico) VALUES (:fkColaborador, :fkServico, :valorHora,:fkTipoServico)";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':fkColaborador', trim($json['fkColaborador']) === '' ? null : trim($json['fkColaborador']));
            $stmt->bindValue(':fkServico', trim($json['fkServico']) === '' ? null : trim($json['fkServico']));
            $stmt->bindValue(':valorHora', trim($json['valorHora']) === '' ? null : trim($json['valorHora']));
            $stmt->bindValue(':fkTipoServico', $json['fkTipoServico'], PDO::PARAM_INT);


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
