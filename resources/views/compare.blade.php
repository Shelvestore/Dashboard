@extends('layouts.master')
@section('content')
    <div class="container-fuild">
        <nav aria-label="breadcrumb">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ trans('lables.bread-crumb-home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ trans('lables.bread-compare') }}</li>
                </ol>
            </div>
        </nav>
    </div>

    <!-- Compare Content -->
    <section class="compare-content pro-content">
        <div class="container">
            <div class="page-heading-title">
                <h2> {{ trans('lables.bread-compare') }}
                </h2>

            </div>
        </div>
        <div class="container">
            <div class="row compare-data-to-show">

            </div>

        </div>
    </section>

@endsection
@section('script')

    <script>
        $(document).ready(function() {
            var url = "{{ url('') }}" + '/api/client/compare';
            fetchCompare(url);
        })

        function fetchCompare(url, appendTo) {
            customerToken = $.trim(localStorage.getItem("customerToken"));

            $.ajax({
                type: 'get',
                url: url,
                headers: {
                    'Authorization': 'Bearer ' + customerToken,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    clientid: "{{ isset(getSetting()['client_id']) ? getSetting()['client_id'] : '' }}",
                    clientsecret: "{{ isset(getSetting()['client_secret']) ? getSetting()['client_secret'] : '' }}",
                },
                beforeSend: function() {},
                success: function(data) {
                    if (data.status == 'Success') {
                        compareDataToShow = '';
                        for (var i = 0; i < data.data.length; i++) {
                            compareDataToShow += '<div class="col-lg-6"><table class="table">';
                            if (data.data[i].gallery != null) {
                                compareDataToShow +=
                                    '<thead><td align="center"><div class="img-div"><img class="img-fluid" src="/gallary/' +
                                    data.data[i].products.gallery.name + '"></div></td></thead>';
                            }
                            if (data.data[i].products.detail != null) {
                                compareDataToShow +=
                                    '<tbody><tr><td><h2>' + data.data[i].products.detail[0].title +
                                    '</h2></td></tr>';
                            }
                            compareDataToShow +=
                                '<tr><td><span>Price&nbsp;:&nbsp;</span><span class="price-tag">' + data.data[i]
                                .products.price + '</span></td></tr>';
                            if (data.data[i].products.product_type == "variable") {
                                if (data.data[i].products.product_attribute != null) {
                                    var attribute = data.data[i].products.product_attribute;
                                    for (var a = 0; a < attribute.length; a++) {

                                        if (attribute[a].attribute != null) {

                                            if (attribute[a].attribute.attribute_detail != null) {

                                                compareDataToShow += '<tr><td><span>'+attribute[a].attribute.attribute_detail[0].name+'&nbsp;:&nbsp;</span>';
                                            }
                                            if (attribute[a].variation != null) {
                                                for (var v = 0; v < attribute[a].variation
                                                    .length; v++) {
                                                    compareDataToShow +=
                                                        '' + attribute[a]
                                                        .variation[v]
                                                        .variation.variation_detail[0].name + ',';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            compareDataToShow += '</td></tr>';


                            compareDataToShow +=
                                '<tr><td><div class="detail-buttons"><a href="product/'+data.data[i].products.id+'" class="btn btn-secondary swipe-to-top">View Details</a><div class="share"><a href="javascript:void(0)">Share &nbsp;<i class="fas fa-share"></i></a> </div></div></td></tr></tbody></table></div>';
                        $('.compare-data-to-show').html(compareDataToShow);

                        }
                        
                        return;
                    }
                    $('.compare-data-to-show').html("no data found");
                },
                error: function(data) {},
            });
        }
    </script>

@endsection
