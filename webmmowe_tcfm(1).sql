-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2014 at 10:26 AM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webmmowe_tcfm`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_baca_tawaran`(p_username varchar (45))
BEGIN
	update `penawaran` set `terbaca` = 1 where `penawar`=p_username and `jawaban`!=99;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_batalkan_tawaran`(p_username varchar (45), p_id int (11))
start_label: BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
START TRANSACTION;
	IF ((SELECT 1 FROM `penawaran` WHERE `penawar`=p_username AND `id_penawaran`=p_id)IS NULL) THEN
		SELECT -1 AS kode, 'Bukan Penawaran Anda' AS pesan;
		LEAVE start_label;
	END IF;
	IF ((SELECT `jawaban` FROM `penawaran` WHERE `penawar`=p_username AND `id_penawaran`=p_id) != 99) THEN
		SELECT -1 AS kode, 'Sudah Dijawab' AS pesan;
		LEAVE start_label;
	END IF;
	
	#proses pembatalan
	delete from `penawaran` where `penawar`=p_username AND `id_penawaran`=p_id;
	SELECT 0 AS kode, 'Tawaran Berhasil Dibatalkan' AS pesan;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_beliPemain`(
  p_username varchar (45),
  p_paket int,
  flag boolean
)
start_label:BEGIN
declare harga, p_id_pemain, tmp int;
declare list_pemain varchar (45);
DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
	SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	show errors;
	show warnings;
END;
start transaction;
	
	if (p_paket > 4 or p_paket < 1) then
		SELECT -1 AS kode, 'Paket Tidak Valid !' AS pesan;
		LEAVE start_label;
	end if;
	
	IF (p_paket = 3 OR p_paket = 2) THEN
		set tmp = 4;
	elseif (p_paket = 1) then
		set tmp = 5;
	else
		set tmp = 3;
	end if;
	
	if (fn_jumlah_pemain_tim(p_username) + tmp > (select setting.`pemain_tim_max` from setting)) then
		SELECT -1 AS kode, 'Pemain Anda Mencapai Batas Maksimal. Release Pemain Dulu!' AS pesan;
		LEAVE start_label;
	end if;
	
	set list_pemain = "";
	#beli pakek uang
	if (flag) then
		select `harga_uang` into harga from `paket` where `id_paket`=p_paket;
		if (harga > fn_uang(p_username)) then
			SELECT -1 AS kode, 'Uang Anda Tidak Mencukupi !' AS pesan;
			LEAVE start_label;
		end if;
		
		update `user` set `uang`=`uang`-harga where `username` = p_username; 
	else #beli pakek balen
		SELECT `harga_balen` INTO harga FROM `paket` WHERE `id_paket`=p_paket;
		IF (harga > (select `balen` from `user` where `username`=p_username)) THEN
			SELECT -1 AS kode, 'Balen Anda Tidak Mencukupi !' AS pesan;
			LEAVE start_label;
		END IF;
		
		UPDATE `user` SET `balen`=`balen`-harga WHERE `username` = p_username;
	end if;
	
	IF (p_paket = 3 OR p_paket = 2) THEN
		#1
		set p_id_pemain = null;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket and `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#2
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#3
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#4
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		ELSEIF (p_paket = 1) THEN
		SET p_id_pemain = NULL;
			if ((select 1 from `user_pemain`, pemain
			where user_pemain.`user_username`=p_username and user_pemain.`pemain_id_pemain`=pemain.`id_pemain` and pemain.`posisi`='G' limit 1) is null) then
				SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
					SELECT `pemain_id_pemain`
					FROM `user_pemain`
					WHERE `user_username`=p_username
				) AND `flag_paket` = p_paket AND `flag_limited` = 0 and `posisi` = 'G'
				ORDER BY RAND()
				LIMIT 1;
			else
				SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
					SELECT `pemain_id_pemain`
					FROM `user_pemain`
					WHERE `user_username`=p_username
				) AND `flag_paket` = p_paket AND `flag_limited` = 0
				ORDER BY RAND()
				LIMIT 1;
			end if;
		#1
		
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#2
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#3
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#4
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#5
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_paket` = p_paket AND `flag_limited` = 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		ELSE
		#1
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_limited` = 1 and `flag_tersedia` != 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#2
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_limited` = 1 AND `flag_tersedia` != 0
		ORDER BY RAND()
		LIMIT 1;
		IF (p_id_pemain IS NOT NULL) THEN
			INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
			SELECT p_username, p_id_pemain, 0;
			SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
		END IF;
		
		#3
		SET p_id_pemain = NULL;
		SELECT `id_pemain` INTO p_id_pemain FROM `pemain` WHERE `id_pemain` NOT IN (
			SELECT `pemain_id_pemain`
			FROM `user_pemain`
			WHERE `user_username`=p_username
		) AND `flag_limited` = 1 AND `flag_tersedia` != 0
		ORDER BY RAND()
		LIMIT 1;
			if (p_id_pemain is not null) then
				INSERT INTO `user_pemain` (`user_username`, `pemain_id_pemain`, `aktif`)
				SELECT p_username, p_id_pemain, 0;
				SELECT CONCAT(list_pemain,'.',p_id_pemain) INTO list_pemain;
			end if;
		END IF;
		SELECT 0 AS kode, list_pemain AS pesan;
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_best_team_lineup`(p_username varchar (45))
BEGIN
	declare def_limit, mid_limit, att_limit, counter, off, p1 int(10);
	declare nama_forma varchar(45);
	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
start transaction;
	select `nama_formasi` into nama_forma from `formasi` where `id_formasi` = 
	(
		select `formasi_id_formasi`
		from `user`
		where `username` = p_username
	);
	
	SELECT CONVERT(SUBSTRING(nama_forma, 1, LOCATE('-', nama_forma) - 1), UNSIGNED INTEGER) INTO def_limit;
	SELECT CONVERT(SUBSTRING(nama_forma, 3, LOCATE('-', nama_forma) - 1), UNSIGNED INTEGER) INTO mid_limit;
	SELECT CONVERT(SUBSTRING(nama_forma, 5, LOCATE('-', nama_forma) - 1), UNSIGNED INTEGER) INTO att_limit;
	
	UPDATE `user_pemain` SET `aktif` = 0 WHERE `user_username` = p_username;
	#select def_limit, mid_limit, att_limit;
	
	set counter = 0;
	#### F ####
	set p1 = 0;
	label1: LOOP
	set counter = counter + 1;
	UPDATE `user_pemain` up
	INNER JOIN
	(
		SELECT `id_user_pemain`
		FROM `user_pemain`, `pemain`
		WHERE `user_username` = p_username
		AND `pemain_id_pemain` = `id_pemain` 
		AND `posisi` = 'F'
		ORDER BY `rating` DESC
		LIMIT 1
		OFFSET p1
	) t
	ON up.`id_user_pemain` = t.`id_user_pemain`
	SET up.aktif = counter;
	SET p1 = p1 + 1;
	    IF p1 < att_limit THEN
	      ITERATE label1;
	    END IF;
	LEAVE label1;
	END LOOP label1;
	
	### M ###
	SET p1 = 0;
	label1: LOOP
	SET counter = counter + 1;
	UPDATE `user_pemain` up
	INNER JOIN
	(
		SELECT `id_user_pemain`
		FROM `user_pemain`, `pemain`
		WHERE `user_username` = p_username
		AND `pemain_id_pemain` = `id_pemain` 
		AND `posisi` = 'M'
		ORDER BY `rating` DESC
		LIMIT 1
		OFFSET p1
	) t
	ON up.`id_user_pemain` = t.`id_user_pemain`
	SET up.aktif = counter;
	SET p1 = p1 + 1;
	    IF p1 < mid_limit THEN
	      ITERATE label1;
	    END IF;
	LEAVE label1;
	END LOOP label1;
	
	### D ###
	SET p1 = 0;
	label1: LOOP
	SET counter = counter + 1;
	UPDATE `user_pemain` up
	INNER JOIN
	(
		SELECT `id_user_pemain`
		FROM `user_pemain`, `pemain`
		WHERE `user_username` = p_username
		AND `pemain_id_pemain` = `id_pemain` 
		AND `posisi` = 'D'
		ORDER BY `rating` DESC
		LIMIT 1
		OFFSET p1
	) t
	ON up.`id_user_pemain` = t.`id_user_pemain`
	SET up.aktif = counter;
	SET p1 = p1 + 1;
	    IF p1 < def_limit THEN
	      ITERATE label1;
	    END IF;
	LEAVE label1;
	END LOOP label1;
	
	### GK ###
	SET counter = counter + 1;
	UPDATE `user_pemain` up
	INNER JOIN
	(
		SELECT `id_user_pemain`
		FROM `user_pemain`, `pemain`
		WHERE `user_username` = p_username
		AND `pemain_id_pemain` = `id_pemain` 
		AND `posisi` = 'G'
		ORDER BY `rating` DESC
		LIMIT 1
	) t
	ON up.`id_user_pemain` = t.`id_user_pemain`
	SET up.aktif = counter;
	
	SELECT 0 AS kode, 'Sukses Generate Best Lineup' AS pesan; 
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cek_biaya_perawatan_stadion`(p_username varchar(45))
start_label: BEGIN
	declare biaya int;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
	start transaction;
	if ((`fn_server_time_TU`() - (select `biaya_perawatan_lastset_tu` from `user` where `username`=p_username)) >= 10080) then
		select `biaya_perawatan` into biaya from `stadion` where `id_stadion` = (
			select `stadion_id_stadion`
			from `user`
			where `username`=p_username
		);
		if (`fn_uang`(p_username) < biaya) then
			call sp_downgrade_stadion(p_username);
			leave start_label;
		end if;
		
		update `user` set `uang`=`uang`-biaya where `username`=p_username;
		UPDATE `user` SET `biaya_perawatan_lastset_tu` = fn_server_time_TU() WHERE `username` = p_username;
		SELECT 0 AS kode, 'Biaya Perawatan Berhasil Dibayar' AS pesan;
	else
		select -1 as kode, 'Belum Saatnya Bayar' as pesan;
	end if;
	commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cek_latihan`(p_username varchar (45))
start_label: BEGIN
DECLARE durasi_lat, id_lat, kompak_last_set_tu INT(10);
DECLARE tambah_kekompakan, kompak_last_set_val TINYINT(3);
SELECT `latihan_id_latihan` INTO id_lat FROM `user` WHERE `username`=p_username;
IF (id_lat IS NULL) THEN
	SELECT -1 AS kode, 'tidak sedang latihan' AS pesan;
	LEAVE start_label;
END IF;
SELECT `durasi_latihan_tu` INTO durasi_lat FROM `latihan` WHERE `id_latihan`=id_lat;
SELECT `penambahan_kekompakan` INTO tambah_kekompakan FROM `latihan` WHERE `id_latihan`=id_lat;
SELECT `kekompakan_lastset_tu` INTO kompak_last_set_tu FROM `user` WHERE `username`=p_username;
SELECT `kekompakan_lastset_value` INTO kompak_last_set_val FROM `user` WHERE `username`=p_username;
IF (`fn_server_time_TU`() > kompak_last_set_tu+durasi_lat) THEN
	CALL sp_setKekompakan(p_username, kompak_last_set_val+tambah_kekompakan);
	select 0 as kode, concat('Latihan Telah Selesai Kekompakan Bertambah ', tambah_kekompakan, ' Menjadi ', `fn_kekompakan`(p_username)) as pesan;	
ELSE
	SELECT -1 AS kode, concat('latihan belum selesai kurang ', kompak_last_set_tu+durasi_lat-fn_server_time_TU()) AS pesan;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_clear_lineup`(p_username varchar (45))
BEGIN
	update `user_pemain` set `aktif` = 0 where `user_username` = p_username;
	select 0 as kode, 'Clear Lineup Berhasil' as pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_formasi`()
BEGIN
	select * from `formasi`;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_latihan`()
BEGIN
  select 
    * 
  from
    `latihan`
  order by `durasi_latihan_tu` asc;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_lineup`(p_username varchar(45))
BEGIN
	select *
	from `user_pemain`, `pemain`
	where `user_username` = p_username
	and `pemain_id_pemain` = `id_pemain`
	and `aktif` > 0;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_pemain`()
BEGIN
	select
	`id_pemain`,
	`nama_pemain`,
	`tim_asal`,
	`posisi`,
	`nilai_att`,
	`nilai_def`,
	`nilai_speed`,
	`nilai_stamina`,
	`rating`,
	`flag_limited`,
	`flag_tersedia`,
	`flag_paket`,
	`foto_pemain`
	from `pemain`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_pemain_ai`(p_id_tim_ai int (11))
BEGIN
	SELECT *
	FROM `pemain_tim_ai`, `pemain`
	WHERE
	`tim_ai_id_tim_ai` = p_id_tim_ai
	AND `pemain_id_pemain` = `id_pemain`
	AND `aktif` > 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_pemain_user`(p_username varchar (45))
BEGIN
  select 
    `id_user_pemain`,
    `id_pemain`,
    `nama_pemain`,
    `tim_asal`,
    `posisi`,
    `nilai_att`,
    `nilai_def`,
    `nilai_speed`,
    `nilai_stamina`,
    `rating`,
    `flag_limited`,
    `flag_tersedia`,
    `flag_paket`,
    `foto_pemain` 
  from
    `pemain`
    inner join `user_pemain` as up
    on `up`.`pemain_id_pemain` = `pemain`.`id_pemain` and `up`.`user_username` = p_username
  order by
	posisi = 'G' desc,
	posisi = 'D' desc,
	posisi = 'M' DESC,
	posisi = 'F' DESC
    ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_penawaran_keluar`(p_username varchar(45))
BEGIN
	SELECT
	`id_penawaran`,
	`harga_penawaran`,
	`jawaban`,
	`waktu_penawaran`,
	`terbaca`,
	`pemilik`,
	`penawar`,
	`penawaran`.`id_user_pemain`,
	`pesan_penawaran`,
	`pemain`.`id_pemain`,
	`nama_pemain`,
	`tim_asal`,
	`posisi`,
	`nilai_att`,
	`nilai_def`,
	`nilai_speed`,
	`nilai_stamina`,
	`nilai_stamina`,
	`rating`,
	`flag_limited`,
	`flag_tersedia`,
	`flag_paket`,
	`foto_pemain`
	FROM penawaran
	JOIN `user_pemain`
	ON `user_pemain`.`id_user_pemain` = penawaran.`id_user_pemain`
		AND penawaran.`penawar`=p_username
	JOIN pemain
	ON `user_pemain`.`pemain_id_pemain`=`pemain`.`id_pemain`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_penawaran_masuk`(p_username varchar (45))
BEGIN
	select
	`id_penawaran`,
	`harga_penawaran`,
	`jawaban`,
	`waktu_penawaran`,
	`terbaca`,
	`pemilik`,
	`penawar`,
	`penawaran`.`id_user_pemain`,
	`pesan_penawaran`,
	`pemain`.`id_pemain`,
	`nama_pemain`,
	`tim_asal`,
	`posisi`,
	`nilai_att`,
	`nilai_def`,
	`nilai_speed`,
	`nilai_stamina`,
	`nilai_stamina`,
	`rating`,
	`flag_limited`,
	`flag_tersedia`,
	`flag_paket`,
	`foto_pemain`
	from penawaran
	join `user_pemain`
	on `user_pemain`.`id_user_pemain` = penawaran.`id_user_pemain`
		and penawaran.pemilik=p_username
	join pemain
	on `user_pemain`.`pemain_id_pemain`=`pemain`.`id_pemain`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_stadion`()
BEGIN
	select `id_stadion`,`biaya_perawatan`,`harga_stadion`,`pendapatan_persen`,`gambar_stadion`
	from `stadion`;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_tim_liga`(p_id_liga int (11))
BEGIN
  select 
    `id_tim_ai`,
    `nama_tim_ai`,
    `kekompakan_tim_ai` 
  from
    `tim_ai` 
  where `liga_id_liga` = p_id_liga ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_detail_pemain`(p_id_user_pemain int(11))
BEGIN
select *
from `pemain`, `user_pemain`
where `id_user_pemain`=p_id_user_pemain and `pemain_id_pemain`=`id_pemain`;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_detail_pemain_id`(p_id int)
BEGIN
	select * from `pemain`
	where `id_pemain` = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_downgrade_stadion`(p_username VARCHAR(45))
start_label: BEGIN
DECLARE harga INT(10);
DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
START TRANSACTION;
	IF (SELECT 1 FROM `user` WHERE `stadion_id_stadion`=1 AND `username`=p_username) THEN
		SELECT -1 AS kode, 'Stadion Sudah Level Terbawah' AS pesan;
		LEAVE start_label;
	END IF;
	SELECT `harga_stadion` INTO harga FROM `stadion` WHERE `id_stadion` = (
		SELECT `stadion_id_stadion`
		FROM `user`
		WHERE `username` = p_username
	);
	
	set harga = harga*0.5;
	
	UPDATE `user` SET `stadion_id_stadion` = `stadion_id_stadion`-1 WHERE `username` = p_username;
	UPDATE `user` SET `uang` = `uang`+harga WHERE `username` = p_username;
	UPDATE `user` SET `biaya_perawatan_lastset_tu` = fn_server_time_TU() WHERE `username` = p_username;
	SELECT 0 AS kode, concat('Stadion Berhasil Didowngrade Anda Mendapatkan Uang ', harga) AS pesan;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllLimited`()
BEGIN
	select * from pemain
	where `flag_limited` = 1
	ORDER BY
	posisi = 'G' DESC,
	posisi = 'D' DESC,
	posisi = 'M' DESC,
	posisi = 'F' DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllSetting`()
BEGIN
	select
	`server_waktu_mulai`,
	`server_durasi_tu`,
	`ap_durasi_pertambahan_tu`,
	`ap_pertambahan`,
	`ap_maksimal`,
	`ap_nilai_awal`,
	`exp_nilai_awal`,
	`kekompakan_nilai_awal`,
	`kekompakan_maksimal`,
	`proteksi_durasi_tu`,
	`balen_nilai_awal`,
	`uang_nilai_awal`
	from `setting`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllTimAi`(p_username varchar (45))
BEGIN
	select `id_tim_ai`,
	`nama_tim_ai`,
	`kekompakan_tim_ai`,
	tim_ai.`liga_id_liga`,
	fn_nama_liga (tim_ai.`liga_id_liga`) AS nama_liga,
	tim_ai.`formasi_id_formasi`,
	fn_nama_formasi (tim_ai.`formasi_id_formasi`) AS nama_formasi,
	`exp_gain`,
	`uang_gain`,
	(SELECT 1 FROM `ai_user` WHERE `user_username`=p_username AND `tim_ai_id_tim_ai`=tim_ai.`id_tim_ai`) as stat
	from `tim_ai`, `user`
	where tim_ai.`liga_id_liga` = user.`liga_id_liga`
	and user.`username` = p_username;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllUser`()
BEGIN
	select
		`username`,
		`nama_tim`,
		`fn_rating_tim`(username) as rating_tim,
		fn_exp(username) as `exp`
	from `user`
	ORDER BY `exp` DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllUser_Offset`(off int, per_page int)
BEGIN
	SELECT
		`username`,
		`nama_tim`,
		`fn_rating_tim`(username) AS rating_tim,
		fn_exp(username) AS `exp`,
		`foto_user`
	FROM `user`
	ORDER BY `exp` desc
	LIMIT off, per_page;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getFormasiAI`(p_id int)
BEGIN
	select `nama_formasi` from `formasi`, `tim_ai`
	where `id_formasi` = `formasi_id_formasi`
	and `id_tim_ai` = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getLimited`()
BEGIN
	select * from `pemain`
	where `flag_limited` = 1
	and `flag_tersedia` != 0
	ORDER BY
	posisi = 'G' DESC,
	posisi = 'D' DESC,
	posisi = 'M' DESC,
	posisi = 'F' DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getPaket`()
BEGIN
	select `id_paket`,
	`nama_paket`,
	`harga_uang`,
	`harga_balen`,
	`deskripsi_paket`
	from `paket`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getUserInfo`(p_username varchar (45))
BEGIN
  select 
    `nama_tim`,
    `nama_stadion`,
    `email`,
    `balen`,
    `uang`,
    `exp`,
    fn_rating_tim(p_username) as rating,
    fn_kekompakan (p_username) as kekompakan,
    setting.`kekompakan_maksimal`,
    fn_ap (p_username) as ap,
    setting.`ap_maksimal`,
    `foto_user`,
    `formasi_id_formasi` as id_formasi,
    fn_nama_formasi (`formasi_id_formasi`) as nama_formasi,
    `liga_id_liga`,
    fn_nama_liga (`liga_id_liga`) as nama_liga,
    (select `gambar_liga` from `liga` where `id_liga` = liga_id_liga) as gambar_liga,
    `latihan_id_latihan`,
    fn_nama_latihan (`latihan_id_latihan`) as nama_latihan,
    `stadion_id_stadion` 
  from
    `user`,
    `setting` 
  where `username` = p_username ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_latihan`(
  p_username varchar (45),
  p_id_latihan int (11)
)
start_label: BEGIN
DECLARE durasi_lat, id_lat, kompak_last_set_tu int(10);
declare tambah_kekompakan, kompak_last_set_val tinyint(3);
DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
BEGIN
	ROLLBACK;
	SELECT -1 as kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' as pesan;
END;
select `latihan_id_latihan` into id_lat from `user` where `username`=p_username;
if (id_lat is null) then
	update `user`
	set `kekompakan_lastset_tu`=`fn_server_time_TU`(), `latihan_id_latihan`=p_id_latihan
	where `username`=p_username;
	select 0 as kode, 'latihan dimulai' as pesan;
	LEAVE start_label;
end if;
select `durasi_latihan_tu` into durasi_lat from `latihan` where `id_latihan`=id_lat;
select `penambahan_kekompakan` into tambah_kekompakan FROM `latihan` WHERE `id_latihan`=id_lat;
select `kekompakan_lastset_tu` into kompak_last_set_tu from `user` where `username`=p_username;
select `kekompakan_lastset_value` into kompak_last_set_val FROM `user` WHERE `username`=p_username;
 
if (`fn_server_time_TU`() > kompak_last_set_tu+durasi_lat) then
	call sp_setKekompakan(p_username, kompak_last_set_val+tambah_kekompakan);
	SELECT `latihan_id_latihan` INTO id_lat FROM `user` WHERE `username`=p_username;
	IF (id_lat IS NULL) THEN
		UPDATE `user`
		SET `kekompakan_lastset_tu`=`fn_server_time_TU`(), `latihan_id_latihan`=p_id_latihan
		WHERE `username`=p_username;
		SELECT 0 AS kode, 'latihan dimulai' AS pesan;
		LEAVE start_label;
	END IF;
else
	select -1 as kode, 'latihan lain sedang dilakukan' as pesan;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login`(
  p_username varchar (45),
  p_password varchar (45)
)
BEGIN
  IF 
  (SELECT 
    1 
  FROM
    `user` 
  WHERE (
      `username` = p_username 
      or `email` = p_username
    ) 
    AND `password` = md5(p_password)) 
  THEN 
  SELECT 
    0 AS kode,
    'login sukses !' AS pesan ;
  ELSE 
  SELECT 
    - 1 AS kode,
    'GAGAL ! username atau password tidak dikenal.' AS pesan ;
  END IF ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_admin`(
  p_username varchar (45),
  p_password varchar (45)
)
BEGIN
	if (select 1 from `admin` where `username_admin`=p_username and `password_admin`=p_password) then
		SELECT 0 AS kode, 'login sukses !' AS pesan ;
	else
		SELECT -1 AS kode, 'login gagal !' AS pesan ;
	end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_register`(
  p_username varchar (45),
  p_password varchar (45),
  p_nama_tim varchar (45),
  p_nama_stadion varchar (45),
  p_email varchar (45),
  p_foto_user varchar (45)
)
start_label :
BEGIN
  IF 
  (SELECT 
    1 
  FROM
    `user` 
  WHERE `username` = p_username) 
  THEN 
  SELECT 
    - 1 AS kode,
    'Username Sudah Digunakan !' AS pesan ;
  LEAVE start_label ;
  end if ;
  IF 
  (SELECT 
    1 
  FROM
    `user` 
  WHERE `email` = p_email) 
  THEN 
  SELECT 
    - 1 AS kode,
    'Email Sudah Digunakan !' AS pesan ;
  LEAVE start_label ;
  end if ;
  insert into `user` (
    `username`,
    `password`,
    `nama_tim`,
    `nama_stadion`,
    `email`,
    `balen`,
    `uang`,
    `exp`,
    `kekompakan_lastset_tu`,
    `kekompakan_lastset_value`,
    `ap_lastset_tu`,
    `ap_lastset_value`,
    `foto_user`,
    `formasi_id_formasi`,
    `liga_id_liga`,
    `latihan_id_latihan`,
    `stadion_id_stadion`
  ) 
  select 
    p_username,
    md5(p_password),
    p_nama_tim,
    p_nama_stadion,
    p_email,
    setting.`balen_nilai_awal`,
    setting.`uang_nilai_awal`,
    setting.`exp_nilai_awal`,
    fn_server_time_TU (),
    setting.`kekompakan_nilai_awal`,
    fn_server_time_TU (),
    setting.`ap_nilai_awal`,
    p_foto_user,
    1,
    1,
    null,
    1 
  from
    setting ;
  SELECT 
    0 AS kode,
    'Pendaftaran sukses !|Anda bisa login dengan username dan password anda.' AS pesan ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_release_pemain`(p_username varchar (45), p_id int)
start_label: BEGIN
declare harga, tmp int;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
	SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	#show errors;
	#show warnings;
END;
start transaction;
	if ((select 1 from `user_pemain` where `user_username`=p_username and `id_user_pemain`=p_id) is null) then
		select -1 as kode, 'Pemain Tidak Valid!' as pesan;
		leave start_label;
	end if;
	
	select `flag_paket` into tmp from `pemain`, `user_pemain`
	where `user_pemain`.`pemain_id_pemain` = `pemain`.`id_pemain`
	and `user_pemain`.`id_user_pemain` = p_id;
	
	if (tmp = 3) then
		SET harga = 5000;
	elseif (tmp = 2) then
		SET harga = 1000;
	elseif (tmp = 1) then
		SET harga = 500;
	else
		set harga = 20000;
	end if;
	
	DELETE FROM `user_pemain` WHERE `id_user_pemain` = p_id;
	update `user` set `uang`=`uang`+harga where `username`=p_username;
	
	select 0 as kode, concat('Pemain Berhasil Di Release. Anda Mendapat Kompensasi ', harga) as pesan;
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_setAP`(
  p_username varchar (45),
  p_newAP tinyint (3)
)
BEGIN
  UPDATE 
    `user` 
  SET
    `ap_lastset_tu` = fn_server_time_TU (),
    `ap_lastset_value` = p_newAP 
  WHERE `username` = p_username ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_setKekompakan`(
  p_username VARCHAR (45),
  p_newKekompakan TINYINT (3)
)
BEGIN
  UPDATE 
    `user`, setting 
  SET
    `latihan_id_latihan` = NULL,
    `kekompakan_lastset_tu` = fn_server_time_TU (),
    `kekompakan_lastset_value` = if (
      p_newKekompakan > setting.`kekompakan_maksimal`,
      setting.`kekompakan_maksimal`,
      p_newKekompakan
    ) 
  WHERE `username` = p_username ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambahBalen`(
  p_username varchar (45),
  kode varchar (45)
)
BEGIN
declare p_balen int;
start transaction;
	select `penambahan_balen` into p_balen
	from `voucher` 
	where `code_voucher` = kode
	and `flag` = 0
	and `kadaluarsa` >= now();
	
	if (p_balen is null) then
		select -1 as kode, 'Kode Voucher Tidak Valid!' as pesan;
	else
		update `user`
		set `balen` = `balen` + p_balen
		where `username` = p_username;
		
		update `voucher`
		set `flag` = 1,
		`user_username` = p_username
		where `code_voucher` = kode;
		
		select 0 as kode, concat('Balen Berhasil Ditambah ', p_balen) as pesan;
		
	end if;
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_formasi`(p_nama_formasi varchar(45))
BEGIN
	insert into `formasi` (`nama_formasi`)
	select p_nama_formasi;
	select 0 as kode, 'Penambahan Formasi Berhasil' as pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_latihan`(
  p_nama_latihan varchar (45),
  p_durasi int (10),
  p_penambahan tinyint (3),
  p_gambar varchar (45)
)
BEGIN
	insert into `latihan`(
	`nama_latihan`,
	`durasi_latihan_tu`,
	`penambahan_kekompakan`,
	`gambar_latihan`
	)
	select
	p_nama_latihan,
	p_durasi,
	p_penambahan,
	p_gambar;
	
	select 0 as kode, 'Penambahan Latihan Berhasil' as pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_pemain`(
  p_nama_pemain varchar (45),
  p_tim_asal VARCHAR (45),
  p_posisi char(1),
  p_nilai_att int (10),
  p_nilai_def INT (10),
  p_nilai_speed INT (10),
  p_nilai_stamina INT (10),
  p_flag_limited tinyint (1),
  p_flag_tersedia TINYINT (1),
  p_flag_paket TINYINT (1),
  p_foto_pemain VARCHAR (45)
)
BEGIN
	#versi biasa
	insert into `pemain`(
	`nama_pemain`,
	`tim_asal`,
	`posisi`,
	`nilai_att`,
	`nilai_def`,
	`nilai_speed`,
	`nilai_stamina`,
	`rating`,
	`flag_limited`,
	`flag_tersedia`,
	`flag_paket`,
	`foto_pemain`
	)
	select
	p_nama_pemain,
	p_tim_asal,
	p_posisi,
	p_nilai_att,
	p_nilai_def,
	p_nilai_speed,
	p_nilai_stamina,
	fn_rating_pemain(p_posisi, p_nilai_att, p_nilai_def, p_nilai_speed, p_nilai_stamina),
	p_flag_limited,
	p_flag_tersedia,
	p_flag_paket,
	p_foto_pemain;
	
	#versi Limited
	INSERT INTO `pemain`(
	`nama_pemain`,
	`tim_asal`,
	`posisi`,
	`nilai_att`,
	`nilai_def`,
	`nilai_speed`,
	`nilai_stamina`,
	`rating`,
	`flag_limited`,
	`flag_tersedia`,
	`flag_paket`,
	`foto_pemain`
	)
	SELECT
	p_nama_pemain,
	p_tim_asal,
	p_posisi,
	p_nilai_att,
	p_nilai_def,
	p_nilai_speed,
	p_nilai_stamina,
	fn_rating_pemain(p_posisi, p_nilai_att, p_nilai_def, p_nilai_speed, p_nilai_stamina)+4,
	1,
	0,
	4,
	p_foto_pemain;
	
	SELECT 0 AS kode, 'Penambahan Pemain Sukses' AS pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tanding_ai`(IN `att` VARCHAR(45), IN `def` INT(11))
start_label:BEGIN
DECLARE rating_att, rating_def, kekompakan_att, kekompakan_def, total_att, total_def, rumus_formasi, nk, hasil, exp_gain, kekompakan_loss, uang_gain DOUBLE;
declare ctr_max, ctr, hadiah_ap, hadiah_exp, hadiah_uang int;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
	SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	#SHOW WARNINGS;
	#SHOW ERRORS;
END;
	IF ((SELECT 1 FROM `user` WHERE `username`=att) IS NULL) THEN
		SELECT -1 AS kode, 'Username Tidak Valid' AS pesan;
		LEAVE start_label;
	END IF;
	IF ((SELECT 1 FROM `tim_ai` WHERE `id_tim_ai`=def) IS NULL) THEN
		SELECT -1 AS kode, 'Tim AI Tidak Valid' AS pesan;
		LEAVE start_label;
	END IF;
	if ((select 1 from `tim_ai`, `user` where tim_ai.`id_tim_ai` = def and user.`username`=att and tim_ai.`liga_id_liga`=user.`liga_id_liga`) is null) then
		SELECT -1 AS kode, 'Tim AI Tidak Satu Liga' AS pesan;
		LEAVE start_label;
	end if;
	IF (fn_ap(att)<=0) THEN
		SELECT -1 AS kode, 'Poin Aksi Tidak Cukup!' AS pesan;
		LEAVE start_label;
	END IF;
	IF (SELECT 1 FROM `ai_user` WHERE `user_username`=att AND `tim_ai_id_tim_ai`=def) THEN
		SELECT -1 AS kode, 'Anda Sudah Mengalahkan Tim Ini' AS pesan;
		LEAVE start_label;
	END IF;
	IF (fn_hitungLineup(att) != 11) THEN
		SELECT -1 AS kode, 'Lineup Pemain Kurang' AS pesan;
		LEAVE start_label;
	END IF;
	
	START TRANSACTION;
	SELECT fn_rating_tim(att) INTO rating_att;
	SELECT fn_rating_tim_ai(def) INTO rating_def;
	SELECT fn_kekompakan(att) INTO kekompakan_att;
	SELECT `kekompakan_tim_ai` INTO kekompakan_def from `tim_ai` where `id_tim_ai` = def;
	SELECT fn_rumus_formasi_ai(att, def) INTO rumus_formasi;
	IF (rumus_formasi IS NULL) THEN SET rumus_formasi = 100; END IF;
	IF (rating_def IS NULL) THEN SET rating_def = 0; END IF;
	
	SET total_att = rating_att * kekompakan_att/100 * rumus_formasi/100;
	SET total_def = rating_def * kekompakan_def/100;
	SET nk = total_att/total_def * 100;
	
	IF (nk >= 95 AND nk <= 101) THEN
		SET hasil = RAND() % 3;
	ELSEIF (nk < 95 AND nk > 86) THEN
		SET hasil = RAND() % 2;
	ELSEIF (nk <= 86) THEN
		SET hasil = 0;
	ELSEIF (nk >= 102 AND nk <= 110) THEN
		SET hasil = (RAND() % 2) + 1;
	ELSE
		SET hasil = 2;
	END IF;
	
	CALL sp_setAP(att, fn_AP(att)-1);
	
	IF (hasil = 2) THEN #menang
		INSERT INTO `ai_user` (`user_username`, `tim_ai_id_tim_ai`)
		VALUES(att, def);
		
		SELECT tim_ai.`exp_gain` INTO exp_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SELECT tim_ai.`uang_gain` INTO uang_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SET kekompakan_loss = 0;
			
		#sudah mengalahkan brp tim ai
		SELECT COUNT(1) into ctr FROM `ai_user`, `user`, `tim_ai`
		WHERE ai_user.`user_username`=att
		AND ai_user.`tim_ai_id_tim_ai`=tim_ai.`id_tim_ai`
		AND tim_ai.`liga_id_liga`=user.`liga_id_liga`
		AND user.`username`=att;
		
		#tim ai yang ada dalam satu liga
		select count(1) into ctr_max from `tim_ai`, `user`
		where tim_ai.`liga_id_liga` = user.`liga_id_liga`
		and user.`username`=att;
		
		#sudah mengalahkan seluruh tim_ai alias juara liga
		if (ctr = ctr_max) then
			select liga.`hadiah_ap` into hadiah_ap from `liga`, `user` where liga.`id_liga` = user.`liga_id_liga` and user.`username`=att;
			select liga.`hadiah_exp` into hadiah_exp FROM `liga`, `user` WHERE liga.`id_liga` = user.`liga_id_liga` AND user.`username`=att;
			select liga.`hadiah_uang` into hadiah_uang FROM `liga`, `user` WHERE liga.`id_liga` = user.`liga_id_liga` AND user.`username`=att;
			
			#berikan hadiah
			update `user` set
			`uang`=`uang`+hadiah_uang,
			`exp`=`exp`+hadiah_exp
			where `username`=att;
			call `sp_setAP`(att, fn_AP(att)+hadiah_ap);
			
			#naikkan liga
			if ((select `liga_id_liga` from `user` where `username`=att) != 4) then
				update `user` set `liga_id_liga`=`liga_id_liga`+1 where `username`=att;
			end if;	
			
			#select hadiah_uang, hadiah_exp, hadiah_ap;
			select 0 as kode, concat('Selamat Anda Menjuarai Liga. Hadiah Uang ', hadiah_uang, ', Hadiah EXP ', hadiah_exp, ', Hadiah AP ', hadiah_ap) as pesan;
		else
			SELECT 0 AS kode, CONCAT('Anda Menang. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
		end if;
	ELSEif (hasil = 1) then
		SELECT tim_ai.`exp_gain` INTO exp_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SELECT tim_ai.`uang_gain` INTO uang_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SET exp_gain = FLOOR(exp_gain * 33 / 100);
		SET uang_gain = FLOOR(uang_gain * 33 / 100);
		SET kekompakan_loss = 2;
		
		SELECT -1 AS kode, CONCAT('Anda Seri. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
	else
		SELECT tim_ai.`exp_gain` INTO exp_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SELECT tim_ai.`uang_gain` INTO uang_gain FROM `tim_ai` WHERE `id_tim_ai`=def;
		SET exp_gain = FLOOR(exp_gain * 10 / 100);
		SET uang_gain = FLOOR(uang_gain * 10 / 100);
		SET kekompakan_loss = 5;
		
		SELECT -1 AS kode, CONCAT('Anda Kalah. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
	END IF;
	
	UPDATE `user` 
	SET `exp`=`exp`+exp_gain,
	`kekompakan_lastset_value`=`kekompakan_lastset_value`-kekompakan_loss,
	`uang`=`uang`+uang_gain
	WHERE `username`=att;
	
	#select total_att, total_def, nk, rating_att, rating_def, kekompakan_att, kekompakan_def, rumus_formasi, exp_gain, uang_gain, kekompakan_loss;
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tanding_pemain`(att varchar(45), def varchar(45))
start_label:BEGIN
DECLARE rating_att, rating_def, kekompakan_att, kekompakan_def, total_att, total_def, rumus_formasi, nk, hasil, exp_gain, kekompakan_loss, uang_gain double;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
	SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	#SHOW WARNINGS;
	#SHOW ERRORS;
END;
    if ((select 1 from `user` where `username`=att) is null) then
	SELECT -1 AS kode, 'Username Tidak Valid' AS pesan;
	LEAVE start_label;
    end if;
    
    IF ((SELECT 1 FROM `user` WHERE `username`=def) is null) THEN
	SELECT -1 AS kode, 'Username Tidak Valid' AS pesan;
	LEAVE start_label;
    END IF;
    
    if (att=def) then
	SELECT -1 AS kode, 'Username Sama' AS pesan;
	LEAVE start_label;
    end if;
    
    IF (fn_ap(att)<=0) THEN
	SELECT -1 AS kode, 'Poin Aksi Tidak Cukup!' AS pesan;
	LEAVE start_label;
    END IF;
    
    IF (`fn_statusProteksi`(att, def)) THEN
	SELECT -1 AS kode, 'Lawan Sedang Terlindungi!' AS pesan;
	LEAVE start_label;
    END IF;
    
    IF (fn_hitungLineup(att) != 11) THEN
	SELECT -1 AS kode, 'Lineup Pemain Kurang' AS pesan;
	LEAVE start_label;
    END IF;
	
    START TRANSACTION;
	SELECT fn_rating_tim(att) INTO rating_att;
	sELECT fn_rating_tim(def) INTO rating_def;
	SELECT fn_kekompakan(att) INTO kekompakan_att;
	SELECT fn_kekompakan(def) INTO kekompakan_def;
	select fn_rumus_formasi(att, def) into rumus_formasi;
	if (rumus_formasi is null) then set rumus_formasi = 100; end if;
	
	set total_att = rating_att * kekompakan_att/100 * rumus_formasi/100;
	set total_def = rating_def * kekompakan_def/100;
	set nk = total_att/total_def * 100;
	
	#select total_att, total_def, nk;
	
	if (nk >= 95 and nk <= 101) then
		set hasil = rand() % 3;
	elseif (nk < 95 and nk > 86) then
		set hasil = rand() % 2;
	elseif (nk <= 86) then
		set hasil = 0;
	elseif (nk >= 102 and nk <= 110) then
		set hasil = (rand() % 2) + 1;
	else
		set hasil = 2;
	end if;
	
	if (hasil = 2) then
		if (select 1 from `proteksi_user` where `user_username`=att and `user_username1`=def) then
			update `proteksi_user` set `proteksi_lastset_tu` = fn_server_time_TU()
			where `user_username`=att AND `user_username1`=def;
		else
			insert into `proteksi_user` (`proteksi_lastset_tu`, `user_username`, `user_username1`)
			values(fn_server_time_TU(), att, def);
		end if;
		set exp_gain = floor(9 * fn_exp(def) / 100);
		set uang_gain = floor(fn_exp(def) * 5 * fn_persentase_stadion(att) / 100);
		set kekompakan_loss = 0;
		
		SELECT 0 AS kode, concat('Anda Menang. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
	elseif (hasil = 1) then
	
		SET exp_gain = floor(3 * fn_exp(def) / 100);
		SET uang_gain = floor(fn_exp(def) * 3 * fn_persentase_stadion(att) / 100);		
		SET kekompakan_loss = 1;
		
		SELECT -1 AS kode, CONCAT('Anda Seri. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
	else
		
		SET exp_gain = floor(1 * fn_exp(def) / 100);
		SET uang_gain = floor(fn_exp(def) * 0.5 * fn_persentase_stadion(att) / 100);
		SET kekompakan_loss = 3;
		
		SELECT -1 AS kode, CONCAT('Anda Kalah. EXP Gain ',exp_gain,', Uang Gain ', uang_gain, ', Kekompakan Loss ', kekompakan_loss) AS pesan;
	end if;
	
	CALL sp_setAP(att, fn_AP(att)-1);
	update `user` 
	set `exp`=`exp`+exp_gain,
	`kekompakan_lastset_value`=`kekompakan_lastset_value`-kekompakan_loss,
	`uang`=`uang`+uang_gain
	where `username`=att;
	#select rating_att, rating_def, kekompakan_att, kekompakan_def, rumus_formasi;
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tawar_pemain`(
  p_harga int (10),
  p_pemilik varchar (45),
  p_penawar varchar (45),
  p_id_pemain int (11),
  p_pesan text
)
start_label: BEGIN
	declare temp_id int;
	
	if (p_pemilik = p_penawar) then
		SELECT -1 AS kode, 'Anda Tidak Bisa Menawar Pemain Anda Sendiri !' AS pesan;
		LEAVE start_label;
	END IF;
	if (p_harga > `fn_uang`(p_penawar)) then
		SELECT -1 AS kode, 'Uang Anda Tidak Mencukupi !' AS pesan;
		LEAVE start_label;
	end if;
	
	if (select 1 from `penawaran` where `pemilik`=p_pemilik and `penawar`=p_penawar and `id_user_pemain`=p_id_pemain and `jawaban`=99) then
		SELECT -1 AS kode, 'anda sedang menawar pemain ini, tunggulah sampai ada jawaban dari pemiliknya' AS pesan;
		LEAVE start_label;
	end if;
	
	select pemain_id_pemain into temp_id from user_pemain where `id_user_pemain`=p_id_pemain;
	
	if (select 1 from user_pemain where user_username = p_penawar and pemain_id_pemain = temp_id) then
		select -1 as kode, 'Anda Sudah Memiliki Pemain Ini!' as pesan;
		LEAVE start_label;
	end if;
	
	insert into `penawaran`(
	`harga_penawaran`,
	`pemilik`,
	`penawar`,
	`id_user_pemain`,
	`pesan_penawaran`
	)
	select p_harga, p_pemilik, p_penawar, p_id_pemain, p_pesan;
	SELECT 0 AS kode, 'anda berhasil menawar pemain ini, tunggulah jawaban dari pemiliknya' AS pesan;
	LEAVE start_label;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_terima_tawaran`(p_username varchar(45), p_id int(11))
start_label: BEGIN
	DECLARE harga, v_id_user_pemain INT(11);
	declare v_penawar varchar(45);
	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
START TRANSACTION;
	IF ((SELECT 1 FROM `penawaran` WHERE `pemilik`=p_username AND `id_penawaran`=p_id)IS NULL) THEN
		SELECT -1 AS kode, 'Bukan Penawaran Anda' AS pesan;
		LEAVE start_label;
	END IF;
	IF (fn_jumlah_pemain_tim(p_username) + 1 > (SELECT setting.`pemain_tim_max` FROM setting)) THEN
		SELECT -1 AS kode, 'Pemain Anda Mencapai Batas Maksimal. Release Pemain Dulu!' AS pesan;
		LEAVE start_label;
	END IF;
	IF ((SELECT `jawaban` FROM `penawaran` WHERE `pemilik`=p_username AND `id_penawaran`=p_id) != 99) THEN
		SELECT -1 AS kode, 'Sudah Dijawab' AS pesan;
		LEAVE start_label;
	END IF;
	SELECT `harga_penawaran` INTO harga FROM `penawaran` WHERE `id_penawaran`=p_id;
	SELECT `penawar` INTO v_penawar FROM `penawaran` WHERE `id_penawaran`=p_id;
	SELECT `id_user_pemain` INTO v_id_user_pemain FROM `penawaran` WHERE `id_penawaran`=p_id;
	if (fn_uang(v_penawar) < harga) then
		update `penawaran` set `jawaban` = 2 where `id_penawaran`=p_id;
		SELECT -1 AS kode, 'Uang Tidak Mencukupi' AS pesan;
		LEAVE start_label;
	end if;
	
	#proses diterima
	UPDATE `penawaran` SET `jawaban` = 1 WHERE `id_penawaran`=p_id;
	UPDATE `user` SET `uang` = `uang`-harga WHERE `username`=v_penawar;
	UPDATE `user` SET `uang` = `uang`+harga WHERE `username`=p_username;
	update `user_pemain` set `user_username`=v_penawar, `aktif`=0 where `id_user_pemain`=v_id_user_pemain;
	
	SELECT 0 AS kode, CONCAT('Tawaran Berhasil Diterima, Uang Bertambah ', harga, ' Menjadi ', fn_uang(p_username)) AS pesan;
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tolak_tawaran`(p_username VARCHAR(45), p_id INT(11))
start_label: BEGIn
	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
START TRANSACTION;
	IF ((SELECT 1 FROM `penawaran` WHERE `pemilik`=p_username AND `id_penawaran`=p_id)IS NULL) THEN
		SELECT -1 AS kode, 'Bukan Penawaran Anda' AS pesan;
		LEAVE start_label;
	END IF;
	IF ((SELECT `jawaban` FROM `penawaran` WHERE `pemilik`=p_username AND `id_penawaran`=p_id) != 99) THEN
		SELECT -1 AS kode, 'Sudah Dijawab' AS pesan;
		LEAVE start_label;
	END IF;
	
	#proses ditolak
	UPDATE `penawaran` SET `jawaban` = 0 WHERE `id_penawaran`=p_id;
	SELECT 0 AS kode, 'Tawaran Berhasil Ditolak' AS pesan;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ubahSetting`(
  p_server_waktu_mulai datetime,
  p_server_durasi_tu time,
  p_ap_durasi_pertambahan_tu tinyint (3),
  p_ap_pertambahan tinyint (3),
  p_ap_maksimal tinyint (3),
  p_ap_nilai_awal tinyint (3),
  p_exp_nilai_awal int (10),
  p_kekompakan_nilai_awal tinyint (3),
  p_kekompakan_maksimal tinyint (3),
  p_proteksi_durasi_tu tinyint (3),
  p_balen_nilai_awal int (10),
  p_uang_nilai_awal int (10)
)
BEGIN
	update `setting`
	set
	`server_waktu_mulai` = p_server_waktu_mulai,
	`server_durasi_tu` = p_server_durasi_tu,
	`ap_durasi_pertambahan_tu` = p_ap_durasi_pertambahan_tu,
	`ap_pertambahan` = p_ap_pertambahan,
	`ap_maksimal` = p_ap_maksimal,
	`ap_nilai_awal` = p_ap_nilai_awal,
	`exp_nilai_awal` = p_exp_nilai_awal,
	`kekompakan_nilai_awal` = p_kekompakan_nilai_awal,
	`kekompakan_maksimal` = p_kekompakan_maksimal,
	`proteksi_durasi_tu` = p_proteksi_durasi_tu,
	`balen_nilai_awal` = p_balen_nilai_awal,
	`uang_nilai_awal` = p_uang_nilai_awal;
	
	select 0 as kode, 'Setting Berhasil Disimpan' as pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ubah_password`(
  p_username varchar (45),
  p_lama varchar (45),
  p_baru varchar (45),
  p_rebaru varchar (45)
)
BEGIN
	if ((select 1 from `user` where `username`=p_username and `password`=md5(p_lama)) and p_baru = p_rebaru) then
		update `user` set `password` = md5(p_baru)
		where `username`=p_username;
		select 0 as kode, 'Password Berhasil Diperbarui' as pesan;
	elseif (p_baru != p_rebaru) then
		select -1 as kode, 'Password Tidak Sama' as pesan;
	else
		select -1 as kode, 'Password Lama Salah' as pesan;
	end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ubah_profil`(
  p_username varchar (45),
  p_nama_tim varchar (45),
  p_nama_stadion varchar (45),
  p_email varchar (45),
  p_foto varchar (45)
)
start_label: BEGIN
IF 
  (SELECT 
    1 
  FROM
    `user` 
  WHERE `email` = p_email and `username` != p_username) 
  THEN 
  SELECT 
    - 1 AS kode,
    'Email Sudah Digunakan !' AS pesan ;
  LEAVE start_label ;
  END IF ;
  
	update `user`
	set `nama_tim` = p_nama_tim, `nama_stadion` = p_nama_stadion, `email` = p_email
	where `username` = p_username;
	
	if (p_foto is not null and p_foto != "") then
		UPDATE `user`
		SET `foto_user` = p_foto
		WHERE `username` = p_username;
	end if;
	
	SELECT 
    0 AS kode,
    'Profil Berhasil Diubah' AS pesan ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_formasi`(p_username varchar(45), p_id_formasi int(10))
BEGIN
DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
BEGIN
	ROLLBACK;
	SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
END;
	update `user` set `formasi_id_formasi` = p_id_formasi where `username` = p_username;
	SELECT 0 AS kode, 'Formasi Berhasil Diganti' AS pesan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_lineup`(
	p_username VARCHAR(45), 
	p_id_user_pemain INT(11), 
	p_aktif TINYINT(1)
)
BEGIN
DECLARE id_up INT(11);
   START TRANSACTION;
	if (select 1 from `user_pemain` where `user_username`=p_username and `id_user_pemain`=p_id_user_pemain) then
	   select `id_user_pemain` into id_up from `user_pemain` where `user_username`=p_username AND `aktif`=p_aktif;
	   update `user_pemain` set `aktif` = 0 where `id_user_pemain` = id_up;
	   UPDATE `user_pemain` SET `aktif` = p_aktif  WHERE `id_user_pemain` = p_id_user_pemain;
	   SELECT 0 AS kode, 'Lineup Berhasil Diupdate' AS pesan;
	else
	   SELECT -1 AS kode, 'Lineup GAGAL Diupdate' AS pesan;
	END IF;
   commit;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_lineup_limited`(p_id int, pos int)
BEGIN
DECLARE id_up INT(11);
   START TRANSACTION;
	IF (SELECT 1 FROM `pemain` WHERE `id_pemain`=p_id) THEN
	   SELECT `id_pemain` INTO id_up FROM `pemain` WHERE `flag_tersedia`=pos;
	   UPDATE `pemain` SET `flag_tersedia` = 0 WHERE `id_pemain` = id_up;
	   UPDATE `pemain` SET `flag_tersedia` = pos  WHERE `id_pemain` = p_id;
	   SELECT 0 AS kode, 'Lineup Berhasil Diupdate' AS pesan;
	ELSE
	   SELECT -1 AS kode, 'Lineup GAGAL Diupdate' AS pesan;
	END IF;
   COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_upgrade_stadion`(p_username varchar(45))
start_label: BEGIN
DECLARE harga INT(10);
DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
	BEGIN
		ROLLBACK;
		SELECT -1 AS kode, 'Telah terjadi kesalahan. Coba ulangi proses anda.' AS pesan;
	END;
START TRANSACTION;
	if (select 1 from `user` where `stadion_id_stadion`=4 and `username`=p_username) then
		select -1 as kode, 'Stadion Sudah Level Teratas' as pesan;
		leave start_label;
	end if;
	select `harga_stadion` into harga from `stadion` where `id_stadion` = (
		select `stadion_id_stadion` + 1
		from `user`
		where `username` = p_username
	);
	
	if (`fn_uang`(p_username) < harga) then
		SELECT -1 AS kode, 'Uang Anda Tidak Mencukupi' AS pesan;
		LEAVE start_label;
	end if;
	
	UPDATE `user` SET `stadion_id_stadion` = `stadion_id_stadion`+1 WHERE `username` = p_username;
	UPDATE `user` SET `uang` = `uang`-harga WHERE `username` = p_username;
	UPDATE `user` SET `biaya_perawatan_lastset_tu` = fn_server_time_TU() WHERE `username` = p_username;
	SELECT 0 AS kode, 'Stadion Berhasil Diupgrade' AS pesan;
commit;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `trial`(p_userid varchar(45))
BEGIN
	DECLARE rating, def_limit, mid_limit, att_limit, minus_rate, counter, pl, temp_rate INT(10);
        DECLARE nama_form VARCHAR(45);  
        DECLARE temp_posisi CHAR(1); 
        
        SELECT nama_formasi INTO nama_form
	FROM `formasi`
	WHERE `id_formasi` = (SELECT `formasi_id_formasi` FROM`user` WHERE username = p_userid); 
	
	SELECT CONVERT(SUBSTRING(nama_form, 1, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO def_limit;
	SELECT CONVERT(SUBSTRING(nama_form, 3, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO mid_limit;
	SELECT CONVERT(SUBSTRING(nama_form, 5, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO att_limit;
	
	SET minus_rate = 0;
	SET counter = 0;
	####F
	SET pl = 0;
	WHILE pl < att_limit DO
		SET pl = pl + 1;
		SET counter = counter + 1;
		SELECT `posisi` INTO temp_posisi
		FROM `pemain`
		WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
		IF ( temp_posisi != 'F') THEN
			SELECT pemain.`rating` into temp_rate
			FROM `pemain`
			WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
			SET minus_rate = minus_rate + temp_rate;
		END IF;
	END WHILE;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_ap`(p_username varchar (45)) RETURNS tinyint(3)
BEGIN
  RETURN 
  (SELECT 
    IF (
      `ap_lastset_value` + FLOOR(
        (
          fn_server_time_TU () - `ap_lastset_tu`
        ) / setting.`ap_durasi_pertambahan_tu`
      ) * setting.`ap_pertambahan` < setting.`ap_maksimal`,
      `ap_lastset_value` + FLOOR(
        (
          fn_server_time_TU () - `ap_lastset_tu`
        ) / setting.`ap_durasi_pertambahan_tu`
      ) * setting.`ap_pertambahan`,
      setting.`ap_maksimal`
    ) 
  FROM
    `user`,
    `setting` 
  WHERE `username` = p_username) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_count_all_user`() RETURNS int(11)
BEGIN
  return (
	select count(1) from `user`
  );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_exp`(p_username varchar (45)) RETURNS int(11)
BEGIN
  return (
	select `exp` from `user` where `username` = p_username
  );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitungLineup`(p_username varchar (45)) RETURNS int(11)
BEGIN
  return (
	select count(1) from `user_pemain` where `user_username`=p_username and `aktif`>0
  );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_jumlah_pemain_tim`(p_username varchar (45)) RETURNS int(11)
BEGIN
  return (
	select count(1) from `user_pemain` where `user_username` = p_username
  );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_kekompakan`(p_username varchar(45)) RETURNS tinyint(3)
BEGIN
	return(
		select `kekompakan_lastset_value`
		from `user`
		where `username` = p_username
	);
    END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_level`(p_username varchar (45)) RETURNS int(11)
BEGIN
  declare expe double ;
  SELECT 
    `exp` INTO expe 
  FROM
    `user` 
  WHERE `username` = p_username ;
  return (floor((sqrt(625+100 * expe) - 25) / 50) + 1) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_nama_formasi`(p_id_formasi int (11)) RETURNS varchar(45) CHARSET utf8
BEGIN
  return 
  (select 
    `nama_formasi` 
  from
    `formasi` 
  where `id_formasi` = p_id_formasi) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_nama_latihan`(p_id_latihan int (11)) RETURNS varchar(45) CHARSET utf8
BEGIN
  return 
  (select 
    `nama_latihan` 
  from
    `latihan` 
  where `id_latihan` = p_id_latihan) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_nama_liga`(p_id_liga int (11)) RETURNS varchar(45) CHARSET utf8
BEGIN
  return 
  (select 
    `nama_liga` 
  from
    `liga` 
  where `id_liga` = p_id_liga) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_persentase_stadion`(p_username varchar (45)) RETURNS int(11)
BEGIN
  return (
	select `pendapatan_persen` from `stadion`, `user`
	where user.`username` = p_username and stadion.`id_stadion`=user.`stadion_id_stadion`
  );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_rating_pemain`(p_pos CHAR(1), p_att INT(10), p_def INT(10), p_speed INT(10), p_stamina INT(10)) RETURNS int(10)
BEGIN
	IF(p_pos = 'F') THEN
	RETURN(
		((0.45 * p_att) + (0.05 * p_def) + (0.30 * p_speed) + (0.20 * p_stamina))
	);
	ELSEIF(p_pos = 'D') THEN
	RETURN(
		((0.05 * p_att) + (0.45 * p_def) + (0.20 * p_speed) + (0.30 * p_stamina))
	);
	ELSEIF(p_pos = 'M') THEN
	RETURN(
		((0.20 * p_att) + (0.20 * p_def) + (0.25 * p_speed) + (0.35 * p_stamina))
	);
	ELSEIF(p_pos = 'G') THEN
	RETURN(
		((0.05 * p_att) + (0.65 * p_def) + (0.10 * p_speed) + (0.20 * p_stamina))
	);
	END IF;
    END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_rating_tim`(p_userid VARCHAR(45)) RETURNS int(10)
BEGIN
    DECLARE rating, def_limit, mid_limit, att_limit, minus_rate, counter, pl, temp_rate INT(10);
    DECLARE nama_form VARCHAR(45);  
    DECLARE temp_posisi CHAR(1); 
	SELECT SUM(pemain.rating) INTO rating
	FROM pemain, user_pemain
	WHERE user_pemain.pemain_id_pemain = pemain.id_pemain AND user_pemain.user_username = p_userid AND user_pemain.aktif != 0;
	SELECT nama_formasi INTO nama_form
	FROM `formasi`
	WHERE `id_formasi` = (SELECT `formasi_id_formasi` FROM`user` WHERE username = p_userid); 
	SELECT CONVERT(SUBSTRING(nama_form, 1, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO def_limit;
	SELECT CONVERT(SUBSTRING(nama_form, 3, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO mid_limit;
	SELECT CONVERT(SUBSTRING(nama_form, 5, LOCATE('-', nama_form) - 1), UNSIGNED INTEGER) INTO att_limit;
	SET minus_rate = 0;
	SET counter = 0;
	### F ###
	SET pl = 0;
	WHILE pl < att_limit DO
		SET pl = pl + 1;
		SET counter = counter + 1;
		SELECT `posisi` INTO temp_posisi
		FROM `pemain`
		WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
		IF ( temp_posisi != 'F') THEN
			SELECT pemain.`rating` INTO temp_rate
			FROM `pemain`
			WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
			if (temp_rate is not null) then
				SET minus_rate = minus_rate + temp_rate;
			end if;
		END IF;
	END WHILE;
	### M ###
	SET pl = 0;
	WHILE pl < mid_limit DO
		SET pl = pl + 1;
		SET counter = counter + 1;
		SELECT `posisi` INTO temp_posisi
		FROM `pemain`
		WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
		IF ( temp_posisi != 'M') THEN
			SELECT pemain.`rating` INTO temp_rate
			FROM `pemain`
			WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
			IF (temp_rate IS NOT NULL) THEN
				SET minus_rate = minus_rate + temp_rate;
			END IF;
		END IF;
	END WHILE;
	### D ###
	SET pl = 0;
	WHILE pl < def_limit DO
		SET pl = pl + 1;
		SET counter = counter + 1;
		SELECT `posisi` INTO temp_posisi
		FROM `pemain`
		WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
		IF ( temp_posisi != 'D') THEN
			SELECT pemain.`rating` INTO temp_rate
			FROM `pemain`
			WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
			IF (temp_rate IS NOT NULL) THEN
				SET minus_rate = minus_rate + temp_rate;
			END IF;
		END IF;
	END WHILE;
	### GK ###
	SET counter = counter + 1;
	SELECT `posisi` INTO temp_posisi
	FROM `pemain`
	WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
	IF ( temp_posisi != 'G') THEN
		SELECT pemain.`rating` INTO temp_rate
		FROM `pemain`
		WHERE `id_pemain` = (SELECT `pemain_id_pemain` FROM `user_pemain` WHERE `user_username` = p_userid AND `aktif` = counter);
		IF (temp_rate IS NOT NULL) THEN
			SET minus_rate = minus_rate + temp_rate;
		END IF;
	END IF;
	SET minus_rate = minus_rate * 0.5;
	SET rating = rating - minus_rate;
	if (rating is not null) then
	    RETURN (floor(rating/11));
	else
	    return 0;
	end if;
  END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_rating_tim_ai`(p_id int) RETURNS int(11)
BEGIN
DECLARE rating INT(10);
	SET rating = (SELECT SUM(`pemain`.rating)
	FROM `pemain`,`pemain_tim_ai`
	WHERE `pemain`.id_pemain = `pemain_tim_ai`.`pemain_id_pemain` AND `pemain_tim_ai`.`tim_ai_id_tim_ai` = p_id);
	RETURN (FLOOR(rating/11));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_rumus_formasi`(att varchar (45), def varchar (45)) RETURNS int(11)
BEGIN
return (
	select `nilai_rumus` from `rumus_formasi`
	where `formasi_id_formasi` = (
		select `formasi_id_formasi` from `user` where `username` = att
	) and `formasi_id_formasi1` = (
		SELECT `formasi_id_formasi` FROM `user` WHERE `username` = def
	)
);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_rumus_formasi_ai`(att varchar (45), def int) RETURNS int(11)
BEGIN
  RETURN (
	SELECT `nilai_rumus` FROM `rumus_formasi`
	WHERE `formasi_id_formasi` = (
		SELECT `formasi_id_formasi` FROM `user` WHERE `username` = att
	) AND `formasi_id_formasi1` = (
		SELECT `formasi_id_formasi` FROM `tim_ai` WHERE `id_tim_ai` = def
	)
);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_server_time_TU`() RETURNS int(11)
BEGIN
  RETURN 
  (SELECT 
    FLOOR(
      TIMESTAMPDIFF(
        SECOND,
        setting.`server_waktu_mulai`,
        NOW()
      ) / TIME_TO_SEC(setting.`server_durasi_tu`)
    ) 
  FROM
    setting) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_statusProteksi`(p_att varchar (45), p_def varchar (45)) RETURNS tinyint(1)
BEGIN
  RETURN 
  (SELECT 
    (
      fn_server_time_TU () < `proteksi_lastset_tu` + setting.`proteksi_durasi_tu`
    ) 
  FROM
    `proteksi_user`,
    setting 
  WHERE `user_username` = p_att 
    and `user_username1` = p_def) ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_uang`(p_username varchar (45)) RETURNS int(10)
BEGIN
  return 
  (select 
    `uang` 
  from
    `user` 
  where `username` = p_username) ;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `username_admin` varchar(45) NOT NULL,
  `password_admin` varchar(45) NOT NULL,
  PRIMARY KEY (`username_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username_admin`, `password_admin`) VALUES
('admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `ai_user`
--

CREATE TABLE IF NOT EXISTS `ai_user` (
  `id_ai_user` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(45) NOT NULL,
  `tim_ai_id_tim_ai` int(11) NOT NULL,
  PRIMARY KEY (`id_ai_user`),
  KEY `fk_ai_user_user1_idx` (`user_username`),
  KEY `fk_ai_user_tim_ai1_idx` (`tim_ai_id_tim_ai`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `ai_user`
--

INSERT INTO `ai_user` (`id_ai_user`, `user_username`, `tim_ai_id_tim_ai`) VALUES
(1, 'baskara', 1),
(2, 'baskara', 2),
(3, 'baskara', 3),
(4, 'baskara', 4),
(5, 'baskara', 5),
(6, 'baskara', 6),
(7, 'imamkd', 1),
(8, 'imamkd', 2),
(9, 'imamkd', 3),
(10, 'imamkd', 4),
(11, 'imamkd', 5),
(12, 'imamkd', 6),
(13, 'imamkd', 7),
(14, 'imamkd', 8),
(15, 'imamkd', 9),
(16, 'imamkd', 10),
(17, 'imamkd', 11),
(18, 'imamkd', 12),
(19, 'imamkd', 13),
(20, 'baskara', 7),
(21, 'baskara', 8),
(22, 'baskara', 9),
(23, 'baskara', 10),
(24, 'baskara', 11),
(25, 'baskara', 12),
(26, 'baskara', 13),
(27, 'baskara', 14),
(28, 'baskara', 15),
(29, 'baskara', 16),
(30, 'baskara', 17),
(31, 'baskara', 18);

-- --------------------------------------------------------

--
-- Table structure for table `formasi`
--

CREATE TABLE IF NOT EXISTS `formasi` (
  `id_formasi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_formasi` varchar(45) NOT NULL,
  PRIMARY KEY (`id_formasi`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `formasi`
--

INSERT INTO `formasi` (`id_formasi`, `nama_formasi`) VALUES
(1, '4-4-2'),
(2, '4-5-1'),
(3, '4-3-3'),
(4, '5-4-1'),
(5, '3-4-3'),
(6, '3-5-2');

-- --------------------------------------------------------

--
-- Table structure for table `latihan`
--

CREATE TABLE IF NOT EXISTS `latihan` (
  `id_latihan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_latihan` varchar(45) NOT NULL,
  `durasi_latihan_tu` int(10) unsigned NOT NULL,
  `penambahan_kekompakan` tinyint(3) unsigned NOT NULL,
  `gambar_latihan` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_latihan`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `latihan`
--

INSERT INTO `latihan` (`id_latihan`, `nama_latihan`, `durasi_latihan_tu`, `penambahan_kekompakan`, `gambar_latihan`) VALUES
(1, 'Dribbling', 1200, 100, 'dribbling.jpg'),
(2, 'Jogging', 120, 60, 'jogging.jpg'),
(3, 'Passing', 60, 50, 'passing.jpg'),
(4, 'Shooting', 10, 8, 'shooting.jpg'),
(5, 'Sprinting', 5, 4, 'sprinting.jpg'),
(6, 'Stretching', 2, 1, 'stretching.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `liga`
--

CREATE TABLE IF NOT EXISTS `liga` (
  `id_liga` int(11) NOT NULL AUTO_INCREMENT,
  `nama_liga` varchar(45) NOT NULL,
  `hadiah_ap` tinyint(3) unsigned NOT NULL,
  `hadiah_exp` int(10) unsigned NOT NULL,
  `hadiah_uang` int(10) unsigned NOT NULL,
  `gambar_liga` varchar(45) NOT NULL,
  PRIMARY KEY (`id_liga`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `liga`
--

INSERT INTO `liga` (`id_liga`, `nama_liga`, `hadiah_ap`, `hadiah_exp`, `hadiah_uang`, `gambar_liga`) VALUES
(1, 'Intertoto', 3, 500, 50000, 'intertoto.jpg'),
(2, 'Europa', 5, 1000, 75000, 'europa.jpg'),
(3, 'Champions', 7, 2000, 100000, 'champions.jpg'),
(4, 'Super League', 10, 6000, 150000, 'super.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE IF NOT EXISTS `paket` (
  `id_paket` int(11) NOT NULL AUTO_INCREMENT,
  `nama_paket` varchar(45) NOT NULL,
  `harga_uang` int(10) unsigned NOT NULL,
  `harga_balen` int(10) unsigned DEFAULT NULL,
  `deskripsi_paket` text,
  PRIMARY KEY (`id_paket`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id_paket`, `nama_paket`, `harga_uang`, `harga_balen`, `deskripsi_paket`) VALUES
(1, 'Bronze', 10000, 10, 'Dapatkan Pemain dengan Rating 60-70'),
(2, 'Silver', 30000, 20, 'Dapatkan Pemain dengan Rating 71-79'),
(3, 'Gold', 80000, 60, 'Dapatkan Pemain dengan Rating 80-100'),
(4, 'Platinum', 0, 175, 'Dapatkan Pemain Limited Untuk Minggu Ini.');

-- --------------------------------------------------------

--
-- Table structure for table `pemain`
--

CREATE TABLE IF NOT EXISTS `pemain` (
  `id_pemain` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pemain` varchar(45) NOT NULL,
  `tim_asal` varchar(45) DEFAULT NULL,
  `posisi` char(1) NOT NULL,
  `nilai_att` int(10) unsigned NOT NULL,
  `nilai_def` int(10) unsigned NOT NULL,
  `nilai_speed` int(10) unsigned NOT NULL,
  `nilai_stamina` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `flag_limited` tinyint(1) DEFAULT NULL,
  `flag_tersedia` tinyint(1) DEFAULT NULL,
  `flag_paket` tinyint(1) DEFAULT NULL,
  `foto_pemain` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_pemain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=579 ;

--
-- Dumping data for table `pemain`
--

INSERT INTO `pemain` (`id_pemain`, `nama_pemain`, `tim_asal`, `posisi`, `nilai_att`, `nilai_def`, `nilai_speed`, `nilai_stamina`, `rating`, `flag_limited`, `flag_tersedia`, `flag_paket`, `foto_pemain`) VALUES
(1, 'Lionel Messi', 'FC Barcelona', 'F', 98, 40, 94, 86, 92, 0, 0, 3, '158023.png'),
(2, 'Lionel Messi', 'FC Barcelona', 'F', 98, 40, 94, 86, 96, 1, 0, 4, '158023.png'),
(3, 'Cristiano Ronaldo', 'Real Madrid', 'F', 97, 33, 98, 88, 92, 0, 0, 3, '20801.png'),
(4, 'Cristiano Ronaldo', 'Real Madrid', 'F', 97, 33, 98, 88, 96, 1, 0, 4, '20801.png'),
(5, 'Radamel Falcao', 'AS Monaco', 'F', 96, 28, 86, 90, 88, 0, 0, 3, '167397.png'),
(6, 'Radamel Falcao', 'AS Monaco', 'F', 96, 28, 86, 90, 92, 1, 0, 4, '167397.png'),
(7, 'Franck Ribery', 'Bayern Munich', 'M', 93, 80, 92, 84, 87, 0, 0, 3, '156616.png'),
(8, 'Franck Ribery', 'Bayern Munich', 'M', 93, 80, 92, 84, 91, 1, 0, 4, '156616.png'),
(9, 'Zlatan Ibrahimovic', 'Paris Saint Germain', 'F', 98, 32, 85, 85, 88, 0, 0, 3, '41236.png'),
(10, 'Zlatan Ibrahimovic', 'Paris Saint Germain', 'F', 98, 32, 85, 85, 92, 1, 0, 4, '41236.png'),
(11, 'Xavi Hernandez', 'FC Barcelona', 'M', 90, 90, 80, 85, 86, 0, 0, 3, '10535.png'),
(12, 'Xavi Hernandez', 'FC Barcelona', 'M', 90, 90, 80, 85, 90, 1, 0, 4, '10535.png'),
(13, 'Robin van Persie', 'Manchester United', 'F', 96, 38, 86, 88, 89, 0, 0, 3, '7826.png'),
(14, 'Robin van Persie', 'Manchester United', 'F', 96, 38, 86, 88, 93, 1, 0, 4, '7826.png'),
(15, 'Andres Iniesta', 'FC Barcelona', 'M', 92, 82, 88, 86, 87, 0, 0, 3, '41.png'),
(16, 'Andres Iniesta', 'FC Barcelona', 'M', 92, 82, 88, 86, 91, 1, 0, 4, '41.png'),
(17, 'Thiago Silva', 'Paris Saint Germain', 'D', 50, 94, 84, 90, 89, 0, 0, 3, '164240.png'),
(18, 'Thiago Silva', 'Paris Saint Germain', 'D', 50, 94, 84, 90, 93, 1, 0, 4, '164240.png'),
(19, 'Bastian Schweinsteiger', 'Bayern Munich', 'M', 84, 86, 85, 90, 87, 0, 0, 3, '121944.png'),
(20, 'Bastian Schweinsteiger', 'Bayern Munich', 'M', 84, 86, 85, 90, 91, 1, 0, 4, '121944.png'),
(21, 'Arjen Robben', 'Bayern Munich', 'M', 94, 78, 96, 80, 86, 0, 0, 3, '9014.png'),
(22, 'Arjen Robben', 'Bayern Munich', 'M', 94, 78, 96, 80, 90, 1, 0, 4, '9014.png'),
(23, 'Edinson Cavani', 'Paris Saint Germain', 'F', 93, 30, 86, 94, 88, 0, 0, 3, '179813.png'),
(24, 'Edinson Cavani', 'Paris Saint Germain', 'F', 93, 30, 86, 94, 92, 1, 0, 4, '179813.png'),
(25, 'Juan Mata', 'Chelsea FC', 'M', 89, 86, 92, 84, 87, 0, 0, 3, '178088.png'),
(26, 'Juan Mata', 'Chelsea FC', 'M', 89, 86, 92, 84, 91, 1, 0, 4, '178088.png'),
(27, 'Mesut Ozil', 'Arsenal', 'M', 90, 87, 90, 86, 88, 0, 0, 3, '176635.png'),
(28, 'Mesut Ozil', 'Arsenal', 'M', 90, 87, 90, 86, 92, 1, 0, 4, '176635.png'),
(29, 'Luis Suarez', 'Liverpool FC', 'F', 95, 40, 92, 87, 90, 0, 0, 3, '176580.png'),
(30, 'Luis Suarez', 'Liverpool FC', 'F', 95, 40, 92, 87, 94, 1, 2, 4, '176580.png'),
(31, 'Gareth Bale', 'Real Madrid', 'M', 90, 76, 94, 89, 88, 0, 0, 3, '173731.png'),
(32, 'Gareth Bale', 'Real Madrid', 'M', 90, 76, 94, 89, 92, 1, 0, 4, '173731.png'),
(33, 'David Silva', 'Manchester City', 'M', 87, 81, 90, 85, 86, 0, 0, 3, '168542.png'),
(34, 'David Silva', 'Manchester City', 'M', 87, 81, 90, 85, 90, 1, 0, 4, '168542.png'),
(35, 'Sergio Aguero', 'Manchester City', 'F', 92, 38, 95, 88, 89, 0, 0, 3, '153079.png'),
(36, 'Sergio Aguero', 'Manchester City', 'F', 92, 38, 95, 88, 93, 1, 0, 4, '153079.png'),
(37, 'Philipp Lahm', 'Bayern Munich', 'D', 70, 88, 86, 88, 87, 0, 0, 3, '121939.png'),
(38, 'Philipp Lahm', 'Bayern Munich', 'D', 70, 88, 86, 88, 91, 1, 0, 4, '121939.png'),
(39, 'Wayne Rooney', 'Manchester United', 'F', 92, 70, 87, 90, 89, 0, 0, 3, '54050.png'),
(40, 'Wayne Rooney', 'Manchester United', 'F', 92, 70, 87, 90, 93, 1, 0, 4, '54050.png'),
(41, 'Robert Lewandowski', 'Borussia Dortmund', 'F', 95, 34, 85, 85, 87, 0, 0, 3, '188545.png'),
(42, 'Robert Lewandowski', 'Borussia Dortmund', 'F', 95, 34, 85, 85, 91, 1, 0, 4, '188545.png'),
(43, 'Angel Di Maria', 'Real Madrid', 'M', 90, 80, 90, 80, 85, 0, 0, 3, '183898.png'),
(44, 'Angel Di Maria', 'Real Madrid', 'M', 90, 80, 90, 80, 89, 1, 0, 4, '183898.png'),
(45, 'Manuel Neuer', 'Bayern Munich', 'G', 30, 95, 91, 90, 90, 0, 0, 3, '167495.png'),
(46, 'Manuel Neuer', 'Bayern Munich', 'G', 30, 95, 91, 90, 94, 1, 0, 4, '167495.png'),
(47, 'Cesc Fabregas', 'FC Barcelona', 'M', 89, 84, 82, 90, 87, 0, 0, 3, '162895.png'),
(48, 'Cesc Fabregas', 'FC Barcelona', 'M', 89, 84, 82, 90, 91, 1, 0, 4, '162895.png'),
(49, 'Sergio Ramos', 'Real Madrid', 'D', 60, 88, 84, 86, 85, 0, 0, 3, '155862.png'),
(50, 'Sergio Ramos', 'Real Madrid', 'D', 60, 88, 84, 86, 89, 1, 0, 4, '155862.png'),
(51, 'Gerard Pique', 'FC Barcelona', 'D', 62, 90, 82, 90, 87, 0, 0, 3, '152729.png'),
(52, 'Gerard Pique', 'FC Barcelona', 'D', 62, 90, 82, 90, 91, 1, 0, 4, '152729.png'),
(53, 'Carlos Tevez', 'Juventus', 'F', 92, 30, 86, 85, 86, 0, 0, 3, '143001.png'),
(54, 'Carlos Tevez', 'Juventus', 'F', 92, 30, 86, 85, 90, 1, 0, 4, '143001.png'),
(55, 'Nemanja Vidic', 'Manchester United', 'D', 40, 92, 80, 90, 86, 0, 0, 3, '140601.png'),
(56, 'Nemanja Vidic', 'Manchester United', 'D', 40, 92, 80, 90, 90, 1, 0, 4, '140601.png'),
(57, 'Vincent Kompany', 'Manchester City', 'D', 42, 93, 78, 89, 86, 0, 0, 3, '139720.png'),
(58, 'Vincent Kompany', 'Manchester City', 'D', 42, 93, 78, 89, 90, 1, 9, 4, '139720.png'),
(59, 'Yaya Toure', 'Manchester City', 'M', 89, 86, 80, 90, 87, 0, 0, 3, '20289.png'),
(60, 'Yaya Toure', 'Manchester City', 'M', 89, 86, 80, 90, 91, 1, 0, 4, '20289.png'),
(61, 'Iker Casillas', 'Real Madrid', 'G', 20, 93, 89, 94, 89, 0, 0, 3, '5479.png'),
(62, 'Iker Casillas', 'Real Madrid', 'G', 20, 93, 89, 94, 93, 1, 0, 4, '5479.png'),
(63, 'Mario Gotze', 'Bayern Munich', 'M', 90, 80, 90, 78, 84, 0, 0, 3, '192318.png'),
(64, 'Mario Gotze', 'Bayern Munich', 'M', 90, 80, 90, 78, 88, 1, 0, 4, '192318.png'),
(65, 'Thomas Muller', 'Bayern Munich', 'M', 92, 78, 84, 88, 86, 0, 0, 3, '189596.png'),
(66, 'Thomas Muller', 'Bayern Munich', 'M', 92, 78, 84, 88, 90, 1, 0, 4, '189596.png'),
(67, 'Sergio Busquets', 'FC Barcelona', 'M', 84, 88, 82, 92, 87, 0, 0, 3, '189511.png'),
(68, 'Sergio Busquets', 'FC Barcelona', 'M', 84, 88, 82, 92, 91, 1, 0, 4, '189511.png'),
(69, 'Marco Reus', 'Borussia Dortmund', 'M', 89, 80, 88, 82, 85, 0, 0, 3, '188350.png'),
(70, 'Marco Reus', 'Borussia Dortmund', 'M', 89, 80, 88, 82, 89, 1, 0, 4, '188350.png'),
(71, 'Eden Hazard', 'Chelsea FC', 'M', 93, 78, 96, 82, 87, 0, 0, 3, '183277.png'),
(72, 'Eden Hazard', 'Chelsea FC', 'M', 93, 78, 96, 82, 91, 1, 0, 4, '183277.png'),
(73, 'Arturo Vidal', 'Juventus', 'M', 86, 87, 83, 90, 87, 0, 0, 3, '181872.png'),
(74, 'Arturo Vidal', 'Juventus', 'M', 86, 87, 83, 90, 91, 1, 0, 4, '181872.png'),
(75, 'Mats Hummels', 'Borussia Dortmund', 'D', 30, 90, 78, 90, 85, 0, 0, 3, '178603.png'),
(76, 'Mats Hummels', 'Borussia Dortmund', 'D', 30, 90, 78, 90, 89, 1, 0, 4, '178603.png'),
(77, 'Luca Modric', 'Real Madrid', 'M', 88, 82, 88, 87, 86, 0, 0, 3, '177003.png'),
(78, 'Luca Modric', 'Real Madrid', 'M', 88, 82, 88, 87, 90, 1, 0, 4, '177003.png'),
(79, 'Karim Benzema', 'Real Madrid', 'F', 89, 40, 88, 87, 86, 0, 0, 3, '165153.png'),
(80, 'Karim Benzema', 'Real Madrid', 'F', 89, 40, 88, 87, 90, 1, 0, 4, '165153.png'),
(81, 'Mario Gomez', 'Fiorentina', 'F', 95, 28, 82, 90, 87, 0, 0, 3, '150418.png'),
(82, 'Mario Gomez', 'Fiorentina', 'F', 95, 28, 82, 90, 91, 1, 0, 4, '150418.png'),
(83, 'Santi Cazorla', 'Arsenal', 'M', 88, 82, 87, 84, 85, 0, 0, 3, '146562.png'),
(84, 'Santi Cazorla', 'Arsenal', 'M', 88, 82, 87, 84, 89, 1, 0, 4, '146562.png'),
(85, 'Giorgio Chiellini', 'Juventus', 'D', 40, 95, 78, 86, 86, 0, 0, 3, '138956.png'),
(86, 'Giorgio Chiellini', 'Juventus', 'D', 40, 95, 78, 86, 90, 1, 0, 4, '138956.png'),
(87, 'Petr Cech', 'Chelsea FC', 'G', 28, 95, 82, 90, 89, 0, 0, 3, '48940.png'),
(88, 'Petr Cech', 'Chelsea FC', 'G', 28, 95, 82, 90, 93, 1, 0, 4, '48940.png'),
(89, 'Xabi Alonso', 'Real Madrid', 'M', 86, 87, 80, 86, 85, 0, 0, 3, '45197.png'),
(90, 'Xabi Alonso', 'Real Madrid', 'M', 86, 87, 80, 86, 89, 1, 0, 4, '45197.png'),
(91, 'Ashley Cole', 'Chelsea FC', 'D', 71, 89, 82, 84, 85, 0, 0, 3, '34079.png'),
(92, 'Ashley Cole', 'Chelsea FC', 'D', 71, 89, 82, 84, 89, 1, 0, 4, '34079.png'),
(93, 'Andrea Pirlo', 'Juventus', 'M', 88, 86, 78, 85, 84, 0, 0, 3, '7763.png'),
(94, 'Andrea Pirlo', 'Juventus', 'M', 88, 86, 78, 85, 88, 1, 0, 4, '7763.png'),
(95, 'Gianluigi Buffon', 'Juventus', 'G', 30, 92, 85, 86, 87, 0, 0, 3, '1179.png'),
(96, 'Gianluigi Buffon', 'Juventus', 'G', 30, 92, 85, 86, 91, 1, 0, 4, '1179.png'),
(97, 'Isco', 'Real Madrid', 'M', 87, 83, 89, 86, 86, 0, 0, 3, '197781.png'),
(98, 'Isco', 'Real Madrid', 'M', 87, 83, 89, 86, 90, 1, 0, 4, '197781.png'),
(99, 'Thibaut Courtois', 'Atletico Madrid', 'G', 30, 86, 84, 90, 84, 0, 0, 3, '192119.png'),
(100, 'Thibaut Courtois', 'Atletico Madrid', 'G', 30, 86, 84, 90, 88, 1, 0, 4, '192119.png'),
(101, 'Neymar', 'FC Barcelona', 'F', 90, 40, 93, 82, 87, 0, 0, 3, '190871.png'),
(102, 'Neymar', 'FC Barcelona', 'F', 90, 40, 93, 82, 91, 1, 0, 4, '190871.png'),
(103, 'Pedro Rodriguez', 'FC Barcelona', 'F', 88, 42, 88, 86, 85, 0, 0, 3, '189505.png'),
(104, 'Pedro Rodriguez', 'FC Barcelona', 'F', 88, 42, 88, 86, 89, 1, 0, 4, '189505.png'),
(105, 'Hulk', 'Zenit', 'F', 89, 40, 86, 93, 86, 0, 0, 3, '189362.png'),
(106, 'Hulk', 'Zenit', 'F', 89, 40, 86, 93, 90, 1, 0, 4, '189362.png'),
(107, 'Ilkay Gundogan', 'Borussia Dortmund', 'M', 86, 84, 84, 88, 86, 0, 0, 3, '186942.png'),
(108, 'Ilkay Gundogan', 'Borussia Dortmund', 'M', 86, 84, 84, 88, 90, 1, 0, 4, '186942.png'),
(109, 'Mario Balotelli', 'AC Milan', 'F', 88, 35, 86, 88, 85, 0, 0, 3, '186627.png'),
(110, 'Mario Balotelli', 'AC Milan', 'F', 88, 35, 86, 88, 89, 1, 0, 4, '186627.png'),
(111, 'Toni Kroos', 'Bayern Munich', 'M', 86, 84, 87, 80, 84, 0, 0, 3, '182521.png'),
(112, 'Toni Kroos', 'Bayern Munich', 'M', 86, 84, 87, 80, 88, 1, 0, 4, '182521.png'),
(113, 'Stevan Jovetic', 'Manchester City', 'F', 87, 40, 86, 80, 83, 0, 0, 3, '181820.png'),
(114, 'Stevan Jovetic', 'Manchester City', 'F', 87, 40, 86, 80, 87, 1, 0, 4, '181820.png'),
(115, 'Sami Khedira', 'Real Madrid', 'M', 82, 88, 83, 87, 85, 0, 0, 3, '179846.png'),
(116, 'Sami Khedira', 'Real Madrid', 'M', 82, 88, 83, 87, 89, 1, 0, 4, '179846.png'),
(117, 'Javi Martinez', 'Bayern Munich', 'M', 82, 92, 81, 90, 87, 0, 0, 3, '177610.png'),
(118, 'Javi Martinez', 'Bayern Munich', 'M', 82, 92, 81, 90, 91, 1, 0, 4, '177610.png'),
(119, 'Claudio Marchisio', 'Juventus', 'M', 85, 84, 84, 83, 84, 0, 0, 3, '173210.png'),
(120, 'Claudio Marchisio', 'Juventus', 'M', 85, 84, 84, 83, 88, 1, 0, 4, '173210.png'),
(121, 'Marek Hamsik', 'Napoli', 'M', 86, 85, 85, 88, 86, 0, 0, 3, '171877.png'),
(122, 'Marek Hamsik', 'Napoli', 'M', 86, 85, 85, 88, 90, 1, 0, 4, '171877.png'),
(123, 'Salvatore Sirigu', 'Paris Saint Germain', 'G', 28, 89, 82, 90, 85, 0, 0, 3, '168435.png'),
(124, 'Salvatore Sirigu', 'Paris Saint Germain', 'G', 28, 89, 82, 90, 89, 1, 0, 4, '168435.png'),
(125, 'Hugo Lloris', 'Tottenham Hotspur', 'G', 40, 88, 82, 88, 85, 0, 0, 3, '167948.png'),
(126, 'Hugo Lloris', 'Tottenham Hotspur', 'G', 40, 88, 82, 88, 89, 1, 11, 4, '167948.png'),
(127, 'Gonzalo Higuain', 'Napoli', 'F', 90, 31, 86, 86, 85, 0, 0, 3, '167664.png'),
(128, 'Gonzalo Higuain', 'Napoli', 'F', 90, 31, 86, 86, 89, 1, 0, 4, '167664.png'),
(129, 'Samir Nasri', 'Manchester City', 'M', 87, 79, 86, 80, 83, 0, 0, 3, '165239.png'),
(130, 'Samir Nasri', 'Manchester City', 'M', 87, 79, 86, 80, 87, 1, 0, 4, '165239.png'),
(131, 'Ezequiel Lavezzi', 'Paris Saint Germain', 'F', 87, 50, 88, 80, 84, 0, 0, 3, '159065.png'),
(132, 'Ezequiel Lavezzi', 'Paris Saint Germain', 'F', 87, 50, 88, 80, 88, 1, 0, 4, '159065.png'),
(133, 'Dante', 'Bayern Munich', 'D', 45, 88, 84, 87, 85, 0, 0, 3, '158625.png'),
(134, 'Dante', 'Bayern Munich', 'D', 45, 88, 84, 87, 89, 1, 0, 4, '158625.png'),
(135, 'Klaas Jan Huntelaar', 'Schalke 04', 'F', 93, 30, 80, 81, 84, 0, 0, 3, '148803.png'),
(136, 'Klaas Jan Huntelaar', 'Schalke 04', 'F', 93, 30, 80, 81, 88, 1, 0, 4, '148803.png'),
(137, 'Igor Akinfeev', 'CSKA Moscow', 'G', 40, 88, 84, 87, 85, 0, 0, 3, '148119.png'),
(138, 'Igor Akinfeev', 'CSKA Moscow', 'G', 40, 88, 84, 87, 89, 1, 0, 4, '148119.png'),
(139, 'Jesus Navas', 'Manchester City', 'M', 88, 77, 89, 82, 84, 0, 0, 3, '146536.png'),
(140, 'Jesus Navas', 'Manchester City', 'M', 88, 77, 89, 82, 88, 1, 0, 4, '146536.png'),
(141, 'Dani Alves', 'FC Barcelona', 'D', 75, 86, 87, 85, 85, 0, 0, 3, '146530.png'),
(142, 'Dani Alves', 'FC Barcelona', 'D', 75, 86, 87, 85, 89, 1, 0, 4, '146530.png'),
(143, 'Arda Turan', 'Atletico Madrid', 'M', 87, 79, 85, 84, 84, 0, 0, 3, '143745.png'),
(144, 'Arda Turan', 'Atletico Madrid', 'M', 87, 79, 85, 84, 88, 1, 7, 4, '143745.png'),
(145, 'Javier Mascherano', 'FC Barcelona', 'M', 76, 89, 80, 88, 84, 0, 0, 3, '142754.png'),
(146, 'Javier Mascherano', 'FC Barcelona', 'M', 76, 89, 80, 88, 88, 1, 0, 4, '142754.png'),
(147, 'Wesley Sneijder', 'Galatasaray', 'M', 88, 79, 82, 86, 84, 0, 0, 3, '139869.png'),
(148, 'Wesley Sneijder', 'Galatasaray', 'M', 88, 79, 82, 86, 88, 1, 0, 4, '139869.png'),
(149, 'Federico Marchetti', 'SS Lazio', 'G', 40, 86, 85, 89, 84, 0, 0, 3, '139668.png'),
(150, 'Federico Marchetti', 'SS Lazio', 'G', 40, 86, 85, 89, 88, 1, 0, 4, '139668.png'),
(151, 'Andrea Barzagli', 'Juventus', 'D', 40, 88, 80, 85, 83, 0, 0, 3, '137186.png'),
(152, 'Andrea Barzagli', 'Juventus', 'D', 40, 88, 80, 85, 87, 1, 0, 4, '137186.png'),
(153, 'Antonio Di Natale', 'Udinese', 'F', 94, 40, 79, 84, 85, 0, 0, 3, '120274.png'),
(154, 'Antonio Di Natale', 'Udinese', 'F', 94, 40, 79, 84, 89, 1, 0, 4, '120274.png'),
(155, 'Daniele De Rossi', 'AS Roma', 'M', 85, 86, 83, 85, 85, 0, 0, 3, '53302.png'),
(156, 'Daniele De Rossi', 'AS Roma', 'M', 85, 86, 83, 85, 89, 1, 0, 4, '53302.png'),
(157, 'Didier Drogba', 'Galatasaray', 'F', 91, 38, 83, 84, 85, 0, 0, 3, '31432.png'),
(158, 'Didier Drogba', 'Galatasaray', 'F', 91, 38, 83, 84, 89, 1, 0, 4, '31432.png'),
(159, 'James Rodriguez', 'AS Monaco', 'M', 89, 75, 90, 83, 84, 0, 0, 3, '198710.png'),
(160, 'James Rodriguez', 'AS Monaco', 'M', 89, 75, 90, 83, 88, 1, 4, 4, '198710.png'),
(161, 'Shinji Kagawa', 'Manchester United', 'M', 86, 83, 85, 81, 83, 0, 0, 3, '189358.png'),
(162, 'Shinji Kagawa', 'Manchester United', 'M', 86, 83, 85, 81, 87, 1, 0, 4, '189358.png'),
(163, 'Oscar', 'Chelsea FC', 'M', 88, 84, 90, 85, 87, 0, 0, 3, '188152.png'),
(164, 'Oscar', 'Chelsea FC', 'M', 88, 84, 90, 85, 91, 1, 6, 4, '188152.png'),
(165, 'Jerome Boateng', 'Bayern Munich', 'D', 69, 89, 85, 83, 85, 0, 0, 3, '183907.png'),
(166, 'Jerome Boateng', 'Bayern Munich', 'D', 69, 89, 85, 83, 89, 1, 0, 4, '183907.png'),
(167, 'Diego Godin', 'Atletico Madrid', 'D', 40, 91, 78, 84, 84, 0, 0, 3, '182493.png'),
(168, 'Diego Godin', 'Atletico Madrid', 'D', 40, 91, 78, 84, 88, 1, 0, 4, '182493.png'),
(169, 'Mario Mandzukic', 'Bayern Munich', 'F', 88, 32, 86, 88, 85, 0, 0, 3, '181783.png'),
(170, 'Mario Mandzukic', 'Bayern Munich', 'F', 88, 32, 86, 88, 89, 1, 0, 4, '181783.png'),
(171, 'Edin Dzeko', 'Manchester City', 'F', 87, 40, 85, 85, 84, 0, 0, 3, '180930.png'),
(172, 'Edin Dzeko', 'Manchester City', 'F', 87, 40, 85, 85, 88, 1, 0, 4, '180930.png'),
(173, 'Hernanes', 'SS Lazio', 'M', 84, 83, 82, 85, 84, 0, 0, 3, '180432.png'),
(174, 'Hernanes', 'SS Lazio', 'M', 84, 83, 82, 85, 88, 1, 0, 4, '180432.png'),
(175, 'Diego Costa', 'Atletico Madrid', 'F', 89, 30, 88, 85, 85, 0, 0, 3, '179844.png'),
(176, 'Diego Costa', 'Atletico Madrid', 'F', 89, 30, 88, 85, 89, 1, 0, 4, '179844.png'),
(177, 'Medhi Benatia', 'AS Roma', 'D', 50, 88, 82, 90, 86, 0, 0, 3, '177509.png'),
(178, 'Medhi Benatia', 'AS Roma', 'D', 50, 88, 82, 90, 90, 1, 0, 4, '177509.png'),
(179, 'Marcelo', 'Real Madrid', 'D', 70, 86, 90, 84, 85, 0, 0, 3, '176676.png'),
(180, 'Marcelo', 'Real Madrid', 'D', 70, 86, 90, 84, 89, 1, 0, 4, '176676.png'),
(181, 'Lukasz Piszczek', 'Borussia Dortmund', 'D', 71, 88, 82, 86, 85, 0, 0, 3, '173771.png'),
(182, 'Lukasz Piszczek', 'Borussia Dortmund', 'D', 71, 88, 82, 86, 89, 1, 0, 4, '173771.png'),
(183, 'Antonio Candreva', 'SS Lazio', 'M', 85, 82, 84, 80, 82, 0, 0, 3, '173221.png'),
(184, 'Antonio Candreva', 'SS Lazio', 'M', 85, 82, 84, 80, 86, 1, 0, 4, '173221.png'),
(185, 'Antonio Valencia', 'Manchester United', 'M', 86, 78, 86, 80, 82, 0, 0, 3, '167905.png'),
(186, 'Antonio Valencia', 'Manchester United', 'M', 86, 78, 86, 80, 86, 1, 0, 4, '167905.png'),
(187, 'Jakub Blaszczykowski', 'Borussia Dortmund', 'M', 84, 80, 86, 81, 83, 0, 0, 3, '164994.png'),
(188, 'Jakub Blaszczykowski', 'Borussia Dortmund', 'M', 84, 80, 86, 81, 87, 1, 0, 4, '164994.png'),
(189, 'Alessandro Diamanti', 'Bologna', 'F', 89, 40, 85, 80, 84, 0, 0, 3, '163925.png'),
(190, 'Alessandro Diamanti', 'Bologna', 'F', 89, 40, 85, 80, 88, 1, 0, 4, '163925.png'),
(191, 'Steve Mandanda', 'Marseille', 'G', 30, 85, 88, 86, 83, 0, 0, 3, '163705.png'),
(192, 'Steve Mandanda', 'Marseille', 'G', 30, 85, 88, 86, 87, 1, 0, 4, '163705.png'),
(193, 'Leighton Baines', 'Everton', 'D', 79, 84, 83, 93, 86, 0, 0, 3, '163631.png'),
(194, 'Leighton Baines', 'Everton', 'D', 79, 84, 83, 93, 90, 1, 0, 4, '163631.png'),
(195, 'Samir Handanovic', 'Inter Milan', 'G', 40, 90, 90, 84, 86, 0, 0, 3, '162835.png'),
(196, 'Samir Handanovic', 'Inter Milan', 'G', 40, 90, 90, 84, 90, 1, 0, 4, '162835.png'),
(197, 'Joao Moutinho', 'AS Monaco', 'M', 85, 84, 80, 87, 84, 0, 0, 3, '162347.png'),
(198, 'Joao Moutinho', 'AS Monaco', 'M', 85, 84, 80, 87, 88, 1, 0, 4, '162347.png'),
(199, 'Riccardo Montolivo', 'AC Milan', 'M', 84, 82, 83, 80, 82, 0, 0, 3, '159287.png'),
(200, 'Riccardo Montolivo', 'AC Milan', 'M', 84, 82, 83, 80, 86, 1, 0, 4, '159287.png'),
(201, 'Jefferson Farfan', 'Schalke 04', 'M', 85, 81, 81, 80, 81, 0, 0, 3, '158133.png'),
(202, 'Jefferson Farfan', 'Schalke 04', 'M', 85, 81, 81, 80, 85, 1, 0, 4, '158133.png'),
(203, 'Alexandre Song', 'FC Barcelona', 'M', 81, 85, 81, 83, 83, 0, 0, 3, '157503.png'),
(204, 'Alexandre Song', 'FC Barcelona', 'M', 81, 85, 81, 83, 87, 1, 0, 4, '157503.png'),
(205, 'Roberto Soldado', 'Tottenham Hotspur', 'F', 85, 40, 85, 81, 82, 0, 0, 3, '146758.png'),
(206, 'Roberto Soldado', 'Tottenham Hotspur', 'F', 85, 40, 85, 81, 86, 1, 0, 4, '146758.png'),
(207, 'Diego Lopez', 'Real Madrid', 'G', 40, 86, 88, 84, 84, 0, 0, 3, '146748.png'),
(208, 'Diego Lopez', 'Real Madrid', 'G', 40, 86, 88, 84, 88, 1, 0, 4, '146748.png'),
(209, 'Alvaro Negredo', 'Manchester City', 'F', 88, 40, 82, 81, 82, 0, 0, 3, '146439.png'),
(210, 'Alvaro Negredo', 'Manchester City', 'F', 88, 40, 82, 81, 86, 1, 0, 4, '146439.png'),
(211, 'Luis Nani', 'Manchester United', 'M', 88, 78, 84, 79, 82, 0, 0, 3, '139068.png'),
(212, 'Luis Nani', 'Manchester United', 'M', 88, 78, 84, 79, 86, 1, 0, 4, '139068.png'),
(213, 'Ricardo Kaka', 'AC Milan', 'M', 86, 78, 83, 83, 83, 0, 0, 3, '138449.png'),
(214, 'Ricardo Kaka', 'AC Milan', 'M', 86, 78, 83, 83, 87, 1, 0, 4, '138449.png'),
(215, 'Diego Ribas', 'Vfl Wolfsburg', 'M', 88, 76, 82, 84, 83, 0, 0, 3, '136138.png'),
(216, 'Diego Ribas', 'Vfl Wolfsburg', 'M', 88, 76, 82, 84, 87, 1, 0, 4, '136138.png'),
(217, 'Rene Adler', 'Hamburg SV', 'G', 34, 84, 86, 87, 82, 0, 0, 3, '121933.png'),
(218, 'Rene Adler', 'Hamburg SV', 'G', 34, 84, 86, 87, 86, 1, 0, 4, '121933.png'),
(219, 'Pepe', 'Real Madrid', 'D', 40, 86, 83, 85, 83, 0, 0, 3, '120533.png'),
(220, 'Pepe', 'Real Madrid', 'D', 40, 86, 83, 85, 87, 1, 0, 4, '120533.png'),
(221, 'David Villa', 'Atletico Madrid', 'F', 88, 34, 82, 81, 82, 0, 0, 3, '113422.png'),
(222, 'David Villa', 'Atletico Madrid', 'F', 88, 34, 82, 81, 86, 1, 0, 4, '113422.png'),
(223, 'Victor Valdes', 'FC Barcelona', 'G', 40, 86, 88, 85, 84, 0, 0, 3, '106573.png'),
(224, 'Victor Valdes', 'FC Barcelona', 'G', 40, 86, 88, 85, 88, 1, 0, 4, '106573.png'),
(225, 'Rafael van der Vaart', 'Hamburg SV', 'M', 85, 80, 80, 76, 80, 0, 0, 3, '45574.png'),
(226, 'Rafael van der Vaart', 'Hamburg SV', 'M', 85, 80, 80, 76, 84, 1, 0, 4, '45574.png'),
(227, 'Jose Reina', 'Napoli', 'G', 40, 86, 86, 82, 83, 0, 0, 3, '24630.png'),
(228, 'Jose Reina', 'Napoli', 'G', 40, 86, 86, 82, 87, 1, 0, 4, '24630.png'),
(229, 'Danny', 'Zenit', 'M', 86, 77, 83, 80, 81, 0, 0, 3, '20800.png'),
(230, 'Danny', 'Zenit', 'M', 86, 77, 83, 80, 85, 1, 0, 4, '20800.png'),
(231, 'Steven Gerrard', 'Liverpool FC', 'M', 86, 86, 83, 84, 85, 0, 0, 3, '13743.png'),
(232, 'Steven Gerrard', 'Liverpool FC', 'M', 86, 86, 83, 84, 89, 1, 0, 4, '13743.png'),
(233, 'John Terry', 'Chelsea FC', 'D', 40, 91, 75, 84, 83, 0, 0, 3, '13732.png'),
(234, 'John Terry', 'Chelsea FC', 'D', 40, 91, 75, 84, 87, 1, 0, 4, '13732.png'),
(235, 'Carles Puyol', 'FC Barcelona', 'D', 45, 88, 79, 81, 82, 0, 0, 3, '13038.png'),
(236, 'Carles Puyol', 'FC Barcelona', 'D', 45, 88, 79, 81, 86, 1, 0, 4, '13038.png'),
(237, 'Miroslav Klose', 'SS Lazio', 'F', 86, 34, 80, 84, 81, 0, 0, 3, '11141.png'),
(238, 'Miroslav Klose', 'SS Lazio', 'F', 86, 34, 80, 84, 85, 1, 0, 4, '11141.png'),
(239, 'Samuel Etoo', 'Chelsea FC', 'F', 85, 38, 86, 80, 82, 0, 0, 3, '9676.png'),
(240, 'Samuel Etoo', 'Chelsea FC', 'F', 85, 38, 86, 80, 86, 1, 0, 4, '9676.png'),
(241, 'Roman Weidenfeller', 'Borussia Dortmund', 'G', 32, 88, 83, 84, 84, 0, 0, 3, '2196.png'),
(242, 'Roman Weidenfeller', 'Borussia Dortmund', 'G', 32, 88, 83, 84, 88, 1, 0, 4, '2196.png'),
(243, 'Juan Cuadrado', 'Fiorentina', 'M', 83, 81, 86, 79, 82, 0, 0, 3, '193082.png'),
(244, 'Juan Cuadrado', 'Fiorentina', 'M', 83, 81, 86, 79, 86, 1, 0, 4, '193082.png'),
(245, 'David De Gea', 'Manchester United', 'G', 40, 85, 90, 84, 83, 0, 0, 3, '193080.png'),
(246, 'David De Gea', 'Manchester United', 'G', 40, 85, 90, 84, 87, 1, 0, 4, '193080.png'),
(247, 'Henrikh Mkhitaryan', 'Borussia Dortmund', 'M', 88, 80, 81, 78, 81, 0, 0, 3, '192883.png'),
(248, 'Henrikh Mkhitaryan', 'Borussia Dortmund', 'M', 88, 80, 81, 78, 85, 1, 0, 4, '192883.png'),
(249, 'Christian Eriksen', 'Tottenham Hotspur', 'M', 85, 81, 84, 79, 82, 0, 0, 3, '190460.png'),
(250, 'Christian Eriksen', 'Tottenham Hotspur', 'M', 85, 81, 84, 79, 86, 1, 0, 4, '190460.png'),
(251, 'Jack Wilshere', 'Arsenal', 'M', 83, 85, 84, 82, 83, 0, 0, 3, '189461.png'),
(252, 'Jack Wilshere', 'Arsenal', 'M', 83, 85, 84, 82, 87, 1, 0, 4, '189461.png'),
(253, 'Jordi Alba', 'FC Barcelona', 'D', 70, 85, 88, 85, 85, 0, 0, 3, '189332.png'),
(254, 'Jordi Alba', 'FC Barcelona', 'D', 70, 85, 88, 85, 89, 1, 0, 4, '189332.png'),
(255, 'Alexis Sanchez', 'FC Barcelona', 'F', 82, 40, 90, 80, 82, 0, 0, 3, '184941.png'),
(256, 'Alexis Sanchez', 'FC Barcelona', 'F', 82, 40, 90, 80, 86, 1, 0, 4, '184941.png'),
(257, 'Neven Subotic', 'Borussia Dortmund', 'D', 35, 89, 71, 90, 83, 0, 0, 3, '183556.png'),
(258, 'Neven Subotic', 'Borussia Dortmund', 'D', 35, 89, 71, 90, 87, 1, 0, 4, '183556.png'),
(259, 'Fernando Muslera', 'Galatasaray', 'G', 40, 84, 86, 81, 81, 0, 0, 3, '182494.png'),
(260, 'Fernando Muslera', 'Galatasaray', 'G', 40, 84, 86, 81, 85, 1, 0, 4, '182494.png'),
(261, 'Willian', 'Chelsea FC', 'M', 86, 80, 89, 76, 82, 0, 0, 3, '180403.png'),
(262, 'Willian', 'Chelsea FC', 'M', 86, 80, 89, 76, 86, 1, 0, 4, '180403.png'),
(263, 'David Luiz', 'Chelsea FC', 'D', 60, 86, 82, 85, 84, 0, 0, 3, '179944.png'),
(264, 'David Luiz', 'Chelsea FC', 'D', 60, 86, 82, 85, 88, 1, 0, 4, '179944.png'),
(265, 'Benedikt Howedes', 'Schalke 04', 'D', 50, 88, 80, 85, 84, 0, 0, 3, '179784.png'),
(266, 'Benedikt Howedes', 'Schalke 04', 'D', 50, 88, 80, 85, 88, 1, 0, 4, '179784.png'),
(267, 'Javier Hernandez', 'Manchester United', 'F', 88, 25, 87, 74, 82, 0, 0, 3, '178224.png'),
(268, 'Javier Hernandez', 'Manchester United', 'F', 88, 25, 87, 74, 86, 1, 0, 4, '178224.png'),
(269, 'Mathieu Valbuena', 'Marseille', 'M', 86, 78, 84, 80, 82, 0, 0, 3, '177326.png'),
(270, 'Mathieu Valbuena', 'Marseille', 'M', 86, 78, 84, 80, 86, 1, 0, 4, '177326.png'),
(271, 'Kevin Prince Boateng', 'Schalke 04', 'M', 85, 79, 83, 79, 81, 0, 0, 3, '173909.png'),
(272, 'Kevin Prince Boateng', 'Schalke 04', 'M', 85, 79, 83, 79, 85, 1, 0, 4, '173909.png'),
(273, 'Jan Vertonghen', 'Tottenham Hotspur', 'D', 50, 84, 85, 82, 82, 0, 0, 3, '172871.png'),
(274, 'Jan Vertonghen', 'Tottenham Hotspur', 'D', 50, 84, 85, 82, 86, 1, 0, 4, '172871.png'),
(275, 'Daniel Sturridge', 'Liverpool FC', 'F', 84, 50, 86, 74, 81, 0, 0, 3, '171833.png'),
(276, 'Daniel Sturridge', 'Liverpool FC', 'F', 84, 50, 86, 74, 85, 1, 0, 4, '171833.png'),
(277, 'Blaise Matuidi', 'Paris Saint Germain', 'M', 79, 85, 80, 83, 82, 0, 0, 3, '170890.png'),
(278, 'Blaise Matuidi', 'Paris Saint Germain', 'M', 79, 85, 80, 83, 86, 1, 0, 4, '170890.png'),
(279, 'Nuri Sahin', 'Borussia Dortmund', 'M', 83, 85, 84, 82, 83, 0, 0, 3, '170797.png'),
(280, 'Nuri Sahin', 'Borussia Dortmund', 'M', 83, 85, 84, 82, 87, 1, 0, 4, '170797.png'),
(281, 'Lars Bender', 'Bayer Leverkusen', 'M', 80, 85, 84, 82, 83, 0, 0, 3, '177457.png'),
(282, 'Lars Bender', 'Bayer Leverkusen', 'M', 80, 85, 84, 82, 87, 1, 0, 4, '177457.png'),
(283, 'Ezequiel Garay', 'SL Benfica', 'D', 50, 88, 81, 85, 84, 0, 0, 3, '170481.png'),
(284, 'Ezequiel Garay', 'SL Benfica', 'D', 50, 88, 81, 85, 88, 1, 8, 4, '170481.png'),
(285, 'Carlos Vela', 'Real Sociedad', 'F', 83, 40, 86, 80, 81, 0, 0, 3, '169416.png'),
(286, 'Carlos Vela', 'Real Sociedad', 'F', 83, 40, 86, 80, 85, 1, 0, 4, '169416.png'),
(287, 'Yohan Cabaye', 'Newcastle United', 'M', 84, 81, 84, 80, 82, 0, 0, 3, '167943.png'),
(288, 'Yohan Cabaye', 'Newcastle United', 'M', 84, 81, 84, 80, 86, 1, 0, 4, '167943.png'),
(289, 'Stephane Ruffier', 'Saint Etienne', 'G', 40, 86, 82, 83, 83, 0, 0, 3, '167628.png'),
(290, 'Stephane Ruffier', 'Saint Etienne', 'G', 40, 86, 82, 83, 87, 1, 0, 4, '167628.png'),
(291, 'Laurent Koscielny', 'Arsenal', 'D', 50, 84, 85, 80, 81, 0, 0, 3, '165229.png'),
(292, 'Laurent Koscielny', 'Arsenal', 'D', 50, 84, 85, 80, 85, 1, 0, 4, '165229.png'),
(293, 'Filipe Luis', 'Atletico Madrid', 'D', 40, 84, 83, 88, 83, 0, 0, 3, '164169.png'),
(294, 'Filipe Luis', 'Atletico Madrid', 'D', 40, 84, 83, 88, 87, 1, 0, 4, '164169.png'),
(295, 'Giuseppe Rossi', 'Fiorentina', 'F', 84, 50, 85, 76, 81, 0, 0, 3, '162409.png'),
(296, 'Giuseppe Rossi', 'Fiorentina', 'F', 84, 50, 85, 76, 85, 1, 0, 4, '162409.png'),
(297, 'Fernando Llorente', 'Juventus', 'F', 89, 40, 80, 81, 82, 0, 0, 3, '162131.png'),
(298, 'Fernando Llorente', 'Juventus', 'F', 89, 40, 80, 81, 86, 1, 0, 4, '162131.png'),
(299, 'Borja Valero', 'Fiorentina', 'M', 84, 81, 84, 80, 82, 0, 0, 3, '161956.png'),
(300, 'Borja Valero', 'Fiorentina', 'M', 84, 81, 84, 80, 86, 1, 0, 4, '161956.png'),
(301, 'Rodrigo Palacio', 'Inter Milan', 'F', 82, 48, 81, 78, 79, 0, 0, 2, '152999.png'),
(302, 'Rodrigo Palacio', 'Inter Milan', 'F', 82, 48, 81, 78, 83, 1, 0, 4, '152999.png'),
(303, 'Daniel Agger', 'Liverpool FC', 'D', 38, 85, 68, 82, 78, 0, 0, 2, '152039.png'),
(304, 'Daniel Agger', 'Liverpool FC', 'D', 38, 85, 68, 82, 82, 1, 0, 4, '152039.png'),
(305, 'Pablo Zabaleta', 'Manchester City', 'D', 58, 78, 80, 78, 77, 0, 0, 2, '142784.png'),
(306, 'Pablo Zabaleta', 'Manchester City', 'D', 58, 78, 80, 78, 81, 1, 0, 4, '142784.png'),
(307, 'Fernandinho', 'Manchester City', 'M', 75, 82, 78, 81, 79, 0, 0, 2, '135507.png'),
(308, 'Fernandinho', 'Manchester City', 'M', 75, 82, 78, 81, 83, 1, 5, 4, '135507.png'),
(309, 'Felipe Melo', 'Galatasaray', 'M', 78, 80, 68, 83, 78, 0, 0, 2, '135475.png'),
(310, 'Felipe Melo', 'Galatasaray', 'M', 78, 80, 68, 83, 82, 1, 0, 4, '135475.png'),
(311, 'Jefferson', 'Botafogo', 'G', 28, 83, 68, 80, 78, 0, 0, 2, '135377.png'),
(312, 'Jefferson', 'Botafogo', 'G', 28, 83, 68, 80, 82, 1, 0, 4, '135377.png'),
(313, 'Hugo Campagnaro', 'Inter Milan', 'D', 38, 82, 73, 82, 78, 0, 0, 2, '123603.png'),
(314, 'Hugo Campagnaro', 'Inter Milan', 'D', 38, 82, 73, 82, 82, 1, 0, 4, '123603.png'),
(315, 'Per Mertesacker', 'Arsenal', 'D', 40, 86, 64, 82, 78, 0, 0, 2, '53612.png'),
(316, 'Per Mertesacker', 'Arsenal', 'D', 40, 86, 64, 82, 82, 1, 0, 4, '53612.png'),
(317, 'Fernando Torres', 'Chelsea', 'F', 83, 36, 81, 80, 79, 0, 0, 2, '49369.png'),
(318, 'Fernando Torres', 'Chelsea', 'F', 83, 36, 81, 80, 83, 1, 0, 4, '49369.png'),
(319, 'Julio Cesar', 'QPR', 'G', 28, 84, 68, 79, 79, 0, 0, 2, '48717.png'),
(320, 'Julio Cesar', 'QPR', 'G', 28, 84, 68, 79, 83, 1, 0, 4, '48717.png'),
(321, 'Joaquin Sanchez', 'Fiorentina', 'M', 84, 72, 80, 74, 77, 0, 0, 2, '45186.png'),
(322, 'Joaquin Sanchez', 'Fiorentina', 'M', 84, 72, 80, 74, 81, 1, 0, 4, '45186.png'),
(323, 'Mikel Arteta', 'Arsenal', 'M', 82, 80, 80, 76, 79, 0, 0, 2, '45119.png'),
(324, 'Mikel Arteta', 'Arsenal', 'M', 82, 80, 80, 76, 83, 1, 0, 4, '45119.png'),
(325, 'Dimitar Berbatov', 'Fulham', 'F', 85, 36, 76, 80, 79, 0, 0, 2, '30110.png'),
(326, 'Dimitar Berbatov', 'Fulham', 'F', 85, 36, 76, 80, 83, 1, 0, 4, '30110.png'),
(327, 'Michael Carrick', 'Manchester United', 'M', 82, 81, 78, 76, 79, 0, 0, 2, '21146.png'),
(328, 'Michael Carrick', 'Manchester United', 'M', 82, 81, 78, 76, 83, 1, 0, 4, '21146.png'),
(329, 'Raphael Varane', 'Real Madrid', 'D', 31, 82, 75, 80, 77, 0, 0, 2, '201535.png'),
(330, 'Raphael Varane', 'Real Madrid', 'D', 31, 82, 75, 80, 81, 1, 0, 4, '201535.png'),
(331, 'Lucas Moura', 'Paris Saint Germain', 'M', 86, 70, 85, 71, 77, 0, 0, 2, '200949.png'),
(332, 'Lucas Moura', 'Paris Saint Germain', 'M', 86, 70, 85, 71, 81, 1, 0, 4, '200949.png'),
(333, 'Aymen Abdennour', 'Toulouse FC', 'D', 31, 82, 76, 80, 78, 0, 0, 2, '198076.png'),
(334, 'Aymen Abdennour', 'Toulouse FC', 'D', 31, 82, 76, 80, 82, 1, 0, 4, '198076.png'),
(335, 'Jackson Martinez', 'FC Porto', 'F', 81, 48, 81, 78, 79, 0, 0, 2, '196144.png'),
(336, 'Jackson Martinez', 'FC Porto', 'F', 81, 48, 81, 78, 83, 1, 0, 4, '196144.png'),
(337, 'Paul Pogba', 'Juventus', 'M', 82, 79, 80, 76, 79, 0, 0, 2, '195864.png'),
(338, 'Paul Pogba', 'Juventus', 'M', 82, 79, 80, 76, 83, 1, 0, 4, '195864.png'),
(339, 'Koke', 'Atletico Madrid', 'M', 79, 78, 80, 80, 79, 0, 0, 2, '193747.png'),
(340, 'Koke', 'Atletico Madrid', 'M', 79, 78, 80, 80, 83, 1, 0, 4, '193747.png'),
(341, 'Kevin De Bruyne', 'Chelsea FC', 'M', 76, 79, 85, 76, 79, 0, 0, 2, '192985.png'),
(342, 'Kevin De Bruyne', 'Chelsea FC', 'M', 76, 79, 85, 76, 83, 1, 0, 4, '192985.png'),
(343, 'Holger Badstuber', 'Bayern Munich', 'D', 35, 83, 79, 79, 79, 0, 0, 2, '192620.png'),
(344, 'Holger Badstuber', 'Bayern Munich', 'D', 35, 83, 79, 79, 83, 1, 0, 4, '192620.png'),
(345, 'Bernd Leno', 'Bayer Leverkusen', 'G', 18, 85, 68, 79, 79, 0, 0, 2, '192563.png'),
(346, 'Bernd Leno', 'Bayer Leverkusen', 'G', 18, 85, 68, 79, 83, 1, 0, 4, '192563.png'),
(347, 'Marc Andre ter Stegen', 'Borussia MGladbach', 'G', 28, 82, 70, 74, 77, 0, 0, 2, '192448.png'),
(348, 'Marc Andre ter Stegen', 'Borussia MGladbach', 'G', 28, 82, 70, 74, 81, 1, 0, 4, '192448.png'),
(349, 'Stephan El Shaarawy', 'AC Milan', 'F', 81, 28, 80, 78, 77, 0, 0, 2, '190813.png'),
(350, 'Stephan El Shaarawy', 'AC Milan', 'F', 81, 28, 80, 78, 81, 1, 0, 4, '190813.png'),
(351, 'Sandro', 'Tottenham Hotspur', 'M', 76, 78, 74, 85, 79, 0, 0, 2, '190782.png'),
(352, 'Sandro', 'Tottenham Hotspur', 'M', 76, 78, 74, 85, 83, 1, 0, 4, '190782.png'),
(353, 'Eliaquim Mangala', 'FC Porto', 'D', 40, 78, 69, 76, 74, 0, 0, 2, '190531.png'),
(354, 'Eliaquim Mangala', 'FC Porto', 'D', 40, 78, 69, 76, 78, 1, 0, 4, '190531.png'),
(355, 'Kevin Stwebmmowe_bdl2013man', 'AS Roma', 'M', 80, 80, 78, 74, 77, 0, 0, 2, '189712.png'),
(356, 'Kevin Stwebmmowe_bdl2013man', 'AS Roma', 'M', 80, 80, 78, 74, 81, 1, 0, 4, '189712.png'),
(357, 'Nicolas Nkoulou', 'Marseille', 'D', 28, 84, 68, 78, 76, 0, 0, 2, '188829.png'),
(358, 'Nicolas Nkoulou', 'Marseille', 'D', 28, 84, 68, 78, 80, 1, 0, 4, '188829.png'),
(359, 'Paulinho', 'Tottenham Hotspur', 'M', 74, 78, 68, 80, 75, 0, 0, 2, '187961.png'),
(360, 'Paulinho', 'Tottenham Hotspur', 'M', 74, 78, 68, 80, 79, 1, 0, 4, '187961.png'),
(361, 'Keisuke Honda', 'AC Milan', 'M', 78, 68, 83, 74, 76, 0, 0, 2, '186581.png'),
(362, 'Keisuke Honda', 'AC Milan', 'M', 78, 68, 83, 74, 80, 1, 0, 4, '186581.png'),
(363, 'Aaron Ramsey', 'Arsenal', 'M', 82, 68, 78, 83, 79, 0, 0, 2, '186561.png'),
(364, 'Aaron Ramsey', 'Arsenal', 'M', 82, 68, 78, 83, 83, 1, 0, 4, '186561.png'),
(365, 'Luiz Gustavo', 'Vfl Wolfsburg', 'M', 68, 80, 68, 78, 74, 0, 0, 2, '185221.png'),
(366, 'Luiz Gustavo', 'Vfl Wolfsburg', 'M', 68, 80, 68, 78, 78, 1, 0, 4, '185221.png'),
(367, 'Ramires', 'Chelsea FC', 'M', 79, 74, 79, 78, 78, 0, 0, 2, '184943.png'),
(368, 'Ramires', 'Chelsea FC', 'M', 79, 74, 79, 78, 82, 1, 0, 4, '184943.png'),
(369, 'Sofiane Feghouli', 'Valencia', 'M', 75, 70, 79, 74, 75, 0, 0, 2, '184881.png'),
(370, 'Sofiane Feghouli', 'Valencia', 'M', 75, 70, 79, 74, 79, 1, 0, 4, '184881.png'),
(371, 'Nico Gaitan', 'SL Benfica', 'M', 78, 71, 79, 74, 75, 0, 0, 2, '184144.png'),
(372, 'Nico Gaitan', 'SL Benfica', 'M', 78, 71, 79, 74, 79, 1, 0, 4, '184144.png'),
(373, 'Christian Benteke', 'Aston Villa', 'F', 80, 28, 80, 78, 77, 0, 0, 2, '184111.png'),
(374, 'Christian Benteke', 'Aston Villa', 'F', 80, 28, 80, 78, 81, 1, 0, 4, '184111.png'),
(375, 'Mamadou Sakho', 'Liverpool FC', 'D', 35, 79, 78, 79, 77, 0, 0, 2, '183285.png'),
(376, 'Mamadou Sakho', 'Liverpool FC', 'D', 35, 79, 78, 79, 81, 1, 0, 4, '183285.png'),
(377, 'Adil Rami', 'Valencia', 'D', 35, 84, 76, 79, 78, 0, 0, 2, '183280.png'),
(378, 'Adil Rami', 'Valencia', 'D', 35, 84, 76, 79, 82, 1, 0, 4, '183280.png'),
(379, 'Oscar Cardozo', 'SL Benfica', 'F', 83, 28, 76, 80, 78, 0, 0, 2, '179752.png'),
(380, 'Oscar Cardozo', 'SL Benfica', 'F', 83, 28, 76, 80, 82, 1, 0, 4, '179752.png'),
(381, 'Olivier Giroud', 'Arsenal', 'F', 81, 18, 80, 78, 77, 0, 0, 2, '178509.png'),
(382, 'Olivier Giroud', 'Arsenal', 'F', 81, 18, 80, 78, 81, 1, 0, 4, '178509.png'),
(383, 'Branislav Ivanovic', 'Chelsea FC', 'D', 62, 78, 78, 82, 78, 0, 0, 2, '178372.png'),
(384, 'Branislav Ivanovic', 'Chelsea FC', 'D', 62, 78, 78, 82, 82, 1, 0, 4, '178372.png'),
(385, 'Rui Patricio', 'Sporting Lisbon', 'G', 28, 84, 70, 74, 78, 0, 0, 2, '178005.png'),
(386, 'Rui Patricio', 'Sporting Lisbon', 'G', 28, 84, 70, 74, 82, 1, 0, 4, '178005.png'),
(387, 'Benat Etxeberria', 'Athletic Bilbao', 'M', 80, 68, 78, 72, 74, 0, 0, 2, '177600.png'),
(388, 'Benat Etxeberria', 'Athletic Bilbao', 'M', 80, 68, 78, 72, 78, 1, 0, 4, '177600.png'),
(389, 'Sven Bender', 'Borussia Dortmund', 'M', 76, 78, 72, 74, 75, 0, 0, 2, '177458.png'),
(390, 'Sven Bender', 'Borussia Dortmund', 'M', 76, 78, 72, 74, 79, 1, 0, 4, '177458.png'),
(391, 'Demba Ba', 'Chelsea FC', 'F', 80, 18, 83, 78, 77, 0, 0, 2, '177134.png'),
(392, 'Demba Ba', 'Chelsea FC', 'F', 80, 18, 83, 78, 81, 1, 0, 4, '177134.png'),
(393, 'Jonas', 'Valencia', 'F', 83, 18, 74, 80, 76, 0, 0, 2, '176769.png'),
(394, 'Jonas', 'Valencia', 'F', 83, 18, 74, 80, 80, 1, 0, 4, '176769.png'),
(395, 'Claudio Bravo', 'Real Sociedad', 'G', 32, 80, 70, 74, 75, 0, 0, 2, '174543.png'),
(396, 'Claudio Bravo', 'Real Sociedad', 'G', 32, 80, 70, 74, 79, 1, 0, 4, '174543.png'),
(397, 'Simon Mignolet', 'Liverpool FC', 'G', 30, 83, 70, 76, 78, 0, 0, 2, '173426.png'),
(398, 'Simon Mignolet', 'Liverpool FC', 'G', 30, 83, 70, 76, 82, 1, 0, 4, '173426.png'),
(399, 'Sokratis Papastathopoulos', 'Borussia Dortmund', 'D', 35, 84, 76, 80, 79, 0, 0, 2, '172879.png'),
(400, 'Sokratis Papastathopoulos', 'Borussia Dortmund', 'D', 35, 84, 76, 80, 83, 1, 0, 4, '172879.png'),
(401, 'Asmir Begovic', 'Stoke City', 'G', 35, 83, 75, 76, 78, 0, 0, 2, '172723.png'),
(402, 'Asmir Begovic', 'Stoke City', 'G', 35, 83, 75, 76, 82, 1, 0, 4, '172723.png'),
(403, 'Andres Guardado', 'Valencia', 'M', 74, 70, 71, 72, 72, 0, 0, 2, '171897.png'),
(404, 'Andres Guardado', 'Valencia', 'M', 74, 70, 71, 72, 76, 1, 0, 4, '171897.png'),
(405, 'Erik Lamela', 'Tottenham Hotspur', 'M', 80, 60, 85, 74, 75, 0, 0, 2, '170368.png'),
(406, 'Erik Lamela', 'Tottenham Hotspur', 'M', 80, 60, 85, 74, 79, 1, 0, 4, '170368.png'),
(407, 'Ivan Rakitic', 'Sevilla', 'M', 81, 73, 71, 80, 77, 0, 0, 2, '168651.png'),
(408, 'Ivan Rakitic', 'Sevilla', 'M', 81, 73, 71, 80, 81, 1, 0, 4, '168651.png'),
(409, 'Miranda', 'Atletico Madrid', 'D', 28, 80, 78, 82, 78, 0, 0, 2, '168609.png'),
(410, 'Miranda', 'Atletico Madrid', 'D', 28, 80, 78, 82, 82, 1, 0, 4, '168609.png'),
(411, 'Martin Skrtel', 'Liverpool FC', 'D', 38, 81, 68, 75, 74, 0, 0, 2, '166706.png'),
(412, 'Martin Skrtel', 'Liverpool FC', 'D', 38, 81, 68, 75, 78, 1, 0, 4, '166706.png'),
(413, 'Diego Alves', 'Valencia', 'G', 38, 81, 68, 80, 77, 0, 0, 2, '165580.png'),
(414, 'Diego Alves', 'Valencia', 'G', 38, 81, 68, 80, 81, 1, 0, 4, '165580.png'),
(415, 'Theo Walcott', 'Arsenal', 'F', 75, 36, 85, 80, 77, 0, 0, 2, '164859.png'),
(416, 'Theo Walcott', 'Arsenal', 'F', 75, 36, 85, 80, 81, 1, 3, 4, '164859.png'),
(417, 'Mbark Boussoufa', 'Lokomotiv Moskva', 'M', 81, 80, 80, 68, 76, 0, 0, 2, '164435.png'),
(418, 'Mbark Boussoufa', 'Lokomotiv Moskva', 'M', 81, 80, 80, 68, 80, 1, 0, 4, '164435.png'),
(419, 'Clint Dempsey', 'Fulham', 'F', 80, 39, 80, 80, 78, 0, 0, 2, '155897.png'),
(420, 'Clint Dempsey', 'Fulham', 'F', 80, 39, 80, 80, 82, 1, 0, 4, '155897.png'),
(421, 'Rio Mavuba', 'Lille', 'M', 78, 82, 68, 80, 77, 0, 0, 2, '150656.png'),
(422, 'Rio Mavuba', 'Lille', 'M', 78, 82, 68, 80, 81, 1, 0, 4, '150656.png'),
(423, 'Gonzalo Rodriguez', 'Fiorentina', 'D', 38, 84, 68, 82, 78, 0, 0, 2, '142780.png'),
(424, 'Gonzalo Rodriguez', 'Fiorentina', 'D', 38, 84, 68, 82, 82, 1, 0, 4, '142780.png'),
(425, 'Diego Milito', 'Inter Milan', 'F', 83, 38, 77, 78, 78, 0, 0, 2, '142708.png'),
(426, 'Diego Milito', 'Inter Milan', 'F', 83, 38, 77, 78, 82, 1, 0, 4, '142708.png'),
(427, 'Helton', 'FC Porto', 'G', 30, 80, 71, 76, 76, 0, 0, 2, '139094.png'),
(428, 'Helton', 'FC Porto', 'G', 30, 80, 71, 76, 80, 1, 0, 4, '139094.png'),
(429, 'James Milner', 'Manchester City', 'M', 81, 70, 75, 70, 73, 0, 0, 2, '138412.png'),
(430, 'James Milner', 'Manchester City', 'M', 81, 70, 75, 70, 77, 1, 0, 4, '138412.png'),
(431, 'Stefan Kiessling', 'Bayer Leverkusen', 'F', 83, 48, 70, 85, 78, 0, 0, 2, '137262.png'),
(432, 'Stefan Kiessling', 'Bayer Leverkusen', 'F', 83, 48, 70, 85, 82, 1, 0, 4, '137262.png'),
(433, 'Alex', 'Paris Saint Germain', 'D', 38, 85, 68, 80, 78, 0, 0, 2, '136130.png'),
(434, 'Alex', 'Paris Saint Germain', 'D', 38, 85, 68, 80, 82, 1, 0, 4, '136130.png'),
(435, 'Martin Demichelis', 'Manchester City', 'D', 38, 85, 68, 82, 78, 0, 0, 2, '134979.png'),
(436, 'Martin Demichelis', 'Manchester City', 'D', 38, 85, 68, 82, 82, 1, 0, 4, '134979.png'),
(437, 'Jermain Defoe', 'Tottenham Hotspur', 'F', 82, 30, 81, 78, 78, 0, 0, 2, '50542.png'),
(438, 'Jermain Defoe', 'Tottenham Hotspur', 'F', 82, 30, 81, 78, 82, 1, 0, 4, '50542.png'),
(439, 'Michael Essien', 'Chelsea FC', 'M', 70, 83, 71, 80, 76, 0, 0, 2, '45674.png'),
(440, 'Michael Essien', 'Chelsea FC', 'M', 70, 83, 71, 80, 80, 1, 0, 4, '45674.png'),
(441, 'Philippe Mexes', 'AC Milan', 'D', 38, 82, 70, 81, 77, 0, 0, 2, '41635.png'),
(442, 'Philippe Mexes', 'AC Milan', 'D', 38, 82, 70, 81, 81, 1, 0, 4, '41635.png'),
(443, 'Ronaldinho', 'Atletico Mineiro', 'M', 85, 69, 71, 80, 77, 0, 0, 2, '28130.png'),
(444, 'Ronaldinho', 'Atletico Mineiro', 'M', 85, 69, 71, 80, 81, 1, 0, 4, '28130.png'),
(445, 'Walter Samuel', 'Inter Milan', 'D', 38, 85, 67, 82, 78, 0, 0, 2, '23461.png'),
(446, 'Walter Samuel', 'Inter Milan', 'D', 38, 85, 67, 82, 82, 1, 0, 4, '23461.png'),
(447, 'Tim Howard', 'Everton', 'G', 30, 83, 70, 77, 78, 0, 0, 2, '16254.png'),
(448, 'Tim Howard', 'Everton', 'G', 30, 83, 70, 77, 82, 1, 0, 4, '16254.png'),
(449, 'Frank Lampard', 'Chelsea FC', 'M', 81, 73, 75, 80, 78, 0, 0, 2, '5471.png'),
(450, 'Frank Lampard', 'Chelsea FC', 'M', 81, 73, 75, 80, 82, 1, 0, 4, '5471.png'),
(451, 'Mickael Landreau', 'Bastia', 'G', 30, 86, 50, 76, 78, 0, 0, 2, '3813.png'),
(452, 'Mickael Landreau', 'Bastia', 'G', 30, 86, 50, 76, 82, 1, 0, 4, '3813.png'),
(453, 'Francesco Totti', 'AS Roma', 'F', 88, 35, 68, 78, 77, 0, 0, 2, '1238.png'),
(454, 'Francesco Totti', 'AS Roma', 'F', 88, 35, 68, 78, 81, 1, 0, 4, '1238.png'),
(455, 'Bernard', 'Shakhtar Donetsk', 'M', 70, 65, 70, 70, 69, 0, 0, 1, '205525.png'),
(456, 'Bernard', 'Shakhtar Donetsk', 'M', 70, 65, 70, 70, 73, 1, 0, 4, '205525.png'),
(457, 'Inigo Martinez', 'Real Sociedad', 'D', 65, 69, 69, 62, 67, 0, 0, 1, '204525.png'),
(458, 'Inigo Martinez', 'Real Sociedad', 'D', 65, 69, 69, 62, 71, 1, 0, 4, '204525.png'),
(459, 'Julian Draxler', 'Schalke 04', 'M', 70, 68, 70, 70, 70, 0, 0, 1, '202166.png'),
(460, 'Julian Draxler', 'Schalke 04', 'M', 70, 68, 70, 70, 74, 1, 0, 4, '202166.png'),
(461, 'Dusan Tadic', 'FC Twente', 'F', 65, 64, 69, 68, 67, 0, 0, 1, '199434.png'),
(462, 'Dusan Tadic', 'FC Twente', 'F', 65, 64, 69, 68, 71, 1, 0, 4, '199434.png'),
(463, 'Phil Jones', 'Manchester United', 'D', 70, 70, 68, 70, 70, 0, 0, 1, '194957.png'),
(464, 'Phil Jones', 'Manchester United', 'D', 70, 70, 68, 70, 74, 1, 0, 4, '194957.png'),
(465, 'Antoine Griezmann', 'Real Sociedad', 'M', 70, 70, 70, 64, 68, 0, 0, 1, '194765.png'),
(466, 'Antoine Griezmann', 'Real Sociedad', 'M', 70, 70, 70, 64, 72, 1, 0, 4, '194765.png'),
(467, 'Xherdan Shaqiri', 'Bayern Munich', 'M', 70, 68, 70, 70, 70, 0, 0, 1, '193348.png'),
(468, 'Xherdan Shaqiri', 'Bayern Munich', 'M', 70, 68, 70, 70, 74, 1, 0, 4, '193348.png'),
(469, 'Andre Schurrle', 'Chelsea FC', 'F', 71, 30, 70, 70, 68, 0, 0, 1, '193130.png'),
(470, 'Andre Schurrle', 'Chelsea FC', 'F', 71, 30, 70, 70, 72, 1, 0, 4, '193130.png'),
(471, 'Emanuele Giaccherini', 'Sunderland', 'M', 70, 63, 70, 70, 69, 0, 0, 1, '192841.png'),
(472, 'Emanuele Giaccherini', 'Sunderland', 'M', 70, 63, 70, 70, 73, 1, 0, 4, '192841.png'),
(473, 'Romelu Lukaku', 'Everton', 'F', 71, 40, 70, 68, 69, 0, 0, 1, '192505.png'),
(474, 'Romelu Lukaku', 'Everton', 'F', 71, 40, 70, 68, 73, 1, 0, 4, '192505.png'),
(475, 'Nicolas Otamendi', 'FC Porto', 'D', 40, 70, 70, 70, 69, 0, 0, 1, '192366.png'),
(476, 'Nicolas Otamendi', 'FC Porto', 'D', 40, 70, 70, 70, 73, 1, 0, 4, '192366.png'),
(477, 'Nemanja Matic', 'SL Benfica', 'M', 70, 70, 60, 70, 68, 0, 0, 1, '191202.png'),
(478, 'Nemanja Matic', 'SL Benfica', 'M', 70, 70, 60, 70, 72, 1, 0, 4, '191202.png'),
(479, 'Asier Ilarramendi', 'Real Madrid', 'M', 66, 70, 61, 70, 67, 0, 0, 1, '190584.png'),
(480, 'Asier Ilarramendi', 'Real Madrid', 'M', 66, 70, 61, 70, 71, 1, 0, 4, '190584.png'),
(481, 'Adem Ljajic', 'AS Roma', 'F', 69, 68, 67, 66, 68, 0, 0, 1, '190544.png'),
(482, 'Adem Ljajic', 'AS Roma', 'F', 69, 68, 67, 66, 72, 1, 0, 4, '190544.png'),
(483, 'Wilfried Bony', 'Swansea', 'F', 70, 30, 70, 65, 67, 0, 0, 1, '189963.png'),
(484, 'Wilfried Bony', 'Swansea', 'F', 70, 30, 70, 65, 71, 1, 0, 4, '189963.png'),
(485, 'Vicente Guaita', 'Valencia', 'G', 18, 73, 50, 65, 66, 0, 0, 1, '189690.png'),
(486, 'Vicente Guaita', 'Valencia', 'G', 18, 73, 50, 65, 70, 1, 0, 4, '189690.png'),
(487, 'Thiago Alcantara', 'Bayern Munich', 'M', 70, 70, 64, 69, 68, 0, 0, 1, '189509.png'),
(488, 'Thiago Alcantara', 'Bayern Munich', 'M', 70, 70, 64, 69, 72, 1, 0, 4, '189509.png'),
(489, 'Philippe Coutinho', 'Liverpool FC', 'M', 70, 70, 70, 70, 70, 0, 0, 1, '189242.png'),
(490, 'Philippe Coutinho', 'Liverpool FC', 'M', 70, 70, 70, 70, 74, 1, 0, 4, '189242.png'),
(491, 'Alberto Costa', 'Spartak Moscow', 'M', 64, 68, 70, 70, 68, 0, 0, 1, '188791.png'),
(492, 'Alberto Costa', 'Spartak Moscow', 'M', 64, 68, 70, 70, 72, 1, 0, 4, '188791.png'),
(493, 'Pierre Aubameyang', 'Borussia Dortmund', 'F', 70, 60, 70, 60, 68, 0, 0, 1, '188567.png'),
(494, 'Pierre Aubameyang', 'Borussia Dortmund', 'F', 70, 60, 70, 60, 72, 1, 0, 4, '188567.png'),
(495, 'Seydou Doumbia', 'CSKA Moscow', 'F', 70, 50, 71, 68, 69, 0, 0, 1, '188428.png'),
(496, 'Seydou Doumbia', 'CSKA Moscow', 'F', 70, 50, 71, 68, 73, 1, 0, 4, '188428.png'),
(497, 'Sven Ulreich', 'Vfb Stuttgart', 'G', 50, 71, 68, 69, 69, 0, 0, 1, '186569.png'),
(498, 'Sven Ulreich', 'Vfb Stuttgart', 'G', 50, 71, 68, 69, 73, 1, 0, 4, '186569.png'),
(499, 'Wojciech Szczesny', 'Arsenal', 'G', 50, 70, 68, 69, 69, 0, 0, 1, '186153.png'),
(500, 'Wojciech Szczesny', 'Arsenal', 'G', 50, 70, 68, 69, 73, 1, 0, 4, '186153.png'),
(501, 'Everton Riberio', 'Cruzeiro', 'M', 70, 70, 70, 60, 67, 0, 0, 1, '181019.png'),
(502, 'Everton Riberio', 'Cruzeiro', 'M', 70, 70, 70, 60, 71, 1, 0, 4, '181019.png'),
(503, 'Lima', 'SL Benfica', 'F', 70, 50, 71, 68, 69, 0, 0, 1, '180695.png'),
(504, 'Lima', 'SL Benfica', 'F', 70, 50, 71, 68, 73, 1, 0, 4, '180695.png'),
(505, 'Miralem Pjanic', 'AS Roma', 'M', 70, 70, 67, 70, 69, 0, 0, 1, '180206.png'),
(506, 'Miralem Pjanic', 'AS Roma', 'M', 70, 70, 67, 70, 73, 1, 0, 4, '180206.png'),
(507, 'Radja Nainggolan', 'Cagliari', 'M', 65, 70, 70, 70, 69, 0, 0, 1, '178518.png'),
(508, 'Radja Nainggolan', 'Cagliari', 'M', 65, 70, 70, 70, 73, 1, 0, 4, '178518.png'),
(509, 'Sydney Sam', 'Bayer Leverkusen', 'M', 70, 70, 70, 62, 67, 0, 0, 1, '177934.png'),
(510, 'Sydney Sam', 'Bayer Leverkusen', 'M', 70, 70, 70, 62, 71, 1, 0, 4, '177934.png'),
(511, 'Axel Witsel', 'Zenit', 'M', 70, 70, 70, 60, 67, 0, 0, 1, '177413.png'),
(512, 'Axel Witsel', 'Zenit', 'M', 70, 70, 70, 60, 71, 1, 0, 4, '177413.png'),
(513, 'Dimitri Payet', 'Marseille', 'M', 70, 64, 70, 70, 69, 0, 0, 1, '177388.png'),
(514, 'Dimitri Payet', 'Marseille', 'M', 70, 64, 70, 70, 73, 1, 0, 4, '177388.png'),
(515, 'Patrick Ebert', 'Valladolid', 'M', 68, 66, 70, 70, 69, 0, 0, 1, '177105.png'),
(516, 'Patrick Ebert', 'Valladolid', 'M', 68, 66, 70, 70, 73, 1, 0, 4, '177105.png'),
(517, 'Marouane Fellaini', 'Manchester United', 'M', 69, 70, 63, 70, 68, 0, 0, 1, '176944.png'),
(518, 'Marouane Fellaini', 'Manchester United', 'M', 69, 70, 63, 70, 72, 1, 0, 4, '176944.png'),
(519, 'Andre Ayew', 'Marseille', 'F', 70, 50, 70, 68, 69, 0, 0, 1, '176571.png'),
(520, 'Andre Ayew', 'Marseille', 'F', 70, 50, 70, 68, 73, 1, 0, 4, '176571.png'),
(521, 'Lucas Leiva', 'Liverpool FC', 'M', 70, 70, 64, 70, 69, 0, 0, 1, '176266.png'),
(522, 'Lucas Leiva', 'Liverpool FC', 'M', 70, 70, 64, 70, 73, 1, 0, 4, '176266.png'),
(523, 'Domenico Criscito', 'Zenit', 'D', 60, 70, 70, 70, 70, 0, 0, 1, '173208.png'),
(524, 'Domenico Criscito', 'Zenit', 'D', 60, 70, 70, 70, 74, 1, 0, 4, '173208.png'),
(525, 'Leandro Castan', 'AS Roma', 'D', 50, 70, 70, 70, 69, 0, 0, 1, '172610.png'),
(526, 'Leandro Castan', 'AS Roma', 'D', 50, 70, 70, 70, 73, 1, 10, 4, '172610.png'),
(527, 'Kevin Mirallas', 'Everton', 'F', 69, 50, 71, 68, 68, 0, 0, 1, '172175.png'),
(528, 'Kevin Mirallas', 'Everton', 'F', 69, 50, 71, 68, 72, 1, 1, 4, '172175.png'),
(529, 'Lucho Gonzalez', 'FC Porto', 'M', 70, 70, 64, 70, 69, 0, 0, 1, '142750.png'),
(530, 'Lucho Gonzalez', 'FC Porto', 'M', 70, 70, 64, 70, 73, 1, 0, 4, '142750.png'),
(531, 'Darijo Srna', 'Shakhtar Donetsk', 'D', 68, 66, 70, 70, 68, 0, 0, 1, '139997.png'),
(532, 'Darijo Srna', 'Shakhtar Donetsk', 'D', 68, 66, 70, 70, 72, 1, 0, 4, '139997.png'),
(533, 'Andres Dalessandro', 'Internacional', 'M', 70, 70, 70, 70, 70, 0, 0, 1, '138703.png'),
(534, 'Andres Dalessandro', 'Internacional', 'M', 70, 70, 70, 70, 74, 1, 0, 4, '138703.png'),
(535, 'Kossi Agassa', 'Reims', 'G', 60, 70, 68, 69, 69, 0, 0, 1, '137763.png'),
(536, 'Kossi Agassa', 'Reims', 'G', 60, 70, 68, 69, 73, 1, 0, 4, '137763.png'),
(537, 'Michel Vorm', 'Swansea', 'G', 61, 70, 68, 69, 69, 0, 0, 1, '137677.png'),
(538, 'Michel Vorm', 'Swansea', 'G', 61, 70, 68, 69, 73, 1, 0, 4, '137677.png'),
(539, 'Alex de Souza', 'Coritibia', 'M', 68, 69, 70, 70, 69, 0, 0, 1, '135460.png'),
(540, 'Alex de Souza', 'Coritibia', 'M', 68, 69, 70, 70, 73, 1, 0, 4, '135460.png'),
(541, 'Kolo Toure', 'Liverpool FC', 'D', 40, 70, 70, 68, 68, 0, 0, 1, '119152.png'),
(542, 'Kolo Toure', 'Liverpool FC', 'D', 40, 70, 70, 68, 72, 1, 0, 4, '119152.png'),
(543, 'Ruben Castro', 'Real Betis', 'F', 70, 60, 71, 68, 69, 0, 0, 1, '115909.png'),
(544, 'Ruben Castro', 'Real Betis', 'F', 70, 60, 71, 68, 73, 1, 0, 4, '115909.png'),
(545, 'Diego Benaglio', 'Vfl Wolfsburg', 'G', 50, 71, 68, 69, 69, 0, 0, 1, '115533.png'),
(546, 'Diego Benaglio', 'Vfl Wolfsburg', 'G', 50, 71, 68, 69, 73, 1, 0, 4, '115533.png'),
(547, 'Mirko Vucinic', 'Juventus', 'F', 73, 50, 71, 68, 70, 0, 0, 1, '106850.png'),
(548, 'Mirko Vucinic', 'Juventus', 'F', 73, 50, 71, 68, 74, 1, 0, 4, '106850.png'),
(549, 'Alberto Aquilani', 'Fiorentina', 'M', 70, 70, 64, 70, 69, 0, 0, 1, '103935.png'),
(550, 'Alberto Aquilani', 'Fiorentina', 'M', 70, 70, 64, 70, 73, 1, 0, 4, '103935.png'),
(551, 'Phil Jagielka', 'Everton', 'D', 40, 71, 70, 65, 67, 0, 0, 1, '53914.png'),
(552, 'Phil Jagielka', 'Everton', 'D', 40, 71, 70, 65, 71, 1, 0, 4, '53914.png'),
(553, 'Patrice Evra', 'Manchester United', 'D', 69, 65, 72, 68, 68, 0, 0, 1, '52091.png'),
(554, 'Patrice Evra', 'Manchester United', 'D', 69, 65, 72, 68, 72, 1, 0, 4, '52091.png'),
(555, 'Steven Pienaar', 'Everton', 'M', 66, 62, 70, 70, 68, 0, 0, 1, '50327.png'),
(556, 'Steven Pienaar', 'Everton', 'M', 66, 62, 70, 70, 72, 1, 0, 4, '50327.png'),
(557, 'Thiago Motta', 'Paris Saint Germain', 'M', 63, 69, 70, 70, 68, 0, 0, 1, '49370.png'),
(558, 'Thiago Motta', 'Paris Saint Germain', 'M', 63, 69, 70, 70, 72, 1, 0, 4, '49370.png'),
(559, 'Fabricio Coloccini', 'Newcastle United', 'D', 40, 70, 70, 68, 68, 0, 0, 1, '48745.png'),
(560, 'Fabricio Coloccini', 'Newcastle United', 'D', 40, 70, 70, 68, 72, 1, 0, 4, '48745.png'),
(561, 'Brede Hangeland', 'Fulham', 'D', 40, 70, 70, 70, 69, 0, 0, 1, '46815.png'),
(562, 'Brede Hangeland', 'Fulham', 'D', 40, 70, 70, 70, 73, 1, 0, 4, '46815.png'),
(563, 'Claudio Pizarro', 'Bayern Munich', 'F', 71, 60, 64, 70, 68, 0, 0, 1, '25420.png'),
(564, 'Claudio Pizarro', 'Bayern Munich', 'F', 71, 60, 64, 70, 72, 1, 0, 4, '25420.png'),
(565, 'David Pizarro', 'Fiorentina', 'M', 70, 69, 70, 70, 70, 0, 0, 1, '24248.png'),
(566, 'David Pizarro', 'Fiorentina', 'M', 70, 69, 70, 70, 74, 1, 0, 4, '24248.png'),
(567, 'Tomas Rosicky', 'Arsenal', 'M', 70, 70, 70, 70, 70, 0, 0, 1, '8473.png'),
(568, 'Tomas Rosicky', 'Arsenal', 'M', 70, 70, 70, 70, 74, 1, 0, 4, '8473.png'),
(569, 'Antonio Cassano', 'Parma', 'F', 71, 63, 64, 70, 68, 0, 0, 1, '7631.png'),
(570, 'Antonio Cassano', 'Parma', 'F', 71, 63, 64, 70, 72, 1, 0, 4, '7631.png'),
(571, 'Gareth Barry', 'Everton', 'M', 70, 70, 65, 70, 69, 0, 0, 1, '6826.png'),
(572, 'Gareth Barry', 'Everton', 'M', 70, 70, 65, 70, 73, 1, 0, 4, '6826.png'),
(573, 'Morgan De Sanctis', 'AS Roma', 'G', 60, 72, 63, 69, 70, 0, 0, 1, '4667.png'),
(574, 'Morgan De Sanctis', 'AS Roma', 'G', 60, 72, 63, 69, 74, 1, 0, 4, '4667.png'),
(575, 'Tiago Mendes', 'Atletico Madrid', 'M', 70, 70, 70, 70, 70, 0, 0, 1, '4098.png'),
(576, 'Tiago Mendes', 'Atletico Madrid', 'M', 70, 70, 70, 70, 74, 1, 0, 4, '4098.png'),
(577, 'Christian Abbiati', 'AC Milan', 'G', 60, 71, 67, 69, 70, 0, 0, 1, '1219.png'),
(578, 'Christian Abbiati', 'AC Milan', 'G', 60, 71, 67, 69, 74, 1, 0, 4, '1219.png');

-- --------------------------------------------------------

--
-- Table structure for table `pemain_tim_ai`
--

CREATE TABLE IF NOT EXISTS `pemain_tim_ai` (
  `id_pemain_tim_ai` int(11) NOT NULL AUTO_INCREMENT,
  `pemain_id_pemain` int(11) NOT NULL,
  `tim_ai_id_tim_ai` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_pemain_tim_ai`),
  KEY `fk_pemain_tim_ai_pemain1_idx` (`pemain_id_pemain`),
  KEY `fk_pemain_tim_ai_tim_ai1_idx` (`tim_ai_id_tim_ai`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=276 ;

--
-- Dumping data for table `pemain_tim_ai`
--

INSERT INTO `pemain_tim_ai` (`id_pemain_tim_ai`, `pemain_id_pemain`, `tim_ai_id_tim_ai`, `aktif`) VALUES
(1, 461, 1, 1),
(2, 469, 1, 2),
(3, 455, 1, 3),
(4, 459, 1, 4),
(5, 465, 1, 5),
(6, 467, 1, 6),
(7, 457, 1, 7),
(8, 463, 1, 8),
(9, 475, 1, 9),
(10, 523, 1, 10),
(11, 485, 1, 11),
(12, 473, 2, 1),
(13, 471, 2, 2),
(14, 477, 2, 3),
(15, 479, 2, 4),
(16, 487, 2, 5),
(17, 489, 2, 6),
(18, 525, 2, 7),
(19, 531, 2, 8),
(20, 541, 2, 9),
(21, 551, 2, 10),
(22, 497, 2, 11),
(23, 569, 3, 1),
(24, 481, 3, 2),
(25, 527, 3, 3),
(26, 505, 3, 4),
(27, 501, 3, 5),
(28, 491, 3, 6),
(29, 457, 3, 7),
(30, 463, 3, 8),
(31, 475, 3, 9),
(32, 523, 3, 10),
(33, 499, 3, 11),
(34, 543, 4, 1),
(35, 513, 4, 2),
(36, 511, 4, 3),
(37, 509, 4, 4),
(38, 507, 4, 5),
(39, 561, 4, 6),
(40, 559, 4, 7),
(41, 531, 4, 8),
(42, 541, 4, 9),
(43, 553, 4, 10),
(44, 535, 4, 11),
(45, 527, 5, 1),
(46, 519, 5, 2),
(47, 503, 5, 3),
(48, 557, 5, 4),
(49, 555, 5, 5),
(50, 549, 5, 6),
(51, 539, 5, 7),
(52, 475, 5, 8),
(53, 561, 5, 9),
(54, 525, 5, 10),
(55, 537, 5, 11),
(56, 563, 6, 1),
(57, 547, 6, 2),
(58, 515, 6, 3),
(59, 517, 6, 4),
(60, 521, 6, 5),
(61, 529, 6, 6),
(62, 533, 6, 7),
(63, 523, 6, 8),
(64, 457, 6, 9),
(65, 551, 6, 10),
(66, 545, 6, 11),
(67, 461, 7, 1),
(68, 469, 7, 2),
(69, 455, 7, 3),
(70, 459, 7, 4),
(71, 465, 7, 5),
(72, 467, 7, 6),
(73, 305, 7, 7),
(74, 303, 7, 8),
(75, 313, 7, 9),
(76, 343, 7, 10),
(77, 397, 7, 11),
(78, 473, 8, 1),
(79, 421, 8, 2),
(80, 387, 8, 3),
(81, 365, 8, 4),
(82, 361, 8, 5),
(83, 355, 8, 6),
(84, 525, 8, 7),
(85, 531, 8, 8),
(86, 541, 8, 9),
(87, 551, 8, 10),
(88, 385, 8, 11),
(89, 569, 9, 1),
(90, 481, 9, 2),
(91, 527, 9, 3),
(92, 505, 9, 4),
(93, 501, 9, 5),
(94, 491, 9, 6),
(95, 423, 9, 7),
(96, 411, 9, 8),
(97, 409, 9, 9),
(98, 399, 9, 10),
(99, 347, 9, 11),
(100, 453, 10, 1),
(101, 429, 10, 2),
(102, 417, 10, 3),
(103, 407, 10, 4),
(104, 405, 10, 5),
(105, 561, 10, 6),
(106, 559, 10, 7),
(107, 531, 10, 8),
(108, 541, 10, 9),
(109, 553, 10, 10),
(110, 345, 10, 11),
(111, 325, 11, 1),
(112, 379, 11, 2),
(113, 381, 11, 3),
(114, 331, 11, 4),
(115, 337, 11, 5),
(116, 339, 11, 6),
(117, 341, 11, 7),
(118, 475, 11, 8),
(119, 561, 11, 9),
(120, 525, 11, 10),
(121, 319, 11, 11),
(122, 301, 12, 1),
(123, 317, 12, 2),
(124, 327, 12, 3),
(125, 323, 12, 4),
(126, 321, 12, 5),
(127, 309, 12, 6),
(128, 307, 12, 7),
(129, 445, 12, 8),
(130, 441, 12, 9),
(131, 435, 12, 10),
(132, 311, 12, 11),
(133, 301, 13, 1),
(134, 285, 13, 2),
(135, 403, 13, 3),
(136, 429, 13, 4),
(137, 365, 13, 5),
(138, 387, 13, 6),
(139, 305, 13, 7),
(140, 303, 13, 8),
(141, 313, 13, 9),
(142, 343, 13, 10),
(143, 259, 13, 11),
(144, 317, 14, 1),
(145, 389, 14, 2),
(146, 387, 14, 3),
(147, 369, 14, 4),
(148, 389, 14, 5),
(149, 405, 14, 6),
(150, 399, 14, 7),
(151, 233, 14, 8),
(152, 219, 14, 9),
(153, 151, 14, 10),
(154, 217, 14, 11),
(155, 325, 15, 1),
(156, 295, 15, 2),
(157, 237, 15, 3),
(158, 439, 15, 4),
(159, 417, 15, 5),
(160, 361, 15, 6),
(161, 291, 15, 7),
(162, 257, 15, 8),
(163, 293, 15, 9),
(164, 399, 15, 10),
(165, 191, 15, 11),
(166, 335, 16, 1),
(167, 407, 16, 2),
(168, 355, 16, 3),
(169, 331, 16, 4),
(170, 321, 16, 5),
(171, 235, 16, 6),
(172, 399, 16, 7),
(173, 151, 16, 8),
(174, 343, 16, 9),
(175, 273, 16, 10),
(176, 227, 16, 11),
(177, 237, 17, 1),
(178, 209, 17, 2),
(179, 221, 17, 3),
(180, 363, 17, 4),
(181, 225, 17, 5),
(182, 201, 17, 6),
(183, 229, 17, 7),
(184, 343, 17, 8),
(185, 273, 17, 9),
(186, 235, 17, 10),
(187, 245, 17, 11),
(188, 275, 18, 1),
(189, 205, 18, 2),
(190, 229, 18, 3),
(191, 201, 18, 4),
(192, 225, 18, 5),
(193, 271, 18, 6),
(194, 247, 18, 7),
(195, 273, 18, 8),
(196, 235, 18, 9),
(197, 291, 18, 10),
(198, 289, 18, 11),
(199, 29, 19, 1),
(200, 169, 19, 2),
(201, 21, 19, 3),
(202, 19, 19, 4),
(203, 117, 19, 5),
(204, 7, 19, 6),
(205, 193, 19, 7),
(206, 133, 19, 8),
(207, 165, 19, 9),
(208, 37, 19, 10),
(209, 45, 19, 11),
(210, 39, 20, 1),
(211, 31, 20, 2),
(212, 163, 20, 3),
(213, 73, 20, 4),
(214, 27, 20, 5),
(215, 69, 20, 6),
(216, 193, 20, 7),
(217, 55, 20, 8),
(218, 57, 20, 9),
(219, 181, 20, 10),
(220, 245, 20, 11),
(221, 101, 21, 1),
(222, 1, 21, 2),
(223, 103, 21, 3),
(224, 15, 21, 4),
(225, 67, 21, 5),
(226, 11, 21, 6),
(227, 253, 21, 7),
(228, 235, 21, 8),
(229, 51, 21, 9),
(230, 141, 21, 10),
(231, 223, 21, 11),
(232, 53, 22, 1),
(233, 71, 22, 2),
(234, 73, 22, 3),
(235, 93, 22, 4),
(236, 119, 22, 5),
(237, 193, 22, 6),
(238, 177, 22, 7),
(239, 85, 22, 8),
(240, 49, 22, 9),
(241, 151, 22, 10),
(242, 95, 22, 11),
(243, 131, 23, 1),
(244, 9, 23, 2),
(245, 23, 23, 3),
(246, 31, 23, 4),
(247, 59, 23, 5),
(248, 277, 23, 6),
(249, 33, 23, 7),
(250, 283, 23, 8),
(251, 17, 23, 9),
(252, 177, 23, 10),
(253, 123, 23, 11),
(254, 1, 24, 1),
(255, 3, 24, 2),
(256, 31, 24, 3),
(257, 27, 24, 4),
(258, 163, 24, 5),
(259, 117, 24, 6),
(260, 71, 24, 7),
(261, 51, 24, 8),
(262, 17, 24, 9),
(263, 37, 24, 10),
(264, 45, 24, 11),
(265, 3, 25, 1),
(266, 1, 25, 2),
(267, 29, 25, 3),
(268, 31, 25, 4),
(269, 27, 25, 5),
(270, 47, 25, 6),
(271, 7, 25, 7),
(272, 37, 25, 8),
(273, 17, 25, 9),
(274, 51, 25, 10),
(275, 45, 25, 11);

-- --------------------------------------------------------

--
-- Table structure for table `penawaran`
--

CREATE TABLE IF NOT EXISTS `penawaran` (
  `id_penawaran` int(11) NOT NULL AUTO_INCREMENT,
  `harga_penawaran` int(10) unsigned NOT NULL,
  `jawaban` tinyint(3) unsigned NOT NULL DEFAULT '99',
  `waktu_penawaran` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `terbaca` tinyint(1) NOT NULL DEFAULT '0',
  `pemilik` varchar(45) NOT NULL,
  `penawar` varchar(45) NOT NULL,
  `id_user_pemain` int(11) NOT NULL,
  `pesan_penawaran` text,
  PRIMARY KEY (`id_penawaran`),
  KEY `fk_penawaran_user1_idx` (`pemilik`),
  KEY `fk_penawaran_user2_idx` (`penawar`),
  KEY `fk_penawaran_user_pemain1_idx` (`id_user_pemain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `penawaran`
--

INSERT INTO `penawaran` (`id_penawaran`, `harga_penawaran`, `jawaban`, `waktu_penawaran`, `terbaca`, `pemilik`, `penawar`, `id_user_pemain`, `pesan_penawaran`) VALUES
(1, 1000, 0, '2014-01-06 07:42:23', 0, 'ali', 'baskara', 25, 'accept cuk'),
(2, 50000, 99, '2014-01-06 09:19:29', 0, 'baskara', 'ali', 121, 'a');

-- --------------------------------------------------------

--
-- Table structure for table `proteksi_user`
--

CREATE TABLE IF NOT EXISTS `proteksi_user` (
  `id_proteksi_user` int(11) NOT NULL AUTO_INCREMENT,
  `proteksi_lastset_tu` int(10) unsigned NOT NULL,
  `user_username` varchar(45) NOT NULL,
  `user_username1` varchar(45) NOT NULL,
  PRIMARY KEY (`id_proteksi_user`),
  KEY `fk_proteksi_user_user1_idx` (`user_username`),
  KEY `fk_proteksi_user_user2_idx` (`user_username1`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `proteksi_user`
--

INSERT INTO `proteksi_user` (`id_proteksi_user`, `proteksi_lastset_tu`, `user_username`, `user_username1`) VALUES
(1, 70022, 'ali', 'joko'),
(2, 70022, 'ali', 'test'),
(3, 70023, 'ali', 'baskara'),
(4, 70118, 'imamkd', 'joko'),
(5, 70149, 'baskara', 'imamkd'),
(6, 70151, 'baskara', 'test'),
(7, 70184, 'wawang', 'bustan'),
(8, 70184, 'wawang', 'imamkd'),
(9, 70184, 'wawang', 'test'),
(10, 70184, 'wawang', 'joko'),
(11, 70201, 'ali', 'imamkd'),
(12, 70220, 'baskara', 'wawang'),
(13, 70220, 'baskara', 'bustan'),
(14, 70220, 'baskara', 'joko');

-- --------------------------------------------------------

--
-- Table structure for table `rumus_formasi`
--

CREATE TABLE IF NOT EXISTS `rumus_formasi` (
  `id_rumus_formasi` int(11) NOT NULL AUTO_INCREMENT,
  `nilai_rumus` int(10) unsigned NOT NULL,
  `formasi_id_formasi` int(11) NOT NULL,
  `formasi_id_formasi1` int(11) NOT NULL,
  PRIMARY KEY (`id_rumus_formasi`),
  KEY `fk_rumus_formasi_formasi1_idx` (`formasi_id_formasi`),
  KEY `fk_rumus_formasi_formasi2_idx` (`formasi_id_formasi1`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `rumus_formasi`
--

INSERT INTO `rumus_formasi` (`id_rumus_formasi`, `nilai_rumus`, `formasi_id_formasi`, `formasi_id_formasi1`) VALUES
(1, 100, 1, 1),
(2, 95, 1, 2),
(3, 90, 1, 3),
(4, 100, 1, 4),
(5, 105, 1, 5),
(6, 110, 1, 6),
(7, 105, 2, 1),
(8, 100, 2, 2),
(9, 95, 2, 3),
(10, 90, 2, 4),
(11, 110, 2, 5),
(12, 100, 2, 6),
(13, 110, 3, 1),
(14, 105, 3, 2),
(15, 100, 3, 3),
(16, 95, 3, 4),
(17, 100, 3, 5),
(18, 90, 3, 6),
(19, 100, 4, 1),
(20, 110, 4, 2),
(21, 105, 4, 3),
(22, 100, 4, 4),
(23, 90, 4, 5),
(24, 95, 4, 6),
(25, 95, 5, 1),
(26, 90, 5, 2),
(27, 100, 5, 3),
(28, 110, 5, 4),
(29, 100, 5, 5),
(30, 105, 5, 6),
(31, 90, 6, 1),
(32, 100, 6, 2),
(33, 110, 6, 3),
(34, 105, 6, 4),
(35, 95, 6, 5),
(36, 100, 6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `server_waktu_mulai` datetime DEFAULT NULL,
  `server_durasi_tu` time DEFAULT NULL,
  `ap_durasi_pertambahan_tu` tinyint(3) unsigned DEFAULT NULL,
  `ap_pertambahan` tinyint(3) unsigned DEFAULT NULL,
  `ap_maksimal` tinyint(3) unsigned DEFAULT NULL,
  `ap_nilai_awal` tinyint(3) unsigned DEFAULT NULL,
  `exp_nilai_awal` int(10) unsigned DEFAULT NULL,
  `kekompakan_nilai_awal` tinyint(3) unsigned DEFAULT NULL,
  `kekompakan_maksimal` tinyint(3) unsigned DEFAULT NULL,
  `proteksi_durasi_tu` tinyint(3) unsigned DEFAULT NULL,
  `balen_nilai_awal` int(10) unsigned DEFAULT NULL,
  `uang_nilai_awal` int(10) unsigned DEFAULT NULL,
  `pemain_tim_max` tinyint(3) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`server_waktu_mulai`, `server_durasi_tu`, `ap_durasi_pertambahan_tu`, `ap_pertambahan`, `ap_maksimal`, `ap_nilai_awal`, `exp_nilai_awal`, `kekompakan_nilai_awal`, `kekompakan_maksimal`, `proteksi_durasi_tu`, `balen_nilai_awal`, `uang_nilai_awal`, `pemain_tim_max`) VALUES
('2013-11-18 21:29:20', '00:01:00', 2, 1, 15, 15, 0, 100, 100, 127, 0, 55000, 23);

-- --------------------------------------------------------

--
-- Table structure for table `stadion`
--

CREATE TABLE IF NOT EXISTS `stadion` (
  `id_stadion` int(11) NOT NULL AUTO_INCREMENT,
  `harga_stadion` int(10) unsigned NOT NULL,
  `biaya_perawatan` int(10) unsigned NOT NULL,
  `pendapatan_persen` tinyint(3) unsigned NOT NULL,
  `gambar_stadion` varchar(45) NOT NULL,
  PRIMARY KEY (`id_stadion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `stadion`
--

INSERT INTO `stadion` (`id_stadion`, `harga_stadion`, `biaya_perawatan`, `pendapatan_persen`, `gambar_stadion`) VALUES
(1, 0, 0, 50, ''),
(2, 50000, 2000, 60, '1.png'),
(3, 100000, 3000, 75, '2.jpg'),
(4, 275000, 4000, 100, '3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tim_ai`
--

CREATE TABLE IF NOT EXISTS `tim_ai` (
  `id_tim_ai` int(11) NOT NULL AUTO_INCREMENT,
  `nama_tim_ai` varchar(45) NOT NULL,
  `kekompakan_tim_ai` tinyint(3) unsigned NOT NULL,
  `liga_id_liga` int(11) NOT NULL,
  `formasi_id_formasi` int(11) NOT NULL,
  `exp_gain` int(11) unsigned NOT NULL,
  `uang_gain` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_tim_ai`),
  KEY `fk_tim_ai_liga1_idx` (`liga_id_liga`),
  KEY `fk_tim_ai_formasi1_idx` (`formasi_id_formasi`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `tim_ai`
--

INSERT INTO `tim_ai` (`id_tim_ai`, `nama_tim_ai`, `kekompakan_tim_ai`, `liga_id_liga`, `formasi_id_formasi`, `exp_gain`, `uang_gain`) VALUES
(1, 'Persik Kediri', 50, 1, 1, 10, 5000),
(2, 'Persija Jakarta', 50, 1, 2, 10, 5000),
(3, 'Persis Solo', 50, 1, 3, 10, 5000),
(4, 'Persatu Tuban', 50, 1, 4, 10, 5000),
(5, 'Persipura Jayapura', 50, 1, 5, 10, 5000),
(6, 'Persisam Samarinda', 50, 1, 6, 10, 5000),
(7, 'Adelaide United', 65, 2, 1, 50, 10000),
(8, 'Brisbane Roar', 65, 2, 2, 50, 10000),
(9, 'Melbourne Victory', 65, 2, 3, 50, 10000),
(10, 'Perth Glory', 65, 2, 4, 50, 10000),
(11, 'Sydney FC', 65, 2, 5, 50, 10000),
(12, 'Newcastle Jets', 65, 2, 6, 50, 10000),
(13, 'River Plate', 80, 3, 1, 120, 15000),
(14, 'Independiente', 80, 3, 2, 120, 15000),
(15, 'Boca Juniors', 80, 3, 3, 120, 15000),
(16, 'Velez Sarsfield', 80, 3, 4, 120, 15000),
(17, 'San Lorenzo', 80, 3, 5, 120, 15000),
(18, 'Gimnasia La Plata', 80, 3, 6, 120, 15000),
(19, 'Bayern Munich', 100, 4, 1, 2500, 40000),
(20, 'Manchester United', 100, 4, 2, 2500, 40000),
(21, 'FC Barcelona', 100, 4, 3, 2500, 40000),
(22, 'Juventus', 100, 4, 4, 2500, 40000),
(23, 'Paris Saint Germain', 100, 4, 5, 2500, 40000),
(24, 'FC Porto', 100, 4, 6, 2500, 40000),
(25, 'Ultimate Team 1', 100, 4, 5, 250000, 500000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `nama_tim` varchar(45) NOT NULL,
  `nama_stadion` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `balen` int(10) unsigned DEFAULT NULL,
  `uang` int(10) unsigned DEFAULT NULL,
  `exp` int(10) unsigned DEFAULT NULL,
  `kekompakan_lastset_tu` int(10) unsigned DEFAULT NULL,
  `kekompakan_lastset_value` tinyint(3) unsigned DEFAULT NULL,
  `ap_lastset_tu` int(10) unsigned DEFAULT NULL,
  `ap_lastset_value` tinyint(3) unsigned DEFAULT NULL,
  `biaya_perawatan_lastset_tu` int(10) unsigned DEFAULT NULL,
  `foto_user` varchar(45) DEFAULT NULL,
  `formasi_id_formasi` int(11) NOT NULL,
  `liga_id_liga` int(11) NOT NULL,
  `latihan_id_latihan` int(11) DEFAULT NULL,
  `stadion_id_stadion` int(11) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `fk_user_formasi1_idx` (`formasi_id_formasi`),
  KEY `fk_user_liga1_idx` (`liga_id_liga`),
  KEY `fk_user_latihan1_idx` (`latihan_id_latihan`),
  KEY `fk_user_stadion1_idx` (`stadion_id_stadion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `nama_tim`, `nama_stadion`, `email`, `balen`, `uang`, `exp`, `kekompakan_lastset_tu`, `kekompakan_lastset_value`, `ap_lastset_tu`, `ap_lastset_value`, `biaya_perawatan_lastset_tu`, `foto_user`, `formasi_id_formasi`, `liga_id_liga`, `latihan_id_latihan`, `stadion_id_stadion`) VALUES
('ali', '0cc175b9c0f1b6a831c399e269772661', 'Ali', 'Ali', 'ali.ariff12@gmail.com', 55, 13232129, 54675, 70026, 90, 70201, 14, 69277, '175962acd63d10e505208e1e4e1b37efc399d818.jpeg', 6, 4, NULL, 4),
('baskara', '4729d324bfdc580fd929db152d911dc5', 'Wesbiyasakalah FC', 'Cipo', 'baskara@asd.jp', 25, 50101, 5596, 70231, 97, 70231, 14, 70025, NULL, 3, 3, 5, 2),
('bustan', '5bf84df6db8a0ae7f17f7a8970fea67f', 'Team', 'Stadion', 'bustan@bus.cmo', 0, 8000, 0, 70176, 100, 70176, 15, NULL, NULL, 1, 1, NULL, 1),
('imamkd', '85064efb60a9601805dcea56ec5402f7', 'Team', 'Stadion', 'imam@its.ac.id', 0, 258280, 2014, 70116, 84, 70119, 5, NULL, NULL, 1, 3, NULL, 1),
('joko', '0cc175b9c0f1b6a831c399e269772661', 'Team', 'Stadion', 'joko@asd.com', 0, 55000, 0, 69281, 100, 69281, 15, NULL, NULL, 1, 1, NULL, 1),
('test', '7815696ecbf1c96e6894b779456d330e', 'Team', 'Stadion', 'test@gmail.com', 0, 7616, 2248, 48011, 100, 69016, 14, 67255, NULL, 6, 1, NULL, 1),
('wawang', '1c804224f3ae87f37fced1a5eb9bee37', 'Team', 'Stadion', 'wawang@qq', 0, 31398, 971, 70182, 91, 70184, 8, NULL, NULL, 1, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_pemain`
--

CREATE TABLE IF NOT EXISTS `user_pemain` (
  `id_user_pemain` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(45) NOT NULL,
  `pemain_id_pemain` int(11) NOT NULL,
  `aktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_user_pemain`),
  KEY `fk_user_pemain_user1_idx` (`user_username`),
  KEY `fk_user_pemain_pemain1_idx` (`pemain_id_pemain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=122 ;

--
-- Dumping data for table `user_pemain`
--

INSERT INTO `user_pemain` (`id_user_pemain`, `user_username`, `pemain_id_pemain`, `aktif`) VALUES
(7, 'ali', 402, 11),
(8, 'ali', 386, 0),
(9, 'ali', 58, 8),
(10, 'ali', 30, 1),
(11, 'ali', 528, 0),
(12, 'ali', 416, 0),
(13, 'ali', 308, 6),
(14, 'ali', 526, 10),
(15, 'ali', 39, 2),
(16, 'ali', 181, 9),
(17, 'ali', 81, 0),
(18, 'ali', 5, 0),
(19, 'ali', 211, 7),
(20, 'ali', 145, 5),
(21, 'ali', 15, 3),
(22, 'ali', 199, 0),
(23, 'ali', 239, 0),
(24, 'ali', 185, 0),
(25, 'ali', 21, 4),
(26, 'ali', 183, 0),
(43, 'baskara', 284, 7),
(44, 'imamkd', 535, 11),
(45, 'imamkd', 523, 7),
(46, 'imamkd', 465, 0),
(47, 'imamkd', 517, 10),
(48, 'imamkd', 575, 3),
(49, 'imamkd', 505, 0),
(50, 'imamkd', 495, 4),
(51, 'imamkd', 471, 5),
(52, 'imamkd', 521, 6),
(53, 'imamkd', 563, 1),
(54, 'imamkd', 549, 0),
(55, 'imamkd', 559, 9),
(56, 'imamkd', 503, 2),
(57, 'imamkd', 501, 0),
(58, 'imamkd', 475, 8),
(63, 'bustan', 437, 1),
(65, 'bustan', 373, 0),
(66, 'bustan', 425, 2),
(68, 'bustan', 529, 0),
(70, 'bustan', 479, 6),
(71, 'bustan', 533, 3),
(72, 'bustan', 511, 0),
(73, 'bustan', 549, 5),
(76, 'bustan', 499, 11),
(77, 'wawang', 573, 11),
(79, 'wawang', 505, 6),
(81, 'wawang', 517, 0),
(82, 'wawang', 483, 0),
(83, 'wawang', 551, 0),
(84, 'wawang', 575, 5),
(85, 'wawang', 553, 10),
(86, 'wawang', 557, 0),
(87, 'wawang', 499, 0),
(88, 'wawang', 525, 8),
(89, 'wawang', 515, 0),
(90, 'wawang', 475, 9),
(91, 'wawang', 491, 0),
(92, 'wawang', 463, 7),
(93, 'wawang', 455, 0),
(94, 'wawang', 513, 0),
(95, 'wawang', 539, 0),
(96, 'wawang', 521, 0),
(97, 'wawang', 511, 0),
(98, 'wawang', 567, 3),
(99, 'wawang', 533, 4),
(100, 'wawang', 519, 1),
(101, 'wawang', 569, 2),
(102, 'baskara', 19, 4),
(106, 'baskara', 165, 0),
(107, 'baskara', 135, 3),
(110, 'baskara', 61, 11),
(111, 'baskara', 229, 6),
(112, 'baskara', 109, 2),
(114, 'baskara', 249, 5),
(115, 'baskara', 247, 0),
(117, 'baskara', 37, 8),
(118, 'baskara', 177, 10),
(119, 'baskara', 101, 1),
(120, 'baskara', 167, 0),
(121, 'baskara', 57, 9);

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE IF NOT EXISTS `voucher` (
  `code_voucher` varchar(45) NOT NULL,
  `harga_voucher` int(11) DEFAULT NULL,
  `penambahan_balen` int(11) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL,
  `kadaluarsa` datetime DEFAULT NULL,
  `user_username` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`code_voucher`),
  KEY `fk_voucher_user1_idx` (`user_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`code_voucher`, `harga_voucher`, `penambahan_balen`, `flag`, `kadaluarsa`, `user_username`) VALUES
('123', 50000, 200, 1, '2014-01-24 00:00:00', 'baskara'),
('911', 50000, 200, 1, '2014-01-31 22:23:15', 'ali');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_user`
--
ALTER TABLE `ai_user`
  ADD CONSTRAINT `fk_ai_user_tim_ai1` FOREIGN KEY (`tim_ai_id_tim_ai`) REFERENCES `tim_ai` (`id_tim_ai`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ai_user_user1` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pemain_tim_ai`
--
ALTER TABLE `pemain_tim_ai`
  ADD CONSTRAINT `fk_pemain_tim_ai_pemain1` FOREIGN KEY (`pemain_id_pemain`) REFERENCES `pemain` (`id_pemain`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pemain_tim_ai_tim_ai1` FOREIGN KEY (`tim_ai_id_tim_ai`) REFERENCES `tim_ai` (`id_tim_ai`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `penawaran`
--
ALTER TABLE `penawaran`
  ADD CONSTRAINT `fk_penawaran_user1` FOREIGN KEY (`pemilik`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_penawaran_user2` FOREIGN KEY (`penawar`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_penawaran_user_pemain1` FOREIGN KEY (`id_user_pemain`) REFERENCES `user_pemain` (`id_user_pemain`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `proteksi_user`
--
ALTER TABLE `proteksi_user`
  ADD CONSTRAINT `fk_proteksi_user_user1` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proteksi_user_user2` FOREIGN KEY (`user_username1`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `rumus_formasi`
--
ALTER TABLE `rumus_formasi`
  ADD CONSTRAINT `fk_rumus_formasi_formasi1` FOREIGN KEY (`formasi_id_formasi`) REFERENCES `formasi` (`id_formasi`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rumus_formasi_formasi2` FOREIGN KEY (`formasi_id_formasi1`) REFERENCES `formasi` (`id_formasi`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tim_ai`
--
ALTER TABLE `tim_ai`
  ADD CONSTRAINT `fk_tim_ai_formasi1` FOREIGN KEY (`formasi_id_formasi`) REFERENCES `formasi` (`id_formasi`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tim_ai_liga1` FOREIGN KEY (`liga_id_liga`) REFERENCES `liga` (`id_liga`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_formasi1` FOREIGN KEY (`formasi_id_formasi`) REFERENCES `formasi` (`id_formasi`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_latihan1` FOREIGN KEY (`latihan_id_latihan`) REFERENCES `latihan` (`id_latihan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_liga1` FOREIGN KEY (`liga_id_liga`) REFERENCES `liga` (`id_liga`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_stadion1` FOREIGN KEY (`stadion_id_stadion`) REFERENCES `stadion` (`id_stadion`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_pemain`
--
ALTER TABLE `user_pemain`
  ADD CONSTRAINT `fk_user_pemain_pemain1` FOREIGN KEY (`pemain_id_pemain`) REFERENCES `pemain` (`id_pemain`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_pemain_user1` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `voucher`
--
ALTER TABLE `voucher`
  ADD CONSTRAINT `fk_voucher_user1` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
