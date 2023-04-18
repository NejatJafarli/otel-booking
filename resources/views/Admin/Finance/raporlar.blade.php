@extends('layout/master')

@php
    use App\Models\transaction;
    use App\Models\User;
@endphp
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Finans Raporu</h6>
                    </div>
                    <div class="row">
                        <div class="col-12 col-xl-12 stretch-card">
                            <div class="row flex-grow-1">
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-0">Son 7 gün</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 col-md-12 col-xl-5">
                                                    <h3 class="mb-2">
                                                        {{ $data_7_sum }}
                                                        <div class="d-flex align-items-baseline">
                                                            <!--<p class="text-success">
                                                                                <span>+3.3%</span>
                                                                                <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                                                            </p>-->
                                                        </div>
                                                </div>
                                                <div class="col-6 col-md-12 col-xl-7">
                                                    <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-0">Bu Ay</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 col-md-12 col-xl-5">
                                                    <h3 class="mb-2"> 
                                                        {{ $data_30_sum }}

                                                        <div class="d-flex align-items-baseline">
                                                        </div>
                                                </div>
                                                <div class="col-6 col-md-12 col-xl-7">
                                                    <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-0">Geçen ay</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 col-md-12 col-xl-5">
                                                    <h3 class="mb-2">
                                                        <h3 class="mb-2">
                                                            {{ $data_60_sum }}
                                                        </h3>
                                                        <div class="d-flex align-items-baseline">
                                                        </div>
                                                </div>
                                                <div class="col-6 col-md-12 col-xl-7">
                                                    <div id="growthChart" class="mt-md-3 mt-xl-0"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- row -->


                    <div class="row">
                        <div class="col-md-12 stretch-card">

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <h6 class="card-title mb-0">Kazanç Grafiği</h6>
                                    </div>
                                    <div class="table-responsive">

                                        <!-- <div id="Gunluk" style="height: 370px; width: 100%;"></div> -->


                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li onclick="OnClickHaftalik()" class="nav-item">
                                                <a class="nav-link" id="home-tab" data-bs-toggle="tab" href="#haftalikx"
                                                    role="tab" aria-controls="haftalikx"
                                                    aria-selected="true">Haftalık</a>
                                            </li>
                                            <li onclick="OnClickAylik()" class="nav-item">
                                                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                                                    href="#aylikx" role="tab" aria-controls="aylikx"
                                                    aria-selected="false">Aylık</a>
                                            </li>
                                            <li onclick="OnClickLast60Day()" class="nav-item">
                                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#gecenayx"
                                                    role="tab" aria-controls="gecenayx" aria-selected="false">Geçen
                                                    Ay</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="manuel-tab" data-bs-toggle="tab" href="#manuelx"
                                                    role="tab" aria-controls="manuelx" aria-selected="false">Manuel</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content border border-top-0 p-3" id="myTabContent">
                                            <div class="tab-pane fade show" id="haftalikx" role="tabpanel"
                                                aria-labelledby="haftalikx">

                                                <div id="haftalik" style="height: 370px; width: 100%;"></div>
                                            </div>

                                            <div class="tab-pane fade show active" id="aylikx" role="tabpanel"
                                                aria-labelledby="aylikx">
                                                <div id="aylik" style="height: 370px; width: 100%;"></div>
                                            </div>

                                            <div class="tab-pane fade show" id="gecenayx" role="tabpanel"
                                                aria-labelledby="gecenayx">
                                                <div id="gecenay" style="height: 370px; width: 100%;"></div>
                                            </div>
                                            <div class="tab-pane fade show" id="manuelx" role="tabpanel"
                                                aria-labelledby="manuelx">
                                                <div class="form-group">
                                                    <label for="date">Tarih Aralığı</label>
                                                    <div class="d-flex">
                                                        <!-- //start date label -->
                                                        <input type="date" class="form-control" id="startDate"
                                                            name="startDate" placeholder="Tarih Aralığı Seçiniz" />
                                                        <!-- //end date label -->
                                                        <input type="date" class="form-control" id="endDate"
                                                            name="endDate" placeholder="Tarih Aralığı Seçiniz" />
                                                    </div>
                                                    <button type="button" class="btn btn-primary mt-2"
                                                        id="btnSubmitTwoDates">Göster</button>
                                                </div>

                                                <div class="col-md-4 grid-margin stretch-card">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-baseline">
                                                                <h6 class="card-title mb-0">Gelir</h6>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6 col-md-12 col-xl-5">
                                                                    <h3 class="mb-2">
                                                                        <h3 class="mb-2" id="aralikPrice">0 ₺
                                                                        </h3>
                                                                        <div class="d-flex align-items-baseline">
                                                                        </div>
                                                                </div>
                                                                <div class="col-6 col-md-12 col-xl-7">
                                                                    <div id="growthChart" class="mt-md-3 mt-xl-0"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-baseline">
                                                                <h6 class="card-title mb-0">Satış Adeti</h6>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6 col-md-12 col-xl-5">
                                                                    <h3 class="mb-2">
                                                                        <h3 class="mb-2" id="aralikCount">0
                                                                        </h3>
                                                                        <div class="d-flex align-items-baseline">
                                                                        </div>
                                                                </div>
                                                                <div class="col-6 col-md-12 col-xl-7">
                                                                    <div id="growthChart" class="mt-md-3 mt-xl-0"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="aralikli" style="height: 370px; width: 100%;"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- row -->
                </div>
            </div>
        </div>
    </div> <!-- row -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    {{-- i have data name is data and data is assosative array convert to json and i want to show this data in chart --}}
    <script>
        window.onload = function() {
            window.MyData = @json($data_7);
            window.chart2 = new CanvasJS.Chart("haftalik", {
                animationEnabled: true,
                 backgroundColor: "#0c1427",
                 //text color
                 lineColor:"#",
                title: {
                    text: "Satış Grafiği",
                 fontColor:"#ffbc00",
                },
                axisY: {
                    title: "TL Değeri",
                    titleFontSize: 24,
                    valueFormatSting: "#,###,.## ₺",
                    labelFontColor:"#ffbc00",
                    titleFontColor: "#ffbc00" // Set the Y-axis title text color
                },
                data: [{
                    type: "spline",
                    lineColor: "#ffbc00",
                    valueFormatSting: "#,###,.## ₺",
                    dataPoints: MyData.map(function(item) {
                        console.log(item);
                        var amount = isNaN(item.amount) ? 0 : parseFloat(item.amount);
                        amount = amount / 100;
                        // amount is 330000
                        //convert it to 3300.00 
                        let count = item.count;
                        //this is the string you want to display in the tooltip like "6 Adet Satildi Toplam Tutar : 3300.00 ₺"
                        let date = new Date(item.date);

                        let dateString = date.toLocaleDateString("en-GB", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric"
                        });

                        let string = count + " Adet Satildi Toplam Tutar : " + amount + " ₺";
                        //add date to string
                        string = dateString + " => " + string;

                        return {
                            x: date,
                            y: amount,
                            toolTipContent: string
                        };
                    })
                }]
            });
            window.MyData = @json($data_30);
            window.chart3 = new CanvasJS.Chart("aylik", {
                animationEnabled: true,
                 backgroundColor: "#0c1427",
                 //text color
                 lineColor:"#",
                title: {
                    text: "Satış Grafiği",
                 fontColor:"#ffbc00",
                },
                axisY: {
                    title: "TL Değeri",
                    titleFontSize: 24,
                    valueFormatSting: "#,###,.## ₺",
                    labelFontColor:"#ffbc00",
                    titleFontColor: "#ffbc00" // Set the Y-axis title text color
                },
                data: [{
                    type: "spline",
                    lineColor: "#ffbc00",
                    valueFormatSting: "#,###,.## ₺",
                    dataPoints: MyData.map(function(item) {
                        var amount = isNaN(item.amount) ? 0 : parseFloat(item.amount);
                        amount = amount / 100;
                        // amount is 330000
                        //convert it to 3300.00 
                        let count = item.count;
                        //this is the string you want to display in the tooltip like "6 Adet Satildi Toplam Tutar : 3300.00 ₺"
                        let date = new Date(item.date);

                        let dateString = date.toLocaleDateString("en-GB", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric"
                        });

                        let string = count + " Adet Satildi Toplam Tutar : " + amount + " ₺";
                        //add date to string
                        string = dateString + " => " + string;

                        return {
                            x: date,
                            y: amount,
                            toolTipContent: string
                        };
                    })
                }]
            });
            window.chart3.render();
            window.MyData = @json($data_60);
            window.chart4 = new CanvasJS.Chart("gecenay", {
                animationEnabled: true,
                 backgroundColor: "#0c1427",
                 //text color
                 lineColor:"#",
                title: {
                    text: "Satış Grafiği",
                 fontColor:"#ffbc00",
                },
                axisY: {
                    title: "TL Değeri",
                    titleFontSize: 24,
                    valueFormatSting: "#,###,.## ₺",
                    labelFontColor:"#ffbc00",
                    titleFontColor: "#ffbc00" // Set the Y-axis title text color
                },
                data: [{
                    type: "spline",
                    lineColor: "#ffbc00",
                    valueFormatSting: "#,###,.## ₺",
                    dataPoints: MyData.map(function(item) {
                        console.log(item);
                        var amount = isNaN(item.amount) ? 0 : parseFloat(item.amount);
                        amount = amount / 100;
                        // amount is 330000
                        //convert it to 3300.00 
                        let count = item.count;
                        //this is the string you want to display in the tooltip like "6 Adet Satildi Toplam Tutar : 3300.00 ₺"
                        let date = new Date(item.date);

                        let dateString = date.toLocaleDateString("en-GB", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric"
                        });

                        let string = count + " Adet Satildi Toplam Tutar : " + amount + " ₺";
                        //add date to string
                        string = dateString + " => " + string;

                        return {
                            x: date,
                            y: amount,
                            toolTipContent: string
                        };
                    })
                }]
            });
        }

        function OnClickAylik() {
            //check chart3 is rendered or not

            if (window.chart3.rendered) {
                window.chart3.destroy();
            }
            window.chart3.render();
        }

        function OnClickHaftalik() {
            //check chart2 is rendered or not
            if (window.chart2.rendered) {
                window.chart2.destroy();
            }
            window.chart2.render();
        }

        function OnClickLast60Day() {
            //check chart2 is rendered or not
            if (window.chart4.rendered) {
                window.chart4.destroy();
            }
            window.chart4.render();
        }
    </script>
@endsection
