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

// Datos del profesor
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$cedula = $_POST['cedula'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$grado = $_POST['grado']; // Asumimos que estos vienen del formulario
$turno = $_POST['turno'];
$materia = $_POST['materia']; // Asumimos que esto viene del formulario

// Insertar los datos del profesor
$sql_profesor = "INSERT INTO profesor (nombre, apellido, cedula, telefono, email) VALUES (?, ?, ?, ?, ?)";
$stmt_profesor = $conn->prepare($sql_profesor);
$stmt_profesor->bind_param('sssss', $nombre, $apellido, $cedula, $telefono, $email);

if ($stmt_profesor->execute()) {
    // Obtener el id_profesor generado
    $id_profesor = $conn->insert_id;

    // Consulta para obtener el id_grado
    $sql_grado = "SELECT id_grado FROM grado WHERE nombre = ? AND turno = ?";
    $stmt_grado = $conn->prepare($sql_grado);
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
        exit; // Termina el script si no se encontró el grado
    }

    // Insertar en la tabla grado_profesor
    $sql_grado_profesor = "INSERT INTO grado_profesor (id_grado, id_profesor) VALUES (?, ?)";
    $stmt_grado_profesor = $conn->prepare($sql_grado_profesor);
    $stmt_grado_profesor->bind_param('ii', $id_grado, $id_profesor);

    if ($stmt_grado_profesor->execute()) {
        // Obtener el id_materia de la materia seleccionada
        $sql_materia = "SELECT id_materia FROM materia WHERE nombre_materia = ?";
        $stmt_materia = $conn->prepare($sql_materia);
        $stmt_materia->bind_param('s', $materia);
        $stmt_materia->execute();
        $resultado_materia = $stmt_materia->get_result();
        
        // Obtener el id_materia
        if ($resultado_materia->num_rows > 0) {
            $fila_materia = $resultado_materia->fetch_assoc();
            $id_materia = $fila_materia['id_materia'];

            // Insertar en la tabla profesor_materia
            $sql_profesor_materia = "INSERT INTO profesor_materia (id_profesor, id_materia) VALUES (?, ?)";
            $stmt_profesor_materia = $conn->prepare($sql_profesor_materia);
            $stmt_profesor_materia->bind_param('ii', $id_profesor, $id_materia);

            if ($stmt_profesor_materia->execute()) {
                // Insertar en la tabla grado_materia
                $sql_grado_materia = "INSERT INTO grado_materia (id_grado, id_materia) VALUES (?, ?)";
                $stmt_grado_materia = $conn->prepare($sql_grado_materia);
                $stmt_grado_materia->bind_param('ii', $id_grado, $id_materia);

                if ($stmt_grado_materia->execute()) {
                    echo "<script>
                            alert('Registro exitoso');
                            window.location.href = '../home.html';  // Redirigir a '../home.html'
                          </script>";
                } else {
                    echo "Error al registrar en grado_materia: " . $conn->error;
                }
            } else {
                echo "Error al registrar en profesor_materia: " . $conn->error;
            }
        } else {
            echo "No se encontró la materia especificada.";
        }
    } else {
        echo "Error al registrar en grado_profesor: " . $conn->error;
    }
} else {
    echo "Error al registrar el profesor: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
