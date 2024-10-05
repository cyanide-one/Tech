<?php
session_start();
// Conectar a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto si tu usuario es diferente
$password = "";     // Cambia esto si tienes una contraseña
$dbname = "escuela";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Datos del Tutor
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$cedula = $_POST['cedula'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];

// Insertar los datos del tutor
$sql_tutor = "INSERT INTO tutor (nombre,apellido, ci, telefono, email) VALUES ('$nombre','$apellido', '$cedula', '$telefono', '$email')";
if ($conn->query($sql_tutor) === TRUE) {
    echo "<script>
            alert('Registro exitoso');
            window.location.href = '../home.html';  // Redirigir a '../home.html'
          </script>";
    // Obtener el id_tutor generado
    $id_tutor = $conn->insert_id;
} else {
    echo "Error al registrar el tutor: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
