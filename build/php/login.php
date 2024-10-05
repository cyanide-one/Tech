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

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

// Verificar las credenciales
$sql = "SELECT * FROM administrador WHERE nombre='$nombre' AND contraseña='$contraseña'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si las credenciales son correctas, redirigir a home.html
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['id']; // Guardar el ID del usuario
    echo "<script>
            alert('Inicio de sesión exitoso');
            window.location.href = '../home.html';  // Redirigir a la página de inicio
          </script>";
} else {
    // Si las credenciales son incorrectas, mostrar un mensaje de error
    echo "<script>
            alert('Correo o contraseña incorrectos');
            window.location.href = '../index.html';  // Redirigir de nuevo a la página de login
          </script>";
}

// Cerrar la conexión
$conn->close();
?>
