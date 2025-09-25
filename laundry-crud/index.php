<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "laundry_db";
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) die("Koneksi Gagal:" . mysqli_connect_error());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $id = !empty($_POST['id']) ? $_POST['id'] : null;
        $id_pelanggan = $_POST["id_pelanggan"];
        $id_jenis = $_POST["id_jenis"];
        $harga = $_POST["harga"];
        $jumlah = $_POST["jumlah"];
        $total = $_POST["total"];
        $tanggal_terima = date('Y-m-d');
        $tanggal_selesai = date('Y-m-d', strtotime('+3 days'));

        if ($id) {
            $query = "UPDATE transaksi SET 
                        id_pelanggan='$id_pelanggan',
                        id_jenis='$id_jenis',
                        harga='$harga',
                        jumlah='$jumlah',
                        total='$total'
                      WHERE id_transaksi='$id'";
        } else {
            $query = "INSERT INTO transaksi(id_pelanggan, id_jenis, tanggal_terima, tanggal_selesai, harga, jumlah, total)
                      VALUES ('$id_pelanggan','$id_jenis','$tanggal_terima','$tanggal_selesai','$harga','$jumlah','$total')";
        }
        mysqli_query($conn, $query);
        header("Location: index.php?success=1");
    }

    if (isset($_POST['hapus'])) {
        $ids = explode(",", $_POST['id']);
        foreach ($ids as $id) {
            $id = mysqli_real_escape_string($conn, $id);
            $query = "DELETE FROM transaksi WHERE id_transaksi='$id'";
            mysqli_query($conn, $query);
        }
        header("Location: index.php");
    }
}

$pelanggan_result = mysqli_query($conn, "SELECT * FROM pelanggan");
$jenis_result = mysqli_query($conn, "SELECT * FROM jenis");
$transaksi_result = mysqli_query($conn, "SELECT t.id_transaksi, p.id_pelanggan, p.nama, j.id_jenis, j.jenis, j.harga, 
                           t.tanggal_terima, t.tanggal_selesai, t.jumlah, t.total
                    FROM transaksi t
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    JOIN jenis j ON t.id_jenis = j.id_jenis
                    ORDER BY t.id_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Laundry</title>
    <style>
        body { font-family: arial, serif; }
        .container { margin: 20px; display: grid; grid-template-columns: 3fr 1fr; gap:20px;}
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; text-align: center; padding: 8px 10px; }
        th { background-color: #f5f5f5; }
        button { padding: 5px 10px; border-radius: 5px; border: 1px solid #ddd; cursor:pointer; margin-right:5px;}
        .form { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .form label { display:block; text-align:center; }
        .form-group input, .form-group select { width: 95%; height: 30px; text-align: center; margin-top:5px;}
        .selected { background-color:#d1e7dd !important; }
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
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while ($row = mysqli_fetch_assoc($transaksi_result)) { ?>
                        <tr class="selectable-row" 
                            data-id="<?= $row['id_transaksi'] ?>"
                            data-pelanggan="<?= $row['id_pelanggan'] ?>"
                            data-jenis="<?= $row['id_jenis'] ?>"
                            data-harga="<?= $row['harga'] ?>"
                            data-jumlah="<?= $row['jumlah'] ?>"
                            data-total="<?= $row['total'] ?>">
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['jenis'] ?></td>
                            <td><?= $row['tanggal_terima'] ?></td>
                            <td><?= $row['tanggal_selesai'] ?></td>
                            <td>Rp<?= number_format($row['harga'],0,',','.') ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp<?= number_format($row['total'],0,',','.') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div style="margin-top:10px;" align="center">
                <form method="post" id="actionForm">
                    <input type="hidden" name="id" id="selectedId">
                    <button type="button" onclick="setUpdate()">Update</button>
                    <button type="submit" name="hapus" onclick="return confirm('Yakin hapus data ini?')">Delete</button>
                </form>
            </div>
        </div>
        <div class="form">
            <form action="" method="post" id="formLaundry">
                <input type="hidden" name="id" id="formId">
                <div class="form-group">
                    <select name="id_pelanggan" id="formPelanggan" required>
                        <option value="">---Pilih Pelanggan---</option>
                        <?php mysqli_data_seek($pelanggan_result,0);
                        while ($row=mysqli_fetch_assoc($pelanggan_result)) { ?>
                            <option value="<?= $row['id_pelanggan'] ?>"><?= $row['nama'] ?></option>
                        <?php } ?>
                    </select>
                </div><br>
                <div class="form-group">
                    <select name="id_jenis" id="formJenis" required onchange="tampilHarga()">
                        <option value="">---Pilih Jenis---</option>
                        <?php mysqli_data_seek($jenis_result,0);
                        while ($row=mysqli_fetch_assoc($jenis_result)) { ?>
                            <option value="<?= $row['id_jenis'] ?>" data-harga="<?= $row['harga'] ?>">
                                <?= $row['jenis'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div><br>
                <div class="form-group">
                    <label>Harga:</label>
                    <input type="text" id="harga" readonly>
                    <input type="hidden" name="harga" id="harga_numeric">
                </div><br>
                <div class="form-group">
                    <label>Jumlah:</label>
                    <input type="number" name="jumlah" id="jumlah" value="1" oninput="hitungTotal()">
                </div><br>
                <div class="form-group">
                    <label>Total:</label>
                    <input type="text" id="total" readonly>
                    <input type="hidden" name="total" id="total_numeric">
                </div><br>
                <div class="form-group">
                    <button type="submit" name="save" style="width: 97%;">Save</button>
                </div>
            </form>
        </div>
    </div>

<script>
function formatRupiah(number) {
    return parseInt(number).toLocaleString('id-ID');
}
function tampilHarga() {
    let select = document.getElementById("formJenis");
    let harga = select.options[select.selectedIndex]?.getAttribute("data-harga");
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

let selectedIds = [];
document.querySelectorAll(".selectable-row").forEach(row => {
    row.addEventListener("click", function () {
        let id = this.getAttribute("data-id");

        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter(x => x !== id);
            this.classList.remove("selected");
        } else {
            selectedIds.push(id);
            this.classList.add("selected");
        }
        document.getElementById("selectedId").value = selectedIds.join(",");
    });
});

function setUpdate() {
    if (selectedIds.length !== 1) {
        alert("Pilih tepat satu baris untuk update!");
        return;
    }
    let row = document.querySelector(".selectable-row[data-id='"+selectedIds[0]+"']");
    document.getElementById("formId").value = row.getAttribute("data-id");
    document.getElementById("formPelanggan").value = row.getAttribute("data-pelanggan");
    document.getElementById("formJenis").value = row.getAttribute("data-jenis");
    document.getElementById("harga_numeric").value = row.getAttribute("data-harga");
    document.getElementById("jumlah").value = row.getAttribute("data-jumlah");
    document.getElementById("total_numeric").value = row.getAttribute("data-total");
    tampilHarga();
}

function resetForm() {
    document.getElementById("formId").value = "";
    document.getElementById("formPelanggan").value = "";
    document.getElementById("formJenis").value = "";
    document.getElementById("harga").value = "";
    document.getElementById("harga_numeric").value = "";
    document.getElementById("jumlah").value = 1;
    document.getElementById("total").value = "";
    document.getElementById("total_numeric").value = "";
}

window.onload = function() {
    tampilHarga();
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("success")) {
        resetForm();
        window.history.replaceState({}, document.title, "index.php");
    }
}
</script>
</body>
</html>