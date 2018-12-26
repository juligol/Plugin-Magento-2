define([
        'jquery',
        'Magento_Ui/js/grid/columns/column',
        'Magento_Ui/js/modal/modal',
        "uiRegistry",
        'mage/url',
        'jquery/jquery.cookie'
    ],
    function ($, Column, modal, registry, url) {
        'use strict';

        return Column.extend({
            defaults: {
                bodyTmpl: 'Doublepoint_Pickit/link',
            },
			
			generateTicket: function (item, data, event) {
                var linkUrl = $("#baseUrl").val() + "admin/pickit/post/imprimir";
                var self = this;
                $.ajax({
                    method: "POST",
                    url: linkUrl,
                    showLoader: true,
                    data: {item: item},
                    dataType: "json",
                    success: function(data){
                        var Status = data.Status;
                        //Check if is a valid response
                        if(self.isValidResponse(Status)){
                            //Set the url to the iframe
                            self.setUrlToIframe(data.Response, item);
                            //Generate the modal with pickit data
                            self.generateModal(item);
                            //Refresh the table
                            self.refreshTable();
                        }else{
                            //Show the message of the bad request
                            alert(Status.Text);
                        }
                    },
                    error: function(data){
                        console.log(data);
                        alert("Hubo un error, por favor vuelva a intentarlo");
                    }
                });
            },

            isValidResponse: function (Status) {
                return Status.Code == "200" && Status.Text == "OK";
            },

            setUrlToIframe: function (Response, item) {
                $('#etiquetaImg' + item.post_id).attr('src', Response.UrlEtiqueta);
            },

            generateModal: function (item) {
                //Modal options
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: "PickIt",
                    buttons: false
                };
                //Select the iframe and show it in the modal
                var pickit_modal = $('#etiquetaDiv' + item.post_id);
                var popup = modal(options, pickit_modal);
                pickit_modal.css("display", "block");
                pickit_modal.modal('openModal');
            },

            refreshTable: function () {
                var gridName = "pickit_post_listing.pickit_post_listing_data_source";
                var params = [];
                var target = registry.get(gridName);
                if (target && typeof target === 'object') {
                    target.set('params.t ', Date.now());
                }
            },

            print: function (item, data, event) {
                var elem = 'etiquetaDivImprimir' + item.post_id;
                var mywindow = window.open('', 'PRINT', 'height=400,width=600');
                mywindow.document.write('<html><body>');
                mywindow.document.write(document.getElementById(elem).innerHTML);
                mywindow.document.write('</body></html>');
                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10*/
                mywindow.print();
                mywindow.close();
                return true;
            },

            imponerTransaccionEnvioV2: function (item, data, event) {
                var linkUrl = $("#baseUrl").val() + "admin/pickit/post/imponerTransaccionEnvioV2";
                var self = this;
                $.ajax({
                    method: "POST",
                    url: linkUrl,
                    showLoader: true,
                    data: {item: item},
                    dataType: "json",
                    success: function(data){
                        var Status = data.Status;
                        //Check if is a valid response
                        if(self.isValidResponse(Status)){
                            //Refresh the table
                            self.refreshTable();
                        }else{
                            //Show the message of the bad request
                            alert(Status.Text);
                        }
                    },
                    error: function(data){
                        console.log(data);
                        alert("Hubo un error, por favor vuelva a intentarlo");
                    }
                });
            },

            getFieldHandler: function (record) {
                return false;
            }
        });
    }
);