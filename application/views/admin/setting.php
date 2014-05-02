<div class="page-header">
      <h1>Setting <small>Game</small>  </h1>
    </div>
    <div class="alert" style="display: none;" id="pesan">
      <button type="button" class="close" id="dismiss_btn">&times;</button>
      <div class="isi_pesan">
      </div>
    </div>
    <form id="pengaturan_sistem_form">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Nama Pengaturan</th>
          <th>Nilai</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Waktu Mulai Server</td>
          <td>
            <input class="form-control" type="text" name="server_waktu_mulai" value="<?php echo $data->pengaturan_sistem->server_waktu_mulai;?>">
          </td>
        </tr>
        <tr>
          <td>Durasi Server (TU)</td>
          <td>
            <input class="form-control" type="text" name="server_durasi_tu" value="<?php echo $data->pengaturan_sistem->server_durasi_tu;?>">
          </td>
        </tr>
        <tr>
          <td>Durasi AP Pertambahan (TU)</td>
          <td>
            <input class="form-control" type="text" name="ap_durasi_pertambahan_tu" value="<?php echo $data->pengaturan_sistem->ap_durasi_pertambahan_tu;?>">
          </td>
        </tr>
        <tr>
          <td>AP Pertambahan</td>
          <td>
            <input class="form-control" type="text" name="ap_pertambahan" value="<?php echo $data->pengaturan_sistem->ap_pertambahan;?>">
          </td>
        </tr>
        <tr>
          <td>AP Maksimal</td>
          <td>
            <input class="form-control" type="text" name="ap_maksimal" value="<?php echo $data->pengaturan_sistem->ap_maksimal;?>">
          </td>
        </tr>
        <tr>
          <td>AP Nilai Awal</td>
          <td>
            <input class="form-control" type="text" name="ap_nilai_awal" value="<?php echo $data->pengaturan_sistem->ap_nilai_awal;?>">
          </td>
        </tr>
        <tr>
          <td>EXP Nilai Awal</td>
          <td>
            <input class="form-control" type="text" name="exp_nilai_awal" value="<?php echo $data->pengaturan_sistem->exp_nilai_awal;?>">
          </td>
        </tr>
        <tr>
          <td>Kekompakan Nilai Awal</td>
          <td>
            <input class="form-control" type="text" name="kekompakan_nilai_awal" value="<?php echo $data->pengaturan_sistem->kekompakan_nilai_awal;?>">
          </td>
        </tr>
        <tr>
          <td>Kekompakan Maksimal</td>
          <td>
            <input class="form-control" type="text" name="kekompakan_maksimal" value="<?php echo $data->pengaturan_sistem->kekompakan_maksimal;?>">
          </td>
        </tr>
        <tr>
          <td>Durasi Proteksi (TU)</td>
          <td>
            <input class="form-control" type="text" name="proteksi_durasi_tu" value="<?php echo $data->pengaturan_sistem->proteksi_durasi_tu;?>">
          </td>
        </tr>
        <tr>
          <td>Balen Nilai Awal</td>
          <td>
            <input class="form-control" type="text" name="balen_nilai_awal" value="<?php echo $data->pengaturan_sistem->balen_nilai_awal;?>">
          </td>
        </tr>
        <tr>
          <td>Uang Nilai Awal</td>
          <td>
            <input class="form-control" type="text" name="uang_nilai_awal" value="<?php echo $data->pengaturan_sistem->uang_nilai_awal;?>">
          </td>
        </tr>
      </tbody>
    </table>
    <input type="submit" class="btn btn-primary" value="Simpan Pengaturan" />
  </form>

<script type="text/javascript">
  $(document).ready(function() {

    $('#pengaturan_sistem_form').submit(function() {
      $.post('<?php echo base_url('admin/setting')?>', $(this).serialize(), function(data) {
        $('#pesan').fadeOut(500);
        if (data.sukses==true)
        {
          $('#pesan').removeClass('alert-danger').addClass('alert-success');
        }
        else
        {
          $('#pesan').removeClass('alert-success').addClass('alert-danger');
        }
        $('#pesan').html(data.pesan);
        $('#pesan').fadeIn(500);
      }, "json");
      return false;
    });

  });
</script>