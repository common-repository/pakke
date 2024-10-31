(function( $ ) {
    'use strict';



    $(document).ready(function () {
        jQuery('.pakke-woo-settings-check').parents('table').addClass('cotizacion-table')
        jQuery('.pakke-woo-settings-check').parents('label').addClass('cotizacion-label')
        jQuery('.pakke-woo-settings-check:checked').parents('label').addClass('cotizacion-labelcheck')
        function selectenvio() {
            if(jQuery('#woocommerce_pakke_shipping_tipo').val()=='fijo'){
                jQuery('#woocommerce_pakke_shipping_fijo').parents('tr').css('display','contents');
                jQuery('#woocommerce_pakke_shipping_extra').parents('tr').css('display','none');
            }
            if(jQuery('#woocommerce_pakke_shipping_tipo').val()=='dinamico'){
                jQuery('#woocommerce_pakke_shipping_fijo').parents('tr').css('display','none');
                jQuery('#woocommerce_pakke_shipping_extra').parents('tr').css('display','contents');
            }
        }
        selectenvio();
        jQuery('#woocommerce_pakke_shipping_tipo').change(function () {
            selectenvio()
        })

        function descargar () {
            alert('inicio')
            // $.ajax({
            //     url: ajaxurl,
            //     type: 'post',
            //     data: {
            //         'action':'pakke_descargar',
            //
            //     },
            //     dataType: 'json',
            //     success: function(data){
            //         alert('fin')
            //
            //     }
            // })
        };



        $('#error-guia').css('display','none');
        $('#exito-guia').css('display','none');
        if( $('#plugin_url').length >0 ) {
            $.getJSON($('#plugin_url').val() + "../json/acerca.json", function (data) {

                $('#acerca').html("<p style='margin-top: 20px'>" + data.acerca + "</p>")
            })

            $('#accordionDocumentacion').html('');
            $.getJSON($('#plugin_url').val() + "../json/doc.json", function (data) {
                var cont = 1;
                let htmlContent = '';
                $.each(data, function (index, value) {
                    if( cont <= 9 ) {
                        htmlContent += '<div class="accordion-item">' +
                            '<h2 class="accordion-header" id="flush-heading' + cont + '">' +
                            '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' + cont + '" aria-expanded="false" aria-controls="flush-collapse' + cont + '">' + value.titulo + '</button>' +
                            '</h2>' +
                            '<div id="flush-collapse' + cont + '" class="accordion-collapse collapse" aria-labelledby="flush-heading' + cont + '" data-bs-parent="#accordionDocumentacion">' +
                            '<div class="accordion-body">' + value.contenido + '</div>' +
                            '</div>' +
                            '</div>';

                    }
                    cont++;
                });
                $('#accordionDocumentacion').html(htmlContent);
            });
        }

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                'action':'pakke_connect_get',
                'url':'/Couriers'
            },
            dataType: 'json',
            // async: false,
            success: function(data){
                if(data['error']){
                    $('#woocommerce_pakke_shipping_apistatus').val("ERROR DE CONEXIÓN")
                    $('#woocommerce_pakke_shipping_apistatus').css('color','red')

                }
                else {
                    $('#woocommerce_pakke_shipping_apistatus').val("CORRECTO")
                    $('#woocommerce_pakke_shipping_apistatus').css('color','green')

                }
            }
        });

        $('#pakke-btn-portal').click(function () {
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action':'pakke_connect_portal'

                },
                dataType: 'json',
                // async: false,
                success: function(data){
                    if(data.Url)
                    window.open(data.Url);
                    else
                        alert('Debe autenticarse primero')


                }
            });
        })

       function courier_img(courier_id) {
            var logo="";
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action':'pakke_courier_img',

                },
                async: false,
                dataType: 'json',
                success: function(data2){
                    $.each(data2,function (index,value) {

                        if(value.code==courier_id){

                            logo=value.logo

                        }

                    })

                }
            })
           return logo
        }


        $('#pakke-btn-cotizar').click(function() {
            $('#error-guia').css('display','none')
            $('#exito-guia').css('display','none')
            var $orderid = $('#pakke-order-id').val();
            var $largo=$('#pakke_largo').val();
            var $ancho=$('#pakke_ancho').val();
            var $alto=$('#pakke_alto').val();
            var $peso=$('#pakke_peso').val();

            if($largo && $ancho && $alto && $peso){

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action':'pakke_cotizar_guia',
                    'order_id':$orderid,
                    'largo':$largo,
                    'ancho':$ancho,
                    'alto':$alto,
                    'peso':$peso
                },
                beforeSend: function (response) {
                    $('.load-cotizar').css('display','block');
                },
                complete: function (response) {
                    $('.load-cotizar').css('display','none');
                },
                success: function(data){
                    data=jQuery.parseJSON(data)
                    var cont=0;
                    $('.tabla-cuerpo').empty();
                    $.each(data.Pakke,function (index,value) {
                        var selected=''
                        var check='<div style="width: 20px"></div>'
                        if(cont==0){
                            selected='checked'

                        }
                        if(value.BestOption)
                            check='<img width="20px" src="'+$('#check-icon').val()+'" >'

                        var img=courier_img(value.CourierCode);


                        var html='<tr>'+
                            '<td><input '+selected+'  type="radio" name="curier-select" value="'+value.CourierCode+'%'+value.CourierServiceId+'"></td>'+
                            '<td >'+check+'</td>'+
                            '<td ><img width="100px" src="'+img+'" ></td>'+
                            '<td>'+value.CourierServiceName+'</td>'+
                            '<td>'+value.DeliveryDays+'</td>'+

                            '<td>'+value.EstimatedDeliveryDate+'</td>'+
                            '<td  >$ '+value.TotalPrice+'</td>'+
                            '</tr>';

                        $('.tabla-cuerpo').append(html);
                        $('.tabla-cotizar').css('display','block');
                        $('.medidas-div').css('display','none');



                        cont++
                    })



                },
                error:function (data) {
                    $('#pakke-btn-cotizar').trigger('click')
                }
            });
            }
            else{
                $('#error-guia').html('Llene las medidas del paquete')
                $('#error-guia').css('display','block')
            }
            return false;
        })

        $('#pakke-btn-guia').click(function(){
            $('#error-guia').css('display','none')

            var $orderid=$('#pakke-order-id').val();
            var $largo=$('#pakke_largo').val();
            var $ancho=$('#pakke_ancho').val();
            var $alto=$('#pakke_alto').val();
            var $peso=$('#pakke_peso').val();
            var $courier=$('input[name="curier-select"]:checked').val();
            var $courier_id=$courier.split("%")[0]
            var $service_id=$courier.split("%")[1]
            if($courier){
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action':'pakke_connect_guia',
                    'order_id':$orderid,
                    'largo':$largo,
                    'ancho':$ancho,
                    'alto':$alto,
                    'peso':$peso,
                    'courier_id':$courier_id,
                    'service_id':$service_id
                },
                beforeSend: function (response) {
                    $('.load-guia').css('display','inline-block');
                },
                complete: function (response) {
                    $('.load-guia').css('display','none');
                },
                success: function(data){
                     data=jQuery.parseJSON(data)

                    if(data['error']){

                        $('#error-guia').html(data['error']['message'])
                        $('#error-guia').css('display','block')

                    }
                    else {

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: {
                                'action':'pakke_update_guia',
                                'order_id':$orderid,
                                'fecha_guia':data['CreatedAt'],
                                'no_guia':data['TrackingNumber'],
                                'courier':data['CourierCode'],
                                'servicio':data['CourierServiceId'],


                            },
                            dataType: 'json',
                            success: function(data2){

                                $('#exito-guia').html('Guía generada correctamente')
                                $('#exito-guia').css('display','block')
                                $('.tabla-cotizar').css('display','none');
                                $('.medidas-div').css('display','flex');
                                var d = new Date();
                                var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();

                                var img=courier_img(data['CourierCode']);
                                var html='<tr>'+

                                    '<td><img width="100px" src="'+img+'" ></td>'+
                                    '<td>'+data['CourierService']+'</td>'+
                                    '<td>'+strDate+'</td>'+
                                    '<td  >$ '+data['CoveredAmount']+'</td>'+
                                    '<td><a target="_blank" href="http://trackit.pakke.mx/'+data['TrackingNumber']+'">'+data['TrackingNumber']+'</a></td>'+
                                    '<td><input type="hidden" id="label-'+data['TrackingNumber']+'" value="'+data['Label']+'"><button type="button" class="descargar-guia" onclick="descargar()" id="btn-'+data['TrackingNumber']+'">Descargar</button></td>'+

                                    '</tr>';
                                $('.tabla-cuerpo-guias').append(html);
                            }
                        })

                    }
                },
                error:function (data) {

                    $('#error-guia').html('Error de conexion')
                    $('#error-guia').css('display','block')
                }
            });
            }
            else{
                $('#error-guia').html('Seleccione un Courier')
                $('#error-guia').css('display','block')
            }
            return false;
        })


        $('#pakke-btn-guia-prev').click(function(){
            $('#error-guia').css('display','none')

            var $orderid=$('#pakke-order-id').val();
            var $largo=$('#pakke_largo_prev').val();
            var $ancho=$('#pakke_ancho_prev').val();
            var $alto=$('#pakke_alto_prev').val();
            var $peso=$('#pakke_peso_prev').val();

            var $courier_id=$('#pakke_courierid_prev').val()
            var $service_id=$('#pakke_serviceid_prev').val()
            if($courier_id){
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: {
                        'action':'pakke_connect_guia',
                        'order_id':$orderid,
                        'largo':$largo,
                        'ancho':$ancho,
                        'alto':$alto,
                        'peso':$peso,
                        'courier_id':$courier_id,
                        'service_id':$service_id
                    },
                    beforeSend: function (response) {
                        $('.load-guia').css('display','inline-block');
                    },
                    complete: function (response) {
                        $('.load-guia').css('display','none');
                    },
                    success: function(data){
                        data=jQuery.parseJSON(data)

                        if(data['error']){

                            $('#error-guia').html(data['error']['message'])
                            $('#error-guia').css('display','block')

                        }
                        else {

                            $.ajax({
                                url: ajaxurl,
                                type: 'post',
                                data: {
                                    'action':'pakke_update_guia',
                                    'order_id':$orderid,
                                    'fecha_guia':data['CreatedAt'],
                                    'no_guia':data['TrackingNumber'],
                                    'courier':data['CourierCode'],
                                    'servicio':data['CourierServiceId'],


                                },
                                dataType: 'json',
                                success: function(data2){

                                    $('#exito-guia').html('Guía generada correctamente')
                                    $('#exito-guia').css('display','block')
                                    $('.tabla-cotizar').css('display','none');
                                    $('.medidas-div').css('display','flex');
                                    var d = new Date();
                                    var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
                                    var img=courier_img(data['CourierCode']);
                                    var html='<tr>'+

                                        '<td><img width="100px" src="'+img+'" ></td>'+
                                        '<td>'+data['CourierService']+'</td>'+
                                        '<td>'+strDate+'</td>'+
                                        '<td  >$ '+data['CoveredAmount']+'</td>'+
                                        '<td><a target="_blank" href="http://trackit.pakke.mx/'+data['TrackingNumber']+'">'+data['TrackingNumber']+'</a></td>'+
                                        '<td><input type="hidden" id="label-'+data['TrackingNumber']+'" value="'+data['Label']+'"><button type="button" class="descargar-guia" onclick="descargar()" id="btn-'+data['TrackingNumber']+'">Descargar</button></td>'+

                                        '</tr>';
                                    $('.tabla-cuerpo-guias').append(html);
                                }
                            })

                        }
                    },
                    error:function (data) {

                        $('#error-guia').html('Error de conexion')
                        $('#error-guia').css('display','block')
                    }
                });
            }
            else{
                $('#error-guia').html('Seleccione un Courier')
                $('#error-guia').css('display','block')
            }
            return false;
        })



        $('#pakke-btn-cancelar').click(function () {
            $('.tabla-cotizar').css('display','none');
            $('.medidas-div').css('display','block');
        })

        var $orderid=$('#pakke-order-id').val();
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                'action':'pakke_cargar_guia',
                'order_id':$orderid,

            },
            beforeSend: function (response) {
                $('.load-cotizar').css('display','block');
            },
            complete: function (response) {
                $('.load-cotizar').css('display','none');
            },
            success: function(data){
                data=jQuery.parseJSON(data)

                $('.tabla-cuerpo-guias').empty();
                $.each(data,function (index,value) {

                    var img=courier_img(value.courier);
                    var html='<tr>'+

                        '<td><img width="100px" src="'+img+'" ></td>'+
                        '<td>'+value.servicio+'</td>'+
                        '<td>'+value.fecha_guia+'</td>'+

                        '<td> $'+value.costo+'</td>'+
                        '<td><a target="_blank" href="https://trackit.pakke.mx/'+value.no_guia+'">'+value.no_guia+'</a></td>'+
                        '<td> <input type="hidden" id="label-'+value.no_guia+'" value="'+value.descargar+'"><button type="button" class="descargar-guia" onclick="descargar()" id="btn-'+value.no_guia+'">Descargar</button></td>'+

                        '</tr>';
                    $('.tabla-cuerpo-guias').append(html);

                })



            },
            error:function (data) {
                $('#error-guia').html('Error de conexion')
                $('#error-guia').css('display','block')
            }
        });





    });


})( jQuery );
function downloadPDF(pdf,ng) {
    const linkSource = 'data:application/pdf;base64,'+pdf;
    const downloadLink = document.createElement("a");
    const fileName = "guia_"+ng+".pdf";
    downloadLink.href = linkSource;
    downloadLink.download = fileName;
    downloadLink.click();}
function descargar () {
    var id = this.event.target.id;
   var ng=id.split("-")[1]
    var pdf=jQuery("#label-"+ng).val()
     downloadPDF(pdf,ng)
};
