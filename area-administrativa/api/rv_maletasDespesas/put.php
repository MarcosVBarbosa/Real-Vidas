    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_maletasDespesas SET fkMaleta = :fkMaleta, fkDespesa = :fkDespesa, qtde = :qtde, dataValidade = :dataValidade, lote = :lote WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkMaleta', isset($json['fkMaleta']) && trim($json['fkMaleta']) !== '' ? trim($json['fkMaleta']) : null);
            $stmt->bindValue(':fkDespesa', isset($json['fkDespesa']) && trim($json['fkDespesa']) !== '' ? trim($json['fkDespesa']) : null);
            $stmt->bindValue(':qtde', isset($json['qtde']) && trim($json['qtde']) !== '' ? trim($json['qtde']) : null);
            $stmt->bindValue(':dataValidade', isset($json['dataValidade']) && trim($json['dataValidade']) !== '' ? trim($json['dataValidade']) : null);
            $stmt->bindValue(':lote', isset($json['lote']) && trim($json['lote']) !== '' ? trim($json['lote']) : null);
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