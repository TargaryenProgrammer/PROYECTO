<?php
/**
 * Script para mostrar los detalles de los productos
 * Auto: Carlos Andrés Romero
 */
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($id == '' || $token == '') {
    echo 'Error al procesar la petición';
    exit;
} else {
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);
    if ($token == $token_tmp) {
        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id = ? AND activo = 1");
        $sql->execute([$id]);
        if ($sql->fetchColumn() > 0) {
            $sql = $con->prepare("SELECT id, nombre, descripcion, precio, descuento FROM productos WHERE id = ? AND activo = 1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $dir_images = 'images/productos/' . $id . '/';
            $rutaImg = $dir_images . 'principal.jpg';

            if (!file_exists($rutaImg)) {
                $rutaImg = 'images/no-photo.jpg';
            }

            $imagenes = array();
            if (file_exists($dir_images)) {
                $dir = dir($dir_images);
                while (($archivo = $dir->read()) != false) {
                    if ($archivo != 'principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))) {
                        $imagenes[] = $dir_images . $archivo;
                    }
                }
                $dir->close();
            }

            $sqlCaracter = $con->prepare("SELECT DISTINCT(det.id_caracteristica) AS idCat, cat.caracteristica FROM det_prod_caracter AS det INNER JOIN caracteristicas AS cat ON det.id_caracteristica=cat.id WHERE det.id_producto = ?");
            $sqlCaracter->execute([$id]);
        } else {
            echo 'Error al procesar la petición';
            exit;
        }
    }
}

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= TITLE; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column h-100">
    <!-- Contenido -->

    <?php include 'layout/menu.php'; ?>

    <main class="flex-shrink-0">
        <div class="album py-5 bg-body-tertiary flex-shrink-0">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <div class="col-md-5 order-md-1">
                        <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="<?php echo $rutaImg; ?>" class="d-block w-100">
                                </div>
                                <?php foreach ($imagenes as $img) { ?>
                                <div class="carousel-item">
                                    <img src="<?php echo $img; ?>" class="d-block w-100">
                                </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselImages"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7 order-md-2">
                        <h2><?php echo $nombre; ?></h2>
                        <input type="hidden" name="token"
                            value="<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>">
                        <?php if ($descuento > 0) { ?>
                        <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
                        <h2>
                            <?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?>
                            <small class="text-success"><?php echo $descuento; ?>% descuento</small>
                        </h2>
                        <?php } else { ?>
                        <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>
                        <?php } ?>
                        <p class="lead">
                            <?php echo $descripcion; ?>
                        </p>
                        <div class="col-3 my-3">
                            <?php
                            while ($row_cat = $sqlCaracter->fetch(PDO::FETCH_ASSOC)) {
                                $idCat = $row_cat['idCat'];
                                echo $row_cat['caracteristica'] . " : ";
                                echo "<select class='form-select' id='cat_$idCat'>";
                                $sqlDet = $con->prepare('SELECT id, valor, stock FROM det_prod_caracter WHERE id_producto=? AND id_caracteristica=?');
                                $sqlDet->execute([$id, $idCat]);
                                while ($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option id='" . $row_det['id'] . "'>" . $row_det['valor'] . "</option>";
                                }
                                echo "</select>";
                            }
                            ?>
                        </div>
                        <div class="col-3 my-3">
                            Cantidad: <input type="number" class="form-control" id="cantidad" name="cantidad" min="1"
                                max="10" value="1">
                        </div>
                        <div class="d-grid gap-3 col-7">
                            <button class="btn btn-primary btn-outline" type="button">Comprar ahora</button>
                            <button class="btn btn-secondary btn-outline" type="button"
                                onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">Agregar
                                al carrito</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
    // let btnAgregar = document.getElementById("btnAgregar")
    // let inputCantidad = document.getElementById("cantidad").value
    // btnAgregar.onclick = addProducto(</?php echo $id; ?>, inputCantidad, '</?php echo $token_tmp; ?>')

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