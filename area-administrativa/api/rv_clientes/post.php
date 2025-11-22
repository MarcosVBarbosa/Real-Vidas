    <?php
        try {
            if (isset($json['razaoSocial']) && isset($json['nomeFantasia']) && isset($json['cpfCnpj']) && isset($json['senha']) && isset($json['email']) && isset($json['telefone']) && isset($json['celular']) && isset($json['endereco']) && isset($json['complemento']) && isset($json['bairro']) && isset($json['cidade']) && isset($json['estado']) && isset($json['cep']) && isset($json['responsavel']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_clientes (razaoSocial, nomeFantasia, cpfCnpj, senha, email, telefone, celular, endereco, complemento, bairro, cidade, estado, cep, responsavel, ativo) VALUES (:razaoSocial, :nomeFantasia, :cpfCnpj, :senha, :email, :telefone, :celular, :endereco, :complemento, :bairro, :cidade, :estado, :cep, :responsavel, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':razaoSocial', trim($json['razaoSocial']) === '' ? null : trim($json['razaoSocial']));
            $stmt->bindValue(':nomeFantasia', trim($json['nomeFantasia']) === '' ? null : trim($json['nomeFantasia']));
            $stmt->bindValue(':cpfCnpj', trim($json['cpfCnpj']) === '' ? null : trim($json['cpfCnpj']));
            $stmt->bindValue(':senha', trim($json['senha']) === '' ? null : trim($json['senha']));
            $stmt->bindValue(':email', trim($json['email']) === '' ? null : trim($json['email']));
            $stmt->bindValue(':telefone', trim($json['telefone']) === '' ? null : trim($json['telefone']));
            $stmt->bindValue(':celular', trim($json['celular']) === '' ? null : trim($json['celular']));
            $stmt->bindValue(':endereco', trim($json['endereco']) === '' ? null : trim($json['endereco']));
            $stmt->bindValue(':complemento', trim($json['complemento']) === '' ? null : trim($json['complemento']));
            $stmt->bindValue(':bairro', trim($json['bairro']) === '' ? null : trim($json['bairro']));
            $stmt->bindValue(':cidade', trim($json['cidade']) === '' ? null : trim($json['cidade']));
            $stmt->bindValue(':estado', trim($json['estado']) === '' ? null : trim($json['estado']));
            $stmt->bindValue(':cep', trim($json['cep']) === '' ? null : trim($json['cep']));
            $stmt->bindValue(':responsavel', trim($json['responsavel']) === '' ? null : trim($json['responsavel']));
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