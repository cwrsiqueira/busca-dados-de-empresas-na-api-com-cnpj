<?php
session_start();

$nome = '';
$cnpj = '';
$abertura = '';
$fantasia = '';
$porte = '';
$qsa = '';
$endereco = '';
$email = '';
$telefone = '';

$dados = filter_input_array(INPUT_POST);

if ($dados) {
    $cnpj = str_replace([".", "/", "-"], "", $dados['cnpj']);

    $url = "https://receitaws.com.br/v1/cnpj/{$cnpj}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    $response = (array)json_decode($response);

    if (!empty($response['status']) && $response['status'] === 'ERROR') {
        $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>" . $response['message'] . "!</div>";
    } else {
        if (!empty($response)) {
            $nome = $response['nome'];
            $cnpj = $response['cnpj'];
            $abertura = $response['abertura'];
            $fantasia = $response['fantasia'];
            $porte = $response['porte'];
            $qsa = $response['qsa'];
            $endereco = $response['logradouro'] . ', ' . $response['numero'] . ', ' . $response['complemento'] . ', ' . $response['municipio'] . ', ' . $response['bairro'] . ', ' . $response['uf'] . ', ' . $response['cep'];
            $email = $response['email'];
            $telefone = $response['telefone'];
        } else {
            $_SESSION['msg'] = "<div class='alert alert-warning' role='alert'>Aguarde alguns minutos para fazer nova requisição!</div>";
        }
    }

    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNPJ API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-3">
        <h3>CNPJ API</h3>

        <?php
        if (!empty($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        ?>

        <form method="post" class="row g-3 needs-validation" novalidate>
            <div class="col-md-4">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" name="cnpj" id="cnpj" class="form-control" required>
                <div class="invalid-feedback">O campo dever ser um CNPJ válido.</div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Buscar dados</button>
            </div>
        </form>

        <hr>

        <ul>
            <li>Nome: <?= $nome; ?></li>
            <li>CNPJ: <?= $cnpj; ?></li>
            <li>Data de Abertura: <?= $abertura; ?></li>
            <li>Nome de Fantasia: <?= $fantasia; ?></li>
            <li>Porte: <?= $porte; ?></li>
            <li>Quant. de Sócios: <?= !empty($qsa) ? count($qsa) : 0; ?></li>
            <li>Endereço: <?= $endereco; ?></li>
            <li>E-mail: <?= $email; ?></li>
            <li>Contatos: <?= $telefone; ?></li>
        </ul>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="./jquery.mask.min.js"></script>
    <script>
        $('#cnpj').mask('00.000.000/0000-00');

        var form = document.querySelector('form');
        var cnpj = document.querySelector('#cnpj');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (cnpj.value.length !== 18) {
                cnpj.classList.remove('is-valid');
                cnpj.classList.add('is-invalid');
            } else {
                this.submit();
            }
        })

        cnpj.addEventListener('keyup', function() {
            if (this.value.length === 18) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        })
    </script>
</body>

</html>