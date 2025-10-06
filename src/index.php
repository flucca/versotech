<?php
require 'connection.php';

$connection = new Connection();

// Definir o número de usuários por página
$limit = 10;

// Pegar a página atual via GET, padrão 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calcular o OFFSET
$offset = ($page - 1) * $limit;

// Buscar total de usuários para calcular páginas
$totalUsers = $connection->query("SELECT COUNT(*) as total FROM users")->fetch()->total;
$totalPages = ceil($totalUsers / $limit);

// Buscar usuários da página atual
$users = $connection->query("SELECT * FROM users ORDER BY name LIMIT $limit OFFSET $offset")->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Lista de Usuários</h4>
            <button class="btn btn-sm btn-warning me-2 btn-user-ctrl">
                <i class="bi bi-plus-circle"></i> Novo Usuário
            </button>
        </div>

        <div class="card-body">
            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user->id ?></td>
                                <td><?= htmlspecialchars($user->name) ?></td>
                                <td><?= htmlspecialchars($user->email) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-2 btn-user-ctrl"
                                            data-id="<?= $user->id ?>"
                                            data-email="<?= $user->email ?>"
                                    >
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>
                                    <button
                                            class="btn btn-sm btn-danger btn-delete-user"
                                            data-id="<?= $user->id ?>"
                                    <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Paginação -->
                    <?php if ($totalPages > 1): ?>
                        <nav>
                            <ul class="pagination justify-content-center mt-3">
                                <!-- Link anterior -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                                </li>

                                <!-- Links das páginas -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Link próximo -->
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Próximo</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted mb-0">Nenhum usuário encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center">
                <div id="editUserContent">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="main.js"></script>

</body>
</html>
