<?php

require_once '../config/config.php';
require_once '../layout/header.php';

if (!isset($_SESSION['user_type'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SESSION['user_type'] != 'admin') {
    header('Location: ../../index.php');
    exit;
}

$db = new Database();
$con = $db->conectar();

$id = $_GET['id'];

$sql = $con->prepare("SELECT id, nombre, descripcion, precio, descuento, stock, id_categoria FROM productos WHERE id = ? AND activo = 1");
$sql->execute([$id]);
$producto = $sql->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT id, nombre FROM categorias WHERE activo = 1";
$resultado = $con->query($sql);
$categorias = $resultado->fetchAll(PDO::FETCH_ASSOC);

$rutaImagenes = '../../images/productos/' . $id . '/';
$imagenPrincipal = $rutaImagenes . 'principal.jpg';
$imagenes = [];
$dirInit = dir($rutaImagenes);
while (($archivo = $dirInit->read()) !== false) {
    if ($archivo != 'principal.jpd' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))) {
        $image = $rutaImagenes . $archivo;
        $imagenes[] = $image;
    }
}
$dirInit->close();

$resultado = $con->query("SELECT id, nombre FROM c_tallas");
$tallas = $resultado->fetchAll(PDO::FETCH_ASSOC);

$resultado = $con->query("SELECT id, nombre FROM c_colores");
$colores = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
.ck-editor__editable[role="textbox"] {
    min-height: 250px;
}
</style>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<main>
    <div class="container-fluid px-4">
        <h2 class="mt-3">Modifica Producto</h2>
        <form action="actualiza.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>" />
            <div class=" mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" id="nombre"
                    value="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>" required autofocus />
            </div>
            <div class="col mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea type="text" class="form-control" name="descripcion" id="editor"
                    required><?php echo $producto['descripcion']; ?></textarea>
            </div>
            <div class="row mb-2">
                <div class="col col-md-6">
                    <label for="imagen_principal" class="form-label">Imagen principal:</label>
                    <input type="file" class="form-control" name="imagen_principal" id="imagen_principal"
                        accept="image/jpeg">
                </div>
                <div class="col col-md-6">
                    <label for="otras_imagenes" class="form-label">Otras imagenes:</label>
                    <input type="file" class="form-control" name="otras_imagenes[]" id="otras_imagenes"
                        accept="image/jpeg" multiple>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 col-md-6">
                    <?php if (file_exists($imagenPrincipal)) { ?>
                    <img src="<?php echo $imagenPrincipal . '?id=' . time(); ?>" alt="Imagen principal"
                        class="img-thumbnail my-3"><br>
                    <button class="btn btn-danger btn-sm"
                        onclick="eliminaImagen('<?php echo $imagenPrincipal; ?>');">Eliminar</button>
                    <?php } ?>
                </div>
                <div class="col-12 col-md-6">
                    <div class="row">
                        <?php foreach ($imagenes as $imagen) { ?>
                        <div class="col-4">
                            <img src="<?php echo $imagen . '?id=' . time();
                                ?>" alt="Otras imagenes" class="img-thumbnail my-3"><br>
                            <button class="btn btn-danger btn-sm"
                                onclick="eliminaImagen('<?php echo $imagen; ?>');">Eliminar</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="precio" class="form-label">Precio:</label>
                    <input type="number" class="form-control" name="precio" value="<?php echo $producto['precio']; ?>"
                        id="precio" aria-describedby="helpId" required />
                </div>
                <div class="col mb-3">
                    <label for="descuento" class="form-label">Descuento:</label>
                    <input type="text" class="form-control" name="descuento"
                        value="<?php echo $producto['descuento']; ?>" id="descuento" aria-describedby="helpId"
                        required />
                </div>
                <div class="col mb-3">
                    <label for="stock" class="form-label">Stock:</label>
                    <input type="text" class="form-control" name="stock" id="stock"
                        value="<?php echo $producto['stock']; ?>" aria-describedby="helpId" required />
                </div>
            </div>
            <div class="row">
                <div class="col-4 mb-3">
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select class="form-select" name="categoria" id="categoria" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($categorias as $categoria) { ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php if ($categoria['id'] == $producto['id_categoria'])
                                   echo 'selected'; ?>><?php echo $categoria['nombre']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12 mb-3">
                    <h4 class="me-4">Variantes</h4>
                    <button type="button" class="btn btn-success btn-sm" id="agrega-variante">+ Variante</button>
                </div>
            </div>
            <div id="conetenido">

            </div>
            <template id="pantilla_variante">
                <div class="row mb-3">
                    <div class="col">
                        <label for="plantilla" class="form-label">Talla:</label>
                        <select class="form-select" name="talla[]">
                            <option value="">Seleccionar</option>
                            <?php foreach ($tallas as $talla) { ?>
                            <option value="<?php echo $talla['id']; ?>" <?php if ($talla['id'] == $producto['nombre'])
                                   echo 'selected'; ?>><?php echo $talla['nombre']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col">
                        <label for="plantilla" class="form-label">Color:</label>
                        <select class="form-select" name="color[]">
                            <option value="">Seleccionar</option>
                            <?php foreach ($colores as $color) { ?>
                            <option value="<?php echo $color['id']; ?>" <?php if ($color['id'] == $producto['nombre'])
                                   echo 'selected'; ?>><?php echo $color['nombre']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col">
                        <label for="plantilla" class="form-label">Precio:</label>
                        <input type="text" class="form-control" name="precio_variante[]">
                    </div>
                    <div class="col">
                        <label for="plantilla" class="form-label">Stock:</label>
                        <input type="text" class="form-control" name="stock_variante[]">
                    </div>
                </div>
            </template>
            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>

    </div>
</main>
<script>
ClassicEditor
    .create(document.querySelector('#editor'))
    .catch(error => {
        console.error(error);
    });

function eliminaImagen(urlImagen) {
    let url = 'eliminar_imagen.php'
    let formData = new FormData()
    formData.append('urlImagen', urlImagen)
    fetch(url, {
        method: 'post',
        body: formData
    }).then((response) => {
        if (response.ok) {
            location.reload()
        }
    })
}

const btnVariante = document.getElementById('agrega-variante')
btnVariante.addEventListener('click', agregaVariante)

function agregaVariante() {
    const contenido = document.getElementById('conetenido')
    const plantilla = document.getElementById('pantilla_variante').content.cloneNode(true)

    contenido.appendChild(plantilla)
}
</script>
<?php require_once '../layout/footer.php'; ?>