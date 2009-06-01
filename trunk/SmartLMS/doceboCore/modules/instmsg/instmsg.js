var serverIMUrl="";
var userid="";
var username="";
var pingTimer;
var _TTim;
var pathImage;
var chat_windows=0;
var im_platform="";

function setup_instmsg(passed_url,passed_userid,passed_username,passed_path,platform) {

	if (platform == null) {
		platform ='lms';
	}

	serverIMUrl=passed_url;
	userid=passed_userid;
	username=passed_username;
	pathImage=passed_path;
	im_platform =platform;
}

function openUsersList() {
	var data="op=getUsersList&im_platform="+im_platform;
	var objAjax = new Ajax.Request(
        	serverIMUrl,
        	{method: 'post', parameters: data, onSuccess: showUsersList, onFailure: server_connection_failure}
    );

}

function server_connection_failure() {

 alert('Si è verificato un errore durante la connessione al servizio');
}

function showUsersList(ObjReq) {
	var listText = ObjReq.responseText; // '{"list":[{"idSt":"8","idUser":"Admin","userName":"Ciccio"}]}';

	var users = listText.evalJSON(true);

	displayUsersList(users.list,true);
}

function displayUsersList(usersList,openwin) {

	$('user_online_n').firstChild.innerHTML=usersList.length;

	var str='<div id="listContainer"><ul id="userList">';

	for (var i=0;i<usersList.length;i++) {
		if (usersList[i].idUser!=userid)
			str+='<li><a href="javascript:;" onclick="startChat(\''+usersList[i].idUser+'\',\''+usersList[i].userName+'\');" class="callOut"><span>'+usersList[i].userName+'</span></a></li>';
		else
			str+='<li><span class="callOutDisabled"><span>'+usersList[i].userName+'</span></a></li>';

	}

	str+='</ul></div>';

	if (wObjList['wUsersList']!=null) {
		var w0=$('wUsersList');
		var el=document.getElementsByClassName("w_content",w0.parentNode);
		el[0].innerHTML=str;
	} else {
		if (openwin) {
			var name="wUsersList";
			var title="";

   		var w=new Window();
			w.top=getTop();
			w.id=name;
			w.width=250;
			w.height=300;
			w.title=title;
			w.content=str;
			w.show();
		}
	}

}

function startChat(id_sender,name_sender) {

	if (id_sender==userid) return 0;

	var wChat=id_sender;

	if (wObjList[wChat]==null) {
		clearInterval(pingTimer);
		pingTimer=setInterval("ping()",5000);
		chat_windows++;
		getChatContent(wChat,name_sender);
	}

}


function getChatContent(wChat,name_sender) {

	var id_receiver=userid;
	var name_receiver=username;
	var id_sender=wChat;


	var data="op=getContent";
	data+="&wChat="+wChat;
	data+="&id_sender="+id_sender;
	data+="&id_receiver="+id_receiver;
	data+="&name_sender="+name_sender;
	data+="&name_receiver="+name_receiver;

	var objAjax = new Ajax.Request(
        	serverIMUrl,
        	{method: 'post', parameters: data, onComplete: showChat}
    );

}

function showChat(ObjReq) {
	var chatText = ObjReq.responseText;

	var chatObj = chatText.evalJSON(true);

	var wChat=chatObj.wChat;
	var name_sender=chatObj.name_sender;
	var msg="";
	str='<div class="chat">';
	str+='<div id="'+wChat+'_text'+'" class="chatText">';

	for (var i=0;i<chatObj.content.length;i++) {
		msg=unescape(chatObj.content[i].msg);
		msg=replaceEmoticon(msg);

		str+="<span class=\"timestamp\"> (" + chatObj.content[i].timestamp + ")</span> <strong class=\""+chatObj.content[i].userClass+"\">" + chatObj.content[i].userName + ":</strong> <span class=\""+chatObj.content[i].lineStatus+"\">" + msg + "</span><br />\n";
	}

	str+='</div>';
	str+='<input type="text" name="'+wChat+'_inputBox'+'" id="'+wChat+'_inputBox'+'" onkeypress="keyHandler(event,'+"'"+wChat+"'"+');" style="width:350px;" /> <button onclick="sendLine(\''+wChat+'\')">'+_TTim._SEND+'</button>';
	str+="</div>";

	str+='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="1" height="1" id="soundeffect" align=""> <param name="movie" value="./modules/instmsg/grilli.swf"> <param name="quality" value="high"> <parame name="bgcolor" value="#FFFFFF"> <embed src="./modules/instmsg/grilli.swf" quality="high" bgcolor="#FFFFFF" name="soundeffect" width="1" height="1" align="" type="application/x-shockwave-flash" pluginspace="http://www.macromedia.com/go/getflashplayer"></embed></object>';

	Stamp = new Date();
	var day = String(Stamp.getDate());
	var month = String(Stamp.getMonth()+1);
	var year = String(Stamp.getFullYear());

	day = (day.length > 1) ? day : "0"+day;
	month = (month.length > 1) ? month : "0"+month;

	var name=wChat;
	var title=_TTim._CHAT+": "+name_sender+" ("+ day + "/" + month + "/" + year+")";

   	var w=new Window();
	w.top=getTop();
	w.id=name;
	w.width=410;
	w.height=350;
	w.title=title;
	w.content=str;
	w.onClose=updateTimers;
	w.show();

	var chatBox=$(wChat+'_text');
	setTimeout("setScroll('"+wChat+"_text')",500);
}

function setScroll(chatDiv) {
	var chatBox=$(chatDiv);
	chatBox.scrollTop = chatBox.scrollHeight - chatBox.clientHeight;
}

function keyHandler( e, wChat ) {
   var asc = document.all ? event.keyCode : e.which;

   if(asc == 13) {
      sendLine(wChat);
   }
   return asc != 13;
}

function sendLine(wChat) {
	var sentText=String($(wChat+'_inputBox').value);
	if (sentText.length < 1) return true;

	$(wChat+'_inputBox').value="";
	 Stamp = new Date();
	 var h = String(Stamp.getHours());
	 var m = String(Stamp.getMinutes());
	 var s = String(Stamp.getSeconds());
	 var day = String(Stamp.getDate());
	 var month = String(Stamp.getMonth()+1);
	 var year = String(Stamp.getFullYear());

	 day = (day.length > 1) ? day : "0"+day;
	 month = (month.length > 1) ? month : "0"+month;
     h = (h.length > 1) ? h : "0"+h;
	 m = (m.length > 1) ? m : "0"+m;
	 s = (s.length > 1) ? s : "0"+s;

	var msg=replaceEmoticon(sentText);
	var chatBox=$(wChat+'_text');
	chatBox.innerHTML = chatBox.innerHTML + "<span class=\"timestamp\"> (" + h + ":" + m + ":" + s + ")</span> <strong class=\"userA\">" + username + ":</strong> <span class=\"new\">" + msg + "</span><br />\n";

    chatBox.scrollTop = chatBox.scrollHeight - chatBox.clientHeight;



	var id_sender=userid;
	var id_receiver=wChat;
	var msg=new String();

	msg=escape(sentText);
	var data="op=sendLine&wChat="+wChat+"&id_sender="+id_sender+"&id_receiver="+id_receiver+"&msg="+msg;
	var objAjax = new Ajax.Request(
        	serverIMUrl,
        	{method: 'post', parameters: data, onComplete: lineSent}
    );

}

function lineSent(ObjReq) {
	return true;
}

function ping() {
	var id_receiver=userid;
	var name_receiver=username;

	var data="op=ping&id_receiver="+id_receiver+"&name_receiver="+name_receiver+"&im_platform="+im_platform;
	var objAjax = new Ajax.Request(
        	serverIMUrl,
        	{method: 'post', parameters: data, onComplete: pingResponse}
    );

}

function pingResponse(ObjReq) {
	var chatMsgsText = ObjReq.responseText;

	var chatMsgs = chatMsgsText.evalJSON(true);

	for (var i=0;i<chatMsgs.content.length;i++) {
		var wChat=chatMsgs.content[i].id_sender;

		if (wObjList[wChat]==null) {
			startChat(chatMsgs.content[i].id_sender,chatMsgs.content[i].name_sender);
			return true;
		}

		msg=unescape(chatMsgs.content[i].msg);
		msg=replaceEmoticon(msg);

		var chatBox=$(wChat+'_text');

		chatBox.innerHTML = chatBox.innerHTML + "<span class=\"timestamp\"> (" + chatMsgs.content[i].timestamp + ")</span> <strong class=\"userB\">" + chatMsgs.content[i].name_sender + ":</strong> <span class=\"new\">" + msg + "</span><br />\n";

    	chatBox.scrollTop = chatBox.scrollHeight - chatBox.clientHeight;


	}


	displayUsersList(chatMsgs.list,false)


}

function startPinging() {
	pingTimer=setInterval("ping()",15000);
}

function updateTimers() {
	chat_windows--;
	if (chat_windows==0) {
		clearInterval(pingTimer);
		pingTimer=setInterval("ping()",15000);
	}

}

function getLang() {
	var data="op=getLang";
	var objAjax = new Ajax.Request(
        	serverIMUrl,
        	{method: 'post', parameters: data, onComplete: getLangCompleted}
    );

}

function getLangCompleted(ObjReq) {
	var langText = ObjReq.responseText;
	_TTim = langText.evalJSON(true);

	startPinging();
}

/* replacing emoticons with images */
function getChatEmoticon(name) {
	var ext="gif";
	var res ="<img alt=\""+name+"\" title=\""+name+"\" src=\"";
	res+=pathImage+"emoticons/"+name+"."+ext+"\" />";

	return res;
}

function replaceEmoticon(txt) {

		var res=txt;

		res=res.replace(/;[-]?\)/i, getChatEmoticon("wink_smile"));
		res=res.replace(/:[-]?\|/i, getChatEmoticon("whatchutalkingabout_smile"));
		res=res.replace(/:[-]?P/i, getChatEmoticon("tounge_smile"));
		res=res.replace(/o:[-]?\)/i, getChatEmoticon("angel_smile"));
		res=res.replace(/:[-]?\)/i, getChatEmoticon("regular_smile"));
		res=res.replace(/:[-]?\(/i, getChatEmoticon("sad_smile"));
		res=res.replace(/:?\'[-]?(\(|\[)/i, getChatEmoticon("cry_smile"));
		res=res.replace(/:[-]?o/i, getChatEmoticon("omg_smile"));
		res=res.replace(/8[-]?\)/i, getChatEmoticon("shades_smile"));
		res=res.replace(/:[-]?s/i, getChatEmoticon("confused_smile"));
		res=res.replace(/X[-]?\(/i, getChatEmoticon("devil_smile"));
		res=res.replace(/\=\(\(/i, getChatEmoticon("broken_heart"));
		res=res.replace(/:[-]?x/i, getChatEmoticon("heart"));
		res=res.replace(/:[-]?d/i, getChatEmoticon("teeth_smile"));
		res=res.replace(/\[OK\]/, getChatEmoticon("thumbs_up"));
		res=res.replace(/\[BAD\]/, getChatEmoticon("thumbs_down"));
		res=res.replace(/\[IDEA\]/, getChatEmoticon("lightbulb"));
		res=res.replace(/\[MAIL\]/, getChatEmoticon("envelope"));

		return res;
}
/*------------------------------------------------------------*/

window.onunload = function() {
   clearInterval(pingTimer);
}

Event.observe(window,"load",getLang,false);
