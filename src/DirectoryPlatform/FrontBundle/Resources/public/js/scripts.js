$(document).ready(function() {
    'use strict';

    /**
     * Loaded
     */
    $('body').addClass('loaded');

    /**
     * Toggle Header Navigation
     */
    $('.header-toggle').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('navigation-open');
    });

    /**
     * Share
     */
    $('.listing-share-wrapper').jsSocials({
        shares: ["twitter", "facebook", "googleplus", "linkedin"]
    });

    /**
     * WYSIWYG
     */
    $('textarea.wysiwyg').summernote({
        popover: {},
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'link']],
            ['para', ['ul', 'ol']]
        ]
    });
    
    /**
     * Listing Detail Gallery
     */
    $('.post-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: {
          enabled: true
        }
    });

    /**
     * Bootstrap Select
     */
    $('select:not(.rate):not(.rating)').selectpicker({
        size: 4,
        iconBase: 'fa',
        tickIcon: 'fa-check'        
    });

    /**
     * Modals
     */
    $('#modal-login').on('show.bs.modal', function(e) {
        var modal = $(this);
        var href = $('a[data-target="#modal-login"]').attr('href');                    

        $.get(href, function(data) {
            var form = $(data).find('form');
            modal.find('.modal-body').html(form);
        });
    });

    $('#modal-register').on('show.bs.modal', function(e) {
        var modal = $(this);
        var href = $('a[data-target="#modal-register"]').attr('href');                    

        $.get(href, function(data) {
            var form = $(data).find('form');
            modal.find('.modal-body').html(form);
            var fosUsersearchInput = $('input#fos_user_registration_form_address');
            var fosUserlatitudeInput = $('input#fos_user_registration_form_latitude');
            var fosUserlongitudeInput = $('input#fos_user_registration_form_longitude');

            if (0 !== fosUsersearchInput.length && 0 !== fosUserlatitudeInput.length && 0 !== fosUserlongitudeInput.length) {
                var autocomplete = new google.maps.places.Autocomplete(fosUsersearchInput[0]);

               google.maps.event.addListener(autocomplete, 'place_changed', function () {            
                    var place = autocomplete.getPlace();

                    if (!place.geometry) {
                        return;
                    }            

                    fosUserlatitudeInput.val(place.geometry.location.lat());
                    fosUserlongitudeInput.val(place.geometry.location.lng());
                });        

                $(fosUsersearchInput).keypress(function(event) {
                    $('.pac-container.pac-logo').css({ "z-index": "9999"});
                    if (13 === event.keyCode) {
                        event.preventDefault();
                    }
                });       
            }
        });
    });

    /**
     * Listing Top Gallery
     */
    var items = $('.listing-gallery .listing-gallery-item');
    var count = 3;    
    
    if (items.length === 1) {
        count = 1
    } else if (items.length === 2) {
        count = 2;
    }    

    $('.listing-gallery').slick({
        infinite: true,
        slidesToShow: count,
        slidesToScroll: 1
    });

    /**
     * Ratings
     */
    $('.rating').each(function() {
        var options = {
            readonly: true,
            theme: 'fontawesome-stars'
        };

        $(this).barrating(options);
    });

    $('.rate').each(function() {
        var options = {
            theme: 'fontawesome-stars'
        };

        $(this).barrating(options);
    });

    /**
     * Custom radio & checkbox
     */
    $('input[type=checkbox]').wrap('<div class="checkbox-wrapper"/>');
    $('.checkbox-wrapper').append('<span class="indicator"></span>');

    $('input[type=radio]').wrap('<div class="radio-wrapper"/>');
    $('.radio-wrapper').append('<span class="indicator"></span>');

    /**
     * Sliders for header search
     */
    if (0 != $('.filter .form-group.price').length) {
        $('.filter .form-group.price').append('<div id="price-slider"></div>');
        var min = $('#price_from').val() ? $('#price_from').val() : 0;
        var max = $('#price_to').val() ? $('#price_to').val() : 300;

        noUiSlider.create(document.getElementById('price-slider'), {
            start: [min, max],
            connect: true,
            step: 1,
            tooltips: [wNumb({ prefix: '$', decimals: 0, thousand: '.' }), wNumb({ prefix: '$', decimals: 0, thousand: '.' })],
            range: {
                'min': 0,
                'max': 300
            }
        });

        document.getElementById('price-slider').noUiSlider.on('update', function(values, handle) {
            $('#price_from').val(values[0]);
            $('#price_to').val(values[1]);
        });
    }

    if (0 != $('.filter .form-group.radius').length) {
        $('.filter .form-group.radius').append('<div id="radius-slider"></div>');
        var val = $('#radius').val() ? $('#radius').val() : 25;
        noUiSlider.create(document.getElementById('radius-slider'), {
            start: val,
            connect: true,
            step: 1,
            tooltips: [wNumb({ decimals: 0 })],
            range: {
                'min': 0,
                'max': 50
            }
        });

        document.getElementById('radius-slider').noUiSlider.on('update', function(values, handle) {
            $('#radius').val(values);
        });
    }

    /**
     * Google Place Search
     */
    var searchInput = $('input#place');
    var latitudeInput = $('input#place_latitude');
    var longitudeInput = $('input#place_longitude');

    if (0 !== searchInput.length && 0 !== latitudeInput.length && 0 !== longitudeInput.length) {
        var autocomplete = new google.maps.places.Autocomplete(searchInput[0]);

       google.maps.event.addListener(autocomplete, 'place_changed', function () {            
            var place = autocomplete.getPlace();

            if (!place.geometry) {
                return;
            }            

            latitudeInput.val(place.geometry.location.lat());
            longitudeInput.val(place.geometry.location.lng());
        });        

        $(searchInput).keypress(function(event) {
            $('.pac-container.pac-logo').css({ "z-index": "9999"});
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        });
        $(searchInput).bind('touchstart',function(event) {
            $('.pac-container.pac-logo').css({ "z-index": "9999"});
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        });       
    }   

    /**
     * Google Place Search
     */
    var fosUsersearchInput = $('input#fos_user_registration_form_address');
    var fosUserlatitudeInput = $('input#fos_user_registration_form_latitude');
    var fosUserlongitudeInput = $('input#fos_user_registration_form_longitude');

    if (0 !== fosUsersearchInput.length && 0 !== fosUserlatitudeInput.length && 0 !== fosUserlongitudeInput.length) {
        var autocomplete = new google.maps.places.Autocomplete(fosUsersearchInput[0]);

       google.maps.event.addListener(autocomplete, 'place_changed', function () {            
            var place = autocomplete.getPlace();

            if (!place.geometry) {
                return;
            }            

            fosUserlatitudeInput.val(place.geometry.location.lat());
            fosUserlongitudeInput.val(place.geometry.location.lng());
        });        

        $(fosUsersearchInput).keypress(function(event) {
            $('.pac-container.pac-logo').css({ "z-index": "9999"});
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        });       
    }  
    
    /**
     * Google Map Single
     */
    var mapItem = $('.google-map-position');
    if (0 !== mapItem.length) {
        var latitude = mapItem.data('latitude');
        var longitude = mapItem.data('longitude');
        var center = new google.maps.LatLng(latitude, longitude);
        var args = {
            zoom: 14,
            scrollwheel: false,
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        if (typeof map_styles !== 'undefined') {
            args['styles'] = map_styles;
        }

        var map = new google.maps.Map(document.getElementById('google-map-object'), args);

        new RichMarker({
            flat: true,
            position: center,
            map: map,
            shadow: 0,
            content: '<div class="marker"><div class="marker-image" style="background-image: url(' + mapItem.data('image') + ');"></div></div>'
        });
    }

    var mapItem = $('.listing-header-map');
    if (0 !== mapItem.length) {
        var latitude = mapItem.data('latitude');
        var longitude = mapItem.data('longitude');
        var center = new google.maps.LatLng(latitude, longitude);

        var map = new google.maps.Map(document.getElementById('listing-header-map'), {
            zoom: 14,
            scrollwheel: false,
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#f49935"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#fad959"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#a1cdfc"},{"saturation":30},{"lightness":49}]}]
        });

        new RichMarker({
            flat: true,
            position: center,
            map: map,
            shadow: 0,
            content: '<div class="marker"><div class="marker-image" style="background-image: url(' + mapItem.data('image') + ');"></div></div>'
        });
    }

    /**
     * Google Map Markers
     */
    var mapItem = $('.google-map-markers');  
    var markers = [];   
    
    if (0 !== mapItem.length) {              
        var bounds = new google.maps.LatLngBounds();
        var data = mapItem.find('> div');

        var args = {
            zoom: 14,
            scrollwheel: false,
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,             
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        if (typeof map_styles !== 'undefined') {
            args['styles'] = map_styles;
        }

        if (mapItem.data('latitude') && mapItem.data('longitude')) {
            args['center'] = new google.maps.LatLng(mapItem.data('latitude'), mapItem.data('longitude'));
        } else {            
            args['center'] = new google.maps.LatLng(47.603138, -122.332302);
        }        

        var map = new google.maps.Map(document.getElementById('google-map-markers'), args);        

        var infobox = new InfoBox({
            content: 'empty',
            disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-160, -330),
            zIndex: null,
            closeBoxURL: '',
            infoBoxClearance: new google.maps.Size(1, 1),
            isHidden: false,
            isOpen: false,
            pane: 'floatPane',
            enableEventPropagation: false
        });

        infobox.addListener('domready', function () {
            $('.infobox-close').on('click', function () {
                infobox.close(map, this);
                infobox.isOpen = false;
            });
        });

        $.each(data, function(i, value) {
            var center = new google.maps.LatLng($(data[i]).data('latitude'), $(data[i]).data('longitude'));            
            var marker = new RichMarker({
                id: $(data[i]).data('id'),
                data: {
                    'id': $(data[i]).data('id'),
                    'name': $(data[i]).data('name'),
                    'image': $(data[i]).data('image'),
                    'address': $(data[i]).data('address'),
                    'link': $(data[i]).data('link'),
                    'price': $(data[i]).data('price')
                },
                flat: true,
                position: center,
                map: map,
                shadow: 0,
                content: '<div class="marker"><div class="marker-image" style="background-image: url(' + $(data[i]).data('image') + ');"></div></div>'
            }); 

            google.maps.event.addListener(marker, 'click', function() {     
                var c = '<div class="infobox"><div class="infobox-close"><i class="fa fa-close"></i></div>';

                c += '<div class="infobox-content">' +
                    '<div class="infobox-image" style="background-image: url(' + marker.data.image + ');"></div>' + 
                    '<h3 class="infobox-title"><a href="' + marker.data.link + '">' + marker.data.name + '</a></h3>'
                
                if (marker.data.price) {
                    c += '<div class="infobox-price">' + marker.data.price + '</div>';
                }                            

                c += '</div><div>';

                if (!infobox.isOpen) {
                    infobox.setContent(c);
                    infobox.open(map, this);
                    infobox.isOpen = true;
                    infobox.markerId = marker.data.id;
                } else {
                    if (infobox.markerId == marker.data.id) {
                        infobox.close(map, this);
                        infobox.isOpen = false;
                    } else {
                        infobox.close(map, this);
                        infobox.isOpen = false;

                        infobox.setContent(c);
                        infobox.open(map, this);
                        infobox.isOpen = true;
                        infobox.markerId = marker.data.id;
                    }
                }
            });

            markers.push(marker); 
            bounds.extend(center)
        });
          
        if (markers.length > 0) {
            map.fitBounds(bounds);  
        }      

        var cluster = [{
            url: '/bundles/front/img/cluster.png',
            textColor: 'white',
            height: 36,
            width: 36
        }];

        var markerClusterer = new MarkerClusterer(map, markers, {styles: cluster});          

        $('#google-map-action-zoom-in').on('click', function(e) {
            e.preventDefault();
            var zoom = map.getZoom();
            map.setZoom(zoom + 1);
        });

        $('#google-map-action-zoom-out').on('click', function(e) {
            e.preventDefault();
            var zoom = map.getZoom();
            map.setZoom(zoom - 1);
        });        
    }

    /**
     * Geolocation
     */
    if ($('#listing_geolocation').length !== 0) {
        $('#listing_geolocation #listing_geolocation').prepend('<div class="google-map-search">Google Map</div>');
        initializeMap($('#listing_geolocation .google-map-search'));
    }

    function initializeMap(el) {
        var map = {};
        var id = el.attr('id');
        var searchInput = $('#listing_geolocation_search');
        var mapCanvas = el;
        var latitude = $('#listing_geolocation_latitude');
        var longitude = $('#listing_geolocation_longitude');
        var latLng = new google.maps.LatLng(54.800685, -4.130859);
        var zoom = 5;

        // If we have saved values, let's set the position and zoom level
        if (latitude.val().length > 0 && longitude.val().length > 0) {
            latLng = new google.maps.LatLng(latitude.val(), longitude.val());
            zoom = 17;
        }

        // Map
        var mapOptions = {
            center: latLng,
            zoom: zoom
        };

        map = new google.maps.Map(mapCanvas[0], mapOptions);

        // Marker
        var markerOptions = {
            map: map,
            draggable: true,
            title: 'Drag to set the exact location'
        };
        var marker = new google.maps.Marker(markerOptions);

        if (latitude.val().length > 0 && longitude.val().length > 0) {
            marker.setPosition(latLng);
        }

        // Search
        var autocomplete = new google.maps.places.Autocomplete(searchInput[0]);
        autocomplete.bindTo('bounds', map);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);

            latitude.val(place.geometry.location.lat());
            longitude.val(place.geometry.location.lng());

            var address = $('#listing_geolocation_search').val();
            $('#listing_address').val(address);

        });

        $(searchInput).keypress(function (event) {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        });

        // Allow marker to be repositioned
        google.maps.event.addListener(marker, 'drag', function () {
            latitude.val(marker.getPosition().lat());
            longitude.val(marker.getPosition().lng());
        });
    }

    /**
     * Collections
     */
    var collection = $('.collection');

    if (collection.length) {
        collection.collection({
            'add_at_the_end': true,
            'fade_in': false,
            'fade_out': false,
            'allow_down': false,
            'allow_up': false,
            'allow_remove': true
        });
    }

    $('#listing_name').focusout( function()  {
        var slug = $('#listing_name').val();

        $('#listing_slug').val(slug);
    });

    if ( $('form div.form-group').hasClass('Product_type') ) {
        
        $('form div.price label.control-label').after('&nbsp;<i class="fa fa-info-circle" style="font-size:18px" title="This is the price amount for the user who will find this product."></i>');
        $('form div.header label.control-label').after('&nbsp;<i class="fa fa-info-circle" style="font-size:18px" title="This header will be shown on the top section."></i>');
        $('form div.geolocation fieldset#listing_geolocation legend').html('Location&nbsp;<i class="fa fa-info-circle" style="font-size:18px" title="Possible location where do you think the product has been lost or found."></i>');
        $('form div.amenities label.control-label').after('&nbsp;<i class="fa fa-info-circle" style="font-size:18px" title="Possible location into area where the product has been lost or found."></i>');
        $('form div.custom_fields fieldset#listing_custom_fields legend').html('Custom Information&nbsp;<i class="fa fa-info-circle" style="font-size:18px" title="Here you can add custom information of product like product color, product cost etc."></i>');
      
    }

    $('.listingPageFilter div.product_type, .listingPageFilter div.keyword, .listingPageFilter div.place, .listingPageFilter div.category').addClass('widthStyle');

    $('#advanceSearchPopup div.product_type, #advanceSearchPopup div.keyword, #advanceSearchPopup div.place, #advanceSearchPopup div.category').addClass('popupFilterWidth');



});
function loginWithMobile(){
    $('.login-mobile-body').html('<div class="error" style="color: red;"></div><form id="frm-login-mobile"><div class="form-group"><label>Number</label><input type="number" id="mobileNumber" class="form-control" placeholder="Enter the 10 digit mobile"></div><input type="button" class="btnMobileVerification btn btn-primary" value="Sent OTP" onclick="sendOTP()"></form>');
    $('#modal-login').modal('hide');
    $('#modal-login-mobile').modal('show');
}

function openLogin(){
    $('#modal-login').modal('show');
}

function openLoginMobile(){
    $('#modal-login-mobile').modal('show');
}

function openLoginMobileOTP(){
    $('#modal-login-mobile-OTP').modal('show');
}

function sendOTP(){
    $('.error').html('').hide();
    var number = $('#mobileNumber').val();
    if( number.length == 10 && number != "" ){
        $.ajax({
            url: 'send-OPT',
            dataType: 'json',
            type: 'POST',
            data: {number: number},
            success : function(){
                $('.login-mobile-OTP-body').html('<div class="error" style="color: red;"></div><form id="frm-login-mobile"><div class="form-group"><label>OTP</label><input type="number" id="OTPNumber" class="form-control" placeholder="Enter your OTP"></div><input type="button" class="btnOTPVerification btn btn-primary" value="Verify OTP" onclick="verifyOTP()"></form>');
                $('#modal-login-mobile').modal('hide');
                $('#modal-login-mobile-OTP').modal('show');
            }
        });
    }else{
       $('.error').html('Please enter valid number!'); 
       $('.error').show();
    }
}

function verifyOTP(){

    $('.error').html('').hide();
    var otp = $('#OTPNumber').val();
    if( otp.length == 6 && otp != "" )
    {
        $.ajax({
            url: 'verify-OPT',
            dataType: 'json',
            type: 'POST',
            data: {otp: otp},
            success : function(result){
                console.log(result);
                if( result.response == "success" )
                {
                    $('.login-mobile-pass-body').html('<div class="error" style="color: red;"></div><div class="form-group"><span style="text-align:center;">You are creating a password for <strong>'+ result.number +'</strong>. This will help you login faster next time.</span></div><form id="frm-login-mobile"><div class="form-group"><div class="errorPassword" style="color: red;"></div><label class="required">Password</label><input type="password" id="mobLoginPassword" class="form-control" placeholder="Password" required="required"></div><div class="form-group"><div class="errorConfirmPassword" style="color: red;"></div><label class="required">Confirm Password</label><input type="password" id="mobLoginConfirmPassword" class="form-control" placeholder="Confirm Password" required="required"></div><input type="button" class="btnCreatePassword btn btn-primary" value="Submit" onclick="createPassword()"></form>');
                    $('#modal-login-mobile-OTP').modal('hide');
                    $('#modal-login-mobile-pass').modal('show');
                }
                else
                {
                    $('.error').html(result.message);
                }
            }
        });
    }
    else
    {
        $('.error').html('Please enter valid OTP!'); 
        $('.error').show();
    }        
}

function createPassword(){
    $('.error').html('').hide();
    $('.errorPassword').html('').hide();
    $('.errorConfirmPassword').html('').hide();

    var password = $("#mobLoginPassword").val();
    var confirmPassword = $("#mobLoginConfirmPassword").val();

    if( password == '' ){
        $('.errorPassword').html('Please enter password.');
        $('.errorPassword').show();
    }
    else if( confirmPassword == '' )
    {
        $('.errorConfirmPassword').html('Please enter confrim password.');
        $('.errorConfirmPassword').show();
    }
    else if ( password != '' && confirmPassword != '' && password !== confirmPassword )
    {
        $('.errorPassword').html('Password and Confirm password is not matched!');
        $('.errorPassword').show();
    }
    else
    {
        $.ajax({
            url: 'create-user',
            dataType: 'json',
            type: 'POST',
            data: {password: password},
            success : function(result){
                if( result.message == "success" )
                {
                    window.location.reload();
                }
            }
        });
    }
}