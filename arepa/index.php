<?php
/*
* PANEL ADMINISTRATIVO DINÁMICO
* -------------------------------
* - Detecta categorías
* - Lista tablas por categoría
* - CRUD automático
* - Manejo correcto de conexión (evita errores 500)
*/

/* ======================================================
   1. CARGAR CONEXIÓN (una carpeta atrás)
====================================================== */

// ruta real garantizada
$conexion_path = realpath(__DIR__ . "/../conexion.php");

if (!$conexion_path) {
    die("❌ ERROR: No se pudo encontrar conexion.php (ruta incorrecta).");
}

require __DIR__ . "/../conexion.php";

// verificar conexión mysqli
if (!isset($conn) || !$conn instanceof mysqli) {
    die("❌ ERROR: La variable \$conn no está definida en conexion.php");
}

if ($conn->connect_error) {
    die("❌ ERROR de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


/* ======================================================
   2. OBTENER CATEGORÍAS
====================================================== */

$cats = [];
$qc = $conn->query("SELECT DISTINCT categoria FROM tabla_categorias ORDER BY categoria");

if ($qc) {
    while ($r = $qc->fetch_assoc()) {
        $cats[] = $r['categoria'];
    }
}

$categoria_sel = $_GET['cat'] ?? null;


/* ======================================================
   3. OBTENER TABLAS POR CATEGORÍA
====================================================== */

$tablas = [];

if ($categoria_sel) {

    $categoria_safe = $conn->real_escape_string($categoria_sel);
    $qt = $conn->query("SELECT tabla FROM tabla_categorias WHERE categoria='$categoria_safe'");

    while ($r = $qt->fetch_assoc()) {
        $tablas[] = $r['tabla'];
    }

} else {

    // todas las tablas
    $qt = $conn->query("SHOW TABLES");
    while ($r = $qt->fetch_array()) {
        $tablas[] = $r[0];
    }
}

$tabla_sel = $_GET['tabla'] ?? null;


/* ======================================================
   4. CRUD DINÁMICO
====================================================== */

if ($tabla_sel) {

    $tabla_safe = $conn->real_escape_string($tabla_sel);

    /* ---------- INSERTAR ---------- */
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {

        $cols = [];
        $vals = [];

        foreach ($_POST as $col => $val) {
            if ($col == 'accion') continue;
            $cols[] = $col;
            $vals[] = "'" . $conn->real_escape_string($val) . "'";
        }

        if ($cols) {
            $conn->query("INSERT INTO $tabla_safe (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")");
        }
    }

    /* ---------- ELIMINAR ---------- */
    if (isset($_GET['del'])) {

        $pk = $conn->real_escape_string($_GET['pk']);
        $id = intval($_GET['del']);

        $conn->query("DELETE FROM $tabla_safe WHERE $pk=$id");
    }

    /* ---------- EDITAR ---------- */
    if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {

        $pk = $_POST['pk'];
        $pkvalue = intval($_POST[$pk]);

        $sets = [];
        foreach ($_POST as $col => $val) {
            if (in_array($col, ["accion", "pk", $pk])) continue;
            $sets[] = $col . "='" . $conn->real_escape_string($val) . "'";
        }

        if ($sets) {
            $conn->query("UPDATE $tabla_safe SET " . implode(",", $sets) . " WHERE $pk=$pkvalue");
        }
    }

    /* ======================================================
       5. OBTENER COLUMNAS Y DATOS DE LA TABLA
    ======================================================= */

    $columnas = [];
    $resCol = $conn->query("SHOW COLUMNS FROM $tabla_safe");

    while ($r = $resCol->fetch_assoc()) {
        $columnas[] = $r;
    }

    // obtener clave primaria
    $pk_column = null;
    foreach ($columnas as $c) {
        if ($c['Key'] == 'PRI') {
            $pk_column = $c['Field'];
            break;
        }
    }

    $datos = $conn->query("SELECT * FROM $tabla_safe");
}










/* ======================================================
   3. OBTENER TABLAS POR CATEGORÍA
====================================================== */
/* ======================================================
   ADMINISTRACIÓN DE CATEGORÍAS (MODAL)
====================================================== */

if (isset($_POST['admin_categorias'])) {

    // AGREGAR CATEGORÍA
    if ($_POST['admin_categorias'] == 'agregar_categoria') {
        $cat = $conn->real_escape_string($_POST['categoria']);
        $conn->query("INSERT INTO tabla_categorias (categoria, tabla) VALUES ('$cat', '')");
    }

    // EDITAR CATEGORÍA
    if ($_POST['admin_categorias'] == 'editar_categoria') {
        $old = $conn->real_escape_string($_POST['categoria_old']);
        $new = $conn->real_escape_string($_POST['categoria_new']);
        $conn->query("UPDATE tabla_categorias SET categoria='$new' WHERE categoria='$old'");
    }

    // ASIGNAR TABLA A UNA CATEGORÍA
    if ($_POST['admin_categorias'] == 'asignar_tabla') {
        $cat = $conn->real_escape_string($_POST['categoria']);
        $tabla = $conn->real_escape_string($_POST['tabla']);
        $conn->query("INSERT INTO tabla_categorias (categoria, tabla) VALUES ('$cat', '$tabla')");
    }

    echo "<script>location.href='index.php';</script>";
    exit;
}

// ELIMINAR CATEGORÍA COMPLETA
if (isset($_GET['del_categoria'])) {
    $cat = $conn->real_escape_string($_GET['del_categoria']);
    $conn->query("DELETE FROM tabla_categorias WHERE categoria='$cat'");
    echo "<script>location.href='index.php';</script>";
    exit;
}

// QUITAR ASIGNACIÓN DE TABLA
if (isset($_GET['quitar_tabla']) && isset($_GET['cat'])) {
    $tabla = $conn->real_escape_string($_GET['quitar_tabla']);
    $cat = $conn->real_escape_string($_GET['cat']);

    $conn->query("DELETE FROM tabla_categorias WHERE categoria='$cat' AND tabla='$tabla'");

    echo "<script>location.href='index.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi Cuenta</title>
<title>Panel Administrativo</title>

<link rel="stylesheet" href="estilo.css"> <!-- Reutiliza tu CSS existente -->

<style>
    /* =========================================================
   FUENTES Y ESTILOS GENERALES (del diseño Lista.css)
   ========================================================= */
    @import url('https://fonts.googleapis.com/css2?family=Sansation&family=Oleo+Script&display=swap');

    body {
        font-family: 'Sansation', sans-serif;
        margin: 0;
        padding: 0;
        /**background: #000;/**/
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        overflow-y: scroll;
        scrollbar-width: none;
    }

    body::-webkit-scrollbar {
        display: none;
    }

    /* Fondo estilo Lista.css */
    body::after {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top;
        opacity: 0.6;
        z-index: -1;
    }

    /* Contenedor principal */
    .container {
        width: 100%;
        max-width: 1200px;
        margin: 40px auto;
        padding: 15px;
        color: #EAE4D5;
    }

    /* =========================================================
    TITULOS
    ========================================================= */
    h2, h3, h4 {
        font-family: 'Oleo Script', cursive;
        text-align: center;
        color: #2a3e42;
    }

    h3, h4 {
        margin-top: 20px;
    }

    /* =========================================================
    SELECT DE CATEGORÍA/TABLA
    ========================================================= */
    .selector {
        width: 90%;
        margin: 10px auto;
        display: flex;
        justify-content: center;
    }

    .selector select {
        padding: 12px 16px;
        font-size: 15px;
        border-radius: 10px;
        /**background-color: #637983;/**/
        color: #EAE4D5;
        min-width: 240px;

        border: none;
        appearance: none;
        background-image: url('https://directorioasambleasvzla.com/iconos/Barrita.png');
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
    }

    .selector select:focus {
        outline: none;
    }

    /* =========================================================
    FORM AGREGAR
    ========================================================= */
    .form-box {
        background-color: rgba(25, 46, 47, 0.75);
        padding: 20px;
        border-radius: 12px;
        width: 350px;
        margin: 20px auto;
        box-shadow: 0 0 8px rgba(0,0,0,0.4);
    }

    .form-box label {
        color: #EAE4D5;
        font-size: 14px;
    }

    .form-box input, 
    .form-box select {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        margin-bottom: 12px;
        background: #637983;
        color: #EAE4D5;
        border-radius: 8px;
        border: none;
    }

    .form-box button {
        width: 100%;
        background: #637983;
        color: #EAE4D5;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
    }

    .form-box button:hover {
        background: #A2B0BE;
    }

    /* =========================================================
    TABLA CON CONTENEDOR
    ========================================================= */
    .tabla-scroll {
        max-height: 400px;
        overflow-y: scroll;
        scrollbar-width: none;
        margin-top: 20px;
        border-radius: 12px;
        border: 3px solid #637983;
        background-color: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
    }

    .tabla-scroll::-webkit-scrollbar {
        display: none;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    th {
        background: #637983;
        color: #EAE4D5;
        padding: 10px;
        font-size: 17px;
        position: sticky;
        top: 0;
    }

    td {
        background: rgba(255,255,255,0.4);
        color: #192E2F;
        padding: 8px;
        border-bottom: 1px solid #637983;
    }

    input[type=text] {
        padding: 8px;
        border-radius: 6px;
        width: 120px;
        border: none;
        background: #EAE4D5;
    }

    /* =========================================================
    BOTONES
    ======================================================== */
    button {
        font-family: 'Sansation';
    }

    .btn-delete {
        background-color: #c02020;
        padding: 6px 10px;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-delete:hover {
        background-color: #ff4040;
    }

    /* =========================================================
    RESPONSIVE
    ========================================================= */
    @media (max-width: 768px) {
        .form-box {
            width: 90%;
        }

        table input[type=text] {
            width: 90px;
        }
    }

    @media (max-width: 480px) {
        th, td {
            font-size: 12px;
            padding: 4px;
        }

        table input[type=text] {
            width: 70px;
        }

        .selector select {
            min-width: 180px;
        }
    }






    /* =========================================================
    MODAL – CONTENEDOR GENERAL
    ========================================================= */

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.65);
        display: none;
        padding-top: 60px;
        z-index: 999;
    }

    /* Caja interna del modal */
    .modal-content {
        background: white;
        width: 85%;
        max-width: 900px;
        margin: auto;
        padding: 20px;
        border-radius: 12px;
        max-height: 85vh;   /* evita que sea enorme */
        overflow: hidden;   /* importante */
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    }

    /* Botón cerrar */
    .modal-close {
        float: right;
        background: #c02020;
        border: none;
        color: white;
        padding: 6px 10px;
        cursor: pointer;
        border-radius: 6px;
    }
    .modal-close:hover {
        background: #ff4040;
    }

    /* =========================================================
    TABLA DENTRO DEL MODAL CON SCROLL
    ========================================================= */

    .modal-table-scroll {
        flex-grow: 1;
        overflow-y: auto;
        scrollbar-width: none;
        margin-top: 15px;
        padding-right: 5px;
    }

    .modal-table-scroll::-webkit-scrollbar {
        display: none;
    }

</style>
</head>
<body>

<h2>Panel Administrativo</h2>

<!-- Modificar la categoría -->
<button onclick="document.getElementById('modalCategorias').style.display='block'">
    Administrar Categorías
</button>

<!-- Selección de categoría -->
<form method="GET">
    <label>Categoría: </label>
    <select name="cat" onchange="this.form.submit()">
        <option value="">-- Todas --</option>
        <?php foreach ($cats as $c): ?>
            <option value="<?= $c ?>" <?= ($categoria_sel==$c?"selected":"") ?>><?= $c ?></option>
        <?php endforeach; ?>
    </select>
</form>

<!-- Selección de tabla -->
<?php if ($tablas): ?>
<form method="GET">
    <input type="hidden" name="cat" value="<?= $categoria_sel ?>">
    <label>Tabla: </label>
    <select name="tabla" onchange="this.form.submit()">
        <option value="">-- Seleccione tabla --</option>
        <?php foreach ($tablas as $t): ?>
            <option value="<?= $t ?>" <?= ($tabla_sel==$t?"selected":"") ?>><?= $t ?></option>
        <?php endforeach; ?>
    </select>
</form>
<?php endif; ?>


<!-- CRUD dinámico -->
<?php if ($tabla_sel && $columnas): ?>

<h3>Tabla: <?= $tabla_sel ?></h3>

<!-- Agregar registro -->
<h4>Agregar</h4>
<form method="POST">
    <input type="hidden" name="accion" value="agregar">
    <?php foreach ($columnas as $c): ?>
        <?php if ($c['Key'] == 'PRI') continue; ?>
        <?= $c['Field'] ?>: <input name="<?= $c['Field'] ?>"><br>
    <?php endforeach; ?>
    <button>Guardar</button>
</form>

<br>

<!-- Listado -->
<div class="table-wrap">
<table>
<tr>
    <?php foreach ($columnas as $c): ?>
        <th><?= $c['Field'] ?></th>
    <?php endforeach; ?>
    <th>Acciones</th>
</tr>

<?php while ($row = $datos->fetch_assoc()): ?>
<tr>
<form method="POST">
    <?php foreach ($columnas as $c): ?>
        <td>
        <?php if ($c['Key'] == 'PRI'): ?>
            <input type="hidden" name="pk" value="<?= $c['Field'] ?>">
            <input type="hidden" name="<?= $c['Field'] ?>" value="<?= $row[$c['Field']] ?>">
            <?= $row[$c['Field']] ?>
        <?php else: ?>
            <input name="<?= $c['Field'] ?>" value="<?= $row[$c['Field']] ?>">
        <?php endif; ?>
        </td>
    <?php endforeach; ?>

    <td>
        <button name="accion" value="editar">✏️</button>
        <a href="?cat=<?= $categoria_sel ?>&tabla=<?= $tabla_sel ?>&pk=<?= $pk_column ?>&del=<?= $row[$pk_column] ?>" onclick="return confirm('¿Eliminar?')">🗑️</a>
    </td>
</form>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php endif; ?>







<!-- ===========================
     MODAL CRUD DE CATEGORÍAS
=========================== -->
<div id="modalCategorias" class="modal">
    <div class="modal-content">

        <h2>Administrar Categorías</h2>

        <!-- Cerrar modal -->
        <button style="float:right;" onclick="document.getElementById('modalCategorias').style.display='none'">
            ✖
        </button>

        <!-- ===========================
             AGREGAR NUEVA CATEGORÍA
        ============================ -->
        <h3>Agregar Categoría</h3>
        <form method="POST">
            <input type="hidden" name="admin_categorias" value="agregar_categoria">
            <input name="categoria" placeholder="Nombre de la categoría" required>
            <button>Guardar</button>
        </form>

        <hr>

        <!-- ===========================
             LISTADO DE CATEGORÍAS
        ============================ -->
        <h3>Categorías Existentes</h3>
        <div class="modal-table-scroll">
            <table border="1" cellpadding="5" width="100%">

                <tr>
                    <th>Categoría</th>
                    <th>Tablas asignadas</th>
                    <th>Acciones</th>
                </tr>

                <?php
                $catsFull = $conn->query("SELECT DISTINCT categoria FROM tabla_categorias ORDER BY categoria");
                ?>

                <?php while($c = $catsFull->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['categoria'] ?></td>

                    <!-- TABLAS ASIGNADAS -->
                    <td>
                        <?php
                        $catname = $conn->real_escape_string($c['categoria']);
                        $tt = $conn->query("SELECT tabla FROM tabla_categorias WHERE categoria='$catname'");
                        while($t = $tt->fetch_assoc()){
                            echo "<div style='margin-bottom:4px;'>".$t['tabla'].
                                " <a href='?quitar_tabla={$t['tabla']}&cat={$c['categoria']}'>[Quitar]</a></div>";
                        }
                        ?>
                    </td>

                    <td>
                        <!-- EDITAR NOMBRE -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="admin_categorias" value="editar_categoria">
                            <input type="hidden" name="categoria_old" value="<?= $c['categoria'] ?>">
                            <input name="categoria_new" value="<?= $c['categoria'] ?>" required>
                            <button>✏️</button>
                        </form>

                        <!-- ELIMINAR CATEGORÍA -->
                        <a href="?del_categoria=<?= $c['categoria'] ?>"
                        onclick="return confirm('¿Eliminar categoría y sus asignaciones?')">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>


        <hr>

        <!-- ===========================
             ASIGNAR TABLAS A CATEGORÍA
        ============================ -->
        <h3>Asignar tabla a categoría</h3>
        <form method="POST">
            <input type="hidden" name="admin_categorias" value="asignar_tabla">

            <!-- CATEGORÍA -->
            <label>Categoría:</label>
            <select name="categoria" required>
                <?php foreach ($cats as $c): ?>
                    <option><?= $c ?></option>
                <?php endforeach; ?>
            </select>

            <!-- TABLA -->
            <label>Tabla:</label>
            <select name="tabla" required>
                <?php
                $allT = $conn->query("SHOW TABLES");
                while($t = $allT->fetch_array()){
                    echo "<option>".$t[0]."</option>";
                }
                ?>
            </select>

            <button>Asignar</button>
        </form>

    </div>
</div>


</body>
</html>
