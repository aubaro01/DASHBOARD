<?php 

function checkCredentials($db, $email, $password)
{
    $sql = "SELECT * FROM user WHERE USER_email = ? AND USER_pass = ?";
    $args = [$email, $password];
    $result = $db->send2db($sql, $args);

    if ($result->num_rows > 0) {
        return true;
    }

    return false;
}

// Procura do produto 

function procuraProdutos($db, $procura){
    $sql = "SELECT * FROM produto WHERE produtoNAME = ?";
    $args = [$procura];
    $result = $db->send2db($sql, $args);

    $produtos = [];
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }

    return $produtos;
}

function PostEnvio($db){

}

//Tabela para ver os produtos

function GETABProdutos($db)
{
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $Produto = isset($_GET['produto']) ? $_GET['produto_id'] : '';

    $sql = "SELECT * FROM produto WHERE (produto_fk LIKE ?) AND ((tipocomponente_fk = ?) OR (? = ''))";
    $params = array("%$search%", $Produto, $Produto);
    $r = $db->send2db($sql, $params);

    if ($r->num_rows > 0) {
        echo '<div class="pc-list">';
        $counter = 0;
        while ($row = $r->fetch_assoc()) {
            $id = $row['cod_produto_pk'];
            if ($counter % 5 == 0) {
                echo '<div class="pc-row">';
            }
            echo '<div class="pc-item">';
            echo '<a href="detalhes.php?id=' . $id . '">';
            echo '<div class="name">' . $row['nome'] . '</div>';
            echo '<div class="name">' . $row['preco'] . '€ </div>';
            echo '<div class="name">' . $row['tipocomponente_fk'] . '</div>';
            echo '</a>';
            echo '</div>';
            if (($counter + 1) % 5 == 0 || ($counter + 1) == $r->num_rows) {
                echo '</div>';
            }
            $counter++;
        }
        echo '</div>';
    } else {
        echo "resultado vazio";
    }

    return $r->num_rows;
}

?>