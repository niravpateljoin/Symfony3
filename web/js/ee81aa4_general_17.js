$(document).ready(function () {
	getLocation();
    $('.place').append('<span  class="locateMebtn" onclick="replaceCurrentLocation();">Locate me</span>');
    $('span.locateMebtn').html('<i class="fa fa-map-marker" style="font-size:36px;" title="Get your current location"></i>');
  	loadingSpinner = function(button, loading) {
      $(button).hide();
      $(loading).html("<img src='/uploads/listings/images/Loading.gif'/>");
    }
}); 

/* Added by vishal on date 04/16/2020 for Location Functionality */ 
var allowGeoRecall = true;
function getLocation() {   
    console.log('getLocation was called');
    // alert(document.cookie);
    if(document.cookie){
     

    }else{
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, positionError);
        } else {
            hideLoadingDiv();
            console.log('Geolocation is not supported by this device');
        }
    }
}

function positionError() {    
    console.log('Geolocation is not enabled. Please enable to use this feature');

    // if(allowGeoRecall) getLocation();
}

function showPosition(position){
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    codeLatLng(lat, lng);
    var allowGeoRecall = false;
}

function codeLatLng(lat, lng) {
    var geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

            if (results[1]) {
                //formatted address
                address = results[0].formatted_address;
                //find country name
                for (var i=0; i<results[0].address_components.length; i++) {
                    for (var b=0;b<results[0].address_components[i].types.length;b++) {
                        //there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
                        if (results[0].address_components[i].types[b] == "administrative_area_level_1") {
                            //this is the object you are looking for
                            city= results[0].address_components[i];
                            break;
                        }
                    }
                }
                document.cookie = "location="+results[0].formatted_address+"#"+lat+"#"+lng+";";
            } else {
              // alert("No results found");
            }
        } else {
            // alert("Geocoder failed due to: " + status);
        }
    });
}

function replaceCurrentLocation(){
    var fullAddress = document.cookie.split('=')[1];
    var res = fullAddress.split("#");
    var address = res[0];
    var lat = res[1];
    var long = res[2];
    

    $('#place').val(address);
    $('#place_latitude').val(lat);
    $('#place_longitude').val(long);

} 