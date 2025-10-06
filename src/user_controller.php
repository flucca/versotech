<?php
require 'connection.php';
$connection = new Connection();
$id = $_GET['id'] ?? null;
switch ($_SERVER['REQUEST_METHOD']) {
    case 'DELETE':
        $connection->query(sprintf("DELETE FROM users WHERE id = %d", $id));
        header("Content-Type: application/json");
        echo json_encode([
                "success" => true,
                "message" => "Usuário excluido com sucesso"
        ]);
        exit;
    case 'POST':
        if ($id) {
            $user = $connection->query(sprintf("SELECT * FROM users WHERE id=%d", $id))->fetchObject();
            if (isset($user->id)) {
                $connection->query(sprintf("UPDATE users SET name='%s', email='%s' WHERE id =%d", $_POST['name'], $_POST['email'], $user->id));
            }
        } else {
            $connection->query(sprintf("INSERT INTO users (name, email) VALUES ('%s', '%s')", $_POST['name'], $_POST['email']));
            $id = $connection->getConnection()->lastInsertId();
            $user = $connection->query(sprintf("SELECT * FROM users WHERE id=%d", $id))->fetchObject();
        }


        $connection->query(sprintf("DELETE FROM user_colors WHERE user_id=%d", $user->id));
        foreach ($_POST['groups'] ?? [] as $color) {
            $connection->query(sprintf("INSERT INTO user_colors (user_id, color_id) VALUES ('%d', '%d')", $user->id, $color));
        }
        header('Location: index.php');
        exit;

}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($id)) {
        $user = $connection->query(sprintf("SELECT * FROM users WHERE id=%d", $id))->fetchObject();
        if (isset($user->id)) {
            $connection->query(sprintf("UPDATE users SET name='%s', email='%s' WHERE id =%d", $_POST['name'], $_POST['email'], $user->id));
        }
    } else {
        $connection->query(sprintf("INSERT INTO users (name, email) VALUES ('%s', '%s')", $_POST['name'], $_POST['email']));
    }
    header('Location: index.php');
    exit;
}

if (isset($id)) {
    $user = $connection->query(sprintf("SELECT * FROM users WHERE id = %d LIMIT 1", $id))->fetchObject();
    if (!$user) {
        header("Content-Type: application/json");
        echo json_encode(["message" => "Usuário não encontrado!"]);
        exit;
    }
}

$colors = $connection->query("SELECT id,name FROM colors order by name")->fetchAll();
$user_colors = $connection->query(sprintf("
SELECT c.id,c.name FROM user_colors uc
inner join colors c on uc.color_id=c.id
where user_id=%d
", $id))->fetchAll();

?>


<form method="POST" action="/user_controller.php?<?php if ($user->id ?? false) {
    echo "id=" . $user->id;
} ?>">
    <label>Nome:</label><br>
    <input type="text" name="name" value="<?= $user->name ?? null ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= $user->email ?? null ?>" required><br><br>

    <label>Grupos:</label>
    <div class="group-selector">
        <select id="group-select">
            <option value="">Selecione um grupo</option>
            <?php foreach ($colors as $color): ?>
                <option value=<?= json_encode($color) ?>><?= $color->name ?></option>
            <?php endforeach; ?>

        </select>
        <button type="button" onclick="addGroup()">Adicionar</button>
    </div>
    <div class="mb-3">
        <div id="group-list" class="group-list d-flex flex-wrap gap-2 p-2 border rounded bg-light">
            <?php
            foreach ($user_colors ?? [] as $group) { ?>
                <div class="group-item badge bg-info text-dark d-flex align-items-center" data-group='<?= json_encode((array)$group) ?>' >
                    <?= $group->name ?>
                    <button type="button" onclick="removeGroup(this)">x</button>
                    <input type="hidden" name="groups[]" value="<?= $group->id ?>">
                </div>
            <?php }
            ?>
        </div>
    </div>

    <button type="submit">
        <?php if ($user->id ?? false) {
            echo "Salvar";
        } else {
            echo "Criar";
        }
        ?>
    </button>
    <a href="index.php">Cancelar</a>
</form>

<script>
    function addGroup() {
        const select = document.getElementById('group-select');
        const group = select.value;
        console.log(group)
        data = JSON.parse(group);
        if (!group) return;

        const groupList = document.getElementById('group-list');

        // Evita adicionar duplicados
        if ([...groupList.querySelectorAll('.group-item')].some(el => el.dataset.group === group)) return;

        const div = document.createElement('div');
        div.className = 'group-item badge bg-info text-dark d-flex align-items-center';
        div.dataset.group = group;
        div.innerHTML = `
            ${data.name} <button type="button" onclick="removeGroup(this)">x</button>
            <input type="hidden" name="groups[]" value="${data.id}">
        `;
        groupList.appendChild(div);
    }

    function removeGroup(button) {
        button.parentElement.remove();
    }
</script>