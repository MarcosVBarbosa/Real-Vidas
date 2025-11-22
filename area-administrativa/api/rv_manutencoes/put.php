    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_manutencoes SET data = :data, nome = :nome, fkVTR = :fkVTR, kmAtual = :kmAtual, kmLimite = :kmLimite, realizado = :realizado, valor = :valor, valorPeca = :valorPeca, nfServico = :nfServico, nfPeca = :nfPeca, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':data', isset($json['data']) && trim($json['data']) !== '' ? trim($json['data']) : null);
            $stmt->bindValue(':nome', isset($json['nome']) && trim($json['nome']) !== '' ? trim($json['nome']) : null);
            $stmt->bindValue(':fkVTR', isset($json['fkVTR']) && trim($json['fkVTR']) !== '' ? trim($json['fkVTR']) : null);
            $stmt->bindValue(':kmAtual', isset($json['kmAtual']) && trim($json['kmAtual']) !== '' ? trim($json['kmAtual']) : null);
            $stmt->bindValue(':kmLimite', isset($json['kmLimite']) && trim($json['kmLimite']) !== '' ? trim($json['kmLimite']) : null);
            $stmt->bindValue(':realizado', isset($json['realizado']) && trim($json['realizado']) !== '' ? trim($json['realizado']) : null);
            $stmt->bindValue(':valor', isset($json['valor']) && trim($json['valor']) !== '' ? trim($json['valor']) : null);
            $stmt->bindValue(':valorPeca', isset($json['valorPeca']) && trim($json['valorPeca']) !== '' ? trim($json['valorPeca']) : null);
            $stmt->bindValue(':nfServico', isset($json['nfServico']) && trim($json['nfServico']) !== '' ? trim($json['nfServico']) : null);
            $stmt->bindValue(':nfPeca', isset($json['nfPeca']) && trim($json['nfPeca']) !== '' ? trim($json['nfPeca']) : null);
            $stmt->bindValue(':ativo', isset($json['ativo']) && trim($json['ativo']) !== '' ? trim($json['ativo']) : null);
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