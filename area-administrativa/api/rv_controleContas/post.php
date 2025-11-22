    <?php
    try {
        if (isset($json['fkContaFixa'], $json['dataRecebido'], $json['dataVenc'], $json['dataPrevistaPag'], $json['debAutomatico'], $json['valorConta'])) {

            $sql = "INSERT INTO rv_controleContas (fkContaFixa, dataRecebido,dataVenc,dataPag,dataPrevistaPag,debAutomatico,valorConta) 
                    VALUES (:fkContaFixa, :dataRecebido,:dataVenc,:dataPag,:dataPrevistaPag,:debAutomatico,:valorConta) ";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':fkContaFixa', $json['fkContaFixa'], PDO::PARAM_INT);
            $stmt->bindValue(':dataRecebido', trim($json['dataRecebido']) === '' ? null : trim($json['dataRecebido']));
            $stmt->bindValue(':dataVenc', trim($json['dataVenc']) === '' ? null : trim($json['dataVenc']));
            $stmt->bindValue(':dataPrevistaPag', trim($json['dataPrevistaPag']) === '' ? null : trim($json['dataPrevistaPag']));
            $stmt->bindValue(':dataPag', trim($json['dataPag']) === '' ? null : trim($json['dataPag']));
            $stmt->bindValue(':debAutomatico', trim($json['debAutomatico']) === '' ? null : trim($json['debAutomatico']));
            $stmt->bindValue(':valorConta', trim($json['valorConta']) === '' ? null : trim($json['valorConta']));

            $stmt->execute();

            http_response_code(200);
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
