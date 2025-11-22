    <?php
        try {
            if (isset($json['fkMaleta']) && isset($json['fkDespesa']) && isset($json['qtde']) && isset($json['dataValidade']) && isset($json['lote'])) {

                $sql = "INSERT INTO rv_maletasDespesas (fkMaleta, fkDespesa, qtde, dataValidade, lote) VALUES (:fkMaleta, :fkDespesa, :qtde, :dataValidade, :lote)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkMaleta', trim($json['fkMaleta']) === '' ? null : trim($json['fkMaleta']));
            $stmt->bindValue(':fkDespesa', trim($json['fkDespesa']) === '' ? null : trim($json['fkDespesa']));
            $stmt->bindValue(':qtde', trim($json['qtde']) === '' ? null : trim($json['qtde']));
            $stmt->bindValue(':dataValidade', trim($json['dataValidade']) === '' ? null : trim($json['dataValidade']));
            $stmt->bindValue(':lote', trim($json['lote']) === '' ? null : trim($json['lote']));
            

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