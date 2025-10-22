<?php
// Usa $pdo fornecido por relatorio.php

$sql = "
  SELECT 
    r.cod_relatorio,
    r.titulo_relatorio,
    u.login AS criador
  FROM Relatorio r
  INNER JOIN Usuario u ON u.id_usuario = r.fk_id_usuario
  ORDER BY r.cod_relatorio ASC
";

try {
  $stmt = $pdo->query($sql);
  $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
  $rows = [];
  echo '<tr><td colspan="4">Erro ao carregar relatórios: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

if (empty($rows)) {
  echo '<tr><td colspan="4" class="center">Nenhum relatório encontrado.</td></tr>';
} else {
  foreach ($rows as $r) {
    $cod     = $r['cod_relatorio'] ?? '';
    $titulo  = $r['titulo_relatorio'] ?? '';
    $criador = $r['criador'] ?? '---';

    echo '<tr data-id="' . htmlspecialchars($cod) . '" data-titulo="' . htmlspecialchars($titulo) . '">';
    echo '  <td class="col-cod">' . htmlspecialchars($cod)    . '</td>';
    echo '  <td class="col-titulo">' . htmlspecialchars($titulo) . '</td>';
    echo '  <td class="col-criador">' . htmlspecialchars($criador). '</td>';
    echo '  <td class="actions col-acoes">';
    echo '    <button class="icon-btn delete" type="button" title="Excluir" data-id="' . htmlspecialchars($cod) . '" data-title="' . htmlspecialchars($titulo) . '"></button>';
    echo '    <button class="icon-btn edit"   type="button" title="Editar"  data-id="' . htmlspecialchars($cod) . '"></button>';
    echo '  </td>';
    echo '</tr>';
  }
}

