/*
Filename: livesearch.js
Date Modified: 8/14/07

This File contain the ajax functions for the instant search feature.
*/
var xmlHttp

function showResult(str){
	if (str.length==0){ 
		document.getElementById("livesearch").
		innerHTML="";
		document.getElementById("livesearch").
		style.border="0px";
		return
	}
	xmlHttp=GetXmlHttpObject()

	if (xmlHttp==null){
	alert ("Browser does not support HTTP Request")
	return
	} 

	var url="includes/livesearch.php"
	url=url+"?q="+str
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
} 
//acp showresults
function acp_showResult(str){
	if (str.length==0){ 
		document.getElementById("livesearch").
		innerHTML="";
		document.getElementById("livesearch").
		style.border="0px";
		return
	}
	xmlHttp=GetXmlHttpObject()

	if (xmlHttp==null){
	alert ("Browser does not support HTTP Request")
	return
	} 

	var url="../includes/livesearch.php"
	url=url+"?q="+str
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
} 


function stateChanged(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("livesearch").
		innerHTML=xmlHttp.responseText;
		document.getElementById("livesearch").
		style.border="1px solid #A5ACB2";
	} 
}

function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}