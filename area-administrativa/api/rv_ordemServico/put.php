    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_ordemServico SET nrOrdemServico = :nrOrdemServico, fkServico = :fkServico, finalizada = :finalizada, dataSolicitada = :dataSolicitada, dataAgendada = :dataAgendada, trajeto = :trajeto, fkCliente = :fkCliente, fkOrigem = :fkOrigem, chegadaOrigem = :chegadaOrigem, saidaOrigem = :saidaOrigem, kmOrigem = :kmOrigem, kmInicial = :kmInicial, kmFinal = :kmFinal, kmPercorrido = :kmPercorrido, valorRemocao = :valorRemocao, despesaOperacional = :despesaOperacional, qtdeHoraParada = :qtdeHoraParada, valorHoraParada = :valorHoraParada, limiteHoraParada = :limiteHoraParada, totalHoraParada = :totalHoraParada, fkVTR = :fkVTR, mediaDiesel = :mediaDiesel, valorDiesel = :valorDiesel, gastoDiesel = :gastoDiesel, paciente = :paciente, convenio = :convenio, nrCartao = :nrCartao, solicitante = :solicitante, dataHoraInicio = :dataHoraInicio, dataHoraFim = :dataHoraFim, caminhoFicha = :caminhoFicha, caminhoGuia = :caminhoGuia, status = :status, aliquota = :aliquota, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nrOrdemServico', isset($json['nrOrdemServico']) && trim($json['nrOrdemServico']) !== '' ? trim($json['nrOrdemServico']) : null);
            $stmt->bindValue(':fkServico', isset($json['fkServico']) && trim($json['fkServico']) !== '' ? trim($json['fkServico']) : null);
            $stmt->bindValue(':finalizada', isset($json['finalizada']) && trim($json['finalizada']) !== '' ? trim($json['finalizada']) : null);
            $stmt->bindValue(':dataSolicitada', isset($json['dataSolicitada']) && trim($json['dataSolicitada']) !== '' ? trim($json['dataSolicitada']) : null);
            $stmt->bindValue(':dataAgendada', isset($json['dataAgendada']) && trim($json['dataAgendada']) !== '' ? trim($json['dataAgendada']) : null);
            $stmt->bindValue(':trajeto', isset($json['trajeto']) && trim($json['trajeto']) !== '' ? trim($json['trajeto']) : null);
            $stmt->bindValue(':fkCliente', isset($json['fkCliente']) && trim($json['fkCliente']) !== '' ? trim($json['fkCliente']) : null);
            $stmt->bindValue(':fkOrigem', isset($json['fkOrigem']) && trim($json['fkOrigem']) !== '' ? trim($json['fkOrigem']) : null);
            $stmt->bindValue(':chegadaOrigem', isset($json['chegadaOrigem']) && trim($json['chegadaOrigem']) !== '' ? trim($json['chegadaOrigem']) : null);
            $stmt->bindValue(':saidaOrigem', isset($json['saidaOrigem']) && trim($json['saidaOrigem']) !== '' ? trim($json['saidaOrigem']) : null);
            $stmt->bindValue(':kmOrigem', isset($json['kmOrigem']) && trim($json['kmOrigem']) !== '' ? trim($json['kmOrigem']) : null);
            $stmt->bindValue(':kmInicial', isset($json['kmInicial']) && trim($json['kmInicial']) !== '' ? trim($json['kmInicial']) : null);
            $stmt->bindValue(':kmFinal', isset($json['kmFinal']) && trim($json['kmFinal']) !== '' ? trim($json['kmFinal']) : null);
            $stmt->bindValue(':kmPercorrido', isset($json['kmPercorrido']) && trim($json['kmPercorrido']) !== '' ? trim($json['kmPercorrido']) : null);
            $stmt->bindValue(':valorRemocao', isset($json['valorRemocao']) && trim($json['valorRemocao']) !== '' ? trim($json['valorRemocao']) : null);
            $stmt->bindValue(':despesaOperacional', isset($json['despesaOperacional']) && trim($json['despesaOperacional']) !== '' ? trim($json['despesaOperacional']) : null);
            $stmt->bindValue(':qtdeHoraParada', isset($json['qtdeHoraParada']) && trim($json['qtdeHoraParada']) !== '' ? trim($json['qtdeHoraParada']) : null);
            $stmt->bindValue(':valorHoraParada', isset($json['valorHoraParada']) && trim($json['valorHoraParada']) !== '' ? trim($json['valorHoraParada']) : null);
            $stmt->bindValue(':limiteHoraParada', isset($json['limiteHoraParada']) && trim($json['limiteHoraParada']) !== '' ? trim($json['limiteHoraParada']) : null);
            $stmt->bindValue(':totalHoraParada', isset($json['totalHoraParada']) && trim($json['totalHoraParada']) !== '' ? trim($json['totalHoraParada']) : null);
            $stmt->bindValue(':fkVTR', isset($json['fkVTR']) && trim($json['fkVTR']) !== '' ? trim($json['fkVTR']) : null);
            $stmt->bindValue(':mediaDiesel', isset($json['mediaDiesel']) && trim($json['mediaDiesel']) !== '' ? trim($json['mediaDiesel']) : null);
            $stmt->bindValue(':valorDiesel', isset($json['valorDiesel']) && trim($json['valorDiesel']) !== '' ? trim($json['valorDiesel']) : null);
            $stmt->bindValue(':gastoDiesel', isset($json['gastoDiesel']) && trim($json['gastoDiesel']) !== '' ? trim($json['gastoDiesel']) : null);
            $stmt->bindValue(':paciente', isset($json['paciente']) && trim($json['paciente']) !== '' ? trim($json['paciente']) : null);
            $stmt->bindValue(':convenio', isset($json['convenio']) && trim($json['convenio']) !== '' ? trim($json['convenio']) : null);
            $stmt->bindValue(':nrCartao', isset($json['nrCartao']) && trim($json['nrCartao']) !== '' ? trim($json['nrCartao']) : null);
            $stmt->bindValue(':solicitante', isset($json['solicitante']) && trim($json['solicitante']) !== '' ? trim($json['solicitante']) : null);
            $stmt->bindValue(':dataHoraInicio', isset($json['dataHoraInicio']) && trim($json['dataHoraInicio']) !== '' ? trim($json['dataHoraInicio']) : null);
            $stmt->bindValue(':dataHoraFim', isset($json['dataHoraFim']) && trim($json['dataHoraFim']) !== '' ? trim($json['dataHoraFim']) : null);
            $stmt->bindValue(':caminhoFicha', isset($json['caminhoFicha']) && trim($json['caminhoFicha']) !== '' ? trim($json['caminhoFicha']) : null);
            $stmt->bindValue(':caminhoGuia', isset($json['caminhoGuia']) && trim($json['caminhoGuia']) !== '' ? trim($json['caminhoGuia']) : null);
            $stmt->bindValue(':status', isset($json['status']) && trim($json['status']) !== '' ? trim($json['status']) : null);
            $stmt->bindValue(':aliquota', isset($json['aliquota']) && trim($json['aliquota']) !== '' ? trim($json['aliquota']) : null);
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