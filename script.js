$("#displayInform").click(function(){
  var ICAO=$("#ICAO").val();
  var NOTAM,coordinates,infoString="";
  if(ICAO.length<4){
    alert("Please type correct code");
    $("#ICAO").val('');
  }else{
    $.ajax({
      url: "testSOAP.php",
      method: "POST",
      data:{
        "ICAO":ICAO
      },
      success: function(data){
        NOTAM=JSON.parse(data);
        for(var key in NOTAM){
          infoString+=key+" : "+NOTAM[key].ITEME+"<br><br>";
        }
        console.log(NOTAM);
        if(NOTAM.length==0){
          alert("Wrong ICAO");
        }else{
          $.ajax({
            url: "https://maps.googleapis.com/maps/api/geocode/json?address="+ICAO+"&key=AIzaSyCPAJi__QpLzZh-lf-5LfFV-7drBpOQ8EA",
            method: "GET",
            success: function(data){
              console.log(data);
              coordinates=data.results[0].geometry.location;
              initMap(coordinates,infoString);
            },
            async:false
          })
        }
      }
    })
  }
})

function initMap(coordinates,inform) {
  var uluru = coordinates;
  var inform = inform;
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom:11,
    center: uluru
  });

  var marker = new google.maps.Marker({
    position: uluru,
    map: map,
    title: 'Display info'
  });

  var infowindow = new google.maps.InfoWindow({
    content: inform
  });

  marker.addListener('click', function() {
    infowindow.open(map, marker);
  });
}