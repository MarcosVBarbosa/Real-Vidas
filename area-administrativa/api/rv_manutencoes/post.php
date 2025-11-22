    <?php
        try {
            if (isset($json['data']) && isset($json['nome']) && isset($json['fkVTR']) && isset($json['kmAtual']) && isset($json['kmLimite']) && isset($json['realizado']) && isset($json['valor']) && isset($json['valorPeca']) && isset($json['nfServico']) && isset($json['nfPeca']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_manutencoes (data, nome, fkVTR, kmAtual, kmLimite, realizado, valor, valorPeca, nfServico, nfPeca, ativo) VALUES (:data, :nome, :fkVTR, :kmAtual, :kmLimite, :realizado, :valor, :valorPeca, :nfServico, :nfPeca, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':data', trim($json['data']) === '' ? null : trim($json['data']));
            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':fkVTR', trim($json['fkVTR']) === '' ? null : trim($json['fkVTR']));
            $stmt->bindValue(':kmAtual', trim($json['kmAtual']) === '' ? null : trim($json['kmAtual']));
            $stmt->bindValue(':kmLimite', trim($json['kmLimite']) === '' ? null : trim($json['kmLimite']));
            $stmt->bindValue(':realizado', trim($json['realizado']) === '' ? null : trim($json['realizado']));
            $stmt->bindValue(':valor', trim($json['valor']) === '' ? null : trim($json['valor']));
            $stmt->bindValue(':valorPeca', trim($json['valorPeca']) === '' ? null : trim($json['valorPeca']));
            $stmt->bindValue(':nfServico', trim($json['nfServico']) === '' ? null : trim($json['nfServico']));
            $stmt->bindValue(':nfPeca', trim($json['nfPeca']) === '' ? null : trim($json['nfPeca']));
            $stmt->bindValue(':ativo', trim($json['ativo']) === '' ? null : trim($json['ativo']));
            

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