var pc = {};
var dataChannel = {};
var i_am_the_caller = false;

$(function() {
	localVideo = document.querySelector('#video_me');
	remoteVideo = document.querySelector('#video_remote');
	ringer = document.querySelector('#ringer');
	username_to = false;
	//init_peerConnection();
	if(auto_call_id !== false){
		make_the_call_to(auto_call_id, true);
	}
})


var pc_config = {'iceServers': [
	{'url': 'stun:stun.l.google.com:19302'}, 
	{'url': 'turn:danimdy%40gmail.com@numb.viagenie.ca:3478', 'credential' : 'cokeDizertatie'},
	{'url': 'turn:23.251.129.26:3478?transport=udp', 'username' : '1402850643:41784574', 'credential' : '3NeaUqbedPMCrFY+ISKAFhZu2q8='}
]};

//var pc_constraints = {'optional': [{'DtlsSrtpKeyAgreement': true}]};
var pc_constraints = {'optional': [{'RtpDataChannels': true}]};

init_peerConnection = function(){
	if(username_to !== false && !pc[username_to]){
		pc[username_to] = new RTCPeerConnection(pc_config, pc_constraints);
		console.log("peer i_am_the_caller: " + i_am_the_caller);
		if(!i_am_the_caller) {
			try{
				dataChannel[username_to] = pc[username_to].createDataChannel("sendDataChannel",{reliable: false});
				dataChannel[username_to].onopen = handleSendChannelStateChange;
				dataChannel[username_to].onclose = handleSendChannelStateChange;
				dataChannel[username_to].onmessage = handleMessage;
				console.log("dataChannel created");
			} catch(e){
				console.log(e);
			}
		}
		pc[username_to].onicecandidate = handleIceCandidate;
		pc[username_to].onaddstream = handleRemoteStreamAdded;
		pc[username_to].onremovestream = handleRemoteStreamRemoved;
		if(i_am_the_caller) {
			pc[username_to].ondatachannel = gotReceiveChannel;
		}
	}
}

//data chanel
function gotReceiveChannel(event) {
	console.log('Receive Channel Callback');
	dataChannel[username_to] = event.channel;
	dataChannel[username_to].onmessage = handleMessage;
	dataChannel[username_to].onopen = handleReceiveChannelStateChange;
	dataChannel[username_to].onclose = handleReceiveChannelStateChange;
}

function handleSendChannelStateChange(){
	console.log("handleSendChannelStateChange");
}
function handleReceiveChannelStateChange(){
	console.log("handleReceiveChannelStateChange");
}

function webrtc_send_file(){
	console.log("webrtc_send_file show modal:");
	$('#modalFileUpload').modal('show');
}


var arrayToStoreChunks = [];
function handleMessage(event) {
	console.log("handleMessage");
	console.log(event);
	var data = JSON.parse(event.data);
	if(data.first) {
		arrayToStoreChunks = [];
	}
	console.log(data);

    arrayToStoreChunks.push(data.message); // pushing chunks in array

	console.log(arrayToStoreChunks);
    if (data.last) {
        saveToDisk(arrayToStoreChunks.join(''), data.filename);
        arrayToStoreChunks = []; // resetting array
    }
	console.log(arrayToStoreChunks);
}

var chunkLength = 1000;
var intervalms = 500;
var filename_send = false;
document.querySelector('input[type=file]').onchange = function() {
    var file = this.files[0];
	filename_send = file.name;
	var reader = new window.FileReader();
	reader.readAsDataURL(file);
	reader.onload = onReadAsDataURL;
};

chunks_send = 0;
var timeout_send = false;
function onReadAsDataURL(event, text) {
    if(timeout_send) clearTimeout(timeout_send);
	var data = {}; // data object to transmit over data channel

    if (event) {
		text = event.target.result; // on first invocation
		chunks_send = 0;
		chunks_total = Math.ceil(text.length/chunkLength);
		$('.total_progress').html(chunks_total);
		$('.current_progress').html(chunks_send);
		data.first = true;
	}

    if (text.length > chunkLength) {
        data.message = text.slice(0, chunkLength); // getting chunk using predefined chunk length
    } else {
        data.message = text;
        data.last = true;
		data.filename = filename_send;
    }

	try {
	//todo for sa trimita la toti.
		for(i in dataChannel)
			dataChannel[i].send(JSON.stringify(data)); // use JSON.stringify for chrome!
	} catch(e){
		console.log("ERROR",e);
		setTimeout(function () {
			onReadAsDataURL(null, text); // continue transmitting
		}, intervalms)
		return;
	}

    var remainingDataURL = text.slice(data.message.length);
	chunks_send++;
	$('.current_progress').html(chunks_send);
    if (remainingDataURL.length) {
		timeout_send = setTimeout(function () {
			onReadAsDataURL(null, remainingDataURL); // continue transmitting
		}, intervalms)
	}
	console.log("onReadAsDataURL reamining length: " + data.message.length);
}
function saveToDisk(fileUrl, fileName) {
    console.log("saveToDisk", fileUrl, fileName);
	if(!settings.auto_save) {
		var n = noty({
			layout: 'top',
			type: 'success',
			text: '<a id="a_save">Click here to download the file you receive (' + fileName + ')</a>',
			timeout: false,
			killer: true
		});
		save = document.querySelector('#a_save');
	} else {
		save = document.createElement('a');
	}
    save.href = fileUrl;
    save.target = '_blank';
    save.download = fileName;

	if(settings.auto_save) {
		var event = document.createEvent('Event');
		event.initEvent('click', true, true);

		save.dispatchEvent(event);
		(window.URL || window.webkitURL).revokeObjectURL(save.href);
	}
}
//end data chanel

send_message = function(to_username, message){
	console.log("send_message_to: "  + to_username + " " + message.type)
	//console.log(message);
	socket.emit('send_message_to', to_username, message);
}

socket.on('message', function(message) {
	console.log("MESSAGE receive: " + message.type );
	//console.log(message);
	if(message.type === 'call'){
		username_to = message.user._id;
		if(settings.auto_answer) { 
			webrtc_start(false);
		} else {
			$('#modalCall .call_from_user-avatar').attr('src', message.user.avatar); 
			$('#modalCall .call_from_user-name').html(message.user.name); 
			$('#modalCall').modal('show');
			ringer.play()
		}
	} else if (message.type === 'offer') {
		$('#remote_name').val()
		pc[username_to].setRemoteDescription(new RTCSessionDescription(message));
		pc[username_to].createAnswer(setLocalAndSendMessage, null, sdpConstraints);
	} else if (message.type === 'answer') {
		pc[username_to].setRemoteDescription(new RTCSessionDescription(message));
	} else if (message.type === 'candidate') {
		var candidate = new RTCIceCandidate({
			sdpMLineIndex: message.label,
			candidate: message.candidate
		});
		//console.log(candidate);
		pc[username_to].addIceCandidate(candidate);
	} else if (message.type === 'hangup') {
		stop();
	} else if(message.type === 'chat_message'){
		chat_add_new_message(message);
	} else if(message.type === 'offer_to_somebody'){
		var username_to_call = message.username;
		setTimeout(function(){ make_the_call_to(username_to_call, true);}, 2500);
	}
})

//WebRTC
var constraints = {video: true, audio: true};
make_the_call_to = function(username, force){
	if($('[data-user-id='+username+'].online').length > 0 || force) {
		username_to = username;
		if(localStream === false) {	//luam si streamul local.
			webrtc_start(true);
		} else {	// avem deja streamul local
			i_am_the_caller = true;
			webrtc_call(true);
		}
	} else {
		$("#modalNotOnline").modal('show');
	}
}
webrtc_start = function(here_i_init_the_call){
	ringer.pause() //in caz ca era pornit.
	$("#video_over_all").show();
	i_am_the_caller = here_i_init_the_call
	console.log('Getting user media with constraints', constraints);
	getUserMedia(constraints, handleUserMedia, handleUserMediaError);
	//console.log("2");
}

var localStream = false;
function handleUserMedia(stream) {
	console.log('Adding local stream.');
	console.log(localVideo);
	localVideo.src = window.URL.createObjectURL(stream);
	localVideo.play();
	
	localStream = stream;
	
	webrtc_call();
}

function handleUserMediaError(error){
  console.log('getUserMedia error: ', error);
}

// Set up audio and video regardless of what devices are present.
//*
var sdpConstraints = {'mandatory': {
	'OfferToReceiveAudio':true,
	'OfferToReceiveVideo':true 
}};
/*/
var sdpConstraints = {}
/**/
webrtc_call = function(){
	init_peerConnection();
	pc[username_to].addStream(localStream);
	
	if(i_am_the_caller) 
		send_message(username_to, {type: 'call', 'user' : me});
	else
		pc[username_to].createOffer(setLocalAndSendMessage, handleCreateOfferError, sdpConstraints);
}

function handleIceCandidate(event) {
	//console.log('handleIceCandidate event: ', event);
	if (event.candidate) {
		send_message(username_to, {
			type: 'candidate',
			label: event.candidate.sdpMLineIndex,
			id: event.candidate.sdpMid,
			candidate: event.candidate.candidate
		});
	} else {
		console.log('End of candidates.');
	}
}

function handleRemoteStreamAdded(event) {
	console.log('Remote stream added.');
	if(remoteVideo.username_to && remoteVideo.username_to != username_to) {
		//make a new video element.
		var new_id = "video_" + parseInt(Math.random()*1000);
		var video_remote_new = $("<video autoplay></video>").attr('id', new_id).addClass('video_remote');
		$("#video_remote").after(video_remote_new);
		
		remoteVideo = document.querySelector('#' + new_id);		
	} 
	remoteVideo.username_to = username_to
	remoteVideo.src = window.URL.createObjectURL(event.stream);
	remoteVideo.play();
	
	$("#video_over_all").attr('nr_video_remote', $("#video_over_all .video_remote").length);
	
	//cand am primit video daca e de la add atunci zicem si la restu sa-l sune.

	if(i_am_adding_new_user == username_to){
		mstime = 5000;
		for(i in pc){
			if(i != username_to){
				send_message_later(i,username_to, mstime);
				mstime+=5000;
			}
		}
		i_am_adding_new_user = false;
	}
	
	//remoteStream = event.stream;
}
function send_message_later(uid_to_send, uid_new, mstime){
	setTimeout(function(){
		console.log("send_message offer_to_somebody" , uid_to_send, uid_new);
		send_message(uid_to_send, {'type' : 'offer_to_somebody', 'username' : uid_new}); 
	}, mstime);
}

function setLocalAndSendMessage(sessionDescription) {
	// Set Opus as the preferred codec in SDP if Opus is present.
	//sessionDescription.sdp = preferOpus(sessionDescription.sdp);
	pc[username_to].setLocalDescription(sessionDescription);
	//console.log('setLocalAndSendMessage sending message' , sessionDescription);
	send_message(username_to, sessionDescription);
}

function handleCreateOfferError(event){
	console.log('createOffer() error: ', e);
}



function handleRemoteStreamRemoved(event) {
	console.log('Remote stream removed. Event: ', event);
}

function hangup() {
	ringer.pause()
	console.log('Hanging up.');
	for(i in pc) {
		send_message(i, {type: 'hangup'});
	}
	stop();
}

function stop() {
	for(i in pc){ 
		pc[i].close();
	}
	pc = {};
	localVideo.src="";
	remoteVideo.src="";
	$("#video_over_all").hide();
	$("#modalFileUpload").modal("hide");
	$('#video_over_all .video_messages ul').html('');
	$.noty.closeAll()
}

window.onbeforeunload = function(e){
	hangup();
}

/*

if (location.hostname != "localhost") {
  requestTurn('https://computeengineondemand.appspot.com/turn?username=41784574&key=4080218913');
}

function requestTurn(turn_url) {
  var turnExists = false;
  for (var i in pc_config.iceServers) {
    if (pc_config.iceServers[i].url.substr(0, 5) === 'turn:') {
      turnExists = true;
      turnReady = true;
      break;
    }
  }
  if (!turnExists) {
    console.log('Getting TURN server from ', turn_url);
    // No TURN server. Get one from computeengineondemand.appspot.com:
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
      if (xhr.readyState === 4 && xhr.status === 200) {
        var turnServer = JSON.parse(xhr.responseText);
      	console.log('Got TURN server: ', turnServer);
        pc_config.iceServers.push({
          'url': 'turn:' + turnServer.username + '@' + turnServer.turn,
          'credential': turnServer.password
        });
        turnReady = true;
      }
    };
    xhr.open('GET', turn_url, true);
    xhr.send();
  }
}

*/
// Set Opus as the default audio codec if it's present.
function preferOpus(sdp) {
  var sdpLines = sdp.split('\r\n');
  var mLineIndex;
  // Search for m line.
  for (var i = 0; i < sdpLines.length; i++) {
      if (sdpLines[i].search('m=audio') !== -1) {
        mLineIndex = i;
        break;
      }
  }
  if (mLineIndex === null) {
    return sdp;
  }

  // If Opus is available, set it as the default in m line.
  for (i = 0; i < sdpLines.length; i++) {
    if (sdpLines[i].search('opus/48000') !== -1) {
      var opusPayload = extractSdp(sdpLines[i], /:(\d+) opus\/48000/i);
      if (opusPayload) {
        sdpLines[mLineIndex] = setDefaultCodec(sdpLines[mLineIndex], opusPayload);
      }
      break;
    }
  }

  // Remove CN in m line and sdp.
  sdpLines = removeCN(sdpLines, mLineIndex);

  sdp = sdpLines.join('\r\n');
  return sdp;
}

function extractSdp(sdpLine, pattern) {
  var result = sdpLine.match(pattern);
  return result && result.length === 2 ? result[1] : null;
}

// Set the selected codec to the first in m line.
function setDefaultCodec(mLine, payload) {
  var elements = mLine.split(' ');
  var newLine = [];
  var index = 0;
  for (var i = 0; i < elements.length; i++) {
    if (index === 3) { // Format of media starts from the fourth.
      newLine[index++] = payload; // Put target payload to the first.
    }
    if (elements[i] !== payload) {
      newLine[index++] = elements[i];
    }
  }
  return newLine.join(' ');
}

// Strip CN from sdp before CN constraints is ready.
function removeCN(sdpLines, mLineIndex) {
  var mLineElements = sdpLines[mLineIndex].split(' ');
  // Scan from end for the convenience of removing an item.
  for (var i = sdpLines.length-1; i >= 0; i--) {
    var payload = extractSdp(sdpLines[i], /a=rtpmap:(\d+) CN\/\d+/i);
    if (payload) {
      var cnPos = mLineElements.indexOf(payload);
      if (cnPos !== -1) {
        // Remove CN payload from m line.
        mLineElements.splice(cnPos, 1);
      }
      // Remove CN line in sdp
      sdpLines.splice(i, 1);
    }
  }

  sdpLines[mLineIndex] = mLineElements.join(' ');
  return sdpLines;
}

//add new users
function add_new_users_click(){
	$("#modalAddUsers").modal('show');
}
i_am_adding_new_user = false;
function add_new_users_by_id(uid){
	i_am_adding_new_user = uid;
	make_the_call_to(uid, true);
	$("#modalAddUsers").modal('hide');
}
//end add  new users
//video chat.
video_show_message_bar = function(){
	$('#video_over_all .video_messages').toggle();
	$('#video_over_all #video_remote').toggleClass('push200');
	$('#video_over_all .btns').toggleClass('push200');
}
$(document).keypress(function(e) {
    if(e.which == 13 && $('#video_over_all .video_messages').is(":visible")) {
        chat_send_new_message();
    }
});
chat_send_new_message = function(){
	var text = $("#new_message").val();
	for(i in pc) {
		send_message(i, {type: 'chat_message', 'user' : me, 'text' : text});
	}
	$("#new_message").val('');
	chat_add_new_message({user:me, text: text}, true);
}
chat_add_new_message = function(obj, is_me){
	console.log(obj);
	var date = new Date();
	var context = { user: obj.user, text:obj.text, timeiso8601:date.toISOString()};
	if(is_me) {
		var html    = handlebar_template_chat_message_me(context);
	} else {
		var html    = handlebar_template_chat_message_other(context);
	}
	
	$('#video_over_all .video_messages ul').append(html);
	jQuery("time.timeago.toinit").removeClass('toinit').timeago();
	if(!$('#video_over_all .video_messages').is(":visible")){
		video_show_message_bar();
	}
	$("ul.chat").scrollTop( 999999 );
}
