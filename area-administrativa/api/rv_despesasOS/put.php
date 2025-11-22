    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_despesasOS SET fkOrdemServico = :fkOrdemServico, fkDespesa = :fkDespesa, qtde = :qtde, valor = :valor, confirmado = :confirmado WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOrdemServico', isset($json['fkOrdemServico']) && trim($json['fkOrdemServico']) !== '' ? trim($json['fkOrdemServico']) : null);
            $stmt->bindValue(':fkDespesa', isset($json['fkDespesa']) && trim($json['fkDespesa']) !== '' ? trim($json['fkDespesa']) : null);
            $stmt->bindValue(':qtde', isset($json['qtde']) && trim($json['qtde']) !== '' ? trim($json['qtde']) : null);
            $stmt->bindValue(':valor', isset($json['valor']) && trim($json['valor']) !== '' ? trim($json['valor']) : null);
            $stmt->bindValue(':confirmado', isset($json['confirmado']) && trim($json['confirmado']) !== '' ? trim($json['confirmado']) : null);
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