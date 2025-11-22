    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_recuperaSenha SET fkAdministrador = :fkAdministrador, fkCliente = :fkCliente, codigo = :codigo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkAdministrador', isset($json['fkAdministrador']) && trim($json['fkAdministrador']) !== '' ? trim($json['fkAdministrador']) : null);
            $stmt->bindValue(':fkCliente', isset($json['fkCliente']) && trim($json['fkCliente']) !== '' ? trim($json['fkCliente']) : null);
            $stmt->bindValue(':codigo', isset($json['codigo']) && trim($json['codigo']) !== '' ? trim($json['codigo']) : null);
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