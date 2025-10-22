<?php
include 'conn.php';

$sql = "
    SELECT 
        u.id_usuario,
        u.login,
        u.senha,
        u.tipo_usuario AS tipo,
        CASE 
            WHEN u.tipo_usuario = 1 THEN a.nome_adm
            WHEN u.tipo_usuario = 2 THEN m.nome_medico
            ELSE NULL
        END AS nome,
        CASE 
            WHEN u.tipo_usuario = 1 THEN 'Administrador'
            WHEN u.tipo_usuario = 2 THEN 'Médico'
            ELSE 'Outro'
        END AS perfil
    FROM Usuario u
    LEFT JOIN Administrador a ON u.id_usuario = a.fk_id_usuario
    LEFT JOIN Medico m        ON u.id_usuario = m.fk_id_usuario
    ORDER BY u.id_usuario ASC
";

try {
    $stmt = $pdo->query($sql);
    $usuarios = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
    $usuarios = [];
    echo '<tr><td colspan="6">Erro ao carregar usuários: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

if (empty($usuarios)) {
    echo '<tr><td colspan="6" class="center">Nenhum usuário encontrado.</td></tr>';
} else {
    foreach ($usuarios as $u) {
        $id     = $u['id_usuario'] ?? '';
        $login  = $u['login'] ?? '---';
        $nome   = $u['nome'] ? $u['nome'] : '---';
        $perfil = $u['perfil'] ?? 'Outro';
        // senha é buscada mas não exibida
        $tipo   = isset($u['tipo']) ? (int)$u['tipo'] : 0;

        echo '<tr data-id="' . htmlspecialchars($id) . '" data-login="' . htmlspecialchars($login) . '" data-tipo="' . htmlspecialchars((string)$tipo) . '">';
        echo '  <td class="col-id">'     . htmlspecialchars($id)     . '</td>';
        echo '  <td class="col-login">'  . htmlspecialchars($login)  . '</td>';
        echo '  <td class="col-nome">'   . htmlspecialchars($nome)   . '</td>';
        echo '  <td class="col-perfil">' . htmlspecialchars($perfil) . '</td>';
        echo '  <td class="col-senha">••••••••</td>';
        echo '  <td class="actions col-acoes">';
        echo '    <button class="icon-btn delete" title="Excluir"></button>';
        echo '    <button class="icon-btn edit"   title="Editar"></button>';
        echo '  </td>';
        echo '</tr>';
    }
}
