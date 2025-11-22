    <?php
        try {
            if (isset($json['fkAdministrador']) && isset($json['fkCliente']) && isset($json['codigo'])) {

                $sql = "INSERT INTO rv_recuperaSenha (fkAdministrador, fkCliente, codigo) VALUES (:fkAdministrador, :fkCliente, :codigo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkAdministrador', trim($json['fkAdministrador']) === '' ? null : trim($json['fkAdministrador']));
            $stmt->bindValue(':fkCliente', trim($json['fkCliente']) === '' ? null : trim($json['fkCliente']));
            $stmt->bindValue(':codigo', trim($json['codigo']) === '' ? null : trim($json['codigo']));
            

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