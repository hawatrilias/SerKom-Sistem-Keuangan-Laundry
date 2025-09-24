<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "laundry_db";
$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Koneksi Gagal:". mysqli_connect_error());
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['tambah'])){
        $id_pelanggan = $_POST['id_pelanggan'];
        $id_jenis = $_POST['id_jenis'];
        $harga = $_POST['harga'];
        $jumlah = $_POST['jumlah'];
        $total = $_POST['total'];
        $tanggal_terima = date('Y-m-d');
        $tanggal_selesai = date('Y-m-d', strtotime('+3 days'));

        $query = "INSERT INTO transaksi (id_pelanggan, id_jenis, tanggal_terima, tanggal_selesai, harga, jumlah, total)
                VALUES ('$id_pelanggan', '$id_jenis', '$tanggal_terima', '$tanggal_selesai',   '$harga', '$jumlah', '$total')";
        mysqli_query($conn, $query);        
        header("Location: index.php");
    }
    if(isset($_POST['edit'])){
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
    if(isset($_POST['hapus'])){
        $id = $_POST['id'];
        $query = "DELETE FROM transaksi WHERE id_transaksi = '$id'";
        mysqli_query($conn, $query);
        header("Location: index.php");
    }
}

$pelanggan_query = "SELECT * FROM pelanggan";
$pelanggan_result = mysqli_query($conn, $pelanggan_query);

$jenis_query = "SELECT * FROM jenis_laundry";
$jenis_result = mysqli_query($conn, $jenis_query);

$transaksi_query = "SELECT t.id_transaksi, p.nama, j.jenis_laundry AS jenis, j.harga, t.tanggal_terima, t.tanggal_selesai, t.jumlah, t.total
                    FROM transaksi t
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    JOIN jenis_laundry j ON t.id_jenis = j.id_jenis";
$transaksi_result = mysqli_query($conn, $transaksi_query);

$edit_data = null;
if(isset($_POST['edit'])){
    $id = $_POST['id'];
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
        body{
            margin: 20px;
            padding: 0;
            font-family: cambria;
            background-color: #f5f5f5;
        }
        h1{
            text-align: center;
            color: #333;
        }
        .container{
            display: grid;
            grid-template-columns: 4fr 1fr;
            margin-top: 20px;
        }
        table{
            width: 90%;
            border-collapse: collapse;
            background: white;
        }
        th,td{
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: center;
        }
        th{
            background: #333;
            color: #f5f5f5;
        }
        tr:nth-child(even){
            background: #f9f9f9;
        }
        .form{
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }
        .form-group input, 
        .form-group select, 
        .form-group button {
            padding: 6px 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .form-group button {
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background: green;
        }
        .form-group a button {
            background: #e74c3c;
        }
        .form-group a button:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Sistem Informasi Laundry</h1>
    <div class="container">
        <div class="table">
            <table align="center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Jenis Laundry</th>
                        <th>Tanggal Terima</th>
                        <th>Tanggal Selesai</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($transaksi_result,0);
                    while($row = mysqli_fetch_assoc($transaksi_result)){
                    $no = 1;
                    ?>
                        <tr>
                            <td><?php echo $no++;?></td>
                            <td><?php echo $row['nama'];?></td>
                            <td><?php echo $row['jenis'];?></td>
                            <td><?php echo $row['tanggal_terima'];?></td>
                            <td><?php echo $row['tanggal_selesai'];?></td>
                            <td>Rp<?php echo number_format($row['harga'], 0, ',', '.');?></td>
                            <td><?php echo $row['jumlah'];?></td>
                            <td>Rp<?php echo number_format($row['total'], 0, ',', '.');?></td>
                            <td style="display: flex; gap: 5px;"    >
                                <a href="index.php?edit=<?php echo $row['id_transaksi'];?>">
                                    <button>Edit</button>
                                </a>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $row['id_transaksi'];?>">
                                    <button type="submit" name="hapus" class="delete" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="form">
            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Pelanggan:</label><br>
                        <select name="id_pelanggan" required>
                        <option value="">---Pilih Pelanggan---</option>
                        <?php 
                        mysqli_data_seek($pelanggan_result,0);
                        while($row = mysqli_fetch_assoc($pelanggan_result)) { ?>
                            <option value="<?php echo $row['id_pelanggan'];?>"
                                <?php if($edit_data && $edit_data['id_pelanggan'] == $row['id_pelanggan']) echo 'selected'; ?>>
                                <?php echo $row['nama'];?>
                            </option>
                            <?php } ?>
                        </select><br><br>
                    </div>
                    <div class="form-group">
                        <label>Jenis Laundry:</label><br>
                        <select name="id_jenis" id="jenis" required onchange="tampilHarga()">
                            <option value="">---Pilih Jenis---</option>
                            <?php
                                mysqli_data_seek($jenis_result,0);
                                while($row = mysqli_fetch_assoc($jenis_result)){ 
                                $selected = ($edit_data && $edit_data['id_jenis'] == $row['id_jenis']) ? 'selected':'';?> 
                                <option value="<?php echo $row['id_jenis'];?>" data-harga="<?php echo $row['harga'];?>">
                                    <?php echo $selected;?>
                                    <?php echo $row['jenis_laundry'];?>
                                </option>
                            <?php } ?>
                        </select><br><br>
                    </div>
                    <div class="form-group">
                        <label>Harga:</label><br>
                        <input type="text" id="harga" readonly>
                        <input type="hidden" id="harga_numeric" name="harga" value="<?php echo $edit_data ? $edit_data['harga']:'';?>"><br><br>
                    </div>
                    <div class="form-group">
                        <label>Jumlah:</label><br>
                        <input type="number" id="jumlah" name="jumlah" value="<?php echo $edit_data ? $edit_data['jumlah']:1;?>" oninput="hitungTotal()"><br><br>
                    </div>
                    <div class="form-group">
                        <label>Total Harga:</label><br>
                        <input type="text" id="total" readonly>
                        <input type="hidden" id="total_numeric" name="total" value="<?php echo $edit_data ? $edit_data['total']:''?>" readonly><br><br>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Terima:</label><br>
                        <input type="date" name="tanggal_terima" value="<?php echo $edit_data ? $edit_data['tanggal_terima']: date('Y-m-d');?>" readonly><br><br>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label><br>
                        <input type="date" name="tanggal_selesai" value="<?php echo $edit_data ? $edit_data['tanggal_selesai']: date('Y-m-d', strtotime('+3 days'));?>" readonly><br><br>
                    </div>
                </div>
                <div class="form-group">
                    <?php if($edit_data){?>
                        <input type="hidden" name="id" value="<?php echo $edit_data['id_transaksi'];?>">
                        <button type="submit" name="edit">Update</button>
                        <a href="index.php"><button>Cancel</button></a>
                    <?php }else{ ?>
                        <button type="submit" name="tambah">Save</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    

    <script>
        function formatRupiah(number){
            return parseInt (number).toLocaleString('id-ID');
        }
        function tampilHarga(){
            let select = document.getElementById("jenis");
            let harga = select.options[select.selectedIndex].getAttribute("data-harga");

            document.getElementById("harga").value = harga ? "Rp" + formatRupiah(harga) : "";
            document.getElementById("harga_numeric").value = harga ? harga : "";

            hitungTotal();
        }
        function hitungTotal(){
            let harga = document.getElementById("harga_numeric").value;
            let jumlah = document.getElementById("jumlah").value;

            if(harga && jumlah){
                let total = parseInt(harga) * parseInt(jumlah);
                document.getElementById("total"). value = "Rp" + formatRupiah(total);
                document.getElementById("total_numeric"). value = total;
            }else{
                document.getElementById("total"). value = "";
                document.getElementById("total_numeric"). value = "";
            }
        }
        window.onload = function(){
            tampilHarga();
        }
    </script>
</body>
</html>