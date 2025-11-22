    <?php
        try {
            if (isset($json['fkTipoColaborador']) && isset($json['nome']) && isset($json['pix']) && isset($json['dataNascimento']) && isset($json['rg']) && isset($json['cpf']) && isset($json['nrDoc']) && isset($json['tipoDoc']) && isset($json['ufDoc']) && isset($json['validadeDoc']) && isset($json['banco']) && isset($json['tipoConta']) && isset($json['nrAgencia']) && isset($json['nrConta']) && isset($json['endereco']) && isset($json['complemento']) && isset($json['bairro']) && isset($json['cidade']) && isset($json['cep']) && isset($json['estado']) && isset($json['status']) && isset($json['assinatura']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_colaboradores (fkTipoColaborador, nome, pix, dataNascimento, rg, cpf, nrDoc, tipoDoc, ufDoc, validadeDoc, banco, tipoConta, nrAgencia, nrConta, endereco, complemento, bairro, cidade, cep, estado, status, assinatura, ativo) VALUES (:fkTipoColaborador, :nome, :pix, :dataNascimento, :rg, :cpf, :nrDoc, :tipoDoc, :ufDoc, :validadeDoc, :banco, :tipoConta, :nrAgencia, :nrConta, :endereco, :complemento, :bairro, :cidade, :cep, :estado, :status, :assinatura, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkTipoColaborador', trim($json['fkTipoColaborador']) === '' ? null : trim($json['fkTipoColaborador']));
            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':pix', trim($json['pix']) === '' ? null : trim($json['pix']));
            $stmt->bindValue(':dataNascimento', trim($json['dataNascimento']) === '' ? null : trim($json['dataNascimento']));
            $stmt->bindValue(':rg', trim($json['rg']) === '' ? null : trim($json['rg']));
            $stmt->bindValue(':cpf', trim($json['cpf']) === '' ? null : trim($json['cpf']));
            $stmt->bindValue(':nrDoc', trim($json['nrDoc']) === '' ? null : trim($json['nrDoc']));
            $stmt->bindValue(':tipoDoc', trim($json['tipoDoc']) === '' ? null : trim($json['tipoDoc']));
            $stmt->bindValue(':ufDoc', trim($json['ufDoc']) === '' ? null : trim($json['ufDoc']));
            $stmt->bindValue(':validadeDoc', trim($json['validadeDoc']) === '' ? null : trim($json['validadeDoc']));
            $stmt->bindValue(':banco', trim($json['banco']) === '' ? null : trim($json['banco']));
            $stmt->bindValue(':tipoConta', trim($json['tipoConta']) === '' ? null : trim($json['tipoConta']));
            $stmt->bindValue(':nrAgencia', trim($json['nrAgencia']) === '' ? null : trim($json['nrAgencia']));
            $stmt->bindValue(':nrConta', trim($json['nrConta']) === '' ? null : trim($json['nrConta']));
            $stmt->bindValue(':endereco', trim($json['endereco']) === '' ? null : trim($json['endereco']));
            $stmt->bindValue(':complemento', trim($json['complemento']) === '' ? null : trim($json['complemento']));
            $stmt->bindValue(':bairro', trim($json['bairro']) === '' ? null : trim($json['bairro']));
            $stmt->bindValue(':cidade', trim($json['cidade']) === '' ? null : trim($json['cidade']));
            $stmt->bindValue(':cep', trim($json['cep']) === '' ? null : trim($json['cep']));
            $stmt->bindValue(':estado', trim($json['estado']) === '' ? null : trim($json['estado']));
            $stmt->bindValue(':status', trim($json['status']) === '' ? null : trim($json['status']));
            $stmt->bindValue(':assinatura', trim($json['assinatura']) === '' ? null : trim($json['assinatura']));
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