<?php
if (!isset($_SESSION)) { session_start(); }
if(empty($_SESSION['SESSION_USER']) && empty($_SESSION['SESSION_ID'])){
    header('location:../../login/');
    exit;
} else {
    require_once'../../../library/sw-config.php';
    require_once'../../login/login_session.php';
    include('../../../library/sw-function.php'); 

    switch (@$_GET['action']){
        case 'add':
            function acakangkahuruf($panjang){
                $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                $string = '';
                for ($i = 0; $i < $panjang; $i++) {
                    $pos = rand(0, strlen($karakter)-1);
                    $string .= $karakter[$pos]; // Mengganti kurung kurawal dengan kurung siku
                }
                return $string;
            }
            $code   =  'SW'.acakangkahuruf(3).'/'.$year.'';

            $error = array();

            if (empty($_POST['name'])) {
                $error[] = 'tidak boleh kosong';
            } else {
                $name = mysqli_real_escape_string($connection, $_POST['name']);
            }

            if (empty($_POST['address'])) {
                $error[] = 'tidak boleh kosong';
            } else {
                $address = mysqli_real_escape_string($connection, $_POST['address']);
            }

            if (empty($error)) { 
                $add = "INSERT INTO building (code, name, address, building_scanner) values('$code', '$name', '$address', '')"; 
                if($connection->query($add) === false) { 
                    die($connection->error.__LINE__); 
                    echo 'Data tidak berhasil disimpan!';
                } else {
                    echo 'success';
                }
            } else {           
                echo 'Bidang inputan masih ada yang kosong..!';
            }
            break;

        /* ------------------------------
            Update
        ---------------------------------*/
        case 'update':
            $error = array();
            if (empty($_POST['id'])) {
                $error[] = 'ID tidak boleh kosong';
            } else {
                $id = mysqli_real_escape_string($connection, $_POST['id']);
            }

            if (empty($_POST['name'])) {
                $error[] = 'tidak boleh kosong';
            } else {
                $name = mysqli_real_escape_string($connection, $_POST['name']);
            }

            if (empty($_POST['address'])) {
                $error[] = 'tidak boleh kosong';
            } else {
                $address = mysqli_real_escape_string($connection, $_POST['address']);
            }

            if (empty($error)) { 
                $update = "UPDATE building SET name='$name', address='$address' WHERE building_id='$id'"; 
                if($connection->query($update) === false) { 
                    die($connection->error.__LINE__); 
                    echo 'Data tidak berhasil disimpan!';
                } else {
                    echo 'success';
                }
            } else {           
                echo 'Bidang inputan tidak boleh ada yang kosong..!';
            }
            break;

        /* --------------- Delete ------------*/
        case 'delete':
          $id = mysqli_real_escape_string($connection, epm_decode($_POST['id']));
          echo "ID yang akan dihapus: $id<br>";
      
          // Query untuk memeriksa apakah kantor digunakan oleh karyawan
          $query = "SELECT building.building_id 
                    FROM building 
                    LEFT JOIN employees ON building.building_id = employees.building_id 
                    WHERE building.building_id = '$id'";
          $result = $connection->query($query);
      
          // Debugging: Tampilkan hasil query dan jumlah baris yang ditemukan
          if ($result) {
              echo "Jumlah baris yang ditemukan: " . $result->num_rows . "<br>";
              while ($row = $result->fetch_assoc()) {
                  echo "Ditemukan building_id: " . $row['building_id'] . "<br>";
              }
          } else {
              echo "Kesalahan pada query: " . $connection->error . "<br>";
          }
      
          // Jika tidak ada karyawan yang terkait dengan kantor, lanjutkan penghapusan
          if ($result && $result->num_rows == 0) {
              $deleted = "DELETE FROM building WHERE building_id='$id'";
              if ($connection->query($deleted) === true) {
                  echo 'success';
              } else {
                  echo 'Data tidak berhasil dihapus.!<br>';
                  echo 'Kesalahan: ' . $connection->error . "<br>";
              }
          } else {
              // Debugging: Tampilkan informasi karyawan yang terkait
              echo 'Lokasi digunakan, Data tidak dapat dihapus.!<br>';
              $queryEmployees = "SELECT * FROM employees WHERE building_id = '$id'";
              $resultEmployees = $connection->query($queryEmployees);
              if ($resultEmployees) {
                  while ($rowEmployees = $resultEmployees->fetch_assoc()) {
                      echo "Karyawan terkait: ID karyawan: " . $rowEmployees['id'] . ", Nama: " . $rowEmployees['name'] . "<br>";
                  }
              } else {
                  echo "Kesalahan pada query untuk karyawan: " . $connection->error . "<br>";
              }
          }
          break;
      
    }
}
?>
