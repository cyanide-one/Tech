<?php
session_start();

// Conectar a la base de datos
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "escuela";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Datos del Alumno
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$f_nacimiento = $_POST['f_nacimiento'];
$cedula = $_POST['cedula'];
$cedula_tutor = $_POST['cedula_tutor'];
$grado = $_POST['grado']; // Asumimos que estos vienen del formulario
$turno = $_POST['turno'];

// Verificar si el CI del tutor existe en la tabla de tutores
$consulta_tutor = "SELECT id_tutor FROM tutor WHERE ci = ?";
$stmt_tutor = $conn->prepare($consulta_tutor);
$stmt_tutor->bind_param('s', $cedula_tutor);
$stmt_tutor->execute();
$resultado_tutor = $stmt_tutor->get_result();

if ($resultado_tutor->num_rows > 0) {
    // Si el tutor ya existe, obtener el ID del tutor
    $tutor = $resultado_tutor->fetch_assoc();
    $id_tutor = $tutor['id_tutor'];

    // Insertar los datos del Alumno con el ID del tutor
    $sql_alumno = "INSERT INTO alumno (nombre, apellido, ci, f_nacimiento, id_tutor) VALUES (?, ?, ?, ?, ?)";
    $stmt_alumno = $conn->prepare($sql_alumno);
    $stmt_alumno->bind_param('ssssi', $nombre, $apellido, $cedula, $f_nacimiento, $id_tutor);

    if ($stmt_alumno->execute()) {
        // Obtener el id_alumno generado
        $id_alumno = $conn->insert_id;

        // Consulta para obtener el id_grado
        $sql_grado = "SELECT id_grado FROM grado WHERE nombre = ? AND turno = ?";
        $stmt_grado = $conn->prepare($sql_grado);
        
        // Verifica que $grado y $turno no sean nulos o vacíos
        if (!empty($grado) && !empty($turno)) {
            $stmt_grado->bind_param('ss', $grado, $turno);
            $stmt_grado->execute();
            $resultado_grado = $stmt_grado->get_result();

            // Inicializar la variable id_grado
            $id_grado = null;

            if ($resultado_grado->num_rows > 0) {
                // Obtener el id_grado del resultado de la consulta
                $fila = $resultado_grado->fetch_assoc();
                $id_grado = $fila['id_grado'];
            } else {
                echo "No se encontró el grado especificado.";
                exit;  // Termina el script si no se encontró el grado
            }
        } else {
            echo "Grado o turno no pueden estar vacíos.";
            exit;
        }

        // Asegúrate de que id_grado no sea null antes de insertar
        if ($id_grado !== null) {
            // Insertar en la tabla alumno_grado con la fecha actual
            $sql_alumno_grado = "INSERT INTO alumno_grado (id_alumno, id_grado, f_inicio) VALUES (?, ?, NOW())";
            $stmt_alumno_grado = $conn->prepare($sql_alumno_grado);
            $stmt_alumno_grado->bind_param('ii', $id_alumno, $id_grado);

            if ($stmt_alumno_grado->execute()) {
                echo "<script>
                alert('Registro exitoso');
                window.location.href = '../home.html';  // Redirigir a '../home.html'
                </script>";
            } else {
                echo "Error al registrar el grado del alumno: " . $conn->error;
            }
        }
    } else {
        echo "Error al registrar el alumno: " . $conn->error;
    }
} else {
    // Si el tutor no existe, mostrar alerta
    echo "<script>alert('El tutor con CI ingresado no existe'); window.history.back();</script>";
}

// Cerrar la conexión
$conn->close();
?>
