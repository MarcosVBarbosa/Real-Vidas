    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_faturamentoOS SET fkFaturamento = :fkFaturamento, fkOrdemServico = :fkOrdemServico WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkFaturamento', isset($json['fkFaturamento']) && trim($json['fkFaturamento']) !== '' ? trim($json['fkFaturamento']) : null);
            $stmt->bindValue(':fkOrdemServico', isset($json['fkOrdemServico']) && trim($json['fkOrdemServico']) !== '' ? trim($json['fkOrdemServico']) : null);
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