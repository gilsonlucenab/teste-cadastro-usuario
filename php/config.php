<?php
header('Content-Type: application/json');

$dsn = 'pgsql:host=localhost;port=5432';
$dbname = 'Cadastro de Usuarios';
$user = 'postgres';
$password = 'touro1993';

$response = ['success' => false, 'message' => 'Ocorreu um erro desconhecido.'];

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    $errors = [];
    if (!$nome) $errors[] = 'O campo Nome é obrigatório.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Insira Email válido.';
    if (!$senha || strlen($senha) < 8) $errors[] = 'A Senha deve conter pelo menos 8 caracteres.';

    if ($errors) {
        $response['message'] = implode('<br>', $errors);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetchColumn() > 0) {
            $response['message'] = 'Email já cadastrado.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => password_hash($senha, PASSWORD_DEFAULT)
            ]);
            $response = ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
        }
    }
} catch (PDOException $e) {
    $response['message'] = 'Erro ao conectar com o banco de dados ou ao cadastrar usuário: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Erro: ' . $e->getMessage();
}

echo json_encode($response);
?>