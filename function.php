<?php
session_start();

// untuk mengoneksikan ke database
$conn = mysqli_connect("localhost", "root", "", "stockbarang");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("INSERT INTO stock (namabarang, deskripsi, stock) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $namabarang, $deskripsi, $stock);
    $addtotable = $stmt->execute();

    if ($addtotable) {
        header('Location: index.php');
        exit();
    } else {
        echo 'Gagal menambah barang baru';
        header('Location: index.php');
        exit();
    }
}

// menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang ='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    $stmt1 = $conn->prepare("INSERT INTO masuk (idbarang, keterangan, qty) VALUES (?, ?, ?)");
    $stmt1->bind_param("isi", $barangnya, $penerima, $qty);
    $addtomasuk = $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
    $stmt2->bind_param("ii", $tambahkanstocksekarangdenganquantity, $barangnya);
    $updatestockmasuk = $stmt2->execute();

    if ($addtomasuk && $updatestockmasuk) {
        header('Location: masuk.php');
        exit();
    } else {
        echo 'Gagal menambah barang masuk';
        header('Location: masuk.php');
        exit();
    }
}

// menambah barang keluar
if (isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang ='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $kurangkanstocksekarangdenganquantity = $stocksekarang - $qty;

    $stmt1 = $conn->prepare("INSERT INTO keluar (idbarang, penerima, qty) VALUES (?, ?, ?)");
    $stmt1->bind_param("isi", $barangnya, $penerima, $qty);
    $addtokeluar = $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
    $stmt2->bind_param("ii", $kurangkanstocksekarangdenganquantity, $barangnya);
    $updatestockkeluar = $stmt2->execute();

    if ($addtokeluar && $updatestockkeluar) {
        header('Location: keluar.php');
        exit();
    } else {
        echo 'Gagal menambah barang keluar';
        header('Location: keluar.php');
        exit();
    }
}

// update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$idb'");
    if ($update) {
        header('Location: index.php');
        exit();
    } else {
        echo 'Gagal mengupdate barang';
        header('Location: index.php');
        exit();
    }
}

// Menghapus barang dari stock
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$idb'");
    if ($hapus) {
        header('Location: index.php');
        exit();
    } else {
        echo 'Gagal menghapus barang';
        header('Location: index.php');
        exit();
    }
}

// mengubah data barang masuk
if (isset($_POST['updatebarangmasuk'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang ='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if ($qty > $qtyskrg) {
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih;
        $kuranginstocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty= '$qty', keterangan='$keterangan' WHERE idmasuk='$idm'");
        if ($kuranginstocknya && $updatenya) {
            header('Location: masuk.php');
            exit();
        } else {
            echo 'Gagal mengupdate barang masuk';
            header('Location: masuk.php');
            exit();
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih;
        $kuranginstocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty= '$qty', keterangan='$keterangan' WHERE idmasuk='$idm'");
        if ($kuranginstocknya && $updatenya) {
            header('Location: masuk.php');
            exit();
        } else {
            echo 'Gagal mengupdate barang masuk';
            header('Location: masuk.php');
            exit();
        }
    }
}



//menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn,"select * from stock where idbarang= '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock-$qty;

    $update = mysqli_query($conn,"update stock set stock ='$selisih'where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");

    if($update&&$hapusdata){
        header('location:masuk.php');
    } else {
        header('location:masuk.php');
    }
}



// mengubah data barang keluar
if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk']; // Mengubah dari 'idm' ke 'idk' agar sesuai dengan penggunaan di bawah
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang ='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "SELECT qty FROM keluar WHERE idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if ($qty > $qtyskrg) {
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih;
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih;
    }

    $kuranginstocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang='$idb'");
    $updatenya = mysqli_query($conn, "UPDATE keluar SET qty= '$qty', penerima='$penerima' WHERE idkeluar='$idk'");

    if ($kuranginstocknya && $updatenya) {
        header('Location: keluar.php');
        exit();
    } else {
        echo 'Gagal mengupdate barang keluar';
        header('Location: keluar.php');
        exit();
    }
}

// menghapus barang keluar
if (isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];  // Mengubah dari 'kty' ke 'qty' agar sesuai dengan penggunaan di bawah
    $idk = $_POST['idk'];  // Mengubah dari 'idm' ke 'idk' agar sesuai dengan penggunaan di bawah

    $getdatastock = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang= '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock + $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'");

    if ($update && $hapusdata) {
        header('Location: keluar.php');
        exit();
    } else {
        echo 'Gagal menghapus barang keluar';
        header('Location: keluar.php');
        exit();
    }
}



?>
