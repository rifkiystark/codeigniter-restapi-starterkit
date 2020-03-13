-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 13 Mar 2020 pada 15.18
-- Versi Server: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recekdb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `Guru`
--

CREATE TABLE `Guru` (
  `id_guru` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `jenkel` enum('L','P') NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Guru`
--

INSERT INTO `Guru` (`id_guru`, `email`, `nama`, `jenkel`, `password`) VALUES
(1, 'ananda.rifkiy33@gmail.com', 'RifkiyStark', 'L', 'f3b9ccb09a10633815d9a18df737c472'),
(2, '18104004@ittelkom-pwt.ac.id', 'Yonkou', 'L', 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Jadwal`
--

CREATE TABLE `Jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `hari` varchar(10) NOT NULL,
  `jam` time NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Jadwal`
--

INSERT INTO `Jadwal` (`id_jadwal`, `hari`, `jam`, `id_mapel`, `id_kelas`) VALUES
(1, 'senin', '08:00:00', 1, 1),
(2, 'senin', '06:00:00', 5, 3),
(3, 'selasa', '07:26:00', 3, 2),
(4, 'rabu', '13:00:00', 4, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Kehadiran`
--

CREATE TABLE `Kehadiran` (
  `id_kehadiran` int(11) NOT NULL,
  `id_master_kehadiran` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `kehadiran` enum('hadir','izin','sakit','alpa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Kehadiran`
--

INSERT INTO `Kehadiran` (`id_kehadiran`, `id_master_kehadiran`, `id_siswa`, `kehadiran`) VALUES
(1, 6, 1, 'hadir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Kelas`
--

CREATE TABLE `Kelas` (
  `id_kelas` int(11) NOT NULL,
  `id_master_kelas` int(11) NOT NULL,
  `tahun_ajaran` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Kelas`
--

INSERT INTO `Kelas` (`id_kelas`, `id_master_kelas`, `tahun_ajaran`) VALUES
(1, 1, '2017/2018'),
(2, 2, '2020/2021'),
(3, 3, '20129/2020');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Mapel`
--

CREATE TABLE `Mapel` (
  `id_mapel` int(11) NOT NULL,
  `id_master_mapel` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Mapel`
--

INSERT INTO `Mapel` (`id_mapel`, `id_master_mapel`, `id_guru`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 4, 1),
(4, 3, 1),
(5, 2, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Master_Kehadiran`
--

CREATE TABLE `Master_Kehadiran` (
  `id_master_kehadiran` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Master_Kehadiran`
--

INSERT INTO `Master_Kehadiran` (`id_master_kehadiran`, `id_mapel`, `tanggal`) VALUES
(6, 1, '2020-03-13'),
(7, 1, '2020-03-13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Master_Kelas`
--

CREATE TABLE `Master_Kelas` (
  `id_master_kelas` int(11) NOT NULL,
  `kelas` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Master_Kelas`
--

INSERT INTO `Master_Kelas` (`id_master_kelas`, `kelas`) VALUES
(1, 'X RPL 2'),
(2, 'X MM 1'),
(3, 'X TKJ 3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Master_Mapel`
--

CREATE TABLE `Master_Mapel` (
  `id_master_mapel` int(11) NOT NULL,
  `mapel` varchar(20) NOT NULL,
  `kode_mapel` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Master_Mapel`
--

INSERT INTO `Master_Mapel` (`id_master_mapel`, `mapel`, `kode_mapel`) VALUES
(1, 'Bahasa Indonesia', 'B12'),
(2, 'Matematika', 'MTK'),
(3, 'Jarkom', 'JKM'),
(4, 'IPA', 'IP39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Master_Siswa`
--

CREATE TABLE `Master_Siswa` (
  `id_master_siswa` int(11) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `id_wali` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Master_Siswa`
--

INSERT INTO `Master_Siswa` (`id_master_siswa`, `nis`, `nama`, `id_wali`) VALUES
(1, '3103115228', 'Ananda Rifkiy Hasan', 1),
(2, '19881273', 'Febri Ardiansyah', 1),
(3, '982693', 'Syahrul Samudra', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Nilai`
--

CREATE TABLE `Nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `type` enum('exam','task') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `nilai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Siswa`
--

CREATE TABLE `Siswa` (
  `id_siswa` int(11) NOT NULL,
  `id_master_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Siswa`
--

INSERT INTO `Siswa` (`id_siswa`, `id_master_siswa`, `id_kelas`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Wali`
--

CREATE TABLE `Wali` (
  `id_wali` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `jenkel` enum('L','P') NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `Wali`
--

INSERT INTO `Wali` (`id_wali`, `email`, `nama`, `jenkel`, `password`) VALUES
(1, 'ananda.rifkiy32@gmail.com', 'Wakwaw', 'L', 'e9c0a2cf5405ded232ad873a7c57ff38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Guru`
--
ALTER TABLE `Guru`
  ADD PRIMARY KEY (`id_guru`);

--
-- Indexes for table `Jadwal`
--
ALTER TABLE `Jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indexes for table `Kehadiran`
--
ALTER TABLE `Kehadiran`
  ADD PRIMARY KEY (`id_kehadiran`),
  ADD KEY `id_master_kehadiran` (`id_master_kehadiran`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `Kelas`
--
ALTER TABLE `Kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `id_master_kelas` (`id_master_kelas`);

--
-- Indexes for table `Mapel`
--
ALTER TABLE `Mapel`
  ADD PRIMARY KEY (`id_mapel`),
  ADD KEY `id_master_mapel` (`id_master_mapel`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `Master_Kehadiran`
--
ALTER TABLE `Master_Kehadiran`
  ADD PRIMARY KEY (`id_master_kehadiran`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `Master_Kelas`
--
ALTER TABLE `Master_Kelas`
  ADD PRIMARY KEY (`id_master_kelas`);

--
-- Indexes for table `Master_Mapel`
--
ALTER TABLE `Master_Mapel`
  ADD PRIMARY KEY (`id_master_mapel`);

--
-- Indexes for table `Master_Siswa`
--
ALTER TABLE `Master_Siswa`
  ADD PRIMARY KEY (`id_master_siswa`),
  ADD KEY `id_wali` (`id_wali`);

--
-- Indexes for table `Nilai`
--
ALTER TABLE `Nilai`
  ADD PRIMARY KEY (`id_nilai`);

--
-- Indexes for table `Siswa`
--
ALTER TABLE `Siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD KEY `id_master_siswa` (`id_master_siswa`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indexes for table `Wali`
--
ALTER TABLE `Wali`
  ADD PRIMARY KEY (`id_wali`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Guru`
--
ALTER TABLE `Guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `Jadwal`
--
ALTER TABLE `Jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Kehadiran`
--
ALTER TABLE `Kehadiran`
  MODIFY `id_kehadiran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Kelas`
--
ALTER TABLE `Kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Mapel`
--
ALTER TABLE `Mapel`
  MODIFY `id_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Master_Kehadiran`
--
ALTER TABLE `Master_Kehadiran`
  MODIFY `id_master_kehadiran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `Master_Kelas`
--
ALTER TABLE `Master_Kelas`
  MODIFY `id_master_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Master_Mapel`
--
ALTER TABLE `Master_Mapel`
  MODIFY `id_master_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Master_Siswa`
--
ALTER TABLE `Master_Siswa`
  MODIFY `id_master_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Nilai`
--
ALTER TABLE `Nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Siswa`
--
ALTER TABLE `Siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Wali`
--
ALTER TABLE `Wali`
  MODIFY `id_wali` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `Jadwal`
--
ALTER TABLE `Jadwal`
  ADD CONSTRAINT `Jadwal_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `Mapel` (`id_mapel`),
  ADD CONSTRAINT `Jadwal_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `Kelas` (`id_kelas`);

--
-- Ketidakleluasaan untuk tabel `Kehadiran`
--
ALTER TABLE `Kehadiran`
  ADD CONSTRAINT `Kehadiran_ibfk_1` FOREIGN KEY (`id_master_kehadiran`) REFERENCES `Master_Kehadiran` (`id_master_kehadiran`),
  ADD CONSTRAINT `Kehadiran_ibfk_2` FOREIGN KEY (`id_siswa`) REFERENCES `Siswa` (`id_siswa`);

--
-- Ketidakleluasaan untuk tabel `Kelas`
--
ALTER TABLE `Kelas`
  ADD CONSTRAINT `Kelas_ibfk_1` FOREIGN KEY (`id_master_kelas`) REFERENCES `Master_Kelas` (`id_master_kelas`);

--
-- Ketidakleluasaan untuk tabel `Mapel`
--
ALTER TABLE `Mapel`
  ADD CONSTRAINT `Mapel_ibfk_1` FOREIGN KEY (`id_master_mapel`) REFERENCES `Master_Mapel` (`id_master_mapel`),
  ADD CONSTRAINT `Mapel_ibfk_2` FOREIGN KEY (`id_guru`) REFERENCES `Guru` (`id_guru`);

--
-- Ketidakleluasaan untuk tabel `Master_Kehadiran`
--
ALTER TABLE `Master_Kehadiran`
  ADD CONSTRAINT `Master_Kehadiran_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `Mapel` (`id_mapel`);

--
-- Ketidakleluasaan untuk tabel `Master_Siswa`
--
ALTER TABLE `Master_Siswa`
  ADD CONSTRAINT `Master_Siswa_ibfk_1` FOREIGN KEY (`id_wali`) REFERENCES `Wali` (`id_wali`);

--
-- Ketidakleluasaan untuk tabel `Siswa`
--
ALTER TABLE `Siswa`
  ADD CONSTRAINT `Siswa_ibfk_1` FOREIGN KEY (`id_master_siswa`) REFERENCES `Master_Siswa` (`id_master_siswa`),
  ADD CONSTRAINT `Siswa_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `Kelas` (`id_kelas`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
