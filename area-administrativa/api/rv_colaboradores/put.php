    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_colaboradores SET fkTipoColaborador = :fkTipoColaborador, nome = :nome, pix = :pix, dataNascimento = :dataNascimento, rg = :rg, cpf = :cpf, nrDoc = :nrDoc, tipoDoc = :tipoDoc, ufDoc = :ufDoc, validadeDoc = :validadeDoc, banco = :banco, tipoConta = :tipoConta, nrAgencia = :nrAgencia, nrConta = :nrConta, endereco = :endereco, complemento = :complemento, bairro = :bairro, cidade = :cidade, cep = :cep, estado = :estado, status = :status, assinatura = :assinatura, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkTipoColaborador', isset($json['fkTipoColaborador']) && trim($json['fkTipoColaborador']) !== '' ? trim($json['fkTipoColaborador']) : null);
            $stmt->bindValue(':nome', isset($json['nome']) && trim($json['nome']) !== '' ? trim($json['nome']) : null);
            $stmt->bindValue(':pix', isset($json['pix']) && trim($json['pix']) !== '' ? trim($json['pix']) : null);
            $stmt->bindValue(':dataNascimento', isset($json['dataNascimento']) && trim($json['dataNascimento']) !== '' ? trim($json['dataNascimento']) : null);
            $stmt->bindValue(':rg', isset($json['rg']) && trim($json['rg']) !== '' ? trim($json['rg']) : null);
            $stmt->bindValue(':cpf', isset($json['cpf']) && trim($json['cpf']) !== '' ? trim($json['cpf']) : null);
            $stmt->bindValue(':nrDoc', isset($json['nrDoc']) && trim($json['nrDoc']) !== '' ? trim($json['nrDoc']) : null);
            $stmt->bindValue(':tipoDoc', isset($json['tipoDoc']) && trim($json['tipoDoc']) !== '' ? trim($json['tipoDoc']) : null);
            $stmt->bindValue(':ufDoc', isset($json['ufDoc']) && trim($json['ufDoc']) !== '' ? trim($json['ufDoc']) : null);
            $stmt->bindValue(':validadeDoc', isset($json['validadeDoc']) && trim($json['validadeDoc']) !== '' ? trim($json['validadeDoc']) : null);
            $stmt->bindValue(':banco', isset($json['banco']) && trim($json['banco']) !== '' ? trim($json['banco']) : null);
            $stmt->bindValue(':tipoConta', isset($json['tipoConta']) && trim($json['tipoConta']) !== '' ? trim($json['tipoConta']) : null);
            $stmt->bindValue(':nrAgencia', isset($json['nrAgencia']) && trim($json['nrAgencia']) !== '' ? trim($json['nrAgencia']) : null);
            $stmt->bindValue(':nrConta', isset($json['nrConta']) && trim($json['nrConta']) !== '' ? trim($json['nrConta']) : null);
            $stmt->bindValue(':endereco', isset($json['endereco']) && trim($json['endereco']) !== '' ? trim($json['endereco']) : null);
            $stmt->bindValue(':complemento', isset($json['complemento']) && trim($json['complemento']) !== '' ? trim($json['complemento']) : null);
            $stmt->bindValue(':bairro', isset($json['bairro']) && trim($json['bairro']) !== '' ? trim($json['bairro']) : null);
            $stmt->bindValue(':cidade', isset($json['cidade']) && trim($json['cidade']) !== '' ? trim($json['cidade']) : null);
            $stmt->bindValue(':cep', isset($json['cep']) && trim($json['cep']) !== '' ? trim($json['cep']) : null);
            $stmt->bindValue(':estado', isset($json['estado']) && trim($json['estado']) !== '' ? trim($json['estado']) : null);
            $stmt->bindValue(':status', isset($json['status']) && trim($json['status']) !== '' ? trim($json['status']) : null);
            $stmt->bindValue(':assinatura', isset($json['assinatura']) && trim($json['assinatura']) !== '' ? trim($json['assinatura']) : null);
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