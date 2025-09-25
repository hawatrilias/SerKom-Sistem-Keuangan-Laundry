<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "laundry_db";
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal:" . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["tambah"])) {
        $id_pelanggan = $_POST["id_pelanggan"];
        $id_jenis = $_POST["id_jenis"];
        $harga = $_POST["harga"];
        $jumlah = $_POST["jumlah"];
        $total = $_POST["total"];
        $tanggal_terima = date('Y-m-d');
        $tanggal_selesai = date('Y-m-d', strtotime('+3 days'));

        $query = "INSERT INTO transaksi(id_pelanggan, id_jenis, tanggal_terima, tanggal_selesai, harga, jumlah, total)
                VALUES ('$id_pelanggan', '$id_jenis', '$tanggal_terima', '$tanggal_selesai', '$harga', '$jumlah', '$total')";
        mysqli_query($conn, $query);
        header("Location: index.php");
    }
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $id_pelanggan = $_POST['id_pelanggan'];
        $id_jenis = $_POST['id_jenis'];
        $harga = $_POST['harga'];
        $jumlah = $_POST['jumlah'];
        $total = $_POST['total'];

        $query = "UPDATE transaksi SET
                id_pelanggan = '$id_pelanggan',
                id_jenis = '$id_jenis',
                harga = '$harga',
                jumlah = '$jumlah',
                total = '$total'
                WHERE id_transaksi = '$id'";
        mysqli_query($conn, $query);
        header("Location: index.php");
    }
    if (isset($_POST['hapus'])) {
        $id = $_POST['id'];
        $query = "DELETE FROM transaksi WHERE id_transaksi = '$id'";
        mysqli_query($conn, $query);
        header("Location: index.php");
    }
}

$pelanggan_query = "SELECT * FROM pelanggan";
$pelanggan_result = mysqli_query($conn, $pelanggan_query);
$jenis_query = "SELECT * FROM jenis";
$jenis_result = mysqli_query($conn, $jenis_query);
$transaksi_query = "SELECT t.id_transaksi, p.nama, j.jenis, j.harga, t.tanggal_terima, t.tanggal_selesai, t.jumlah, t.total
                    FROM transaksi t
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    JOIN jenis j ON t.id_jenis = j.id_jenis
                    ORDER BY t.id_transaksi DESC";
$transaksi_result = mysqli_query($conn, $transaksi_query);

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = "SELECT * FROM transaksi WHERE id_transaksi = '$id'";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Laundry</title>
    <style>
        body {
            font-family: arial, serif;
        }

        h1 {
            text-align: center;
        }

        .container {
            margin: 20px;
            display: grid;
            grid-template-columns: 3fr 1fr;
        }

        table {
            border-collapse: collapse;
            width: 90%;
        }

        th,
        td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px 10px;
        }

        th {
            background-color: #f5f5f5;
            color: #333;
        }

        button {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form {
            width: 250px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }

        .form label{
            display: block;
            text-align: center;
        }

        .form-group input,
        .form-group select,
        .form-group option {
            width: 95%;
            height: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Jenis Laundry</th>
                        <th>Tanggal Terima</th>
                        <th>Tanggal Selesai</th>
                        <th>Harga</th>
                        <th>Jumlah Barang</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($transaksi_result, 0);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($transaksi_result)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jenis']; ?></td>
                            <td><?php echo $row['tanggal_terima']; ?></td>
                            <td><?php echo $row['tanggal_selesai']; ?></td>
                            <td>Rp<?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td>Rp<?php echo number_format($row['total'], 0, ',', '.') ?></td>
                            <td style="display: flex; gap: 5px;">
                                <a href="index.php?edit=<?php echo $row['id_transaksi']; ?>">
                                    <button>Edit</button>
                                </a>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $row['id_transaksi']; ?>">
                                    <button type="submit" name="hapus" class="delete"
                                        onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="form">
            <form action="" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <select name="id_pelanggan" required>
                            <option value="">---Pilih Pelanggan---</option>
                            <?php mysqli_data_seek($pelanggan_result, 0);
                            while ($row = mysqli_fetch_assoc($pelanggan_result)) { ?>
                                <option value="<?php echo $row['id_pelanggan']; ?>" <?php if ($edit_data && $edit_data['id_pelanggan'] == $row['id_pelanggan']) echo 'selected'; ?>>
                                    <?php echo $row['nama']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div><br><br>
                    <div class="form-group">
                        <select name="id_jenis" id="jenis" required onchange="tampilHarga()">
                            <option value="">---Pilih Jenis---</option>
                            <?php
                            mysqli_data_seek($jenis_result, 0);
                            while ($row = mysqli_fetch_assoc($jenis_result)) {
                                $selected = ($edit_data && $edit_data['id_jenis'] == $row['id_jenis']) ? 'selected' : ''; ?>
                                <option value="<?php echo $row['id_jenis']; ?>" data-harga="<?php echo $row['harga']; ?>" <?php echo $selected; ?>>
                                    <?php echo $row['jenis']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div><br><br>
                    <div class="form-group">
                        <label>Harga:</label>
                        <input type="text" id="harga" readonly>
                        <input type="hidden" name="harga" id="harga_numeric"
                            value="<?php echo $edit_data ? $edit_data['harga'] : ''; ?>">
                    </div><br><br>
                    <div class="form-group">
                        <label>Jumlah:</label>
                        <input type="number" name="jumlah" id="jumlah"
                            value="<?php echo $edit_data ? $edit_data['jumlah'] : 1; ?>" oninput="hitungTotal()">
                    </div><br><br>
                    <div class="form-group">
                        <label>Total Harga:</label>
                        <input type="text" id="total" readonly>
                        <input type="hidden" id="total_numeric" name="total"
                            value="<?php echo $edit_data ? $edit_data['total'] : ''; ?>" readonly>
                    </div><br><br>
                </div>
                <div class="form-group">
                    <?php if ($edit_data) { ?>
                        <input type="hidden" name="id" value="<?php echo $edit_data['id_transaksi'] ?>">
                        <button name="edit">Update</button>
                        <a href="index.php">
                            <button type="button">Cancel</button>
                        </a>
                    <?php } else { ?>
                        <button type="submit" name="tambah"style="padding: 8px 110px;">Save</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    <script>
        function formatRupiah(number) {
            return parseInt(number).toLocaleString('id-ID');
        }
        function tampilHarga() {
            let select = document.getElementById("jenis");
            let harga = select.options[select.selectedIndex].getAttribute("data-harga");

            document.getElementById("harga").value = harga ? "Rp" + formatRupiah(harga) : "";
            document.getElementById("harga_numeric").value = harga ? harga : "";

            hitungTotal();
        }
        function hitungTotal() {
            let harga = document.getElementById("harga_numeric").value;
            let jumlah = document.getElementById("jumlah").value;

            if (harga && jumlah) {
                let total = parseInt(harga) * parseInt(jumlah);
                document.getElementById("total").value = "Rp" + formatRupiah(total);
                document.getElementById("total_numeric").value = total;
            } else {
                document.getElementById("total").value = "";
                document.getElementById("total_numeric").value = "";
            }
        }
        window.onload = function () {
            tampilHarga();
        }
    </script>
</body>

</html>