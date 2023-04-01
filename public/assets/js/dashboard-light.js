$(function () {
    'use strict'

    /*
     function aPexChartGibi(tarih = null) {
         var dataPoints = [];
         var chart = new CanvasJS.Chart(tarih, {
             animationEnabled: true,
             theme: "light2",
             zoomEnabled: true,
             title: {
                 text: "Kazanç Grafiği"
             },
             axisY: {
                 title: "TL Değeri",
                 titleFontSize: 24,
                 valueFormatString: "#','#0 ₺",
             },
             data: [{
    
                 type: "area",
                 yValueFormatString: "#','#0 ₺",
                 dataPoints: dataPoints
             }]
         });
    
         function addData(data) {
             var dps = data.price_list;
             for (var i = 0; i < dps.length; i++) {
                 dataPoints.push({
                     x: new Date(dps[i][0]),
                     y: dps[i][1]
                 });
             }
             //console.log(dataPoints);
             chart.render();
         }
         var urlhafta = '/panel/chartapi/'+tarih;
         $.getJSON(urlhafta, addData);
     }
    
        aPexChartGibi('haftalik');
        aPexChartGibi('aylik');
        aPexChartGibi('gecenay');
    */
    var colors = {
        primary: "#6571ff",
        secondary: "#7987a1",
        success: "#05a34a",
        info: "#66d1d1",
        warning: "#fbbc06",
        danger: "#ff3366",
        light: "#e9ecef",
        dark: "#060c17",
        muted: "#7987a1",
        gridBorder: "rgba(77, 138, 240, .15)",
        bodyColor: "#000",
        cardBg: "#fff"
    }

    var fontFamily = "'Roboto', Helvetica, sans-serif"



    // Date Picker
    if ($('#dashboardDate').length) {
        flatpickr("#dashboardDate", {
            wrap: true,
            dateFormat: "d-M-Y",
            defaultDate: "today",
        });
    }
    // Date Picker - END

    function bildirimPop(title, url, sonu) {
        Swal.fire({
            title: "Hi " + response.name,
            text: response.success,
            showConfirmButton: false,
            type: "success"
        });

    }

   
    $("a#ScooterAyar").on("click", function (e) {
        e.preventDefault();
        var thisayar = $(this);


        var settings = {
            "url": ScApiUrl,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({
                "token": "209bf41b28c57d6c20ad307cd2eac8c8",
                "data": {
                    "imei": thisayar.data('imei'),
                    "command": thisayar.data('method')
                }
            }),
        };

        $.ajax(settings).done(function (response) {
            Swal.fire({
                title: response.imei,
                html: '<table class="table"> <thead> <tr> <th>Anlık Şarj</th> <th>Hız</th> <th>Anlık Hız</th> <th>Şarj Durumu</th> <th>Şarj 1 Durum</th> <th>Şarj 2 Durum</th> <th>Durumu</th> <th>Sinyal</th> </tr> </thead> <tbody> <tr><td>' + response.CurrentPower + '</td> <td>' + response.SpeedMode + '</td> <td>' + response.CurrentSpeed + '</td> <td>' + response.ChargingStatus + '</td> <td>' + response.BateryOneStatus + '</td> <td>' + response.BateryTwoStatus + '</td> <td>' + response.ScooterStatus + '</td> <td>' + response.signal + ' </td> </tr></tbody></table>',
                showConfirmButton: false,
                type: "success"
            });
        });

    });


    $('span#gerikalan').each(function () {
        var yazdir = $(this);
        var countDownDatex = $(this).data('kalan');

        var countDownDate = Date.parse(new Date(countDownDatex));

        var x = setInterval(function () {

            var now = Date.parse(new Date());

            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display the result in the element with id="demo"
            $(yazdir).text('' + parseInt(hours, 10) + ":" + parseInt(minutes, 10) + ":" + parseInt(seconds, 10));

            if (distance < 1) {
                clearInterval(x);
                $(yazdir).text("Süresi Doldu");
            }
        }, 1000);

    });

});