<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pagina-inicial/index.php");
    exit;
}

$papel = $_SESSION['papel'] ?? 'cliente';
if ($papel === 'cliente') {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

$id_usuario    = $_SESSION['id_usuario'];
$nome_exibicao = $_SESSION['nome'] ?? 'Maquinista';
$mensagem      = "";
$tipo_mensagem = "";

// Atualização de status da viagem pelo maquinista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_status'], $_POST['id_rota'])) {
    $id_rota     = (int) $_POST['id_rota'];
    $novo_status = $_POST['acao_status'];

    if (isset($conexao) && $conexao) {
        if ($novo_status === 'em andamento') {
            $stmt_upd = $conexao->prepare("UPDATE rotas SET status = 'em andamento' WHERE id_rota = ?");
            $stmt_upd->bind_param("i", $id_rota);
            if ($stmt_upd->execute()) {
                $mensagem      = "Viagem #{$id_rota} iniciada com sucesso!";
                $tipo_mensagem = "sucesso";
            }
        } elseif ($novo_status === 'concluida') {
            $hora_atual = date('H:i:s');
            $stmt_upd = $conexao->prepare("UPDATE rotas SET status = 'concluida', hora_chegada = ? WHERE id_rota = ?");
            $stmt_upd->bind_param("si", $hora_atual, $id_rota);
            if ($stmt_upd->execute()) {
                $mensagem      = "Viagem #{$id_rota} concluída às " . substr($hora_atual, 0, 5) . "!";
                $tipo_mensagem = "sucesso";
            }
        }
    }
}

if (isset($conexao) && $conexao) {
    try {
        $stmt_u = $conexao->prepare("SELECT nome FROM usuario WHERE id = ?");
        $stmt_u->bind_param("i", $id_usuario);
        $stmt_u->execute();
        $res_u = $stmt_u->get_result();
        if ($user = $res_u->fetch_assoc()) {
            if (!empty($user['nome'])) {
                $nome_exibicao = $user['nome'];
            }
        }
    } catch (Exception $e) {}
}
$primeiro_nome = explode(' ', trim($nome_exibicao))[0];

$data_hoje = date('Y-m-d');
$f_data    = trim($_GET['data_viagem'] ?? $data_hoje);

$viagens = [];
if (isset($conexao) && $conexao) {
    try {
        $sql = "SELECT r.*, t.prefixo_trem, t.modelo_trem 
                FROM rotas r 
                INNER JOIN trens t ON r.id_trem = t.id_trem 
                WHERE r.data_viagem = ? 
                ORDER BY r.hora_partida ASC";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $f_data);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $viagens[] = $row;
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escala de Viagens - GTT</title>
    <link rel="stylesheet" href="style.css?v=9">
</head>
<body>

    <nav class="sticky-bar no-print">
        <div class="nav-left">
            <div class="logo-container">
                <a href="gera_relatorio.php">
                    <img src="../images/GTT-logo.png" alt="Golden Train Track Logo" class="nav-logo" onerror="this.style.display='none'">
                </a>
            </div>
            
            <ul class="nav-links">
                <li class="nav-item"><a href="gera_relatorio.php">Relatórios</a></li>
                <li class="nav-item tab-viajar active"><a href="viagens.php">Viagens</a></li>
            </ul>
        </div>

        <div class="nav-right">
            <a href="maquinista_usuario.php" class="user-btn">
                <span><?php echo htmlspecialchars($primeiro_nome); ?></span>
                <svg class="user-icon" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </a>
        </div>
    </nav>

    <main class="main-content">
        <h1 class="page-title">Escala de Viagens e Rotas do Dia</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert-msg <?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <section class="search-section">
            <form method="GET" action="viagens.php" class="filter-bar">
                <label for="data_viagem">Selecionar Data:</label>
                <input type="date" name="data_viagem" id="data_viagem" value="<?php echo htmlspecialchars($f_data); ?>">
                <button type="submit" class="btn-filter">Filtrar</button>
                
                <?php if ($f_data !== $data_hoje): ?>
                    <a href="viagens.php" class="btn-hoje">Voltar para Hoje</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="table-section">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trem</th>
                        <th>Origem &rarr; Destino</th>
                        <th>Partida / Chegada</th>
                        <th>Status</th>
                        <th>Operação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($viagens)): ?>
                        <?php foreach ($viagens as $v): ?>
                            <tr>
                                <td><strong>#<?php echo $v['id_rota']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($v['prefixo_trem']); ?></strong> 
                                    <small class="modelo-sub">(<?php echo htmlspecialchars($v['modelo_trem']); ?>)</small>
                                </td>
                                <td><?php echo htmlspecialchars($v['origem']) . ' &rarr; ' . htmlspecialchars($v['destino']); ?></td>
                                <td>
                                    <?php 
                                    echo substr($v['hora_partida'], 0, 5); 
                                    if (!empty($v['hora_chegada'])) {
                                        echo ' / ' . substr($v['hora_chegada'], 0, 5);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge status-<?php echo str_replace(' ', '-', strtolower($v['status'])); ?>">
                                        <?php echo ucfirst($v['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($v['status'] === 'programada'): ?>
                                        <form method="POST" action="viagens.php?data_viagem=<?php echo urlencode($f_data); ?>" style="display:inline;">
                                            <input type="hidden" name="id_rota" value="<?php echo $v['id_rota']; ?>">
                                            <button type="submit" name="acao_status" value="em andamento" class="btn-acao iniciar">Iniciar</button>
                                        </form>
                                    <?php elseif ($v['status'] === 'em andamento'): ?>
                                        <form method="POST" action="viagens.php?data_viagem=<?php echo urlencode($f_data); ?>" style="display:inline;">
                                            <input type="hidden" name="id_rota" value="<?php echo $v['id_rota']; ?>">
                                            <button type="submit" name="acao_status" value="concluida" class="btn-acao concluir">Concluir</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #888; font-size: 13px;">Finalizada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-table">
                                Nenhuma viagem agendada para a data selecionada (<?php echo date('d/m/Y', strtotime($f_data)); ?>).
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>