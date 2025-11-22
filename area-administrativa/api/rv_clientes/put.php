    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_clientes SET razaoSocial = :razaoSocial, nomeFantasia = :nomeFantasia, cpfCnpj = :cpfCnpj, senha = :senha, email = :email, telefone = :telefone, celular = :celular, endereco = :endereco, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, responsavel = :responsavel, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':razaoSocial', isset($json['razaoSocial']) && trim($json['razaoSocial']) !== '' ? trim($json['razaoSocial']) : null);
            $stmt->bindValue(':nomeFantasia', isset($json['nomeFantasia']) && trim($json['nomeFantasia']) !== '' ? trim($json['nomeFantasia']) : null);
            $stmt->bindValue(':cpfCnpj', isset($json['cpfCnpj']) && trim($json['cpfCnpj']) !== '' ? trim($json['cpfCnpj']) : null);
            $stmt->bindValue(':senha', isset($json['senha']) && trim($json['senha']) !== '' ? trim($json['senha']) : null);
            $stmt->bindValue(':email', isset($json['email']) && trim($json['email']) !== '' ? trim($json['email']) : null);
            $stmt->bindValue(':telefone', isset($json['telefone']) && trim($json['telefone']) !== '' ? trim($json['telefone']) : null);
            $stmt->bindValue(':celular', isset($json['celular']) && trim($json['celular']) !== '' ? trim($json['celular']) : null);
            $stmt->bindValue(':endereco', isset($json['endereco']) && trim($json['endereco']) !== '' ? trim($json['endereco']) : null);
            $stmt->bindValue(':complemento', isset($json['complemento']) && trim($json['complemento']) !== '' ? trim($json['complemento']) : null);
            $stmt->bindValue(':bairro', isset($json['bairro']) && trim($json['bairro']) !== '' ? trim($json['bairro']) : null);
            $stmt->bindValue(':cidade', isset($json['cidade']) && trim($json['cidade']) !== '' ? trim($json['cidade']) : null);
            $stmt->bindValue(':estado', isset($json['estado']) && trim($json['estado']) !== '' ? trim($json['estado']) : null);
            $stmt->bindValue(':cep', isset($json['cep']) && trim($json['cep']) !== '' ? trim($json['cep']) : null);
            $stmt->bindValue(':responsavel', isset($json['responsavel']) && trim($json['responsavel']) !== '' ? trim($json['responsavel']) : null);
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