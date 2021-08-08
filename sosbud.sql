CREATE TABLE `atk` (
  `id` varchar(8) PRIMARY KEY,
  `kategori` varchar(25) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `harga` int NOT NULL DEFAULT 0,
  `stok` int NOT NULL DEFAULT 0
);

CREATE TABLE `transaksi` (
  `kode` varchar(8) PRIMARY KEY,
  `barang` varchar(8) NOT NULL,
  `jenis` ENUM ('masuk', 'keluar') NOT NULL,
  `saldo_awal` int NOT NULL DEFAULT 0,
  `saldo_ahir` int NOT NULL DEFAULT 0,
  `stok_awal` int NOT NULL DEFAULT 0,
  `jumlah` int NOT NULL DEFAULT 0,
  `tanggal_transaksi` date NOT NULL
);

ALTER TABLE `transaksi` ADD FOREIGN KEY (`barang`) REFERENCES `atk` (`id`);
