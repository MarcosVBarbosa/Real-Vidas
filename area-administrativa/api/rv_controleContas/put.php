    <?php
    try {
        if (isset($json['pkId']) && is_numeric($json['pkId'])) {

            $sql = "UPDATE rv_controleContas 
                    SET fkContaFixa = :fkContaFixa, 
                        dataRecebido = :dataRecebido,
                        dataVenc = :dataVenc,
                        dataPrevistaPag = :dataPrevistaPag,
                        dataPag = :dataPag,
                        debAutomatico = :debAutomatico,
                        valorConta = :valorConta
                    WHERE pkId = :pkId";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':fkContaFixa', $json['fkContaFixa'], PDO::PARAM_INT);
            $stmt->bindValue(':dataRecebido', trim($json['dataRecebido']) === '' ? null : trim($json['dataRecebido']));
            $stmt->bindValue(':dataVenc', trim($json['dataVenc']) === '' ? null : trim($json['dataVenc']));
            $stmt->bindValue(':dataPrevistaPag', trim($json['dataPrevistaPag']) === '' ? null : trim($json['dataPrevistaPag']));
            $stmt->bindValue(':dataPag', trim($json['dataPag']) === '' ? null : trim($json['dataPag']));
            $stmt->bindValue(':debAutomatico', trim($json['debAutomatico']) === '' ? null : trim($json['debAutomatico']));
            $stmt->bindValue(':valorConta', trim($json['valorConta']) === '' ? null : trim($json['valorConta']));
            $stmt->bindValue(':pkId', $json['pkId'], PDO::PARAM_INT);


            $stmt->execute();

            http_response_code(200);
            $result = [
                'status' => 'success',
                'result' => 'Registro atualizado com sucesso!'
            ];
        } else {
            http_response_code(400);
            $result = [
                'status' => 'fail',
                'result' => 'Chave primária ausente ou inválida!'
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
