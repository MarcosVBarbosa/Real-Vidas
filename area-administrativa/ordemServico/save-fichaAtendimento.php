<?php
include('../verifyConnection.php');
include('../connectDb.php');
$pageActive = "Ordem Serviço";

$fkOs = base64_decode($_POST['pkId']);
// Campos de Serviço
$checkboxBasico = isset($_POST['checkboxBasico']) ? 1 : 0;
$checkboxExames = isset($_POST['checkboxExames']) ? 1 : 0;
$checkboxUtiAdulto = isset($_POST['checkboxUtiAdulto']) ? 1 : 0;
$checkboxInternacao = isset($_POST['checkboxInternacao']) ? 1 : 0;
$checkboxAph = isset($_POST['checkboxAph']) ? 1 : 0;
$checkboxTrasfHospitalar = isset($_POST['checkboxTrasfHospitalar']) ? 1 : 0;
$checkboxUtiNeo = isset($_POST['checkboxUtiNeo']) ? 1 : 0;
$checkboxEventos = isset($_POST['checkboxEventos']) ? 1 : 0;
$checkboxParticular = isset($_POST['checkboxParticular']) ? 1 : 0;
$checkboxSivInt = isset($_POST['checkboxSivInt']) ? 1 : 0;
$checkboxAltaHospitalar = isset($_POST['checkboxAltaHospitalar']) ? 1 : 0;

// Tipo Exames
$checkboxCate = isset($_POST['checkboxCate']) ? 1 : 0;
$checkboxTomo = isset($_POST['checkboxTomo']) ? 1 : 0;
$checkboxRx = isset($_POST['checkboxRx']) ? 1 : 0;
$checkboxGtt = isset($_POST['checkboxGtt']) ? 1 : 0;
$checkboxCprs = isset($_POST['checkboxCprs']) ? 1 : 0;
$checkboxPetScam = isset($_POST['checkboxPetScam']) ? 1 : 0;
$checkboxTcHip = isset($_POST['checkboxTcHip']) ? 1 : 0;
$checkboxTqtTqm = isset($_POST['checkboxTqtTqm']) ? 1 : 0;
$checkboxUs = isset($_POST['checkboxUs']) ? 1 : 0;
$checkboxRmn = isset($_POST['checkboxRmn']) ? 1 : 0;
$txtOutrosExames = mysqli_real_escape_string($connecta, $_POST['txtOutrosExames']) ?? '';

// Dados Pessoais
$numberIdade = $_POST['numberIdade'] ?? null;
$txtContato = mysqli_real_escape_string($connecta, $_POST['txtContato']) ?? '';
$selectSexo = $_POST['selectSexo'] ?? '';
$selectMembrosSuperior = $_POST['selectMembrosSuperior'] ?? '';
$selectMembrosInferior = $_POST['selectMembrosInferior'] ?? '';

// Avaliação Neurológica
$checkboxLucido = isset($_POST['checkboxLucido']) ? 1 : 0;
$checkboxOrientado = isset($_POST['checkboxOrientado']) ? 1 : 0;
$checkboxConsciente = isset($_POST['checkboxConsciente']) ? 1 : 0;
$checkboxConfuso = isset($_POST['checkboxConfuso']) ? 1 : 0;
$checkboxComunicativo = isset($_POST['checkboxComunicativo']) ? 1 : 0;
$checkboxNaoVerbaliza = isset($_POST['checkboxNaoVerbaliza']) ? 1 : 0;

// Pupilas
$checkboxIsocoricas = isset($_POST['checkboxIsocoricas']) ? 1 : 0;
$checkboxAnisocoricas = isset($_POST['checkboxAnisocoricas']) ? 1 : 0;
$checkboxMidriatricas = isset($_POST['checkboxMidriatricas']) ? 1 : 0;
$checkboxMiotica = isset($_POST['checkboxMiotica']) ? 1 : 0;
$checkboxDE = isset($_POST['checkboxDE']) ? 1 : 0;
$checkboxMaior = isset($_POST['checkboxMaior']) ? 1 : 0;

// Respiração
$checkboxEupneico = isset($_POST['checkboxEupneico']) ? 1 : 0;
$checkboxTaquipneico = isset($_POST['checkboxTaquipneico']) ? 1 : 0;
$checkboxBradipneico = isset($_POST['checkboxBradipneico']) ? 1 : 0;
$checkboxDispneico = isset($_POST['checkboxDispneico']) ? 1 : 0;
$checkboxApneia = isset($_POST['checkboxApneia']) ? 1 : 0;

// Circulatório
$checkboxNormocardico = isset($_POST['checkboxNormocardico']) ? 1 : 0;
$checkboxTarquicardico = isset($_POST['checkboxTarquicardico']) ? 1 : 0;
$checkboxBradicardico = isset($_POST['checkboxBradicardico']) ? 1 : 0;
$checkboxFiliforme = isset($_POST['checkboxFiliforme']) ? 1 : 0;

// Abdómen
$checkboxPlano = isset($_POST['checkboxPlano']) ? 1 : 0;
$checkboxGloboso = isset($_POST['checkboxGloboso']) ? 1 : 0;
$checkboxEscavado = isset($_POST['checkboxEscavado']) ? 1 : 0;
$checkboxFlacido = isset($_POST['checkboxFlacido']) ? 1 : 0;
$checkboxEmAventa = isset($_POST['checkboxEmAventa']) ? 1 : 0;
$checkboxGravidico = isset($_POST['checkboxGravidico']) ? 1 : 0;

// Abertura Ocular
$checkboxEspontanea = isset($_POST['checkboxEspontanea']) ? 1 : 0;
$checkboxVoz4 = isset($_POST['checkboxVoz4']) ? 1 : 0;
$checkboxDor2 = isset($_POST['checkboxDor2']) ? 1 : 0;
$checkboxNenhuma1 = isset($_POST['checkboxNenhuma1']) ? 1 : 0;

// Resposta Verbal
$checkboxOrientada5 = isset($_POST['checkboxOrientada5']) ? 1 : 0;
$checkboxConfusa4 = isset($_POST['checkboxConfusa4']) ? 1 : 0;
$checkboxPalavras3 = isset($_POST['checkboxPalavras3']) ? 1 : 0;
$checkboxPalavras2 = isset($_POST['checkboxPalavras2']) ? 1 : 0;

// Resposta Motora
$checkboxObdece6 = isset($_POST['checkboxObdece6']) ? 1 : 0;
$checkboxLocaliza5 = isset($_POST['checkboxLocaliza5']) ? 1 : 0;
$checkboxMovimentos4 = isset($_POST['checkboxMovimentos4']) ? 1 : 0;
$checkboxFlexao3 = isset($_POST['checkboxFlexao3']) ? 1 : 0;
$checkboxExtensao2 = isset($_POST['checkboxExtensao2']) ? 1 : 0;
$checkboxNenhuma = isset($_POST['checkboxNenhuma']) ? 1 : 0;

// Pedágios e Outros
$txtPedagios = mysqli_real_escape_string($connecta, $_POST['txtPedagios']) ?? '';
$txtOutros = mysqli_real_escape_string($connecta, $_POST['txtOutros']) ?? '';
$selectRefeicaoLanche = $_POST['selectRefeicaoLanche'] ?? '';

// Grande/Pequeno Bar
$selectGrandeUm = $_POST['selectGrandeUm'] ?? '';
$grandeUmBarValeu = $_POST['grandeUmBarValeu'] ?? '';
$grandeUmBarQuantidade = $_POST['grandeUmBarQuantidade'] ?? '';

$selectGrandeDois = $_POST['selectGrandeDois'] ?? '';
$grandeDoisBarValeu = $_POST['grandeDoisBarValeu'] ?? '';
$grandeDoisBarQuantidade = $_POST['grandeDoisBarQuantidade'] ?? '';

$selectPequenoTres = $_POST['selectPequenoTres'] ?? '';
$pequenoTresBarValeu = $_POST['pequenoTresBarValeu'] ?? '';
$pequenoTresBarQuantidade = $_POST['pequenoTresBarQuantidade'] ?? '';

$selectPequenoQuatro = $_POST['selectPequenoQuatro'] ?? '';
$pequenoQuatroBarValeu = $_POST['pequenoQuatroBarValeu'] ?? '';
$pequenoQuatroBarQuantidade = $_POST['pequenoQuatroBarQuantidade'] ?? '';

// Observações
$txtObsMedicaEnfermeiros = mysqli_real_escape_string($connecta, $_POST['txtObsMedicaEnfermeiros']) ?? '';
$txtMateriaisUtilizados = mysqli_real_escape_string($connecta, $_POST['txtMateriaisUtilizados']) ?? '';

// Campos de matriz (queimaduras e traumas)
$queimadura = json_encode($_POST['queimadura']);
$trauma = json_encode($_POST['trauma']);
$sinaisVitais = json_encode($_POST['sinaisVitais']);

// 
$sql = "
DELETE FROM rv_fichaAtendimento WHERE fkOs = '$fkOs';
INSERT INTO rv_fichaAtendimento (
    fkOs, checkboxBasico, checkboxExames, checkboxUtiAdulto, checkboxInternacao, checkboxAph, checkboxTrasfHospitalar, checkboxUtiNeo, checkboxEventos, checkboxParticular, checkboxSivInt, checkboxAltaHospitalar,
    checkboxCate, checkboxTomo, checkboxRx, checkboxGtt, checkboxCprs, checkboxPetScam, checkboxTcHip, checkboxTqtTqm, checkboxUs, checkboxRmn, txtOutrosExames,
    numberIdade, txtContato, selectSexo, selectMembrosSuperior, selectMembrosInferior,
    checkboxLucido, checkboxOrientado, checkboxConsciente, checkboxConfuso, checkboxComunicativo, checkboxNaoVerbaliza,
    checkboxIsocoricas, checkboxAnisocoricas, checkboxMidriatricas, checkboxMiotica, checkboxDE, checkboxMaior,
    checkboxEupneico, checkboxTaquipneico, checkboxBradipneico, checkboxDispneico, checkboxApneia,
    checkboxNormocardico, checkboxTarquicardico, checkboxBradicardico, checkboxFiliforme,
    checkboxPlano, checkboxGloboso, checkboxEscavado, checkboxFlacido, checkboxEmAventa, checkboxGravidico,
    checkboxEspontanea, checkboxVoz4, checkboxDor2, checkboxNenhuma1,
    checkboxOrientada5, checkboxConfusa4, checkboxPalavras3, checkboxPalavras2,
    checkboxObdece6, checkboxLocaliza5, checkboxMovimentos4, checkboxFlexao3, checkboxExtensao2, checkboxNenhuma,
    txtPedagios, txtOutros, selectRefeicaoLanche,
    selectGrandeUm, grandeUmBarValeu, grandeUmBarQuantidade,
    selectGrandeDois, grandeDoisBarValeu, grandeDoisBarQuantidade,
    selectPequenoTres, pequenoTresBarValeu, pequenoTresBarQuantidade,
    selectPequenoQuatro, pequenoQuatroBarValeu, pequenoQuatroBarQuantidade,
    txtObsMedicaEnfermeiros, txtMateriaisUtilizados, queimadura, trauma,sinaisVitais
) VALUES (
    '$fkOs','$checkboxBasico', '$checkboxExames', '$checkboxUtiAdulto', '$checkboxInternacao', '$checkboxAph', '$checkboxTrasfHospitalar', '$checkboxUtiNeo', '$checkboxEventos', '$checkboxParticular', '$checkboxSivInt', '$checkboxAltaHospitalar',
    '$checkboxCate', '$checkboxTomo', '$checkboxRx', '$checkboxGtt', '$checkboxCprs', '$checkboxPetScam', '$checkboxTcHip', '$checkboxTqtTqm', '$checkboxUs', '$checkboxRmn', '$txtOutrosExames',
    '$numberIdade', '$txtContato', '$selectSexo', '$selectMembrosSuperior', '$selectMembrosInferior',
    '$checkboxLucido', '$checkboxOrientado', '$checkboxConsciente', '$checkboxConfuso', '$checkboxComunicativo', '$checkboxNaoVerbaliza',
    '$checkboxIsocoricas', '$checkboxAnisocoricas', '$checkboxMidriatricas', '$checkboxMiotica', '$checkboxDE', '$checkboxMaior',
    '$checkboxEupneico', '$checkboxTaquipneico', '$checkboxBradipneico', '$checkboxDispneico', '$checkboxApneia',
    '$checkboxNormocardico', '$checkboxTarquicardico', '$checkboxBradicardico', '$checkboxFiliforme',
    '$checkboxPlano', '$checkboxGloboso', '$checkboxEscavado', '$checkboxFlacido', '$checkboxEmAventa', '$checkboxGravidico',
    '$checkboxEspontanea', '$checkboxVoz4', '$checkboxDor2', '$checkboxNenhuma1',
    '$checkboxOrientada5', '$checkboxConfusa4', '$checkboxPalavras3', '$checkboxPalavras2',
    '$checkboxObdece6', '$checkboxLocaliza5', '$checkboxMovimentos4', '$checkboxFlexao3', '$checkboxExtensao2', '$checkboxNenhuma',
    '$txtPedagios', '$txtOutros', '$selectRefeicaoLanche',
    '$selectGrandeUm', '$grandeUmBarValeu', '$grandeUmBarQuantidade',
    '$selectGrandeDois', '$grandeDoisBarValeu', '$grandeDoisBarQuantidade',
    '$selectPequenoTres', '$pequenoTresBarValeu', '$pequenoTresBarQuantidade',
    '$selectPequenoQuatro', '$pequenoQuatroBarValeu', '$pequenoQuatroBarQuantidade',
    '$txtObsMedicaEnfermeiros', '$txtMateriaisUtilizados',
    '$queimadura', '$trauma','$sinaisVitais'
)";

$query = mysqli_multi_query($connecta, $sql);
if ($query) {
    $type = base64_encode("success");
    $msg = base64_encode("Ficha de atendimento salva com sucesso!");
} else {
    $type = base64_encode("danger");
    $msg = base64_encode("Falha ao salvar a ficha de atendimento! Por favor tente mais tarde.");
}

header('Location: insert.php?msg=' . $msg . '&type=' . $type . '&ref=' . base64_encode($fkOs));
