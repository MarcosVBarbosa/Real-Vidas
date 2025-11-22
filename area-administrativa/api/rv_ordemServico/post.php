    <?php
        try {
            if (isset($json['nrOrdemServico']) && isset($json['fkServico']) && isset($json['finalizada']) && isset($json['dataSolicitada']) && isset($json['dataAgendada']) && isset($json['trajeto']) && isset($json['fkCliente']) && isset($json['fkOrigem']) && isset($json['chegadaOrigem']) && isset($json['saidaOrigem']) && isset($json['kmOrigem']) && isset($json['kmInicial']) && isset($json['kmFinal']) && isset($json['kmPercorrido']) && isset($json['valorRemocao']) && isset($json['despesaOperacional']) && isset($json['qtdeHoraParada']) && isset($json['valorHoraParada']) && isset($json['limiteHoraParada']) && isset($json['totalHoraParada']) && isset($json['fkVTR']) && isset($json['mediaDiesel']) && isset($json['valorDiesel']) && isset($json['gastoDiesel']) && isset($json['paciente']) && isset($json['convenio']) && isset($json['nrCartao']) && isset($json['solicitante']) && isset($json['dataHoraInicio']) && isset($json['dataHoraFim']) && isset($json['caminhoFicha']) && isset($json['caminhoGuia']) && isset($json['status']) && isset($json['aliquota']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_ordemServico (nrOrdemServico, fkServico, finalizada, dataSolicitada, dataAgendada, trajeto, fkCliente, fkOrigem, chegadaOrigem, saidaOrigem, kmOrigem, kmInicial, kmFinal, kmPercorrido, valorRemocao, despesaOperacional, qtdeHoraParada, valorHoraParada, limiteHoraParada, totalHoraParada, fkVTR, mediaDiesel, valorDiesel, gastoDiesel, paciente, convenio, nrCartao, solicitante, dataHoraInicio, dataHoraFim, caminhoFicha, caminhoGuia, status, aliquota, ativo) VALUES (:nrOrdemServico, :fkServico, :finalizada, :dataSolicitada, :dataAgendada, :trajeto, :fkCliente, :fkOrigem, :chegadaOrigem, :saidaOrigem, :kmOrigem, :kmInicial, :kmFinal, :kmPercorrido, :valorRemocao, :despesaOperacional, :qtdeHoraParada, :valorHoraParada, :limiteHoraParada, :totalHoraParada, :fkVTR, :mediaDiesel, :valorDiesel, :gastoDiesel, :paciente, :convenio, :nrCartao, :solicitante, :dataHoraInicio, :dataHoraFim, :caminhoFicha, :caminhoGuia, :status, :aliquota, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nrOrdemServico', trim($json['nrOrdemServico']) === '' ? null : trim($json['nrOrdemServico']));
            $stmt->bindValue(':fkServico', trim($json['fkServico']) === '' ? null : trim($json['fkServico']));
            $stmt->bindValue(':finalizada', trim($json['finalizada']) === '' ? null : trim($json['finalizada']));
            $stmt->bindValue(':dataSolicitada', trim($json['dataSolicitada']) === '' ? null : trim($json['dataSolicitada']));
            $stmt->bindValue(':dataAgendada', trim($json['dataAgendada']) === '' ? null : trim($json['dataAgendada']));
            $stmt->bindValue(':trajeto', trim($json['trajeto']) === '' ? null : trim($json['trajeto']));
            $stmt->bindValue(':fkCliente', trim($json['fkCliente']) === '' ? null : trim($json['fkCliente']));
            $stmt->bindValue(':fkOrigem', trim($json['fkOrigem']) === '' ? null : trim($json['fkOrigem']));
            $stmt->bindValue(':chegadaOrigem', trim($json['chegadaOrigem']) === '' ? null : trim($json['chegadaOrigem']));
            $stmt->bindValue(':saidaOrigem', trim($json['saidaOrigem']) === '' ? null : trim($json['saidaOrigem']));
            $stmt->bindValue(':kmOrigem', trim($json['kmOrigem']) === '' ? null : trim($json['kmOrigem']));
            $stmt->bindValue(':kmInicial', trim($json['kmInicial']) === '' ? null : trim($json['kmInicial']));
            $stmt->bindValue(':kmFinal', trim($json['kmFinal']) === '' ? null : trim($json['kmFinal']));
            $stmt->bindValue(':kmPercorrido', trim($json['kmPercorrido']) === '' ? null : trim($json['kmPercorrido']));
            $stmt->bindValue(':valorRemocao', trim($json['valorRemocao']) === '' ? null : trim($json['valorRemocao']));
            $stmt->bindValue(':despesaOperacional', trim($json['despesaOperacional']) === '' ? null : trim($json['despesaOperacional']));
            $stmt->bindValue(':qtdeHoraParada', trim($json['qtdeHoraParada']) === '' ? null : trim($json['qtdeHoraParada']));
            $stmt->bindValue(':valorHoraParada', trim($json['valorHoraParada']) === '' ? null : trim($json['valorHoraParada']));
            $stmt->bindValue(':limiteHoraParada', trim($json['limiteHoraParada']) === '' ? null : trim($json['limiteHoraParada']));
            $stmt->bindValue(':totalHoraParada', trim($json['totalHoraParada']) === '' ? null : trim($json['totalHoraParada']));
            $stmt->bindValue(':fkVTR', trim($json['fkVTR']) === '' ? null : trim($json['fkVTR']));
            $stmt->bindValue(':mediaDiesel', trim($json['mediaDiesel']) === '' ? null : trim($json['mediaDiesel']));
            $stmt->bindValue(':valorDiesel', trim($json['valorDiesel']) === '' ? null : trim($json['valorDiesel']));
            $stmt->bindValue(':gastoDiesel', trim($json['gastoDiesel']) === '' ? null : trim($json['gastoDiesel']));
            $stmt->bindValue(':paciente', trim($json['paciente']) === '' ? null : trim($json['paciente']));
            $stmt->bindValue(':convenio', trim($json['convenio']) === '' ? null : trim($json['convenio']));
            $stmt->bindValue(':nrCartao', trim($json['nrCartao']) === '' ? null : trim($json['nrCartao']));
            $stmt->bindValue(':solicitante', trim($json['solicitante']) === '' ? null : trim($json['solicitante']));
            $stmt->bindValue(':dataHoraInicio', trim($json['dataHoraInicio']) === '' ? null : trim($json['dataHoraInicio']));
            $stmt->bindValue(':dataHoraFim', trim($json['dataHoraFim']) === '' ? null : trim($json['dataHoraFim']));
            $stmt->bindValue(':caminhoFicha', trim($json['caminhoFicha']) === '' ? null : trim($json['caminhoFicha']));
            $stmt->bindValue(':caminhoGuia', trim($json['caminhoGuia']) === '' ? null : trim($json['caminhoGuia']));
            $stmt->bindValue(':status', trim($json['status']) === '' ? null : trim($json['status']));
            $stmt->bindValue(':aliquota', trim($json['aliquota']) === '' ? null : trim($json['aliquota']));
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