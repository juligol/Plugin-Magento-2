define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'jquery/jquery.cookie'
     
], function ($, ko, Component, quote, url, modal, defaultProcessor, customerAddressProcessor, rateRegistry, setShippingInformationAction, stepNavigator) {
    'use strict';

    $(document).ready(function() {
        //Set pickitActivo to 0 to show the Abrir Panel Pickit
        $.cookie("pickitActivo", 0);
    });

    //addEventClose
    window.addEventListener('message', function (event) {
        if (event.data == 'close') {
            var shippingAddress = quote.shippingAddress();
            //Close the Modal
            $('#modal-pickit-map').modal('closeModal');
            //obtenerInformacionPuntoSeleccionado
            var linkUrl = url.build("pickit/ajax/obtenerinformacionpuntoseleccionado/");
            $.ajax({
                method: "POST",
                url: linkUrl,
                showLoader: true,
                data: { cotizacionId: $.cookie("cotizacionId") },
                dataType: "json",
                success: function(data){
                    var Status = data.Status;
                    //Check if is a valid response
                    if(Status.Code == "200" && Status.Text == "OK"){
                        var address = data.Address;
                        //Update Shipping Address
						shippingAddress.telephone  = address.telephone ;
						shippingAddress.countryId  = address.countryId ;
						shippingAddress.region     = address.region    ;
						shippingAddress.regionCode = address.regionCode;
						shippingAddress.regionId   = address.regionId  ;
						shippingAddress.city       = address.city      ;
						shippingAddress.street     = address.street    ;
                        shippingAddress.postcode   = address.postcode  ;
                        //Update table rates
                        var processors = [];
                        rateRegistry.set(shippingAddress.getCacheKey(), null);
                        processors.default = defaultProcessor;
                        processors['customer-address'] = customerAddressProcessor;
                        var type = shippingAddress.getType();
                        if (processors[type]) {
                            processors[type].getRates(shippingAddress);
                        } else {
                           processors.default.getRates(shippingAddress);
                        }
                        //Set pickitActivo to 1 to show the Cambiar Punto Pickit
                        $.cookie("pickitActivo", 1);
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
        }
    }, false);

    return function (Component) {
        return Component.extend({

            initialize: function () {
                this._super();

                var self = this;
                this.verifyPickit = function (shippingMethod) {
                    self.validateFields(shippingMethod);
                };
                
                return this;
            },

            /**
             * Validate shipping information and verify if is pickit
             */
            validateFields: function (shippingMethod) {
                if (this.validateShippingInformation() && shippingMethod['method_code'] == 'pickit') {
                    this.obtenerCotizacionEnviov2();
                }
            },

            /**
             * ObtenerCotizacionEnviov2
             */
            obtenerCotizacionEnviov2: function () {
                var linkUrl = url.build("pickit/ajax/obtenercotizacionenviov2/");
                var self = this;
                $.ajax({
                    method: "POST",
                    url: linkUrl,
                    showLoader: true,
                    data: { direccionCliente: quote.shippingAddress().street},
                    dataType: "json",
                    success: function(data){
                        var Status = data.Status;
                        //Check if is a valid response
                        if(self.isValidResponse(Status)){
                            self.openPickitModal(data.Response);
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

            /**
             * Check if the response is valid
             */
            isValidResponse: function (Status) {
                return Status.Code == "200" && Status.Text == "OK";
            },

            /**
             * Open pickit modal
             */
            openPickitModal: function (Response) {
                //Set the url to the iframe
                $('#fancyImg').attr('src', Response.urlLightBox);
                //Modal options
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    //title: "PickIt",
                    buttons: false,
                    opened: function($Event) {
                        $(".modal-header").removeClass("modal-header");
                        $(".modal-content").removeClass("modal-content");
                        $(".modal-inner-wrap").css("width", "740px");
                    }
                };
                //Select the iframe and show it in the modal
                var pickit_modal = $('#modal-pickit-map');
                var popup = modal(options, pickit_modal);
                pickit_modal.css("display", "block");
                pickit_modal.modal('openModal');
                //Save the cotizacionId in a cookie to use it in the event close
                $.cookie("cotizacionId", Response.cotizacionId);
            },

            /**
             * Validate shipping informtion and verify that pickit is actived
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            // If pickit point in selected
                            if(quote.shippingMethod()['method_code'] == 'pickit'){
                                if($.cookie("pickitActivo") == 1){
                                    stepNavigator.next();
                                }else{
                                    alert("Debe seleccionar un punto Pickit para avanzar");
                                }
                            }else{
                                stepNavigator.next();
                            }
                        }
                    );
                }
            }
        });
    }
});