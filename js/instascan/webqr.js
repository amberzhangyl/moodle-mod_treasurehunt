// QRCODE reader Copyright 2011 Lazar Laszlo
// http://www.webqr.com

var gCtx = null;
var gCanvas = null;
var c=0;
var stype=0;
var gUM=false;
var webkit=false;
var moz=false;
var v=null;

var vidhtml = '<video width="320px" id="qr_viewport" autoplay></video>';
function enableTest(Y,successString){
	 var cook = {};
     document.cookie.split(';').forEach(function (x) {
         var arr = x.split('=');
         arr[1] && (cook[arr[0].trim()] = arr[1].trim());
     });
     if (cook["QRScanPassed"] != 'Done') {
		loadQR(function (value){
		document.cookie = "QRScanPassed = Done";
		unloadQR();
		$('#QRStatusDiv').html(successString);	        
		});
     } else {
    	$('#QRStatusDiv').html(successString);
     }
}
function enableForm() {
	$('#id_generateQR')
			.click(
					function() {
						unloadQR();
						var val = $('#id_qrtext').val();
						if (val != '') {
							var qrurl = 'https://chart.googleapis.com/chart?cht=qr&chs=500x500&chl='
									+ $('#id_qrtext').val();
							$('#outdiv').prepend($('<img>', {
								id : 'theQRImg',
								src : qrurl,
								width : '150px',
								align : 'left'
							}))
						}
					});
	$('#id_scanQR').click(function() {
		loadQR(function(value) {
			$('#id_qrtext').val(value);
			unloadQR();
		})
	});
}
	
function error(error) {
    gUM=false;
    return;
}
function unloadQR(){
	scanner.stop().then(function(){
		console.info("camera stopped");
	});
	camera = -1;
	let videopreview = document.getElementById('previewQRvideo');
	videopreview.style.display = 'none';
}
function loadQR(callback)
{
	let videopreview = document.getElementById('previewQRvideo');
	videopreview.style.display = 'inline';
    scanner = new Instascan.Scanner({ video: videopreview });
	scanner.addListener('scan',callback);
	setnextwebcam();
}


function setwebcam()
{
	Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        } else {
          console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });	
}
var camera = -1;
var numcameras = 0;
function setnextwebcam()
{
	var nextcamera = -1;
	if (numcameras > camera +1) {
		nextcamera++;
	} else {
		nextcamera = 0;
	}
	if (camera != nextcamera) {
		scanner.stop().then(function () {
			Instascan.Camera.getCameras().then(function (cameras) {
				numcameras = cameras.length;
		        if (cameras.length > 0) {
		          camera = nextcamera;
		          scanner.start(cameras[camera]);
		        } else {
		          console.error('No cameras found.');
		        }
		      }).catch(function (e) {
		        console.error(e);
		      });	
		});
	}
		
}

