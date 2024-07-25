<?php 
if ($mod == '') {
    header('location:../404');
    echo 'kosong';
} else {
    include_once 'mod/sw-header.php';

    if (!isset($_COOKIE['COOKIES_MEMBER'])) {
        
        // Fungsi sanitasi input
        function sanitizeInput($data) {
            return htmlspecialchars(strip_tags(trim($data)));
        }

        // Fungsi validasi input
        function validateInput($email, $password) {
            if (empty($email) || empty($password)) {
                return false;
            }
            return true;
        }

        // Proses login jika form dikirim
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitasi input
            $email = sanitizeInput($_POST['email']);
            $password = sanitizeInput($_POST['password']);

            // Validasi input
            if (validateInput($email, $password)) {
                // Menggunakan prepared statements untuk query database
                $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                // Cek password
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        // Password cocok, set session
                        $_SESSION['SESSION_USER'] = $email;
                        header('Location: ../admin/');
                        exit;
                    } else {
                        echo "Password salah";
                    }
                } else {
                    echo "Email tidak ditemukan";
                }
            } else {
                echo "Email dan password tidak boleh kosong";
            }
        }

        echo '
        <!-- App Capsule -->
        <div id="appCapsule">
            <div style="background:#00B4FF;border-radius:30px;margin:0 16px;padding:10px 15px" class="section text-center">
                <h1 style="color:#FFFFFF;font-size:24px;"><i class="fa fa-user"></i> Login</h1>
                <img src="'.$site_url.'/content/'.$site_logo.'" height="70">
                <h4 style="color:#FFFFFF;">Masukkan email dan password Anda untuk login ke sistem</h4>
            </div>
            <div class="section mb-5 p-2">
                <form id="form-login" method="POST" action="">
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="form-group basic">
                                <div class="input-wrapper">
                                    <label class="label" for="email1">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan E-mail" required>
                                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                                </div>
                            </div>
            
                            <div class="form-group basic">
                                <div class="input-wrapper">
                                    <label class="label" for="password1">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-links mt-2">
                        <div>
                            <a class="btn btn-primary" href="registrasi"><i class="fa fa-user-plus"></i> Register</a>
                        </div>
                        <div>
                            <a class="btn btn-danger" href="forgot"><i class="fa fa-key"></i> Reset Password</a>
                        </div>
                    </div>

                    <div class="form-button-group transparent">
                       <button type="submit" class="btn btn-success btn-block"><ion-icon name="log-in"></ion-icon> Login</button>
                       <a href="oauth/google" class="btn btn-warning btn-block"><ion-icon name="logo-google"></ion-icon> Login with Google</a>
                    </div>

                </form>
            </div>

        </div>
        <!-- * App Capsule -->';
    } else {
        // Tambahkan tindakan yang diperlukan jika pengguna sudah login
    }

    include_once 'mod/sw-footer.php';
}
?>
