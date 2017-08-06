@extends('layouts.app')

@section('titlepage')
  Aplikasi Ujian BLKI
@endsection

@section('content')
  <div class="container">
      <div class="page-header">
          <h1>Aplikasi Ujian BLKI<h1>
      </div>
      <div id="verification-panel" class="panel panel-primary">
          <div class="panel-heading">Lengkapi Data Dibawah Ini</div>
          <div class="panel-body">
            <div class="row">
              <div class="col-md-12 div-verification">
                <input id="search-siswa-input" type="text" name="search-siswa" class="form-control" placeholder="Cari Nama Siswa...">
              </div>
            </div>
            <div class="row" style="margin-top:20px">
              <div class="col-md-6 div-verification">
                @if (count($siswas) > 0)
                  <table id="table-result" class="table table-hover">
                    <thead>
                      <tr>
                        <th>Nama</th>
                      </tr>
                    </thead>
                    <tbody id="nama-siswa-body">
                      @foreach ($siswas as $siswa)
                        <tr class="nama-siswa-tr">
                          <td class="nama-siswa-td">{{ $siswa->nama }}</td>
                          <td class="id-siswa-td" hidden>{{ $siswa->id }}</td>
                          <td class="birth-siswa-td" hidden>{{ $siswa->tanggal_lahir }}</td>
                          <td class="kejuruan-siswa-td" hidden>{{ $siswa->kejuruan_id }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @else
                  Tidak Ada Data
                @endif
              </div>
              <div id="div-ujian" class="col-md-6" hidden>
                <label>Pilih Ujian Yang Akan Diujikan</label>
                <table id="table-ujian" class="table table-hover">
                  <thead>
                    <th>Nama Ujian</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                  </thead>
                  <tbody id="body-ujian">
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <div class="form-group div-verification">
                  <label class="control-label" for="tanggal-lahir">Verivikasi Tanggal Lahir</label>
                  <input id="tanggal-lahir-verification" class="form-control" type="date" name="tanggal-lahir">
                </div>
                <button id="button-verification" type="button" class="btn btn-primary div-verification">Cek Validasi</button>
                <label id="label-verification" hidden></label>
              </div>
            </div>
          </div>
          <div class="panel-footer">
              {{-- <a class="btn btn-danger" href=" route('siswa.index') ">Batal</a>
              <input type="submit" class="btn btn-success pull-right"> --}}
              <button class="btn btn-success" type="button" id="click-start" disabled>Mulai</button>
          </div>
      </div>

      <div id="exam-panel" class="panel panel-primary" hidden>
        <div class="panel-heading">
          Ujian Dimulai
        </div>
        <div id="exam-panel-body" class="panel-body">

        </div>
        <div class="panel-footer">
          <button class="btn btn-success" type="button" name="button">Button Kirim</button>
        </div>
      </div>
  </div>
@endsection

@section('js')
  <script type="text/javascript">
    $(document).ready(function(){
      var siswaCurrentId;
      var siswaCurrentBirthDate;
      var ujian;
      var soal;
      var pilihan;

      var ujianSelected;

      var siswa = (function () {
        var siswa = null;
        $.ajax({
          'async': false,
          'global': false,
          'url': 'http://localhost:8000/api/siswa',
          'dataType': "json",
          'success': function (data) {
            siswa = data;
          }
        });
        return siswa;
      })();

      function getUjianSiswa(kejuruan_id) {
        ujian = (function () {
          var ujian = null;
          $.ajax({
            'async': false,
            'global': false,
            'url': 'http://localhost:8000/api/ujian/'+kejuruan_id+'/siswa',
            'dataType': "json",
            'success': function (data) {
              ujian = data;
            }
          });
          return ujian;
        })();
      }

      function getSoalUjian(ujian_id) {
        soal = (function () {
          var soal = null;
          $.ajax({
            'async': false,
            'global': false,
            'url': 'http://localhost:8000/api/soal/'+ujian_id+'/ujian',
            'dataType': "json",
            'success': function (data) {
              soal = data;
            }
          });
          return soal;
        })();
      }

      // function getJawabanSoal(soal_id) {
      //   jawaban = (function () {
      //     var jawaban = null;
      //     $.ajax({
      //       'async': false,
      //       'global': false,
      //       'url': 'http://localhost:8000/api/jawaban/'+soal_id+'/soal',
      //       'dataType': "json",
      //       'success': function (data) {
      //         jawaban = data;
      //       }
      //     });
      //     return jawaban;
      //   })();
      // }

      function getPilihanSoal(soal_id) {
        pilihan = (function () {
          var pilihan = null;
          $.ajax({
            'async': false,
            'global': false,
            'url': 'http://localhost:8000/api/pilihan/'+soal_id+'/soal',
            'dataType': "json",
            'success': function (data) {
              pilihan = data;
            }
          });
          return pilihan;
        })();
      }

      $('#button-verification').click(function() {
        if (siswaCurrentBirthDate == $('#tanggal-lahir-verification').val()) {
          $('#label-verification').show().text('Verifikasi Data Sukses');
          $('.div-verification').hide(1000);
          getUjianSiswa(siswaCurrentMajor);
          $.each(ujian, function(key, val){
            $('#table-ujian > tbody').append('<tr class="tr-ujian"><td class="td-ujian-id" hidden>'+val.id+'</id><td class="td-ujian-deskripsi">'+val.deskripsi+'</td><td class="td-ujian-tanggal-mulai">'+val.tanggal_mulai+'</td><td class="td-ujian-tanggal-selesai">'+val.tanggal_selesai+'</td></tr>');
          });
          $('#div-ujian').show(1000);
          console.log(ujian);
        }
        else {
          $('#label-verification').show().text('Verifikasi Data Gagal');
        }
      })

      $('#nama-siswa-body').on("click", ".nama-siswa-tr", function() {
        $(this).addClass("active");
        siswaCurrentId = $(this).find(".id-siswa-td").text();
        siswaCurrentBirthDate = $(this).find(".birth-siswa-td").text();
        siswaCurrentMajor = $(this).find(".kejuruan-siswa-td").text();
        console.log(siswaCurrentId);
        console.log(siswaCurrentBirthDate);
      });

      $('#body-ujian').on("click", ".tr-ujian", function() {
        $(this).addClass("active");
        ujianSelected = $(this).find(".td-ujian-id").text();
        if ($('#click-start').prop('disabled')) {
          $('#click-start').prop('disabled', false);
        }
        // siswaCurrentId = $(this).find(".id-siswa-td").text();
        // siswaCurrentBirthDate = $(this).find(".birth-siswa-td").text();
        // siswaCurrentMajor = $(this).find(".kejuruan-siswa-td").text();
        // console.log(siswaCurrentId);
        // console.log(siswaCurrentBirthDate);
      });

      function postJawabanSiswa(siswa_id, pilihan_id) {
        // console.log('siswa_id = '+siswa_id);
        // console.log('pilihan_id = '+pilihan_id);
        $.ajax({
          url: 'http://localhost:8000/api/jawaban',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            siswa_id: siswa_id,
            pilihan_id: pilihan_id
          },
          type: "POST",
          dataType: "json",
          success: function(data) {
            console.log(data);
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

      $('#exam-panel-body').on("click", ".pilihan", function() {
        console.log($(this).val());
        console.log($('meta[name="csrf-token"]').attr('content'));
        postJawabanSiswa(siswaCurrentId, $(this).val());
      });

      $('#search-siswa-input').keyup(function(){
        $('#table-result > tbody').empty();
        $.each(siswa, function(key, val){
          // items.push(val);
          if (val.nama.toLowerCase().indexOf($('#search-siswa-input').val().toLowerCase()) >= 0) {
            $('#table-result > tbody').append('<tr class="nama-siswa-tr"><td class="nama-siswa-td">'+val.nama+'</td><td class="id-siswa-td" hidden>'+val.id+'</td><td class="kejuruan-siswa-td" hidden>'+val.kejuruan_id+'</td><td class="birth-siswa-td" hidden>'+val.tanggal_lahir+'</td></tr>');
            console.log(val.nama);
          }
        });
      });

      $('#click-start').click(function() {
        $('#verification-panel').hide(1000, startExam(ujianSelected));
      });

      $('#exam-panel-body .panel .panel-body').on('')

      function startExam(id) {
        console.log(id);
        $('#verification-panel').hide(1000);
        $('#exam-panel').show(500);
        getSoalUjian(id);
        console.log(soal);
        $.each(soal, function(key, _soal) {
          $('#exam-panel-body').append(
            '<div id="soal-'+_soal.id+'" class="panel panel-default">\
              <div class="panel-heading">'+_soal.deskripsi+'</div>\
              <div class="panel-body"></div>\
            </div>'
          );
          getPilihanSoal(_soal.id);
          $.each(pilihan, function(key, _pilihan) {
            $("#soal-"+_soal.id+" .panel-body").append(
              '<div class="radio"><label><input type="radio" class="pilihan" value="'+_pilihan.id+'" name="pilihan-soal-'+_soal.id+'">'+_pilihan.deskripsi+'</label></div>'
            );
          });
        });
      }
    });
  </script>
@endsection
