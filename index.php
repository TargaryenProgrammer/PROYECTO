<?php
/**
 * Script de la pantalla principal para mostrar el listado de productos de la tienda
 * Auto: Carlos Andrés Romero
 * GitHub: https://github.com/KrlsRomero/
 */
require_once 'config/config.php';
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, descripcion, precio FROM productos WHERE activo=1"); /** Sentencias preparadas */
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title><?= TITLE; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column h-100" data-bs-theme="light">

    <?php include 'layout/menu.php'; ?>

    <main class="flex-shrink-0">
        <div class="album py-5 bg-body-tertiary flex-shrink-0">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php foreach ($resultado as $row) { ?>
                    <div class="col mb-2">
                        <div class="card shadow-sm h-100">
                            <?php
                                $id = $row['id'];
                                $imagen = "images/productos/" . $id . "/principal.jpg";
                                if (!file_exists($imagen)) {
                                    $imagen = "images/no-photo.jpg";
                                }
                                ?>
                            <a
                                href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>"><img
                                    src="<?php echo $imagen; ?>" class="card-img-top"></a>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                                <p class="card-text">
                                    <strong>$<?php echo number_format($row['precio'], 2, ',', ','); ?></strong>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>"
                                            class="btn btn-sm btn-outline-primary">Detalles</a>
                                    </div>
                                    <a class="btn btn-sm btn-secondary btn-outline"
                                        onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">Agregar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

    <script>
    function addProducto(id, token) {
        let url = 'clases/carrito.php'
        let formData = new FormData()
        formData.append('id', id)
        formData.append('token', token)
        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    let elemento = document.getElementById('num_cart')
                    elemento.innerHTML = data.numero
                }
            })
    }
    </script>
    <?php include 'layout/footer.php'; ?>