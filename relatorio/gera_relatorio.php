<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pagina-inicial/index.php");
    exit;
}

if (isset($_SESSION['papel']) && $_SESSION['papel'] === 'cliente') {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

$id_usuario    = $_SESSION['id_usuario'];
$nome_exibicao = $_SESSION['nome'] ?? 'Maquinista';
$historico_relatorios = [];

if (isset($conexao) && $conexao) {
    try {
        $stmt_u = $conexao->prepare("SELECT nome FROM usuario WHERE id = ?");
        $stmt_u->bind_param("i", $id_usuario);
        $stmt_u->execute();
        $res_u = $stmt_u->get_result();
        if ($user = $res_u->fetch_assoc() && !empty($user['nome'])) {
            $nome_exibicao = $user['nome'];
        }

        // Busca os últimos 10 relatórios salvos no banco
        $sql_hist = "SELECT r.*, u.nome AS nome_emissor 
                     FROM relatorios_emitidos r 
                     INNER JOIN usuario u ON r.fk_id_usuario = u.id 
                     ORDER BY r.data_emissao DESC LIMIT 10";
        $res_hist = $conexao->query($sql_hist);
        if ($res_hist) {
            while ($h = $res_hist->fetch_assoc()) {
                $historico_relatorios[] = $h;
            }
        }
    } catch (Exception $e) {}
}
$primeiro_nome = explode(' ', trim($nome_exibicao))[0];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Relatórios - GTT</title>
    <link rel="stylesheet" href="style.css?v=8">
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
                <li class="nav-item active"><a href="gera_relatorio.php">Relatórios</a></li>
                <li class="nav-item tab-viajar"><a href="viagens.php">Viagens</a></li>
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
        <h1 class="page-title">Emissão de Relatórios Operacionais</h1>

        <section class="form-section">
            <form method="GET" action="visualiza_relatorio.php" class="relatorio-form">
                
                <div class="form-group">
                    <label for="tipo">Tipo de Relatório *</label>
                    <select name="tipo" id="tipo" required>
                        <option value="desempenho">Desempenho (Velocidade por Trem)</option>
                        <option value="consumo">Consumo de Energia / Combustível</option>
                        <option value="eficiencia">Eficiência Operacional (Viagens)</option>
                        <option value="manutencao">Manutenção e Alertas</option>
                    </select>
                </div>

                <div class="form-grid-dates">
                    <div class="form-group">
                        <label for="data_inicio">Data Inicial *</label>
                        <input type="date" name="data_inicio" id="data_inicio" required value="<?php echo date('Y-m-01'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="data_fim">Data Final *</label>
                        <input type="date" name="data_fim" id="data_fim" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Gerar Relatório</button>
                </div>
            </form>
        </section>

        <!-- Histórico de Relatórios Emitidos -->
        <section class="history-section" style="margin-top: 45px;">
            <h2 class="page-title" style="font-size: 24px; margin-bottom: 20px;">Histórico de Relatórios Emitidos</h2>
            
            <div class="table-section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Relatório</th>
                            <th>Período Consultado</th>
                            <th>Emitido por</th>
                            <th>Data/Hora</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($historico_relatorios)): ?>
                            <?php foreach ($historico_relatorios as $h): ?>
                                <?php
                                    $url_reabrir = "visualiza_relatorio.php?" . http_build_query([
                                        'tipo'        => $h['tipo_relatorio'],
                                        'data_inicio' => $h['data_inicio'],
                                        'data_fim'    => $h['data_fim'],
                                        'historico'   => '1'
                                    ]);
                                ?>
                                <tr>
                                    <td><strong>#<?php echo $h['id_relatorio']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($h['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($h['data_inicio'])) . ' até ' . date('d/m/Y', strtotime($h['data_fim'])); ?></td>
                                    <td><?php echo htmlspecialchars($h['nome_emissor']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($h['data_emissao'])); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($url_reabrir); ?>" class="btn-print" style="text-decoration: none; font-size: 13px; padding: 6px 12px; display: inline-block;">
                                            Visualizar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-table">Nenhum relatório foi emitido ainda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>